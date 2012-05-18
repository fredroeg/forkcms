<?php

/**
 * This is the showdata-action
 * It displays all the highcharts, the sea-statistics, ...
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendAnalyticsSeaPage extends BackendAnalyticsBase
{
	public function execute()
	{
		parent::execute();
		$this->parseLineChartData();
		$this->parseMultiLineChartData();
		$this->getDataFromThisPeriod($this->periodId);
		$this->getGoals();
		$this->parse();
		$this->display();
	}

	/**
	 * Get all the data from that period, and assign it to the template
	 *
	 * @param int $periodId
	 */
	private function getDataFromThisPeriod($periodId)
	{
		$periodDataArray = BackendAnalyticsModel::getSEAData($periodId);

		// we check if the array isn't empty
		// possible if the adwords account isn't coupled with the GA account
		if(!empty($periodDataArray))
		{
			$this->tpl->assign('visits', $periodDataArray[0]['visits']);
			$this->tpl->assign('conversions', $periodDataArray[0]['conversions']);
			$this->tpl->assign('conversionPercentage', $periodDataArray[0]['conversion_percentage'] . '&#37;');
			$this->tpl->assign('costPerConversion', $periodDataArray[0]['cost_per_conversion']);
			$this->tpl->assign('impressions', $periodDataArray[0]['impressions']);
			$this->tpl->assign('clicks', $periodDataArray[0]['clicks_amount']);
			$this->tpl->assign('ctr', $periodDataArray[0]['click_through_rate']);
			$this->tpl->assign('costPerClick', $periodDataArray[0]['cost_per_click']);
			// $this->tpl->assign('position', $periodDataArray['position']);
			$this->tpl->assign('cost', $periodDataArray[0]['costs']);
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

	/**
	 * Get all the goals and assign them to the template
	 */
	private function getGoals()
	{
		$goals = BackendAnalyticsModel::getGoals();
		$this->tpl->assign('goals', $goals);
	}

	/**
	 * Parses the data to make a single line-chart
	 */
	private function parseLineChartData()
	{
	    	$maxYAxis = 2;
		$metrics = array('visits');
		$graphData = array();
		$table = 'analytics_sea_day_data';

		$metricsPerDay = (array) BackendAnalyticsModel::getMetricsPerDay($metrics, $this->startTimestamp, $this->endTimestamp, $table);

		foreach($metrics as $i => $metric)
		{
			// build graph data array
			$graphData[$i] = array();
			$graphData[$i]['title'] = $metric;
			$graphData[$i]['label'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($metric)));
			$graphData[$i]['data'] = array();

			foreach($metricsPerDay as $j => $data)
			{
				// build array
				$graphData[$i]['data'][$j]['date'] = $data['day'];
				$graphData[$i]['data'][$j]['value'] = (string) $data[$metric];
			}
		}

		// loop the metrics
		foreach($graphData as $metric)
		{
			foreach($metric['data'] as $data)
			{
				// get the maximum value
				if((int) $data['value'] > $maxYAxis) $maxYAxis = (int) $data['value'];
			}
		}

		// parse
		$this->tpl->assign('maxYAxis', $maxYAxis);
		$this->tpl->assign('tickInterval', ($maxYAxis == 2 ? '1' : ''));
		$this->tpl->assign('graphData', $graphData);
	}

	/**
	 * Parses the data to make a multi line-chart
	 */
	private function parseMultiLineChartData()
	{
		$maxYAxisTriple = 2;
		$metrics = array('cost_per_click', 'cost_per_conversion', 'cost_per_mimpressions');
		$graphDataTriple = array();
		$table = 'analytics_sea_day_data';

		$metricsPerDay = (array) BackendAnalyticsModel::getMetricsPerDay($metrics, $this->startTimestamp, $this->endTimestamp, $table);

		foreach($metrics as $i => $metric)
		{
			// build graph data array
			$graphDataTriple[$i] = array();
			$graphDataTriple[$i]['title'] = $metric;
			$graphDataTriple[$i]['label'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($metric)));
			$graphDataTriple[$i]['i'] = $i + 1;
			$graphDataTriple[$i]['data'] = array();

			foreach($metricsPerDay as $j => $data)
			{
				// build array
				$graphDataTriple[$i]['data'][$j]['date'] =  $data['day'];
				$graphDataTriple[$i]['data'][$j]['value'] = (string) $data[$metric];
			}
		}

		$this->tpl->assign('graphDataMulti', $graphDataTriple);
	}
}
