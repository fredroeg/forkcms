<?php

/**
 * This is the configuration-object for the blog module
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
final class BackendBannersConfig extends BackendBaseConfig
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
