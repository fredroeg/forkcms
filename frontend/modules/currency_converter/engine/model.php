<?php

/**
 * In this file we store all generic functions that we will be using in the currency_converter module
 *
 * @author Frederick Roegiers
 */
class FrontendCurrencyConverterModel
{
    const DB_EXCHANGERATES_TABLE = 'currency_converter_exchangerates';
    const DB_UPDATE_TABLE = 'currency_converter_update';

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
               ' WHERE link_id = ?
                 ORDER BY currency ASC',
                 self::returnActiveExchangeRate('id')
                 );

        return $currencies;
    }

    /**
     * Get and return all the exchangerates for the calculation
     *
     * @return array
     */
    public static function getExchangeRate()
    {
        $db = FrontendModel::getDB();

        $exchangeRates = (array) $db->getPairs(
                'SELECT currency, rate
                 FROM ' . self::DB_EXCHANGERATES_TABLE .
               ' WHERE link_id = ?',
                 self::returnActiveExchangeRate('id')
                 );

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
                'SELECT rate
                 FROM ' . self::DB_EXCHANGERATES_TABLE .
               ' WHERE currency = ? AND link_id= ?
                 ORDER BY time_id DESC',
                 array($currency, self::returnActiveExchangeRate('id'))
                 );

        return $rate;
    }

    /**
     * We view the evolution of a currency with this function
     * we can use this later on for the highcharts
     *
     * @param string $currency
     * @return array
     */
    public static function getEvolutionOfCurrency($currency)
    {
        $db = FrontendModel::getDB();
        $evol = $db->getRecords(
                'SELECT *
                 FROM ' . self::DB_EXCHANGERATES_TABLE .
               ' AS A INNER JOIN currency_converter_update AS U ON A.time_id  = U.currency_converter_update_id' .
               ' WHERE currency = ? AND link_id= ?
                 ORDER BY time_id ASC',
                 array($currency, self::returnActiveExchangeRate('id'))
                 );

        return $evol;
    }

    /**
     * With this function we get all settings for the highchart
     *
     * @return array
     */
    public static function getGraphSettings()
    {
        $db = FrontendModel::getDB();
        $settings = (array) $db->getRecord(
               'SELECT *
                FROM currency_converter_graphsettings
                WHERE id = ?',
                    1);

        return $settings;
    }


    /**
     * Check if the table is still up to date
     *
     */
    public static function checkLastUpdatedTable()
    {
        $db = FrontendModel::getDB();

        $lastUpdated = $db->getRecord(
                'SELECT * FROM currency_converter_update
                 ORDER BY currency_converter_update_id
                 DESC LIMIT 1');

        // if the date is different, it means that the table has to be updated
        if($lastUpdated['exchangetable_last_updated'] != date('Y-m-d'))
        {
            $timeId = $lastUpdated['currency_converter_update_id'] + 1;

            // things have changed -> update the table
            if(self::updateExchangeRates($timeId))
            {
                $record['exchangetable_last_updated'] = date('Y-m-d');
                $record['currency_converter_update_id'] = $timeId;

                $db->insert(self::DB_UPDATE_TABLE, $record);
            }
        }
    }

    /**
     * Every day (if the website is visited) this table will be updated
     *
     * @return boolean
     */
    private static function updateExchangeRates($timeId)
    {
        $db = FrontendModel::getDB();

        $ecbExchangeRateArray = self::getECBExchangeRates();
        $openExchangeRateArray = self::getOpenExchangeRates();

        //Insert the table with the exchangerates of ECB
        //LinkID --> 1
        foreach($ecbExchangeRateArray as $currency => $rate)
        {
            $record['rate'] = $rate;
            $record['currency'] = $currency;
            $record['time_id'] = $timeId;
            $record['link_id'] = 1;

            $db->insert(self::DB_EXCHANGERATES_TABLE, $record);
        }
        self::insertEurManually($timeId);

        //Insert the table with the exchangerates of openexchangerates
        //linkID --> 2
        $euroRate = $openExchangeRateArray['EUR'];
        foreach ($openExchangeRateArray as $currency => $rate)
        {
            $record['currency'] = $currency;
            //To maintain the same base, we devide by eurorate
            $record['rate'] = $rate/$euroRate;
            $record['time_id'] = $timeId;
            $record['link_id'] = 2;

            $db->insert(self::DB_EXCHANGERATES_TABLE, $record);
        }

        return true;
    }


    /**
     * Get all the exchangerates from the XML-file of the European Central Bank
     * source: http://www.ecb.int/
     *
     * @return array
     */
    private static function getECBExchangeRates()
    {
        $db = FrontendModel::getDB();

        $link = $db->getVar(
                'SELECT link
                 FROM currency_converter_exchangerates_source
                 WHERE id=1'
                );

        if(!$xml=simplexml_load_file($link))
        {
            return false;
        }

        $exchangeRateArray = array();

        // every exchange rate will be inserted back into the table
        foreach($xml->Cube->Cube->Cube as $cube)
        {
            $currency = (string) $cube->attributes()->currency;
            $rate = (string) $cube->attributes()->rate;

            $exchangeRateArray[$currency] = $rate;
        }

        return $exchangeRateArray;
    }

    /**
     * Get all the exchangerates from the json-string from the openechangerates
     * source: http://openexchangerates.org/
     *
     * @return array
     */
    private static function getOpenExchangeRates()
    {
        $db = FrontendModel::getDB();

        $link = $db->getVar(
                'SELECT link
                 FROM currency_converter_exchangerates_source
                 WHERE id=2'
                );

        // Open CURL session:
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Get the data:
        $json = curl_exec($ch);
        curl_close($ch);

        // Decode JSON response:
        $jsonContent = json_decode($json);

        $exchangeRateArray = array();

        // You can now access the rates inside the parsed object, like so:
        foreach ($jsonContent as $key => $values)
        {
            if($key == 'rates')
            {
                foreach ($values as $cur => $rate)
                {
                    $exchangeRateArray[$cur] = $rate;
                }
            }
        }

        return $exchangeRateArray;
    }

    /**
     * With this function we insert the eurocurrency manually
     * because it ECB didn't provide it
     *
     * @param int $timeId
     * @return boolean
     */
    private static function insertEurManually($timeId)
    {
        $db = FrontendModel::getDB();

        $record['rate'] = "1";
        $record['currency'] = "EUR";
        $record['time_id'] = $timeId;

        $db->insert(self::DB_EXCHANGERATES_TABLE, $record);

        return true;
    }

    /**
     * With this function we get the active url and id (which was chosen in the back-end)
     *
     * @return string
     */
    public static function returnActiveExchangeRate($field)
    {
        $db = FrontendModel::getDB();

        $link = (array) $db->getRecord(
                'SELECT id, link
                 FROM currency_converter_exchangerates_source
                 WHERE active = 1'
                );

        if(isset ($field) && $field == 'id')
        {
            return $link['id'];
        }
        else if(isset ($field) && $field == 'link')
        {
            return $link['link'];
        }
    }
}