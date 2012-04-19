<?php
/**
 * This is the overview-action
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class FrontendCurrencyConverterIndex extends FrontendBaseBlock
{
    public function execute()
    {
        parent::execute();

        //Add the javascript file
        $this->addJS('highcharts/highcharts.js');

        //Theming
        //
        $this->addJS('highcharts/themes/grid.js');

        $this->loadTemplate();
        $this->createForm();
        $this->validateForm();

        $this->createChart();

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
        $this->frm = new FrontendForm('index', null, null, 'indexForm');
        $this->frm->addText('amount');
        $this->frm->addDropdown('currencySource', FrontendCurrencyConverterModel::getCurrencies());
        $this->frm->addDropdown('currencyTarget', FrontendCurrencyConverterModel::getCurrencies());
    }

    private function validateForm()
    {
        // submitted
        if($this->frm->isSubmitted())
        {
            // amount is required and has to be numeric or float
            if($this->frm->getField('amount')->isFilled('Please fill in the amount'))
            {
                $this->frm->getField('amount')->isFloat('Only decimal values please!');
            }

            if($this->frm->isCorrect())
            {
                $this->calculate();
            }
        }
    }

    /**
     * Calculate the exchange rate
     */
    private function calculate()
    {
        // get the submitted values
        $amount = $this->frm->getField('amount')->getValue();
        $curSource = $this->frm->getField('currencySource')->getValue();
        $curTarget = $this->frm->getField('currencyTarget')->getValue();

        $rateSource = FrontendCurrencyConverterModel::getRateByCurrency($curSource);
        $rateTarget = FrontendCurrencyConverterModel::getRateByCurrency($curTarget);

        // calculate the exchange
        $exchange = $rateTarget / $rateSource;

        // calculate the exchange rate
        $converted = $amount * $exchange;

        // make a succes message
        $succesmessage = $amount . " " . $curSource . " = " . $converted . " " . $curTarget;

        // assign the message to the template
        $this->tpl->assign('convertIsSuccess', true);
        $this->tpl->assign('convertSucces', $succesmessage);
    }

    private function createChart()
    {
        //For now we will use a jsonstring.
        //TODO: will be replaced by a exported CSV-file
        //SOURCE to do this:    http://www.highcharts.com/documentation/how-to-use#options ,
        //                      http://www.highcharts.com/studies/data-from-csv.htm
        $chartArray = FrontendCurrencyConverterModel::getExchangeRate();

        $tableData = json_encode($chartArray);
        $this->tpl->assign('val', $tableData);
    }
}
