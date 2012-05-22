<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This edit-action will check the status using Ajax
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendAnalyticsAjaxCheckStatus extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$startTimestamp = SpoonSession::get('analytics_start_timestamp');
		$endTimestamp = SpoonSession::get('analytics_end_timestamp');
		$period = BackendAnalyticsModel::getLatestPeriod();

		if(date('Y-m-d', $startTimestamp) == $period['period_start'] && date('Y-m-d', $endTimestamp) == $period['period_end'])
		{
			// return status
			$status = 'done';
		}
		else
		{
			$status = false;
		}

		// no file - create one
		if($status === false)
		{
			// create file with initial counter
			// SpoonFile::setContent($filename, 'missing1');

			// return status
			$this->output(self::OK, array('status' => false), 'Data was missing. We are inserting the data.');
		}

		if(strpos($status, 'busy') !== false)
		{
			// get counter
			$counter = (int) substr($status, 4) + 1;

			// file's been busy for more than hundred cycles - just stop here
			if($counter > 100)
			{
				// return status
				$this->output(self::ERROR, array('status' => 'timeout'), 'Error while retrieving data - the script took too long to retrieve data.');
			}

			// return status
			$this->output(self::OK, array('status' => 'busy'), 'Data is being retrieved. (' . $counter . ')');
		}

		// unauthorized status
		if($status == 'unauthorized')
		{
			// remove all parameters from the module settings
			// todo

			BackendAnalyticsModel::clearTables();

			$this->output(self::OK, array('status' => 'unauthorized'), 'No longer authorized.');
		}

		// done status
		if($status == 'done')
		{
			// return status
			$this->output(self::OK, array('status' => 'done'), 'Data retrieved.');
		}

		// missing status
		if(strpos($status, 'missing') !== false)
		{
			// get counter
			$counter = (int) substr($status, 7) + 1;

			// file's been missing for more than ten cycles - just stop here
			if($counter > 10)
			{
				$this->output(self::ERROR, array('status' => 'missing'), 'Error while retrieving data - data was never inserted.');
			}

			// return status
			$this->output(self::OK, array('status' => 'busy'), 'Status missing. (' . $counter . ')');
		}

		/* FALLBACK - SOMETHING WENT WRONG */
		$this->output(self::ERROR, array('status' => 'error'), 'Error while retrieving data.');
	}
}
