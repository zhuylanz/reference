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
class ControllerTicketSystemTicketConditions extends controller {
	
	const CONTROLLER_NAME = 'ticketconditions';

	protected $error;
	protected $skipError = array('agent', 'group');

	public function index($args = array('conditions' => array(), 'error' => array(), 'id' => 'all')) {
		$this->error = $args['error'];

		$data = $this->language->load('ticketsystem/all');

		$data['conditions'] = is_array($args['conditions']) ? $args['conditions'] : array();
		$data['conditionsId'] = $this->request->get['name'] = 'conditions_'.$args['id'];

		$this->document->addScript('view/javascript/ticketsystem/ticketCondition.js');

		if(($this->request->post AND !isset($this->request->post['conditions_all']) AND !isset($this->request->post['conditions_one'])  AND $args['id']=='one') OR isset($this->request->post['conditions_all']) AND !$this->request->post['conditions_all'] AND !$this->request->post['conditions_one'] AND $args['id']=='one'){
			$data['error_condition'] = $this->language->get('error_sla_condition');
		} else {
			$data['error_condition'] = array();
		}

		$route = explode('/', $this->request->get['route']);
		//if route has not controller not sef safe
		if(!isset($route[1]))
			$route[1] = '';

		$data['text_info_conditions_all2'] = sprintf($this->language->get('text_info_conditions_all2'),$data['heading_'.$route[1]]);
		$data['text_info_conditions_one2'] = sprintf($this->language->get('text_info_conditions_one2'),$data['heading_'.$route[1]]);

		$data['token'] = $this->session->data['token'];
		$data['ticketConditions'] = $this->TsLoader->TsTicket->ticketConditions;

		foreach($data['conditions'] as $key => $value){
			$this->request->get['id'] = $key;
			$this->request->get['filter_value'] = $value['type'];
			$data['conditions'][$key]['html'] = $this->autocomplete($value['match'], $value['matchCondition'] , true);
		}

		return $this->load->view('ticketsystem/ticketCondition.tpl', $data);
	}

	/**
	 * This method works for 2 types 
	 * - using ajax, which will add - one html with condition to tpl and ($functioncall will be false)
	 * - used by controller (index function) to create html at time or edit or error add view ($functionCall = true and $match, $matchCondition will have value for fields)
	 *
	 * for it's 1st select option we called selectHtml()
	 * 
	 * Source is coming from library file Tsticket
	 */
	public function autocomplete($match = false, $matchCondition = false, $functionCall = false) {
		$html = '';

		$this->language->load('ticketsystem/all');

		if (isset($this->request->get['filter_value'])) {
			$arrayKey = preg_replace('/[a-z_]/', '', $this->request->get['id']);
			$arrayName = $this->request->get['name'];

			switch($this->request->get['filter_value']){

				case 'from_mail' :
				case 'to_mail' :

				case 'subject' :
				case 'description' :
				case 'subject_or_description' :

				case 'customer_name' :
				case 'customer_email' :

				case 'organization_name' :
				case 'organization_domain' :

					$html = $this->selectHtml($this->request->get['filter_value'], $arrayKey, $arrayName ,$matchCondition);
					$html .= '<input type="text" name="'.$arrayName.'['.$arrayKey.'][match]" class="form-control condition-mail" value="'.($match ? $match : false).'">';
					$html .= $this->errorHtml($this->request->get['filter_value']);
					break;

				case 'status' :
					$html = $this->selectHtml($this->request->get['filter_value'], $arrayKey, $arrayName ,$matchCondition);
					$this->load->model('ticketsystem/status');
					$status = $this->model_ticketsystem_status->getStatuss(false);
					if($status){
						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][match]" class="form-control selectpicker" data-live-search="true">';
						foreach ($status as $value) {
							if($value['status'])
								$html .= "<option value='".$value['id']."'".(($match AND $match==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= "</select>";
					}
					break;

				case 'type' :
					$html = $this->selectHtml($this->request->get['filter_value'], $arrayKey, $arrayName ,$matchCondition);
					$this->load->model('ticketsystem/types');
					$types = $this->model_ticketsystem_types->getTypes(false);
					if($types){
						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][match]" class="form-control selectpicker" data-live-search="true">';
						foreach ($types as $value) {
							if($value['status'])
								$html .= "<option value='".$value['id']."'".(($match AND $match==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= "</select>";
					}
					break;

				case 'priority' :
					$html = $this->selectHtml($this->request->get['filter_value'], $arrayKey, $arrayName ,$matchCondition);
					$this->load->model('ticketsystem/priority');
					$priority = $this->model_ticketsystem_priority->getPriorities(false);
					if($priority){
						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][match]" class="form-control selectpicker" data-live-search="true">';
						foreach ($priority as $value) {
							if($value['status'])
								$html .= "<option value='".$value['id']."' ".(($match AND $match==$value['id']) ? ' selected' : false).">".$value['name']."</option>";
						}
						$html .= "</select>";
					}
					break;

				case 'agent' :
					$html = $this->selectHtml($this->request->get['filter_value'], $arrayKey, $arrayName ,$matchCondition);
					$this->load->model('ticketsystem/agents');
					$agents = $this->model_ticketsystem_agents->getAgents(false);
					if($agents){
						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][match]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_none').'">';
						$html .= "<option value='0' selected>".$this->language->get('text_none')."</option>";
						foreach ($agents as $value) {
							$html .= "<option value='".$value['id']."'".(($match AND $match==$value['id']) ? ' selected' : '').">".$value['username'].' - '.$value['email']."</option>";
						}
						$html .= "</select>";
					}
					break;

				case 'group' :
					$html = $this->selectHtml($this->request->get['filter_value'], $arrayKey, $arrayName ,$matchCondition);
					$this->load->model('ticketsystem/groups');
					$groups = $this->model_ticketsystem_groups->getGroups(false);
					if($groups){
						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][match]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_none').'">';
						$html .= "<option value='0' selected>".$this->language->get('text_none')."</option>";
						foreach ($groups as $value) {
							$html .= "<option value='".$value['id']."'".(($match AND $match==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= "</select>";
					}
					break;

				case 'source' :
					$html = $this->selectHtml($this->request->get['filter_value'], $arrayKey, $arrayName ,$matchCondition);
					$source = $this->TsLoader->TsTicket->source;
					if($source){
						$html .= '<select name="'.$arrayName.'['.$arrayKey.'][match]" class="form-control selectpicker" data-live-search="true">';
						foreach ($source as $value) {
							$html .= "<option value='".$value."' ".(($match AND $match==$value) ? ' selected' : false).">".$this->language->get('text_'.$value)."</option>";
						}
						$html .= "</select>";
					}
					break;

				case 'created' :
					$html = $this->selectHtml($this->request->get['filter_value'], $arrayKey, $arrayName ,$matchCondition);
					$html .= '<div class="input-group date pull-right">';
					$html .= '  <input type="text" name="'.$arrayName.'['.$arrayKey.'][match]" class="form-control date" value="'.($match ? $match : false).'" data-date-format="YYYY-MM-DD">';
	                $html .= '  <span class="input-group-btn">';
	                $html .= '      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>';
	                $html .= '    </span>';
	                $html .= '  </div>';
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
	 * This function will add selection for condition
	 * @param  string $type           
	 * @param  string  $arrayKey       key value for name
	 * @param  string  $arrayName      name of field
	 * @param  string $matchCondition  default boolean 
	 * @return string                  html for autocomplete function
	 */
	protected function selectHtml($type, $arrayKey, $arrayName, $matchCondition = false) {

		$html = '<select name="'.$arrayName.'['.$arrayKey.'][matchCondition]" class="form-control">';
		switch($type){
			case 'from_mail' :
			case 'to_mail' :

			case 'customer_email' :

				$html .= "<option value='is' ".($matchCondition=='is' ? 'selected' : false).">".$this->language->get('text_condition_is')."</option>";
				$html .= "<option value='isNot' ".($matchCondition=='isNot' ? 'selected' : false).">".$this->language->get('text_condition_is_not')."</option>";
				$html .= "<option value='contains' ".($matchCondition=='contains' ? 'selected' : false).">".$this->language->get('text_condition_contains')."</option>";
				$html .= "<option value='notContains' ".($matchCondition=='notContains' ? 'selected' : false).">".$this->language->get('text_condition_not_contains')."</option>";
				break;

			case 'subject' :
			case 'description' :
			case 'subject_or_description' :

			case 'customer_name' :

			case 'organization_name' :
			case 'organization_domain' :

				$html .= "<option value='is' ".($matchCondition=='is' ? 'selected' : false).">".$this->language->get('text_condition_is')."</option>";
				$html .= "<option value='isNot' ".($matchCondition=='isNot' ? 'selected' : false).">".$this->language->get('text_condition_is_not')."</option>";
				$html .= "<option value='contains' ".($matchCondition=='contains' ? 'selected' : false).">".$this->language->get('text_condition_contains')."</option>";
				$html .= "<option value='notContains' ".($matchCondition=='notContains' ? 'selected' : false).">".$this->language->get('text_condition_not_contains')."</option>";
				$html .= "<option value='startWith' ".($matchCondition=='startWith' ? 'selected' : false).">".$this->language->get('text_condition_start_with')."</option>";
				$html .= "<option value='endWith' ".($matchCondition=='endWith' ? 'selected' : false).">".$this->language->get('text_condition_end_with')."</option>";
				break;

			case 'status' :
			case 'priority' :
			case 'type' :
			case 'agent' :
			case 'group' :
			case 'source' :
				$html .= "<option value='is' ".($matchCondition=='is' ? 'selected' : false).">".$this->language->get('text_condition_is')."</option>";
				$html .= "<option value='isNot' ".($matchCondition=='isNot' ? 'selected' : false).">".$this->language->get('text_condition_is_not')."</option>";
				break;

			case 'created' :
				$html .= "<option value='before' ".($matchCondition=='before' ? 'selected' : false).">".$this->language->get('text_condition_before')."</option>";
				$html .= "<option value='beforeOn' ".($matchCondition=='beforeOn' ? 'selected' : false).">".$this->language->get('text_condition_before_on')."</option>";
				$html .= "<option value='after' ".($matchCondition=='after' ? 'selected' : false).">".$this->language->get('text_condition_after')."</option>";
				$html .= "<option value='afterOn' ".($matchCondition=='afterOn' ? 'selected' : false).">".$this->language->get('text_condition_after_on')."</option>";
				break;

			default:
				break;
		}
		$html .= "</select>";

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
			$html = '<span class="text-danger prev-red">'.$this->error[$name].'</span>';
		}
		return $html;
	}

	/**
	 * This function validated passed conditions and set error to $this->error and return too
	 */
	public function validation($conditionsArgs = array('condition' => array(), 'key' => '')) {
		$conditions = $conditionsArgs['condition'];
		$keyCondition = $conditionsArgs['key'];

		if($conditions){
			foreach($conditions as $key => $value){
				if(!$value['type']){
					unset($conditions[$key]);
					continue;
				}
				if(isset($value['match'])){
					if(!$value['match'] AND !array_key_exists($value['match'], $this->skipError)){
						$this->error[$value['type']] = $this->language->get('error_condition_'.$value['type']);
					}
				}
			}
		}

		$this->request->post[$keyCondition] = $conditions;

		return $this->error;
	}

}