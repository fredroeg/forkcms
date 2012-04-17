<?php
/**
 * This is the overview-action
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class FrontendCurrencyConverterIndex extends FrontendBaseBlock
{
    /**
     * FrontendForm instance
     *
     * @var	FrontendForm
     */
    private $frm;
        
    protected $currencies = array();
    
    public function execute()
    {
            parent::execute();

            $this->loadTemplate();
            $this->getData();
            $this->validateForm();
            $this->parse();
    }
    
    private function getData()
    {
        $dropdownArray = $this->recordsToArray();

        // create form
        $this->frm = new FrontendForm('index', null, null, 'indexForm');
        
        // create & add elements
        $this->frm->addText('amount');
        $this->frm->addDropdown('currencyTarget', $dropdownArray);
        
        
        
    }
    
    private function recordsToArray()
    {
        $this->currencies = FrontendCurrencyConverterModel::getCurrencies();
        $array = array();
        foreach ($this->currencies as $currency)
        {
            
            $array[$currency['rate']] = $currency['currency'];
        }
        return $array;
    }
    
    
    /**
     * Validate the form.
     */
    private function validateForm()
    {
        // submitted
        if($this->frm->isSubmitted())
        {
            // amount is required
            if($this->frm->getField('amount')->isFilled('Please fill in the amount'))
            {
                $this->frm->getField('amount')->isFloat('Only decimal values please!');
            }
            
            
            if($this->frm->isCorrect())
            {
                // all the information that was submitted
                $data = $this->frm->getValues();

                $amount = $data['amount'];
                $rate = $data['currencyTarget'];
                
                $converted = $amount * $rate;
                
                $this->tpl->assign('convertIsSuccess', true);
                $this->tpl->assign('convertSucces', $converted);
            }
        }
        
        
    }
    
    private function parse()
    {
        // parse form
        $this->frm->parse($this->tpl);
    }


	
}
