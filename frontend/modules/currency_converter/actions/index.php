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

            $this->loadTemplate();
            
            $this->getData();
    }
    
    private function getData()
    {
        $data = $this->record = FrontendCurrencyConverterModel::getCurrencies();

        //var_dump($data);
        
    }
	
}
