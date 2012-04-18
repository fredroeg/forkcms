<?php
/**
 * This is the overview-action
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class FrontendCurrencyConverterIndex extends FrontendBaseBlock
{

    /**
     *
     * @var	FrontendForm
     */
    private $frm;


    /**
     *
     * @var array
     */
    protected $currencies = array();



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
        // parse
        $this->parse();
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        // parse form
        $this->frm->parse($this->tpl);
    }

    /*
     *  Create the form and append the input box and dropdownlist
     */
    private function createForm()
    {
        $this->frm = new FrontendForm('index', null, null, 'indexForm');
        $this->frm->addText('amount');
        $this->frm->addDropdown('currencySource', FrontendCurrencyConverterModel::getCurrencies());
        $this->frm->addDropdown('currencyTarget', FrontendCurrencyConverterModel::getCurrencies());
    }


    /**
     * Validate the form.
     */
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
        $amount = $_POST['amount'];
        $rateSource = FrontendCurrencyConverterModel::getRateByCurrency($_POST['currencySource']);
        $rateTarget = FrontendCurrencyConverterModel::getRateByCurrency($_POST['currencyTarget']);

        // calculate the exchange
        $exchange = $rateTarget / $rateSource;

        // calculate the exchange rate
        $converted = $amount * $exchange;

        // assign the message to the template
        $this->tpl->assign('convertIsSuccess', true);
        $this->tpl->assign('convertSucces', $converted);
    }
}
