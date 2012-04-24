<?php

/**
 * This is the index-action (default)
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendCurrencyConverterIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
                $this->loadDataGrid();
                $this->loadForm();
                $this->validateForm();
                $this->parse();

		$this->display();
	}

	private function loadDataGrid()
	{
		$this->dataGrid = new BackendDataGridDB(BackendCurrencyConverterModel::QRY_BROWSE, BL::getWorkingLanguage());
		$this->dataGrid->setSortingColumns(array('currency', 'rate', 'last_changed', 'link_id', 'time_id'), 'currency');
	}

        private function loadForm()
        {
            $this->frm = new BackendForm('source');
            $this->frm->addRadiobutton('ersource', BackendCurrencyConverterModel::returnLinks(), BackendCurrencyConverterModel::returnActiveLink());
            // submit dialog
            $this->frm->addButton('change', 'update', 'submit', 'inputButton button mainButton');
        }

        private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
                        $this->frm->cleanupFields();

                        // shorten the fields
                        $rbtSource = $this->frm->getField('ersource');

                        // validate the fields
                        $rbtSource->isFilled(BL::getError('ersourceIsRequired'));

                        if($this->frm->isCorrect())
                        {
                                // build array
                                $activeId = $rbtSource->getValue();

                                // insert the item
                                BackendCurrencyConverterModel::updateSource($activeId);
                        }
		}
	}

        protected function parse()
        {
            $this->frm->parse($this->tpl);
            // add datagrid
            $this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
        }
}
