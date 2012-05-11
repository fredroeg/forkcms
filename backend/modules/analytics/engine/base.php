<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class implements a lot of functionality that can be extended by a specific action
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendAnalyticsBase extends BackendBaseActionIndex
{
	/**
	 * The selected page
	 *
	 * @var	string
	 */
	protected $pagePath = null;

	/**
	 * The start and end timestamp of the collected data
	 *
	 * @var	int
	 */
	protected $startTimestamp, $endTimestamp;

	/**
	 *
	 * @var int
	 */
	protected $periodId;

	/**
	 * Check if we have the necessary data in the db, otherwise insert the missing data
	 */
	protected function checkPeriod()
	{
		// Define the period
		$startTimestamp = date('Y-m-d', $this->startTimestamp);
		$endTimestamp = date('Y-m-d', $this->endTimestamp);
		$period = array($startTimestamp, $endTimestamp);

		// Check if we already stored the data for that period in the database. (if not -> insert it!)
		// todo: insert the ! again
		if(!BackendAnalyticsModel::checkPeriod($period))
		{
			BackendAnalyticsHelper::getAllData($startTimestamp, $endTimestamp);
		}
		else
		{
			$this->periodId = BackendAnalyticsModel::getPeriodId(array($startTimestamp, $endTimestamp));
		}
	}

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->header->addJS('highcharts.js', 'core', false);
		BackendAnalyticsHelper::checkStatus();
		$this->setDates();
		$this->checkPeriod();
	}

	/**
	 * Parse this page
	 */
	protected function parse()
	{
		// period picker
		if(isset($this->pagePath)) BackendAnalyticsHelper::parsePeriodPicker($this->tpl, $this->startTimestamp, $this->endTimestamp, array('page_path' => $this->pagePath));
		else BackendAnalyticsHelper::parsePeriodPicker($this->tpl, $this->startTimestamp, $this->endTimestamp);
	}

	/**
	 * Set start and end timestamp needed to collect analytics data
	 */
	private function setDates()
	{
		BackendAnalyticsHelper::setDates();

		$this->startTimestamp = SpoonSession::get('analytics_start_timestamp');
		$this->endTimestamp = SpoonSession::get('analytics_end_timestamp');
	}
}
