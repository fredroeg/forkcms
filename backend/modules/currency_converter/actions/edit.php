<?php

/**
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendCurrencyConverterEdit extends BackendBaseActionEdit
{
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendFormBuilderModel::exists($this->id))
		{
			parent::execute();
			$this->getData();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
			$this->display();
		}

		// no item found, throw an exceptions, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	private function getData()
	{
		$this->record = BackendCurrencyConverterModel::get($this->id);
	}

	private function loadForm()
	{
		$this->frm = new BackendForm('edit');
		$this->frm->addText('block', $this->record['block']);
		$this->frm->addDropdown('type', BackendCurrencyConverterModel::getEnumValues('type'), $this->record['type']);
                $this->frm->addDropdown('theme', BackendCurrencyConverterModel::getEnumValues('theme'), $this->record['theme']);
		$this->frm->addText('title', $this->record['title']);
		$this->frm->addText('subtitle', $this->record['subtitle']);
                $this->frm->addText('xaxistitle', $this->record['xaxis_title']);
		$this->frm->addText('yaxistitle', $this->record['yaxis_title']);

		// submit dialog
                $this->frm->addButton('change', 'update');
	}

        protected function parse()
	{
		parent::parse();

		$this->tpl->assign('id', $this->record['id']);

		// parse error messages
		$this->parseErrorMessages();
	}

	/**
	 * Parse the default error messages
	 */
	private function parseErrorMessages()
	{
		// set frontend locale
		FL::setLocale(BL::getWorkingLanguage());

		// assign error messages
		$this->tpl->assign('errors', BackendCurrencyConverterModel::getErrors());
	}

	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
                        $this->frm->cleanupFields();

                        // shorten the fields
                        $txtBlock = $this->frm->getField('block');
                        $ddmType = $this->frm->getField('type');
                        $ddmTheme = $this->frm->getField('theme');
                        $txtTitle = $this->frm->getField('title');
                        $txtSubtitle = $this->frm->getField('subtitle');
                        $txtXAxisTitle = $this->frm->getField('xaxistitle');
                        $txtYAxisTitle = $this->frm->getField('yaxistitle');

                        // validate the fields
                        $txtBlock->isFilled(BL::getError('BlockIsRequired'));
                        $txtTitle->isFilled(BL::getError('TitleIsRequired'));
                        $txtSubtitle->isFilled(BL::getError('SubtitleIsRequired'));
                        $txtXAxisTitle->isFilled(BL::getError('XaxistitleIsRequired'));
                        $txtYAxisTitle->isFilled(BL::getError('YaxistitleIsRequired'));

                        if($this->frm->isCorrect())
                        {
                                // build array
                                $values['block'] = $txtBlock->getValue();
                                $values['type'] = $ddmType->getValue();
                                $values['theme'] = $ddmTheme->getValue();
                                $values['title'] = $txtTitle->getValue();
                                $values['subtitle'] = $txtSubtitle->getValue();
                                $values['xaxis_title'] = $txtXAxisTitle->getValue();
                                $values['yaxis_title'] = $txtYAxisTitle->getValue();

                                // insert the item
                                $id = (int) BackendCurrencyConverterModel::update($this->id, $values);

                                // trigger event
                                BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $values));

                                // everything is saved, so redirect to the overview
                                $this->redirect(BackendModel::createURLForAction('graph') . '&report=edited&var=' . urlencode($values['block']) . '&highlight=row-' . $id);
                        }
		}
	}
}
