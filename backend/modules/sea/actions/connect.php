<?php

/**
 * This is the connect-action (default)
 * When the user uses this module for the first time, he has to provide the necessary id's and tokens
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaConnect extends BackendBaseActionEdit
{
	/**
	 * Client Id
	 *
	 * @var string
	 */
	private $clientId;

	/**
	 * Client Secret
	 *
	 * @var string
	 */
	private $clientSecret;


	public function execute()
	{
		parent::execute();

		BackendSeaHelper::checkStatus();

		$this->getData();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}
	private function getData()
	{
		$this->record = BackendSeaModel::getAPISettings();

		$this->clientId = $this->record['client_id'];
		$this->clientSecret = $this->record['client_secret'];
		$this->tableId = $this->record['table_id'];
	}

	private function loadForm()
	{
		$this->frm = new BackendForm('connectform');
                $this->frm->addText('clientId', $this->clientId);
		$this->frm->addText('clientIdSecret', $this->clientSecret);

		if($this->clientId != '' && $this->clientSecret != '' && $this->tableId != '')
		{
		    $this->frm->addRadiobutton('profileId', $this->getProfileIds(), $this->record['table_id']);
		}
		else
		{
		    $this->frm->addRadiobutton('profileId', $this->getProfileIds());
		}


		// submit dialog
                $this->frm->addButton('change', 'update', 'submit', 'inputButton button mainButton');

		// the user has to update 2 times
		// 1st time = authentication
		// 2nd time = table selected
		if($this->tableId == '' && $this->clientId != '' && $this->clientSecret != '')
		{
		    $this->tpl->assign("profileError", "Good! Now, please select a profile and update again");
		}
	}

	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
                        $this->frm->cleanupFields();

                        // shorten the fields;
                        $txtClientId = $this->frm->getField('clientId');
                        $txtClientIdSecret = $this->frm->getField('clientIdSecret');
			$ddmTableId = $this->frm->getField('profileId');



                        // validate the fields
                        $txtClientId->isFilled(BL::getError('ClientIdIsRequired'));
                        $txtClientIdSecret->isFilled(BL::getError('ClientIdSecretIsRequired'));

                        if($this->frm->isCorrect())
                        {
                                // build array
                                $values['client_id'] = $txtClientId->getValue();
                                $values['client_secret'] = $txtClientIdSecret->getValue();
				$values['table_id'] = $ddmTableId->getValue();

                                // insert the item
                                $id = (int) BackendSeaModel::updateIds($values);

				// truncate the tables
				$this->truncateTables($values['table_id']);

				// check if nees authentication (only when the inputfields have been changed)
				$this->authNeeded($values['client_id'], $values['client_secret']);
			}
		}
	}

	/**
	 * Get all the id's and names from the different profiles in your account
	 * return an array to display it in a dropdownlist
	 *
	 * @return array
	 */
	private function getProfileIds()
	{
		if($this->record['client_id'] != '')
		{
			$accounts = BackendSeaHelp::getAccounts()->items;
			$accountArray = array();
			foreach ($accounts as $account)
			{
				$accountArray[] = array('value' => $account->id, 'label' => $account->name);
			}
			return $accountArray;
		}
		else
		{
			//todo msg from db
			return array(array('value' => '', 'label' => 'Update with id\'s before selecting a profile'));
		}
	}

	/**
	 * Function to determine if it's necessary to authenticate with Google
	 *
	 * @param string $clientId
	 * @param string $clientSecret
	 */
	private function authNeeded($clientId, $clientSecret)
	{
		if($this->clientId != $clientId || $this->clientSecret != $clientSecret)
		{
			BackendSeaModel::truncateTables();

			$url = BackendSeaHelper::loginWithOAuth();
			$this->redirect($url);
		}
		else
		{
			$this->redirect('connect');
		}
	}

	/**
	 * Truncate the tables if the user has selected a different profile
	 *
	 * @param string $tableId
	 */
	private function truncateTables($tableId)
	{
		if($this->tableId != $tableId)
		{
			BackendSeaModel::truncateTables();
		}
	}

	protected function parse()
	{
		parent::parse();
	}
}
