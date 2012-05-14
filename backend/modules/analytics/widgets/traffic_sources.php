<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This widget will show the latest traffic sources
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class BackendAnalyticsWidgetTrafficSources extends BackendBaseWidget
{
	/**
	 * Execute the widget
	 */
	public function execute()
	{
		// check analytics session token and analytics table id
		$APISettingsArray = BackendAnalyticsModel::getAPISettings();
		if($APISettingsArray['access_token'] == '') return;
		if($APISettingsArray['table_id'] == '') return;

		// settings are ok, set option
		$this->tpl->assign('analyticsValidSettings', true);

		$this->setColumn('left');
		$this->setPosition(0);
		$this->header->addJS('dashboard.js', 'analytics');
		$this->parse();
		$this->getData();
		$this->display();
	}

	/**
	 * Parse into template
	 */
	private function getData()
	{
		$URL = SITE_URL . '/backend/cronjob.php?module=analytics&action=get_traffic_sources&id=2';

		// set options
		$options = array();
		$options[CURLOPT_URL] = $URL;
		if(ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) $options[CURLOPT_FOLLOWLOCATION] = true;
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_TIMEOUT] = 1;

		$curl = curl_init();
		curl_setopt_array($curl, $options);
		curl_exec($curl);
		curl_close($curl);
	}

	/**
	 * Parse into template
	 */
	private function parse()
	{
		// get dashboard data
		$periodId = BackendAnalyticsModel::getLatestPeriod();

		$startTimestamp = $periodId['period_start'];
		$endTimestamp = $periodId['period_end'];

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('settings', 'analytics'))
		{
			// parse redirect link
			$this->tpl->assign('settingsUrl', BackendModel::createURLForAction('settings', 'analytics'));
		}

		$this->parseKeywords();
		$this->parseReferrers();

		$this->tpl->assign('analyticsRecentVisitsStartDate', $startTimestamp);
		$this->tpl->assign('analyticsRecentVisitsEndDate', $endTimestamp);
	}

	/**
	 * Parse the keywords datagrid
	 */
	private function parseKeywords()
	{
		$periodId = BackendAnalyticsModel::getLatestPeriod();
		$results = BackendAnalyticsModel::getRecentKeywords($periodId['period_id']);

		if(!empty($results))
		{
			$dataGrid = new BackendDataGridArray($results);
			$dataGrid->setPaging(false);
			// $dataGrid->setColumnsHidden('id', 'date');

			// parse the datagrid
			$this->tpl->assign('dgAnalyticsKeywords', $dataGrid->getContent());
		}

		// get date
		// $date = (isset($results[0]['date']) ? substr($results[0]['date'], 0, 10) : date('Y-m-d'));
		// $timestamp = mktime(0, 0, 0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));
	}

	/**
	 * Parse the referrers datagrid
	 */
	private function parseReferrers()
	{
		$periodId = BackendAnalyticsModel::getLatestPeriod();
		$results = BackendAnalyticsModel::getRecentReferrers($periodId['period_id']);
		if(!empty($results))
		{
			$dataGrid = new BackendDataGridArray($results);
			$dataGrid->setPaging(false);
			// $dataGrid->setColumnsHidden('id', 'date', 'url');
			// $dataGrid->setColumnURL('referrer', '[url]');

			// parse the datagrid
			$this->tpl->assign('dgAnalyticsReferrers', $dataGrid->getContent());
		}
	}
}
