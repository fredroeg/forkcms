<?php

/**
 * This is the graph-action
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendCurrencyConverterGraph extends BackendBaseActionIndex
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
		$this->dataGrid = new BackendDataGridDB(BackendCurrencyConverterModel::QRY_SETTINGS, BL::getWorkingLanguage());
		$this->dataGrid->setHeaderLabels(
                        array(  'block' => SpoonFilter::ucfirst(BL::getLabel('Block')),
                                'type' => SpoonFilter::ucfirst(BL::getLabel('Type')),
                                'theme' => SpoonFilter::ucfirst(BL::getLabel('Theme')),
                                'title' => SpoonFilter::ucfirst(BL::getLabel('Title')),
                                'subtitle' => SpoonFilter::ucfirst(BL::getLabel('Subtitle')),
                                'xaxis_title' => SpoonFilter::ucfirst(BL::getLabel('XAxisTitle')),
                                'yaxis_title' => SpoonFilter::ucfirst(BL::getLabel('YAxisTitle'))
                             ));
                // check if edit action is allowed
		if(BackendAuthentication::isAllowedAction('edit'))
		{
                    $this->dataGrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::getLabel('Edit'));
                }
	}

        protected function parse()
        {
                // add datagrid
                $this->tpl->assign('dgGraphSettings', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
        }
}
