<?php

/**
 * Installer for the sea module
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class SeaInstaller extends ModuleInstaller
{
	/**
	 * Install the module
	 */
	public function install()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'blog' as a module
		$this->addModule('sea', 'The sea module.');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'sea');

		// action rights
		$this->setActionRights(1, 'sea', 'index');
		$this->setActionRights(1, 'sea', 'connect');
		$this->setActionRights(1, 'sea', 'showdata');
		$this->setActionRights(1, 'sea', 'validate');

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$seaModuleId = $this->setNavigation($navigationModulesId, 'Sea');
		$this->setNavigation($seaModuleId, 'ShowData', 'sea/showdata', array('sea/index', 'sea/showdata'));
		$this->setNavigation($seaModuleId, 'Connect', 'sea/connect', array('sea/connect'));
	}
}
