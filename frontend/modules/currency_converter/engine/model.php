<?php

/**
 * 
 *
 * @author Frederick Roegiers
 */
class FrontendCurrencyConverterModel
{
    /**
	 * Get all currencues for the dropdownlist.
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
	 * Get the rate of the currency
	 *
	 * @param string $currency. The currency of the item to fetch.
	 * @return decimal
	 */
	public static function getRateByCurrency($currency)
	{
		$id = (int) $id;
                
                //return $rate;

	}


}