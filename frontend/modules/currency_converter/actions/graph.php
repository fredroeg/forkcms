<?php
/**
 * This is the overview-action
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class FrontendCurrencyConverterGraph extends FrontendBaseBlock
{
    public function execute()
    {
        parent::execute();

        //Add the javascript file
        $this->addJS('highcharts/highcharts.js');

        //Theming
        $this->addJS('highcharts/themes/grid.js');

        $this->loadTemplate();
        $this->createForm();
        $this->validateForm();

        //$this->createChart();
        $this->display();
    }

    private function display()
    {
        $this->parse();
    }

    private function parse()
    {
        $this->frm->parse($this->tpl);
    }

    private function createForm()
    {
        $this->frm = new FrontendForm('graph', null, null, 'graphForm');
        $this->frm->addDropdown('currency', FrontendCurrencyConverterModel::getCurrencies(true));
    }

    private function validateForm()
    {
        if($this->frm->isSubmitted())
        {
            $this->createEvolutionChart();
        }
    }

    private function createChart()
    {
        $chartArray = FrontendCurrencyConverterModel::getExchangeRate();

        $tableData = json_encode($chartArray);
        //$this->tpl->assign('val', $tableData);
    }

    /**
     * With this function we will be able to view the evolution of a currency
     */
    private function createEvolutionChart()
    {
        $cur = $this->frm->getField('currency')->getValue();
        $evolutionArray = FrontendCurrencyConverterModel::getEvolutionOfCurrency($cur);
        $tempArray = array();
        foreach ($evolutionArray as $value)
        {
            $tempArray[$value['exchangetable_last_updated']] = $value['rate'];
        }

        $tableData = json_encode($tempArray);
        $this->tpl->assign('val', $tableData);
        $this->tpl->assign('cur', $cur);
    }



}
