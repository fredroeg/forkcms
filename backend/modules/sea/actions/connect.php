<?php

/**
 * This is the connect-action (default)
 * When the user uses this module for the first time, he has to provide the necessary id's and tokens
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class BackendSeaConnect extends BackendBaseActionEdit
{
	private $clientId;

	private $clientSecret;


	public function execute()
	{
		parent::execute();
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

		$this->frm->addDropdown('tableId', $this->getTableIds(), $this->record['table_id']);

		// submit dialog
                $this->frm->addButton('change', 'update', 'submit', 'inputButton button mainButton');

		if($this->tableId == '' && $this->clientId != '' && $this->clientSecret != '')
		{
		    $this->tpl->assign("profileError", "Please update again with a profile");
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
			$ddmTableId = $this->frm->getField('tableId');


                        // validate the fields
                        $txtClientId->isFilled(BL::getError('BlockIsRequired'));
                        $txtClientIdSecret->isFilled(BL::getError('TitleIsRequired'));

                        if($this->frm->isCorrect())
                        {
                                // build array
                                $values['client_id'] = $txtClientId->getValue();
                                $values['client_secret'] = $txtClientIdSecret->getValue();
				$values['table_id'] = $ddmTableId->getValue();

                                // insert the item
                                $id = (int) BackendSeaModel::updateIds($values);

				$this->authNeeded($values['client_id'], $values['client_secret']);
			}
		}
	}

	private function getTableIds()
	{
		if($this->record['client_id'] != '')
		{
			$accounts = BackendSeaHelp::getAccounts()->items;
			$accountArray = array();
			foreach ($accounts as $account)
			{
				$accountArray['ga:' . $account->id] = $account->name;
			}
			return $accountArray;
		}
		else
		{
			return array('' => 'Update with id\'s before selecting a profile');
		}
	}

	private function checkStatus()
	{
		$url = BackendSeaHelper::loginWithOAuth();
		$this->redirect($url);
	}

	private function authNeeded($clientId, $clientSecret)
	{
		if($this->clientId != $clientId || $this->clientSecret != $clientSecret)
		{
			$this->checkStatus();
		}
	}

	protected function parse()
	{
		parent::parse();
	}
}
