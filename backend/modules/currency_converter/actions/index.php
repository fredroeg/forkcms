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
		$this->parse();
		$this->display();

	}

	private function loadDataGrid()
	{
		$this->dataGrid = new BackendDataGridDB(BackendCurrencyConverterModel::QRY_BROWSE, BL::getWorkingLanguage());
		//$this->dataGrid->setHeaderLabels(array('currency' => SpoonFilter::ucfirst(BL::getLabel('Currency')), 'rate' => SpoonFilter::ucfirst(BL::getLabel('Rate'))));
		$this->dataGrid->setSortingColumns(array('currency', 'rate', 'last_changed'), 'currency');

	}

        protected function parse()
        {
            // add datagrid
            $this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
        }


}
