<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * This class is used to add events on Helpdesk module
 */
class TsEvents extends TsRegistry{

	/**
	 * Class will work on these Events Triggers
	 * 
	 * $this->event->trigger('pre.admin.ts.ticket.delete', $id);   					deleted
	 * $this->event->trigger('pre.admin.ts.ticket.add', $data);						created
	 * $this->event->trigger('pre.admin.ts.ticket.edit', $data);					updated
	 * $this->event->trigger('pre.admin.ts.ticket.thread.add', $data);				message
	 */
	
	/**
	 * $alreadyExecuted if event exexuted in same call already
	 * @var boolean
	 */
	private $alreadyExecuted;
	
	/**
	 * checkEventsOnRequest Check Events from call
	 * @param  array  $data with ticket details
	 */
	public function checkEventsOnRequest($data = array()){
		if(!isset($data['data']) || !isset($data['event']))
			return;

		$service = new TsService($this->registry);

		$service->model(array('model'=>'ticketsystem/tickets'));
		$this->model_ticketsystem_tickets = $this->registry->get('model_ticketsystem_tickets');

		$service->model(array('model'=>'ticketsystem/events'));
		$this->model_ticketsystem_events = $this->registry->get('model_ticketsystem_events');

		$events = $this->model_ticketsystem_events->getEvents(false, array('status' => 1));
		
		$conditions = [];
		
		$thread_id = 0;
		isset($data['data'][0]['id']) ? $data_data_id = $data['data'][0]['id'] : (isset($data['data']['id']) ? $data_data_id = $data['data']['id'] : $data_data_id = $data['data']);

		$ticketData = $this->model_ticketsystem_tickets->getTicket(array('t.id' => $data_data_id));

		if($data['event']=='updated'){
			$dataData = $data['data'];
			foreach($dataData as $dataDataValue){
				$data['data'] = array(
									$dataDataValue['column'] => $dataDataValue['value'],
									'agent_id' => $dataDataValue['agent_id'],
									);
			}
		}

		foreach($events as $event){
			$status = false;
			
			if($this->performerValidate(unserialize($event['performer']), $data['data'])){
				foreach(unserialize($event['events']) as $checkEvents){
					if($checkEvents['type']=='ticket'){
						if($checkEvents['from']=='created' AND $data['event']=='created'){
							$status = true;
						}elseif($checkEvents['from']=='updated' AND $data['event']=='updated'){
							$status = true;
						}elseif($checkEvents['from']=='deleted' AND $data['event']=='deleted'){
							$status = true;
						}
					}elseif(!$status AND $data['event']=='message'){
						if(isset($data['data']['thread_id']))
							$thread_id = $data['data']['thread_id'];
						if($checkEvents['type']=='reply_added'){
							
							if(isset($data['data']['messagetype']) AND $data['data']['messagetype']=='reply'){
								$status = true;
							}
						}elseif(!$status AND $checkEvents['type']=='note_added'){
							if($checkEvents['from']=='any'){
								$status = true;
							}elseif($checkEvents['from']=='forward' AND (isset($data['data']['messagetype']) AND $data['data']['messagetype']=='forward')){
								$status = true;
							}elseif($checkEvents['from']=='private' AND (isset($data['data']['messagetype']) AND $data['data']['messagetype']=='note')){
								$status = true;
							}
						}
					}elseif(!$status AND $data['event']=='updated'){
						if($ticketData){
							if($checkEvents['type']=='agent_updated' AND isset($data['data']['assign_agent'])){
								if($this->isEqual($checkEvents['from'], $ticketData['assign_agent']) AND $this->isEqual($checkEvents['to'], $data['data']['assign_agent']))
									$status = true;
							}elseif($checkEvents['type']=='group_updated' AND isset($data['data']['group'])){
								if($this->isEqual($checkEvents['from'], $ticketData['group']) AND $this->isEqual($checkEvents['to'], $data['data']['group']))
									$status = true;
							}elseif($checkEvents['type']=='status_updated' AND isset($data['data']['status'])){
								if($this->isEqual($checkEvents['from'], $ticketData['status']) AND $this->isEqual($checkEvents['to'], $data['data']['status']))
									$status = true;
							}elseif($checkEvents['type']=='type_updated' AND isset($data['data']['type'])){
								if($this->isEqual($checkEvents['from'], $ticketData['type']) AND $this->isEqual($checkEvents['to'], $data['data']['type']))
									$status = true;
							}elseif($checkEvents['type']=='priority_updated' AND isset($data['data']['priority'])){
								if($this->isEqual($checkEvents['from'], $ticketData['priority']) AND $this->isEqual($checkEvents['to'], $data['data']['priority']))
									$status = true;
							}
						}
					}

					if($status)
						break;
				}

				if($status)
					$conditions[] = array(
										'actions' => $event['actions'],
										'conditions_all' => $event['conditions_all'],
										'conditions_one' => $event['conditions_one'],
										);
			}
		}

		$conditionObj = new TsConditions($this->registry);
		
		if($conditions){
			$data = array(
						'conditions' => $conditions,
						'ticket_id' => $data_data_id,
						'thread_id' => $thread_id,
					);
			$conditionObj->checkConditionsOnRequest($data);
		}
	}

	/**
	 * isEqual Check if passed values are equal or not
	 * @return boolean 
	 */
	protected function isEqual($event, $ticket){
		return ($event=='any') ? true : ((int)$event==$ticket ? true : false);
	}

	/**
	 * performerValidate Generate who is performing this events based on const
	 */
	protected function performerValidate($event, $data){
		if(defined('HTTP_CATALOG'))
			$performer = 'agents';
		else
			$performer = 'customer';

		if($event['value']=='agents' AND $performer == 'agents'){
			if(in_array('all', $event['agents']) || in_array((isset($data['agent_id']) ? $data['agent_id'] : 0), $event['agents']))
				return true;
		}elseif($event['value']=='everyone' || $event['value']==(isset($data['sender_type']) ? $data['sender_type'] : $performer))
			return true;
	}
}