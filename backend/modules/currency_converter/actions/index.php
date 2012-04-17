<?php

/**
 * This is the index-action (default)
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendCurrencyConverterIndex extends BackendBaseActionIndex
{


	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();
                
                // parse page
		$this->parse();
                
                // display the page
		$this->display();

	}

	
}
