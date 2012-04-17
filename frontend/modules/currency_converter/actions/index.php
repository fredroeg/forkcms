<?php
/**
 * This is the overview-action
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class FrontendCurrencyConverterIndex extends FrontendBaseBlock
{
    protected $currencies = array();
    
    public function execute()
    {
            parent::execute();

            $this->loadTemplate();
            
            $this->getData();
    }
    
    private function getData()
    {
        $this->currencies = $this->record = FrontendCurrencyConverterModel::getCurrencies();

        Spoon::dump($this->currencies);
        
    }
	
}
