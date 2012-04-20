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

        $this->addJS('highcharts/highcharts.js');
        $this->addJS('highcharts/themes/highroller.js');

        $this->loadTemplate();
        $this->createForm();
        $this->validateForm();

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
