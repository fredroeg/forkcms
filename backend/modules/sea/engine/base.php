<?php

/**
 * This class implements a lot of functionality that can be extended by a specific action
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaBase extends BackendBaseActionIndex
{
	/**
	 * The selected page
	 *
	 * @var string
	 */
	protected $pagePath = null;

	/**
	 * The start and end timestamp of the collected data
	 *
	 * @var int
	 */
	protected $startTimestamp, $endTimestamp;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->header->addJS('highcharts.js');
		$this->setDates();
	}

	/**
	 * Parse this page
	 */
	protected function parse()
	{
		// period picker
		if(isset($this->pagePath)) BackendSeaHelp::parsePeriodPicker($this->tpl, $this->startTimestamp, $this->endTimestamp, array('page_path' => $this->pagePath));
		else BackendSeaHelp::parsePeriodPicker($this->tpl, $this->startTimestamp, $this->endTimestamp);
	}

	/**
	 * Set start and end timestamp needed to collect analytics data
	 */
	private function setDates()
	{
		BackendSeaHelp::setDates();

		$this->startTimestamp = SpoonSession::get('sea_start_timestamp');
		$this->endTimestamp = SpoonSession::get('sea_end_timestamp');
	}
}