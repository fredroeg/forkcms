<?php

/**
 * In this file we store all generic functions that we will be using in the currency_converter module
 *
 * @author Frederick Roegiers
 */
class FrontendCurrencyConverterModel
{
    //CONSTANTS
    const CURRENCY_XML_URL = "http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml";
    const DB_EXCHANGERATES_TABLE = "currency_converter_exchangerates";
    const DB_UPDATE_TABLE = "currency_converter_update";

    /**
     * Get all currencies for the dropdownlist.
     *
     * @return array
     */
    public static function getCurrencies()
    {
        //Check updates on the fly
        self::checkLastUpdatedTable();

        $db = FrontendModel::getDB();

        $currencies = (array) $db->getPairs(
                'SELECT currency, currency AS currencyLbl
                 FROM ' . self::DB_EXCHANGERATES_TABLE);


        return $currencies;
    }

    public static function getExchangeRate()
    {
        $db = FrontendModel::getDB();

        $exchangeRates = (array) $db->getPairs(
                'SELECT currency, rate
                 FROM ' . self::DB_EXCHANGERATES_TABLE);


        return $exchangeRates;
    }

    /**
     * Get the rate of the currency
     *
     * @return string
     */
    public static function getRateByCurrency($currency)
    {
        $db = FrontendModel::getDB();

        $rate = $db->getVar(
                "SELECT rate
                 FROM " . self::DB_EXCHANGERATES_TABLE .
                 " WHERE currency = ?", $currency);

        return $rate;
    }



    /**
     * Check if the table is still up to date
     *
     */
    public static function checkLastUpdatedTable()
    {
        $db = FrontendModel::getDB();

        $lastUpdated = $db->getVar(
                "SELECT exchangetable_last_updated
                 FROM " . self::DB_UPDATE_TABLE);

        // if the date is different, it means that the table has to be updated
        if($lastUpdated != date('Y-m-d'))
        {
            // no errors -> update the table
            if(self::updateExchangeTable())
            {
                $db->update(self::DB_UPDATE_TABLE, array("exchangetable_last_updated" => date("Y-m-d")));
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
        $db = FrontendModel::getDB();

        // displays all the file nodes
        if(!$xml=simplexml_load_file(self::CURRENCY_XML_URL))
        {
            return false;
        }

        // every exchange rate will be inserted back into the table
        foreach($xml->Cube->Cube->Cube as $cube)
        {
            $currency = (string) $cube->attributes()->currency;
            $rate = (string) $cube->attributes()->rate;

            //Get the rate of the record from the database
            $rateDB =$db->getVar("SELECT rate FROM " . self::DB_EXCHANGERATES_TABLE ." WHERE currency = ?", $currency);

            // Check if the rate of the xml is different from the rate in the database
            if($rate != $rateDB)
            {
                $record["rate"] = $rate;
                $record["last_changed"] = date('Y-m-d H:i:s');
                $db->update(self::DB_EXCHANGERATES_TABLE, $record, 'currency = ?', $currency);
            }
        }

        return true;
    }


}