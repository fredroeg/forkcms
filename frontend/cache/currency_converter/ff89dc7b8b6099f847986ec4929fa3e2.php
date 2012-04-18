<?php

//Only report errors, not warnings or notices
error_reporting(E_ERROR);

$xmlUrl = "http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml";

// displays all the file nodes
if(!$xml=simplexml_load_file($xmlUrl))
{
    $xmlError = "Error loading exchange rates";
}
else
{
    FrontendCurrencyConverterModel::updateCurrencyTable($xml);
}