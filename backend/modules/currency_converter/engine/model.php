<?php

/**
 * In this file we store all generic functions that we will be using in the form_builder module
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendCurrencyConverterModel
{
    const QRY_BROWSE =
		'SELECT i.currency, i.rate, i.last_changed
		 FROM currency_converter_exchangerates AS i';
}