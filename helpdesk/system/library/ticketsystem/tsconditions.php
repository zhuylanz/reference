<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * This class used to validate passed caonditions based on passed ticket data
 */
class TsConditions extends TsRegistry{

	/**
	 * $ticketData Ticket Data
	 * @var array
	 */
	protected $ticketData;

	public function getTicketData(){
		return $this->ticketData;
	}

	/**
	 * init Initialize required data for class
	 * @param  array $data passed data to class
	 * @return boolean
	 */
	public function init($data){
		if(!isset($data['ticket_id']) || !isset($data['thread_id']))
			return;

		$this->model_ticketsystem_tickets = $this->registry->get('model_ticketsystem_tickets');
		$ticketData = $this->model_ticketsystem_tickets->getTicket(array('t.id' => $data['ticket_id']));
		$ticketThreadData = $this->model_ticketsystem_tickets->getTicketThreads(array('thread_id' => $data['thread_id']));
		if($ticketThreadData)
			$ticketData['description'] = $ticketThreadData[0]['message'];
		else
			$ticketData['description'] = '';

		$this->ticketData = $ticketData;

		return true;
	}

	/**
	 * checkConditionsOnRequest Check passed conditions based on passed ticket data
	 * @param  array  $data 
	 */
	public function checkConditionsOnRequest($data = array()){
		if(!$this->init($data) || !isset($data['conditions']))
			return;

		$ticketData = $this->ticketData;

		$applyActions = [];

		/**
		 * If any condition will return true then it will apply 
		 */
		
		if ($data['conditions']) {
			foreach($data['conditions'] as $conditions){
				$conditionsOne = unserialize($conditions['conditions_one']);
				$conditionsAll = unserialize($conditions['conditions_all']);
				
				if ($conditionsOne && $conditionsAll) {
					$statusOne = $statusAll = false;
					$passConditions = false;
					if($conditionsOne){
						if($statusOne = $this->checkConditionOne($conditionsOne)){
							$passConditions = true;
							// break;
						}
					}
		
					if(!$statusOne AND $conditionsAll){
						if($statusAll = $this->checkConditionAll($conditionsAll)){
							$passConditions = true;
							// break;
						}
					}
					
					if ($passConditions) {
						$applyActions[] = $conditions['actions'];
					}
				} else {
					$applyActions[] = $conditions['actions'];
				}
			}
		}
		
		/**
		 * If Conditions matched then passed it to apply actions
		 */
		if($applyActions){
			$this->language->load('ticketsystem/actions');
			$message = $this->language->get('text_success_ticket_rule');
			$TsActions = new TsActions($this->registry);

			foreach ($applyActions as $applyAction) {
				$message .= $TsActions->applyActionsOnRequest(
								array(
									'actions' => unserialize($applyAction),
									'ticket_id' => $data['ticket_id'], 
									'thread_id' => $data['thread_id'],
									'agent_id' => '0'
								)
							);
			}

			if($this->config->get('ts_add_internal_after_applying_actions')){
				$data = array(
							'id' => $data['ticket_id'],
							'message' => $message,
							'agent_id' => 0,
							'sender_type' => 'agent',
							'messagetype' => 'note',
							);
				//clear files
				$this->request->files = array();
				//add note entry
				$this->model_ticketsystem_tickets->addTicketThread($data);
			}
		}
	}

	/**
	 * checkConditionOne Check one Condition, if one true then returns
	 * @param  array $conditionsOne
	 * @return boolean
	 */
	public function checkConditionOne($conditionsOne){
		$ticketData = $this->ticketData;

		$statusOne = false;

		foreach ($conditionsOne as $condition) {
			if($condition['type']=='to_mail' AND isset($ticketData['agentEmail'])){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['agentEmail'], $condition['match']);

			}elseif($condition['type']=='from_mail' AND isset($ticketData['customerEmail'])){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['customerEmail'], $condition['match']);

			}elseif($condition['type']=='subject'){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['subject'], $condition['match']);

			}elseif($condition['type']=='description'){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['description'], $condition['match']);

			}elseif($condition['type']=='subject_or_description'){
				$statusOne = ($subStatus = $this->{$condition['matchCondition'].'Equal'}($ticketData['subject'], $condition['match'])) ? $subStatus : $this->{$condition['matchCondition'].'Equal'}($ticketData['description'], $condition['match']);

			}elseif($condition['type']=='priority'){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['priority'], $condition['match']);

			}elseif($condition['type']=='type'){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['type'], $condition['match']);

			}elseif($condition['type']=='status'){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['status'], $condition['match']);

			}elseif($condition['type']=='source'){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['provider'], $condition['match']);

			}elseif($condition['type']=='created'){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['date_added'], $condition['match']);

			}elseif($condition['type']=='agent'){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['assign_agent'], $condition['match']);

			}elseif($condition['type']=='group'){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['group'], $condition['match']);

			}elseif($condition['type']=='customer_name'){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['customerName'], $condition['match']);

			}elseif($condition['type']=='customer_email'){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['customerEmail'], $condition['match']);

			}elseif($condition['type']=='organization_name'){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['organizationName'], $condition['match']);

			}elseif($condition['type']=='organization_domain'){
				$statusOne = $this->{$condition['matchCondition'].'Equal'}($ticketData['organizationDomain'], $condition['match']);
			}

			if($statusOne)
				return true;
		}
	}

	/**
	 * checkConditionAll Check all Condition, if all condition true then returns
	 * @param  array $conditionsAll
	 * @return boolean
	 */
	public function checkConditionAll($conditionsAll){
		$ticketData = $this->ticketData;

		$statusAll = false;

		foreach ($conditionsAll as $condition) {
			if($condition['type']=='to_mail'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['agentEmail'], $condition['match']);

			}
			if($condition['type']=='from_mail'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['customerEmail'], $condition['match']);

			}
			if($condition['type']=='subject'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['subject'], $condition['match']);

			}
			if($condition['type']=='description'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['description'], $condition['match']);

			}
			if($condition['type']=='subject_or_description'){
				$statusAll = ($subStatus = $this->{$condition['matchCondition'].'Equal'}($ticketData['subject'], $condition['match'])) ? $subStatus : $this->{$condition['matchCondition'].'Equal'}($ticketData['description'], $condition['match']);

			}
			if($condition['type']=='priority'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['priority'], $condition['match']);

			}
			if($condition['type']=='type'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['type'], $condition['match']);

			}
			if($condition['type']=='status'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['status'], $condition['match']);

			}
			if($condition['type']=='source'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['provider'], $condition['match']);

			}
			if($condition['type']=='created'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['date_added'], $condition['match']);

			}
			if($condition['type']=='agent'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['assign_agent'], $condition['match']);

			}
			if($condition['type']=='group'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['group'], $condition['match']);

			}
			if($condition['type']=='customer_name'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['customerName'], $condition['match']);

			}
			if($condition['type']=='customer_email'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['customerEmail'], $condition['match']);

			}
			if($condition['type']=='organization_name'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['organizationName'], $condition['match']);

			}
			if($condition['type']=='organization_domain'){
				$statusAll = $this->{$condition['matchCondition'].'Equal'}($ticketData['organizationDomain'], $condition['match']);
			}
		}

		return $statusAll;
	}


	/**
	 * customer for customer entered/ created data
	 * admin for admin entered data
	 *
	 * is
	 * isNot
	 * contains
	 * notContains
	 * startWith
	 * endWith
	 * before
	 * beforeOn
	 * after
	 * afterOn
	 * 
	 */
	protected function isEqual($customer, $admin){
		return $customer==$admin ? true : false;
	}

	protected function isNotEqual($customer, $admin){
		return $customer!=$admin ? true : false;
	}

	protected function containsEqual($customer, $admin){
		return strstr(strtolower($customer), strtolower($admin)) ? true : false;
	}

	protected function notContainsEqual($customer, $admin){
		return !strstr(strtolower($customer), strtolower($admin)) ? true : false;
	}

	protected function startWithEqual($customer, $admin){
		return (substr(strtolower($customer), 0, strlen($admin)) == strtolower($admin)) ? true : false;
	}

	protected function endWithEqual($customer, $admin){
		return (substr(strtolower($customer), -strlen($admin)) == strtolower($admin)) ? true : false;
	}

	protected function beforeEqual($customer, $admin){
		return ($customer < $admin) ? true : false;
	}

	protected function beforeOnEqual($customer, $admin){
		return ($customer <= $admin) ? true : false;
	}

	protected function afterEqual($customer, $admin){
		return ($customer > $admin) ? true : false;
	}

	protected function afterOnEqual($customer, $admin){
		return ($customer >= $admin) ? true : false;
	}
}