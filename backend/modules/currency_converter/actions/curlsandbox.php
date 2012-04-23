<?php

/**
 * This is a temporary sandbox to test CURL
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendCurrencyConverterCurlsandbox extends BackendBaseActionIndex
{
        /**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
                $this->startcurl();
		$this->parse();
		$this->display();

	}

        protected function parse()
        {
            // add datagrid
        }

        private function startcurl()
        {
            if(!function_exists("curl_init")) die("cURL extension is not installed");

            $url = 'https://api.twitter.com/1/statuses/user_timeline.json?screen_name=vanbosse';

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: api.twitter.com'));
            $json = curl_exec($ch);
            curl_close ($ch);
            $data = json_decode($json, true);

            $this->tpl->assign('tweets', $data);
        }
}
