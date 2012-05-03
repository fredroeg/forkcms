<?php

/**
 * This is the showdata-action
 * It displays all the highcharts, the sea-statistics, ...
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaShowdata extends BackendSeaBase
{
	public function execute()
	{
		parent::execute();
		$this->checkStatus();
		$this->seaDataDump();
		$this->parseLineChartData();
		$this->parseMultiLineChartData();
		$this->parse();
		$this->display();
	}

	protected function parse()
	{
		parent::parse();
	}

	/**
	 * Check if our access token is still valid
	 */
	private function checkStatus()
	{
		if(!BackendSeaHelper::checkStatus())
		{
			$this->redirect('connect');
		}
	}

	/**
	 * Check if we have the necessary data in the db, otherwise insert the missing data
	 */
	private function seaDataDump()
	{
		//Define the period
		$startTimestamp = date('Y-m-d', SpoonSession::get('sea_start_timestamp'));
		$endTimestamp = date('Y-m-d', SpoonSession::get('sea_end_timestamp'));
		$period = array($startTimestamp, $endTimestamp);

		//Check if we already stored the data for that period in the database. (if not -> insert it!)
		if(!BackendSeaModel::checkPeriod($period))
		{
			BackendSeaHelp::getAllData($period);
		}

		$this->getDataFromThisPeriod(BackendSeaModel::getPeriodId($period));
		$this->getGoals();
	}

	/**
	 * Get all the data from that period, and assign it to the template
	 *
	 * @param array $periodId
	 */
	private function getDataFromThisPeriod($periodId)
	{
		$periodDataArray = BackendSeaModel::getSEAData($periodId);

		// we check if the array isn't empty
		// possible if the adwords account isn't coupled with the GA account
		if(!empty ($periodDataArray))
		{
			$this->tpl->assign('visits', $periodDataArray['visits']);
			$this->tpl->assign('conversions', $periodDataArray['conversions']);
			$this->tpl->assign('conversionPercentage', $periodDataArray['conversion_percentage'] . '&#37;');
			$this->tpl->assign('costPerConversion', $periodDataArray['cost_per_conversion']);
			$this->tpl->assign('impressions', $periodDataArray['impressions']);
			$this->tpl->assign('clicks', $periodDataArray['clicks_amount']);
			$this->tpl->assign('ctr', $periodDataArray['click_through_rate']);
			$this->tpl->assign('costPerClick', $periodDataArray['cost_per_click']);
			//$this->tpl->assign('position', $periodDataArray['position']);
			$this->tpl->assign('cost', $periodDataArray['costs']);
		}
		else
		{
			$value = 0;
			$this->tpl->assign('visits', $value);
			$this->tpl->assign('conversions', $value);
			$this->tpl->assign('conversionPercentage', $value . '&#37;');
			$this->tpl->assign('costPerConversion', $value);
			$this->tpl->assign('impressions', $value);
			$this->tpl->assign('clicks', $value);
			$this->tpl->assign('ctr', $value);
			$this->tpl->assign('costPerClick', $value);
			$this->tpl->assign('position', $value);
			$this->tpl->assign('cost', $value);
		}
	}

	private function getGoals()
	{

		$goals = BackendSeaModel::getGoals();
		$this->tpl->assign('goals', $goals);
	}

	/**
	 * Parses the data to make a single line-chart
	 */
	private function parseLineChartData()
	{
		$startTimestamp = date('Y-m-d', SpoonSession::get('sea_start_timestamp'));
		$endTimestamp = date('Y-m-d', SpoonSession::get('sea_end_timestamp'));

		$maxYAxis = 2;
		$metric = 'visits';
		$graphData = array();

		$metricsPerDay = (array) BackendSeaModel::getMetricPerDay($metric, $startTimestamp, $endTimestamp);

		$graphData[0]['title'] = $metric;
		$graphData[0]['label'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($metric)));
		$graphData[0]['data'] = array();


		foreach($metricsPerDay as $key => $data)
		{
			// build array
			$graphData[0]['data'][$key]['date'] = $key;
			$graphData[0]['data'][$key]['value'] = $data;
		}


		$this->tpl->assign('maxYAxis', $maxYAxis);
		$this->tpl->assign('tickInterval', ($maxYAxis == 2 ? '1' : ''));
		$this->tpl->assign('graphData', $graphData);
	}

	/**
	 * Parses the data to make a multi line-chart
	 */
	private function parseMultiLineChartData()
	{
		$startTimestamp = date('Y-m-d', SpoonSession::get('sea_start_timestamp'));
		$endTimestamp = date('Y-m-d', SpoonSession::get('sea_end_timestamp'));

		$maxYAxis = 2;
		$metricsArr = array('cost_per_click', 'cost_per_conversion', 'cost_per_mimpressions');
		$graphData = array();

		$metricsPerDay = (array) BackendSeaModel::getMetricsPerDay($metricsArr, $startTimestamp, $endTimestamp);

		foreach($metricsArr as $i => $metric)
		{
			// build graph data array
			$graphData[$i] = array();
			$graphData[$i]['title'] = $metric;
			$graphData[$i]['label'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($metric)));
			$graphData[$i]['i'] = $i + 1;
			$graphData[$i]['data'] = array();

			foreach($metricsPerDay as $j => $data)
			{
				// build array
				$graphData[$i]['data'][$j]['date'] =  $data['day'];
				$graphData[$i]['data'][$j]['value'] = (string) $data[$metric];
			}
		}

		$this->tpl->assign('graphDataMulti', $graphData);
	}
}
