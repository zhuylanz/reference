<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 *  This Class function are complete to create all Actions for Help Desk, to use this just add this in your controller and echo in tpl, all work will done by this class. I
 *  t works from both create and edit function - same procedure
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemTicketActions extends controller {
	
	const CONTROLLER_NAME = 'ticketAction';

	protected $error;
	protected $skipError = array('assign_agent', 'assign_group');

	public function index($args = array('action' => array(), 'error' => array())) {
		$this->error = $args['error'];
		$actions = $args['action'];

		$this->document->addScript('view/javascript/ticketsystem/ticketAction.js');

		$data = $this->language->load('ticketsystem/all');

		if (isset($this->error['error_responses'])) {
			$data['error_responses'] = $this->error['error_responses'];
		} else {
			$data['error_responses'] = array();
		}

		$data['token'] = $this->session->data['token'];
		$data['ticketActions'] = $this->TsLoader->TsTicket->ticketAction;
		$data['ticketPlaceHolder'] = $this->TsLoader->TsTicket->ticketPlaceHolder;

		$route = explode('/', $this->request->get['route']);
		//if route has not controller not sef safe
		if(!isset($route[1]))
			$route[1] = '';

		$data['text_info_action2'] = sprintf($this->language->get('text_info_action2'),$data['heading_'.$route[1]]);

		if(is_array($actions))
			foreach($actions as $key => $value){
				$this->request->get['id'] = $key;
				$this->request->get['filter_value'] = $value['type'];
				$actions[$key]['html'] = $this->autocomplete(isset($value['action']) ? $value['action'] : false, true);
			}
		else
			$actions = array();

		$data['actions'] = $actions;

		return $this->load->view('ticketsystem/ticketAction.tpl', $data);
	}

	public function validation(array $actions) {
		if($actions){
			foreach($actions as $key => $value){
				if(!$value['type']){
					unset($actions[$key]);
					continue;
				}
				if(isset($value['action'])){
					if(is_array($value['action'])){
						foreach ($value['action'] as $key2 => $value2) {
							if(($value['type']=='mail_agent' || $value['type']=='mail_group' || $value['type']=='mail_customer') AND ($key2=='subject' || $key2=='message' || $key2=='emailtemplate') ){
								if((!$value['action']['subject'] || !strip_tags(html_entity_decode($value['action']['message']))) && (!isset($value['action']['emailtemplate']) || !$value['action']['emailtemplate'])){
									$this->error[$value['type'].'-subject'] = $this->language->get('error_subject');
									$this->error[$value['type'].'-message'] = $this->language->get('error_message');
									$this->error[$value['type'].'-emailtemplate'] = $this->language->get('error_emailtemplate');

								}
							}elseif(!$value2 OR !strip_tags(html_entity_decode($value2)))
								$this->error[$value['type'].'-'.$key2] = $this->language->get('error_'.$key2);
						}
					}else{
						if(!$value['action'] AND array_key_exists($value['type'], $this->skipError)){
							$this->error[$value['type']] = $this->language->get('error_'.$value['type']);
						}
					}
				}
			}
		}

		if(!$actions)
			$this->error['error_responses'] = $this->language->get('error_response_action');

		$this->request->post['actions'] = $actions;

		return $this->error;
	}

	/**
	 * This method works for 2 types 
	 * - using ajax, which will add - one html with action to tpl and ($functioncall will be false)
	 * - used by controller (index function) to create html at time or edit or error add view ($functionCall = true and $action will have value for fields)
	 * 
	 * Description for keywords used in function
	 * assign - that agents which has ticket (assigned to agent)
	 * current - that agents which is performing response
	 * option value 0 - remove groups or agents from ticket
	 */
	
	public function autocomplete($action = false, $functionCall = false) {
		$html = '';

		$this->language->load('ticketsystem/all');

		if (isset($this->request->get['filter_value'])) {
			$arrayKey = preg_replace('/[a-z]/', '', $this->request->get['id']);
			switch($this->request->get['filter_value']){
				case 'status' :
					$this->load->model('ticketsystem/status');
					$status = $this->model_ticketsystem_status->getStatuss(false);
					if($status){
						$html = '<select name="actions['.$arrayKey.'][action]" class="form-control selectpicker" data-live-search="true">';
						foreach ($status as $value) {
							if($value['status'])
								$html .= "<option value='".$value['id']."'".(($action AND $action==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= "</select>";
					}
					break;

				case 'type' :
					$this->load->model('ticketsystem/types');
					$types = $this->model_ticketsystem_types->getTypes(false);
					if($types){
						$html = '<select name="actions['.$arrayKey.'][action]" class="form-control selectpicker" data-live-search="true">';
						foreach ($types as $value) {
							if($value['status'])
								$html .= "<option value='".$value['id']."'".(($action AND $action==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= "</select>";
					}
					break;

				case 'priority' :
					$this->load->model('ticketsystem/priority');
					$priority = $this->model_ticketsystem_priority->getPriorities(false);
					if($priority){
						$html = '<select name="actions['.$arrayKey.'][action]" class="form-control selectpicker" data-live-search="true">';
						foreach ($priority as $value) {
							if($value['status'])
								$html .= "<option value='".$value['id']."' ".(($action AND $action==$value['id']) ? ' selected' : false).">".$value['name']."</option>";
						}
						$html .= "</select>";
					}
					break;

				case 'tag' :
					$this->load->model('ticketsystem/tags');
					$tags = $this->model_ticketsystem_tags->getTags(false);
					if($tags){
						$html = '<select name="actions['.$arrayKey.'][action][]" class="form-control selectpicker" data-live-search="true" multiple>';
						foreach ($tags as $value) {
							$html .= "<option value='".$value['id']."'".(($action AND in_array($value['id'], $action)) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= "</select>";
						$html .= $this->errorHtml($this->request->get['filter_value']);
					}
					break;

				case 'cc' :
				case 'bcc' :
					$this->load->model('ticketsystem/agents');
					$agents = $this->model_ticketsystem_agents->getAgents(false);
					if($agents){
						$html = '<select name="actions['.$arrayKey.'][action][]" class="form-control selectpicker" data-live-search="true" multiple>';
						foreach ($agents as $value) {
							$html .= "<option value='".$value['email']."'".(($action AND in_array($value['email'], $action)) ? ' selected' : '').">".$value['username'].' - '.$value['email']."</option>";
						}
						$html .= "</select>";
						$html .= $this->errorHtml($this->request->get['filter_value']);
					}
					break;

				case 'note' :
					$html = '<textarea name="actions['.$arrayKey.'][action][note]" class="form-control placeholders-enabled">'.(($action AND $action['note']) ? $action['note'] : false).'</textarea>';
					$html .= $this->errorHtml('note-note');
					$html .= '<span class="add-margin"></span>';
					$html .= '<input type="checkbox" name="actions['.$arrayKey.'][action][isPrivate]" class="margin-none" id="isPrivate"'.(($action AND isset($action['isPrivate'])) ? ' checked' : '').' value="1"/> <label for="isPrivate">'.$this->language->get('text_is_private').'</label>';
					break;

				case 'assign_agent' :
					$this->load->model('ticketsystem/agents');
					$agents = $this->model_ticketsystem_agents->getAgents(false);
					if($agents){
						$html = '<select name="actions['.$arrayKey.'][action]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_none').'">';
						$html .= "<option value='0' selected>".$this->language->get('text_none')."</option>";
						$html .= "<option value='current' ".(($action AND $action=='current') ? ' selected' : '').">".$this->language->get('text_response_performing')."</option>";
						foreach ($agents as $value) {
							$html .= "<option value='".$value['id']."'".(($action AND $action==$value['id']) ? ' selected' : '').">".$value['username'].' - '.$value['email']."</option>";
						}
						$html .= "</select>";
					}
					break;

				case 'assign_group' :
					$this->load->model('ticketsystem/groups');
					$groups = $this->model_ticketsystem_groups->getGroups(false);
					if($groups){
						$html = '<select name="actions['.$arrayKey.'][action]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_none').'">';
						$html .= "<option value='0' selected>".$this->language->get('text_none')."</option>";
						foreach ($groups as $value) {
							$html .= "<option value='".$value['id']."'".(($action AND $action==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= "</select>";
					}
					break;

				case 'mail_agent' :
					$this->load->model('ticketsystem/agents');
					$agents = $this->model_ticketsystem_agents->getAgents(false);
					if($agents){
						$html = '<select name="actions['.$arrayKey.'][action][agent]" class="form-control selectpicker" data-live-search="true">';
						$html .= "<option value='assign' ".(($action AND isset($action['agent']) AND $action['agent']=='assign') ? ' selected' : '').">".$this->language->get('text_assign_agent')."</option>";
						$html .= "<option value='current'".(($action AND isset($action['agent']) AND $action['agent']=='current') ? ' selected' : '').">".$this->language->get('text_response_performing')."</option>";
						foreach ($agents as $value) {
							$html .= "<option value='".$value['id']."'".(($action AND isset($action['agent']) AND $action['agent']==$value['id']) ? ' selected' : '').">".$value['username'].' - '.$value['email']."</option>";
						}
						$html .= '</select><span class="add-margin"></span>';

						$this->load->model('ticketsystem/emailtemplates');
						$emailtemplates = $this->model_ticketsystem_emailtemplates->getEmailTemplates(false, array('status' => 1));
						$html .= '<select name="actions['.$arrayKey.'][action][emailtemplate]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_mail_select_placeholder').'">';
						$html .= "<option value='0'>".$this->language->get('text_none')."</option>";
						foreach ($emailtemplates as $value) {
							$html .= "<option value='".$value['id']."'".(($action AND isset($action['emailtemplate']) AND $action['emailtemplate']==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= '</select>';
						$html .= $this->errorHtml('mail_agent-emailtemplate');
						$html .= '<span class="add-margin"></span>';
						$html .= '<div class="text-center">OR</div>';
						$html .= '<span class="add-margin"></span>';

						$html .= '<input type="text" name="actions['.$arrayKey.'][action][subject]" placeholder="'.$this->language->get('text_subject').'" class="form-control placeholders-enabled" value="'.(($action AND isset($action['subject'])) ? $action['subject'] : '').'">';
						$html .= $this->errorHtml('mail_agent-subject');
						$html .= '<span class="add-margin"></span>';
						$html .= '<textarea name="actions['.$arrayKey.'][action][message]" class="form-control action-summernote placeholders-enabled" placeholder="'.$this->language->get('text_message').'">'.(($action AND isset($action['message'])) ? $action['message'] : '').'</textarea>';
						$html .= $this->errorHtml('mail_agent-message');
					}
					break;

				case 'mail_group' :
					$this->load->model('ticketsystem/groups');
					$groups = $this->model_ticketsystem_groups->getGroups(false);
					if($groups){
						$html = '<select name="actions['.$arrayKey.'][action][group]" class="form-control selectpicker" data-live-search="true">';
						$html .= "<option value='assign' ".(($action AND isset($action['group']) AND $action['group']=='assign') ? ' selected' : '').">".$this->language->get('text_assign_group')."</option>";
						foreach ($groups as $value) {
							$html .= "<option value='".$value['id']."'".(($action AND isset($action['group']) AND $action['group']==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= '</select><span class="add-margin"></span>';

						$this->load->model('ticketsystem/emailtemplates');
						$emailtemplates = $this->model_ticketsystem_emailtemplates->getEmailTemplates(false, array('status' => 1));
						$html .= '<select name="actions['.$arrayKey.'][action][emailtemplate]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_mail_select_placeholder').'">';
						$html .= "<option value='0'>".$this->language->get('text_none')."</option>";
						foreach ($emailtemplates as $value) {
							$html .= "<option value='".$value['id']."'".(($action AND isset($action['emailtemplate']) AND $action['emailtemplate']==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= '</select>';
						$html .= $this->errorHtml('mail_group-emailtemplate');
						$html .= '<span class="add-margin"></span>';
						$html .= '<div class="text-center">OR</div>';
						$html .= '<span class="add-margin"></span>';

						$html .= '<input type="text" name="actions['.$arrayKey.'][action][subject]" placeholder="'.$this->language->get('text_subject').'" class="form-control placeholders-enabled" value="'.(($action AND isset($action['subject'])) ? $action['subject'] : '').'">';
						$html .= $this->errorHtml('mail_group-subject');
						$html .= '<span class="add-margin"></span>';
						$html .= '<textarea name="actions['.$arrayKey.'][action][message]" class="form-control action-summernote placeholders-enabled" placeholder="'.$this->language->get('text_message').'">'.(($action AND isset($action['message'])) ? $action['message'] : '').'</textarea>';
						$html .= $this->errorHtml('mail_group-message');
					}
					break;

				case 'mail_customer' :
						$this->load->model('ticketsystem/emailtemplates');
						$emailtemplates = $this->model_ticketsystem_emailtemplates->getEmailTemplates(false, array('status' => 1));
						$html = '<select name="actions['.$arrayKey.'][action][emailtemplate]" class="form-control selectpicker" data-live-search="true" title="'.$this->language->get('text_mail_select_placeholder').'">';
						$html .= "<option value='0'>".$this->language->get('text_none')."</option>";
						foreach ($emailtemplates as $value) {
							$html .= "<option value='".$value['id']."'".(($action AND isset($action['emailtemplate']) AND $action['emailtemplate']==$value['id']) ? ' selected' : '').">".$value['name']."</option>";
						}
						$html .= '</select>';
						$html .= $this->errorHtml('mail_customer-emailtemplate');
						$html .= '<span class="add-margin"></span>';
						$html .= '<div class="text-center">OR</div>';
						$html .= '<span class="add-margin"></span>';

						$html .= '<input type="text" name="actions['.$arrayKey.'][action][subject]" placeholder="'.$this->language->get('text_subject').'" class="form-control placeholders-enabled" value="'.(($action AND isset($action['subject'])) ? $action['subject'] : '').'">';
						$html .= $this->errorHtml('mail_customer-subject');
						$html .= '<span class="add-margin"></span>';
						$html .= '<textarea name="actions['.$arrayKey.'][action][message]" class="form-control action-summernote placeholders-enabled" placeholder="'.$this->language->get('text_message').'">'.(($action AND isset($action['message'])) ? $action['message'] : '').'</textarea>';
						$html .= $this->errorHtml('mail_customer-message');
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
	 * add error to html if any exits in $this->error realted to passed $name
	 * @param  string $name 
	 * @return string error html
	 */
	protected function errorHtml($name) {
		$html = '';
		if(isset($this->error[$name])){
			$html = '<span class="text-danger prev-red">'.$this->error[$name].'</span>';
		}
		return $html;
	}

	/**
	 * Small but Power-full
	 * This function call TsActions class to apply passed actions to selected ticket
	 * @param  array  $data - Main value in this is ticket-id
	 * @return string       message return from TsActions class
	 */
	public function applyActionsOnRequest($data = array()){
		return $this->TsLoader->TsActions->applyActionsOnRequest($data);
	}

}