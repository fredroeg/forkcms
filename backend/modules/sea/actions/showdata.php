<?php

/**
 * This is the showdata-action
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaShowdata extends BackendBaseActionIndex
{
	public function execute()
	{
		parent::execute();
		$this->testKeywords();
		$this->parse();
		$this->display();
	}

	protected function parse()
	{
		parent::parse();
	}

	//Just a test function to mess around and get some feeling with it
	private function testKeywords()
	{
		$metrics = array('adCost', 'visits');
		$dimensions = array('medium', 'date');
		$startTimestamp = '2012-03-01';
		$endTimestamp = '2012-03-31';


		$returnedTestKeywords = BackendSeaHelp::getKeywords($metrics, $startTimestamp, $endTimestamp, $dimensions);

		spoon::dump(json_decode($returnedTestKeywords));

		$decoded = json_decode($returnedTestKeywords, true);
		$totalSEAVisits = 0;

		foreach ($decoded['rows'] as $key => $row)
		{
		    if((int) $row[2] != 0)
		    {
			$totalSEAVisits += $row[3];
		    }
		}

		var_dump($totalSEAVisits);

		//spoon::dump($totalCost);



		//Test to display the keywords
		$specialArray = array();
		foreach ($decoded['rows'] as $key => $row)
		{
		    $specialArray[$key]['medium'] = $row[0];
		    $specialArray[$key]['source'] = $row[1];
		    $specialArray[$key]['keyword'] = $row[2];
		    $specialArray[$key]['visits'] = $row[3];
		    $specialArray[$key]['bounces'] = $row[4];

		}

		$this->tpl->assign('columnheaders', $decoded['columnHeaders']);
		$this->tpl->assign('columncontent', $specialArray);

	}
}
