<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * This Class function are complete to create all Conditions for Help Desk, to use this just add this 
 * in your controller and echo in tpl, all work will done by this class
 * it works from both create and edit function - same procedure
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemTicketEvents extends controller {
	
	const CONTROLLER_NAME = 'ticketevents';

	protected $error;
	protected $skipError = array('agent_updated', 'group_updated');

	public function index($args = array('events' => array(), 'error' => array(), 'id' => 'all')) {
		$this->error = $args['error'];

		$data = $this->language->load('ticketsystem/all');

		$data['events'] = $args['events'];
		$data['eventsId'] = $this->request->get['name'] = 'events';

		if (isset($this->error['error_blank_event'])) {
			$data['error_blank_event'] = $this->error['error_blank_event'];
		} else {
			$data['error_blank_event'] = array();
		}

		$this->document->addScript('view/javascript/ticketsystem/ticketEvents.js');

		$data['token'] = $this->session->data['token'];
		$data['ticketEvents'] = $this->TsLoader->TsTicket->ticketEvents;

		foreach($data['events'] as $key => $value){
			$this->request->get['id'] = $key;
			$this->request->get['filter_value'] = $value['type'];
			$data['events'][$key]['html'] = $this->autocomplete(isset($value['from']) ? $value['from'] : false, isset($value['to']) ? $value['to'] : false , true);
		}

		return $this->load->view('ticketsystem/ticketEvents.tpl', $data);
	}

	/**
	 * This method works for 2 types 
	 * - using ajax, which will add - one html with event to tpl and ($functioncall will be false)
	 * - used by controller (index function) to create html at time or edit or error add view ($functionCall = true and $from, $fromCondition will have value for fields)
	 *
	 * for it's 1st select option we called selectHtml()
	 * 
	 * Source is coming from library file Tsticket
	 */
	public function autocomplete($from = false, $to = false, $functionCall = false) {
		$html = '';

		$this->language->load('ticketsystem/all');

		if (isset($this->request->get['filter_value'])) {
			$arrayKey = preg_replace('/[a-z_]/', '', $this->request->get['id']);
			$arrayName = $this->request->get['name'];

			switch($this->request->get['filter_value']){

				case 'priority_updated' :
					$html = '';
					$this->load->model('ticketsystem/priority');
					$priority = $this->model_ticketsystem_priority->getPriorities(false);
					if($priority){
						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][from]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_from').'">';
						$html .= "<option value='any' ".(($from AND $from=='any') ? ' selected' : false).">".$this->language->get('text_any')."</option>";
						foreach ($priority as $value) {
							if($value['status'])
								$html .= "<option value='".$value['id']."' ".(($from AND $from==$value['id']) ? ' selected' : false).">".$value['name']."</option>";
						}
						$html .= "</select>";

						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][to]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_to').'">';
						$html .= "<option value='any' ".(($to AND $to=='any') ? ' selected' : false).">".$this->language->get('text_any')."</option>";
						foreach ($priority as $value) {
							if($value['status'])
								$html .= "<option value='".$value['id']."' ".(($to AND $to==$value['id']) ? ' selected' : false).">".$value['name']."</option>";
						}
						$html .= "</select>";
						$html .= $this->errorHtml($this->request->get['filter_value']);
					}
					break;

				case 'type_updated' :
					$html = '';
					$this->load->model('ticketsystem/types');
					$types = $this->model_ticketsystem_types->getTypes(false);
					if($types){
						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][from]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_from').'">';
						$html .= "<option value='any' ".(($from AND $from=='any') ? ' selected' : false).">".$this->language->get('text_any')."</option>";
						foreach ($types as $value) {
							if($value['status'])
								$html .= "<option value='".$value['id']."'".(($from AND $from==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= "</select>";

						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][to]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_to').'">';
						$html .= "<option value='any' ".(($to AND $to=='any') ? ' selected' : false).">".$this->language->get('text_any')."</option>";
						foreach ($types as $value) {
							if($value['status'])
								$html .= "<option value='".$value['id']."'".(($to AND $to==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= "</select>";
						$html .= $this->errorHtml($this->request->get['filter_value']);
					}
					break;
					
				case 'status_updated' :
					$html = '';
					$this->load->model('ticketsystem/status');
					$status = $this->model_ticketsystem_status->getStatuss(false);
					if($status){
						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][from]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_from').'">';
						$html .= "<option value='any' ".(($from AND $from=='any') ? ' selected' : false).">".$this->language->get('text_any')."</option>";
						foreach ($status as $value) {
							if($value['status'])
								$html .= "<option value='".$value['id']."'".(($from AND $from==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= "</select>";

						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][to]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_to').'">';
						$html .= "<option value='any' ".(($to AND $to=='any') ? ' selected' : false).">".$this->language->get('text_any')."</option>";
						foreach ($status as $value) {
							if($value['status'])
								$html .= "<option value='".$value['id']."'".(($to AND $to==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= "</select>";
						$html .= $this->errorHtml($this->request->get['filter_value']);
					}
					break;

				case 'group_updated' :
					$html = '';
					$this->load->model('ticketsystem/groups');
					$groups = $this->model_ticketsystem_groups->getGroups(false);
					if($groups){
						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][from]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_from').'">';
						$html .= "<option value='none' ".(($from AND $from=='none') ? ' selected' : false).">".$this->language->get('text_none')."</option>";
						$html .= "<option value='any' ".(($from AND $from=='any') ? ' selected' : false).">".$this->language->get('text_any')."</option>";
						foreach ($groups as $value) {
							$html .= "<option value='".$value['id']."'".(($from AND $from==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= "</select>";

						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][to]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_to').'">';
						$html .= "<option value='none' ".(($to AND $to=='none') ? ' selected' : false).">".$this->language->get('text_none')."</option>";
						$html .= "<option value='any' ".(($to AND $to=='any') ? ' selected' : false).">".$this->language->get('text_any')."</option>";
						foreach ($groups as $value) {
							$html .= "<option value='".$value['id']."'".(($to AND $to==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= "</select>";
						$html .= $this->errorHtml($this->request->get['filter_value']);
					}
					break;

				case 'agent_updated' :
					$html = '';
					$this->load->model('ticketsystem/agents');
					$agents = $this->model_ticketsystem_agents->getAgents(false);
					if($agents){
						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][from]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_from').'">';
						$html .= "<option value='none' ".(($from AND $from=='none') ? ' selected' : false).">".$this->language->get('text_none')."</option>";
						$html .= "<option value='any' ".(($from AND $from=='any') ? ' selected' : false).">".$this->language->get('text_any')."</option>";
						foreach ($agents as $value) {
							$html .= "<option value='".$value['id']."'".(($from AND $from==$value['id']) ? ' selected' : '').">".$value['username'].' - '.$value['email']."</option>";
						}
						$html .= "</select>";
						
						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][to]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_to').'">';
						$html .= "<option value='none' ".(($to AND $to=='none') ? ' selected' : false).">".$this->language->get('text_none')."</option>";
						$html .= "<option value='any' ".(($to AND $to=='any') ? ' selected' : false).">".$this->language->get('text_any')."</option>";
						foreach ($agents as $value) {
							$html .= "<option value='".$value['id']."'".(($to AND $to==$value['id']) ? ' selected' : '').">".$value['username'].' - '.$value['email']."</option>";
						}
						$html .= "</select>";
						$html .= $this->errorHtml($this->request->get['filter_value']);
					}
					break;

				case 'note_added' :
					$html = '<select name="'.$arrayName.'['.$arrayKey.'][from]" class="form-control selectpicker" data-live-search="true">';
					$html .= "<option value='any' ".(($from AND $from=='any') ? ' selected' : false).">".$this->language->get('text_any')."</option>";
					$html .= "<option value='forward' ".(($from AND $from=='forward') ? ' selected' : false).">".$this->language->get('text_forward')."</option>";
					$html .= "<option value='private' ".(($from AND $from=='private') ? ' selected' : false).">".$this->language->get('text_private')."</option>";
					$html .= "</select>";
					$html .= $this->errorHtml($this->request->get['filter_value']);
					break;

				case 'ticket' :
					$html = '<select name="'.$arrayName.'['.$arrayKey.'][from]" class="form-control selectpicker" data-live-search="true">';
					$html .= "<option value='created' ".(($from AND $from=='created') ? ' selected' : false).">".$this->language->get('text_created')."</option>";
					$html .= "<option value='updated' ".(($from AND $from=='updated') ? ' selected' : false).">".$this->language->get('text_updated')."</option>";
					$html .= "<option value='deleted' ".(($from AND $from=='deleted') ? ' selected' : false).">".$this->language->get('text_deleted')."</option>";
					$html .= "</select>";
					$html .= $this->errorHtml($this->request->get['filter_value']);
					break;

				default :
					break;
			}
		}

		if(!$functionCall)
			$this->response->setOutput($html);
		else
			return $html;
	}

	/**
	 * Return error at time of edit Auto complete function, is set to $this->error
	 * @param  string $name used to find error from $this->error
	 * @return string       error html if any
	 */
	protected function errorHtml($name) {
		$html = '';
		if(isset($this->error[$name])){
			$html = '<br/>';
			$html .= '<span class="text-danger prev-red">'.$this->error[$name].'</span>';
		}
		return $html;
	}

	/**
	 * This function validated passed events and set error to $this->error and return too
	 */
	public function validation($events) {
		if($events){
			foreach($events as $key => $value){
				if(!$value['type']){
					unset($events[$key]);
					continue;
				}
				if(isset($value['from'])){
					if(!$value['from'] AND !array_key_exists($value['from'], $this->skipError)){
						$this->error[$value['type']] = $this->language->get('error_event_'.$value['type']);
					}
				}
				if(isset($value['to'])){
					if(!$value['to'] AND !array_key_exists($value['to'], $this->skipError)){
						$this->error[$value['type']] = $this->language->get('error_event_'.$value['type']);
					}
				}
			}
		}

		$this->request->post['events'] = $events;

		return $this->error;
	}

}