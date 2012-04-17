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
            //$this->parse();
    }
    
    private function getData()
    {
        $dropdownArray = $this->recordsToArray();

        // create form
        $this->frm = new FrontendForm('index', null, null, 'indexForm');
        
        // create & add elements
        $this->frm->addText('amount');
        $this->frm->addDropdown('currency', $dropdownArray);
        
        // parse form
        $this->frm->parse($this->tpl);
        
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
    

	
}
