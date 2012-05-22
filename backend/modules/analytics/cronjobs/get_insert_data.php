<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This cronjob will fetch the requested data
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendAnalyticsCronjobGetInsertData extends BackendBaseCronjob
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$startTimestamp = trim(SpoonFilter::getGetValue('start_date', null, ''));
		$endTimestamp = trim(SpoonFilter::getGetValue('end_date', null, ''));

		$period = BackendAnalyticsModel::getLatestPeriod();
		$periodId = $period['period_id'] + 1;
		BackendAnalyticsHelper::insertAnalyticsData($startTimestamp, $endTimestamp, $periodId);
	}
}
