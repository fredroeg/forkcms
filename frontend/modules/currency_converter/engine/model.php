<?php

/**
 * 
 *
 * @author Frederick Roegiers
 */
class FrontendCurrencyConverterModel
{ 
    
    /**
     * Get all currencies for the dropdownlist.
     *
     * @return array
     */
    public static function getCurrencies()
    {
        // get db
        $db = FrontendModel::getDB();

        $currencies = (array) $db->getRecords(
                'SELECT *
                 FROM currency_converter_rates');

        return $currencies;
    }

    /**
     * Update the table
     * This function will be executed max. once a day
     *
     */
    public static function updateCurrencyTable($xml)
    {            
        // get db
        $db = FrontendModel::getDB();
        
        // truncate the table (only once a day)
        $db->truncate("currency_converter_rates");
        
        // every exchange rate will be inserted back into the table
        foreach($xml->Cube->Cube->Cube as $cube)
        {
            $currency = (string) $cube->attributes()->currency;
            $rate = (string) $cube->attributes()->rate;

            $db->insert("currency_converter_rates", array("currency" => $currency, "rate" => $rate));
        }
    }


}