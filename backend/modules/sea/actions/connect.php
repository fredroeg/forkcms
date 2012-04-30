<?php

/**
 * This is the connect-action (default)
 * When the user uses this module for the first time, he has to provide the necessary id's and tokens
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaConnect extends BackendBaseActionEdit
{
	public function execute()
	{
		parent::execute();
		$this->getData();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	private function getData()
	{
		$this->record = BackendSeaModel::getAPISettings();
	}

	private function loadForm()
	{
		$this->frm = new BackendForm('connectform');

                $this->frm->addText('clientId', $this->record['client_id']);
		$this->frm->addText('clientIdSecret', $this->record['client_secret']);

		// submit dialog
                $this->frm->addButton('change', 'update', 'submit', 'inputButton button mainButton');
	}

	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
                        $this->frm->cleanupFields();

                        // shorten the fields;
                        $txtClientId = $this->frm->getField('clientId');
                        $txtClientIdSecret = $this->frm->getField('clientIdSecret');

                        // validate the fields
                        $txtClientId->isFilled(BL::getError('BlockIsRequired'));
                        $txtClientIdSecret->isFilled(BL::getError('TitleIsRequired'));

                        if($this->frm->isCorrect())
                        {
                                // build array
                                $values['client_id'] = $txtClientId->getValue();
                                $values['client_secret'] = $txtClientIdSecret->getValue();

                                // insert the item
                                $id = (int) BackendSeaModel::updateIds($values);

                                // trigger event
                                //BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $values));

                                // everything is saved, so redirect to the overview
                                //$this->redirect(BackendModel::createURLForAction('graph') . '&report=edited&var=' . urlencode($values['block']) . '&highlight=row-' . $id);

				$this->checkStatus();
			}
		}
	}

	private function checkStatus()
	{
		$redirect = BackendSeaHelper::checkStatus();

		if($redirect != false)
		{

		}
		$accounts = BackendSeaHelp::getAccounts()->items;
		$accountArray = array();
		foreach ($accounts as $account)
		{
		    $accountArray['name'] = $account->name;
		    $accountArray['id'] = $account->id;
		}
		spoon::dump($accountArray);
	}

	protected function parse()
	{
		parent::parse();
	}
}
