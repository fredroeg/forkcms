<?php

/**
 * In this file we store all generic functions that we will be using in the currency converter module
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendCurrencyConverterModel
{
        const QRY_BROWSE =
                    'SELECT i.currency, i.rate, i.last_changed
                     FROM currency_converter_exchangerates AS i';

        const QRY_SETTINGS =
                    'SELECT *
                     FROM currency_converter_graphsettings AS j';

        /**
         * Does the item exist.
         *
         * @param int $id Id of a form.
         * @return bool
         */
        public static function exists($id)
        {
                return (BackendModel::getDB()->getVar('SELECT COUNT(f.id) FROM currency_converter_graphsettings AS f WHERE f.id = ?', (int) $id) >= 1);
        }

        /**
	 * Get all data for a given id.
	 *
	 * @param int $id The id for the record to get.
	 * @return array
	 */
	public static function get($id)
	{
		$return = (array) BackendModel::getDB()->getRecord('SELECT f.*	FROM currency_converter_graphsettings AS f WHERE f.id = ?', (int) $id);

		return $return;
	}

        /**
	 * Get errors (optional by type).
	 *
	 * @param string[optional] $type Type of error.
	 * @return mixed
	 */
	public static function getErrors($type = null)
	{
		$errors['required'] = FL::getError('FieldIsRequired');
		$errors['email'] = FL::getError('EmailIsInvalid');
		$errors['numeric'] = FL::getError('NumericCharactersOnly');

		// specific type
		if($type !== null)
		{
			$type = (string) $type;
			return $errors[$type];
		}

		// all errors
		else
		{
			$return = array();

			// loop errors
			foreach($errors as $key => $error) $return[] = array('type' => $key, 'message' => $error);

			return $return;
		}
	}

        public static function getEnumValues($field)
        {
            $enums = (array) BackendModel::getDB()->getEnumValues('currency_converter_graphsettings', $field);

            //We make an improved array to populate the dropdownbox in a better way
            $imprEnumsarray = array();
            foreach ($enums as $enum)
            {
                $imprEnumsarray[$enum] = $enum;
            }

            return $imprEnumsarray;
        }

        /**
	 * Update an existing item.
	 *
	 * @param int $id The id for the item to update.
	 * @param array $values The new data.
	 * @return int
	 */
	public static function update($id, array $values)
	{
		$id = (int) $id;
		$db = BackendModel::getDB(true);

		// update item
		$db->update('currency_converter_graphsettings', $values, 'id = ?', $id);

		return $id;
	}

}