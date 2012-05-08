<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the settings-action, it will display a form to set general analytics settings
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 * @author Annelies Van Extergem <annelies.vanextergem@wijs.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@wijs.be>
 */
class BackendAnalyticsSettings extends BackendBaseActionEdit
{
	/**
	 * The account name
	 *
	 * @var	string
	 */
	private $accountName;

	/**
	 * All website profiles
	 *
	 * @var	array
	 */
	private $profiles = array();

	/**
	 * The title of the selected profile
	 *
	 * @var	string
	 */
	private $profileTitle;

	/**
	 * The session token
	 *
	 * @var	string
	 */
	private $accessToken;

	/**
	 * The table id
	 *
	 * @var	int
	 */
	private $tableId;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->getAnalyticsParameters();
		$this->parse();
		$this->display();
	}

	/**
	 * Gets all the needed parameters to link a google analytics account to fork
	 */
	private function getAnalyticsParameters()
	{
		$this->record = BackendAnalyticsModel::getAPISettings();

		// BackendAnalyticsHelper::getOAuth2Token($this->record['refresh_token'], true);

		// get session token, account name, the profile's table id, the profile's title
		$this->accessToken = $this->record['access_token'];
		$this->accountName = $this->record['account_name'];
		$this->profileTitle = $this->record['profile_name'];
		$this->tableId = $this->record['table_id'];


		$remove = SpoonFilter::getGetValue('remove', array('session_token', 'table_id'), null);

		// something has to be removed before proceeding
		if(!empty($remove))
		{
			// the session token has te be removed
			if($remove == 'session_token')
			{
			    $values['access_token'] = null;
			    $values['refresh_token'] = null;
			}

			$values['account_name'] = null;
			$values['profile_name'] = null;
			$values['web_property_id'] = null;
			$values['table_id'] = null;

			// remove all parameters from the module settings
			BackendAnalyticsModel::updateIds($values);

			// redirect to the settings page without parameters
			$this->redirect(BackendModel::createURLForAction('settings'));
		}

		// todo: change in spoonfilter
		if(isset($_GET['code']))
		{
			if(BackendAnalyticsHelper::getOAuth2Token($_GET['code'], false))
			{
				// redirect to the settings page without parameters
				$this->redirect(BackendModel::createURLForAction('settings'));
			}
		}

		// session id is present but there is no table_id
		if($this->accessToken != '' && $this->tableId == '')
		{
			// get google analytics instance
			$ga = BackendAnalyticsHelper::getGoogleAnalyticsInstance();

			// get all possible profiles in this account
			$this->profiles = $ga->getAnalyticsAccountList($this->accessToken);

			// not authorized
			if($this->profiles == 'UNAUTHORIZED')
			{
				// the session tokens should be renewed
			}

			// everything went fine
			elseif(is_object($this->profiles))
			{
				$tableId = SpoonFilter::getGetValue('table_id', null, null);

				// a table id is given in the get parameters
				if(!empty($tableId))
				{
					$profiles = array();

					// set the table ids as keys
					foreach($this->profiles->items as $profile)
					{
					    $profiles[$profile->id] = $profile;
					}

					// correct table id
					if(isset($profiles[$tableId]))
					{
						// save table id and account title
						$values['table_id'] = $profiles[$tableId]->id;
						$values['account_name'] = $profiles[$tableId]->name;
						$values['profile_name'] = $values['account_name'];
						$values['web_property_id'] = $profiles[$tableId]->webPropertyId;

						// store the id's and the names in the settings
						BackendAnalyticsModel::updateIds($values);

						// redirect to the settings page without parameters
						$this->redirect(BackendModel::createURLForAction('settings'));
					}
				}
			}
		}
	}

	/**
	 * Parse
	 */
	protected function parse()
	{
		parent::parse();

		if($this->accessToken == '')
		{
			// show the link to the google account authentication form
			$this->tpl->assign('NoSessionToken', true);
			$this->tpl->assign('Wizard', true);

			// build the link to the google account authentication form
			$googleAccountAuthenticationForm = BackendAnalyticsHelper::loginWithOAuth();

			// parse the link to the google account authentication form
			$this->tpl->assign('googleAccountAuthenticationForm', $googleAccountAuthenticationForm);
		}

		// session token is present but no table id
		if($this->accessToken != '' && isset($this->profiles) && $this->tableId == '')
		{
			// show all possible accounts with their profiles
			$this->tpl->assign('NoTableId', true);
			$this->tpl->assign('Wizard', true);

			$accounts = array();

			// no profiles or not authorized
			if(!empty($this->profiles) && $this->profiles !== 'UNAUTHORIZED')
			{
				$accounts[''][0] = BL::msg('ChooseWebsiteProfile');

				// prepare accounts array
				foreach((array) $this->profiles->items as $profile)
				{
					$accounts[$profile->name][$profile->id] = $profile->name;
				}

				// there are accounts
				if(!empty($accounts))
				{
					// sort accounts
					uksort($accounts, array('BackendAnalyticsSettings', 'sortAccounts'));

					// create form
					$frm = new BackendForm('linkProfile', BackendModel::createURLForAction(), 'get');
					$frm->addDropdown('table_id', $accounts);
					$frm->parse($this->tpl);

					if($frm->isSubmitted())
					{
						if($frm->getField('table_id')->getValue() == '0') $this->tpl->assign('ddmTableIdError', BL::err('FieldIsRequired'));
					}

					// parse accounts
					$this->tpl->assign('accounts', true);
				}
			}
		}

		// everything is fine
		if($this->accessToken != '' && $this->tableId != '' && $this->accountName != '')
		{
			// show the linked account
			$this->tpl->assign('EverythingIsPresent', true);

			// show the title of the linked account and profile
			$this->tpl->assign('accountName', $this->accountName);
			$this->tpl->assign('profileTitle', $this->profileTitle);
		}
	}

	/**
	 * Helper function to sort accounts
	 *
	 * @param array $account1 First account for comparison.
	 * @param array $account2 Second account for comparison.
	 * @return int
	 */
	public static function sortAccounts($account1, $account2)
	{
		if(strtolower($account1) > strtolower($account2)) return 1;
		if(strtolower($account1) < strtolower($account2)) return -1;
		return 0;
	}
}
