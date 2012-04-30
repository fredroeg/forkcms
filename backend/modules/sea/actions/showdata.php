<?php

/**
 * This is the showdata-action
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
		$this->parse();
		$this->display();
	}

	protected function parse()
	{
		parent::parse();
	}

	private function checkStatus()
	{
		$redirect = BackendSeaHelper::checkStatus();
		if(!$redirect)
		{
			$this->redirect('index');
		}
	}

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
	}

	private function getDataFromThisPeriod($periodId)
	{
		$periodDataArray = BackendSeaModel::getSEAData($periodId);
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

	/**
	 * Parses the data to make the line-chart
	 */
	private function parseLineChartData()
	{
		$startTimestamp = date('Y-m-d', SpoonSession::get('sea_start_timestamp'));
		$endTimestamp = date('Y-m-d', SpoonSession::get('sea_end_timestamp'));

		$maxYAxis = 2;
		$metric = 'visits';
		$graphData = array();

		$metricsPerDay = (array) BackendSeaModel::getMetricsPerDay($metric, $startTimestamp, $endTimestamp);

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
}
