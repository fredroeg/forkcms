<?php
/**
 * This is a widget with the convert form
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class FrontendCurrencyConverterWidgetConvert extends FrontendBaseWidget
{
    public function execute()
    {
        parent::execute();

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
        $this->frm = new FrontendForm('convert', null, null, 'convertForm');
        $this->frm->addText('amount');
        $this->frm->addDropdown('currencySource', FrontendCurrencyConverterModel::getCurrencies(true));
        $this->frm->addDropdown('currencyTarget', FrontendCurrencyConverterModel::getCurrencies(false));
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
        $this->tpl->assign('convertWidgetIsSuccess', true);
        $this->tpl->assign('convertWidgetSucces', $succesmessage);
    }
}