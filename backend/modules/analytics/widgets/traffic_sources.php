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
		$this->display();
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

			// parse the datagrid
			$this->tpl->assign('dgAnalyticsKeywords', $dataGrid->getContent());
		}
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
			$dataGrid->setColumnsHidden('url');
			$dataGrid->setColumnURL('referrer', '[url]');

			// parse the datagrid
			$this->tpl->assign('dgAnalyticsReferrers', $dataGrid->getContent());
		}
	}
}
