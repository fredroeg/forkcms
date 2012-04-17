<?php

/**
 * This is the configuration-object for the mailmotor module
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendCurrencyConverterConfig extends BackendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'index';

	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();

	/**
	 * Check if all required settings have been set
	 *
	 * @param string $module The module.
	 */
	public function __construct($module)
	{
		parent::__construct($module);


	}

	/**
	 * Checks if all necessary settings were set.
	 */
	private function checkForSettings()
	{
		
	}

	/**
	 * Loads additional engine files
	 */
	private function loadEngineFiles()
	{
		
	}
}
