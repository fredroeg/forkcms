<?php

/**
 * In this file we store all generic functions that we will be using in the currency_converter module
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
                'SELECT currency
                 FROM currency_converter_exchangerates');

        foreach ($currencies as $currency)
        {
            $currencyArray[$currency['currency']] = $currency['currency'];
        }
        return $currencyArray;
    }

    /**
     * Get the rate of the currency
     *
     * @return string
     */
    public static function getRateByCurrency($currency)
    {
        // get db
        $db = FrontendModel::getDB();

        $rate = $db->getRecord(
                "SELECT rate
                 FROM currency_converter_exchangerates
                 WHERE currency = '" . $currency . "'");

        return $rate['rate'];
    }



    /**
     * Check if the table is still up to date
     *
     */
    public static function checkLastUpdatedTable()
    {
        // get db
        $db = FrontendModel::getDB();

        $lastUpdated = $db->getRecord(
                "SELECT exchangetable_last_updated
                 FROM currency_converter_update");

        // if the date is different, it means that the table has to be updated
        if($lastUpdated['exchangetable_last_updated'] != date('Y-m-d'))
        {
            // no errors -> update the table
            if(self::updateExchangeTable())
            {
                $db->update("currency_converter_update", array("exchangetable_last_updated" => date("Y-m-d")));
            }
        }
    }

    /**
     * When the table isn't up to date anymore, we update all necessary fields
     *
     * @return boolean
     */
    private static function updateExchangeTable()
    {
        // get db
        $db = FrontendModel::getDB();

        $xmlUrl = "http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml";

        // displays all the file nodes
        if(!$xml=simplexml_load_file($xmlUrl))
        {
            return false;
        }

        // truncate the table (only once a day)
        //$db->truncate("currency_converter_exchangerates");

        // every exchange rate will be inserted back into the table
        foreach($xml->Cube->Cube->Cube as $cube)
        {
            $currency = (string) $cube->attributes()->currency;
            $rate = (string) $cube->attributes()->rate;

            //Get the rate of the record from the database
            $rateDB =$db->getRecord("SELECT rate FROM currency_converter_exchangerates WHERE currency = '" . $currency . "'");

            // Check if the rate of the xml is different from the rate in the database
            if($rate != $rateDB["rate"])
            {
                $record["rate"] = $rate;
                $record["last_changed"] = date('Y-m-d H:i:s');
                $sql =$db->update("currency_converter_exchangerates", $record, 'currency = ?', $currency);
                //var_dump();
            }
        }

        return true;
    }


}