<?php

/**
 * This is the configuration-object for the SEA module
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaConfig extends BackendBaseConfig
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
}
