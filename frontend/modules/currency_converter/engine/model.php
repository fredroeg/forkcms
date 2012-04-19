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
    public static function getCurrencies($first)
    {
        if($first)
        {
            //Check updates on the fly
            self::checkLastUpdatedTable();
        }


        $db = FrontendModel::getDB();

        $currencies = (array) $db->getPairs(
                'SELECT currency, currency AS currencyLbl
                 FROM ' . self::DB_EXCHANGERATES_TABLE .
                ' ORDER BY currency ASC');

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
                 " WHERE currency = ? ORDER BY time_id DESC", $currency);

        return $rate;
    }

    public static function getEvolutionOfCurrency($currency)
    {
        $db = FrontendModel::getDB();
        $evol = $db->getRecords(
                "SELECT *
                 FROM " . self::DB_EXCHANGERATES_TABLE .
                " AS A INNER JOIN currency_converter_update AS U ON A.time_id  = U.currency_converter_update_id" .
                " WHERE currency = ? ORDER BY time_id ASC", $currency);

        return $evol;
    }



    /**
     * Check if the table is still up to date
     *
     */
    public static function checkLastUpdatedTable()
    {
        $db = FrontendModel::getDB();

        $lastUpdated = $db->getRecord(
                "SELECT * FROM currency_converter_update ORDER BY currency_converter_update_id DESC LIMIT 1");

        // if the date is different, it means that the table has to be updated
        if($lastUpdated['exchangetable_last_updated'] != date('Y-m-d'))
        {
            // no errors -> update the table
            if(self::updateExchangeTable($lastUpdated['currency_converter_update_id']))
            {
                //$db->insert(self::DB_UPDATE_TABLE, array("exchangetable_last_updated" => date("Y-m-d")));
            }
        }
    }

    /**
     * When the table isn't up to date anymore, we update all necessary fields
     *
     * @return boolean
     */
    private static function updateExchangeTable($timeId)
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
            $rateDB =$db->getVar("SELECT rate FROM " . self::DB_EXCHANGERATES_TABLE ." WHERE currency = ? ORDER BY time_id DESC", $currency);

            // Check if the rate of the xml is different from the rate in the database
            if($rate != $rateDB)
            {
                $record["rate"] = $rate;
                $record["currency"] = $currency;
                $record["time_id"] = $timeId;
                $record["last_changed"] = date('Y-m-d H:i:s');
                $db->insert(self::DB_EXCHANGERATES_TABLE, $record);
           }

        }

        return true;
    }


}