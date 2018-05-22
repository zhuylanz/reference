<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to show Tickets added in Helpdesk, work on those
 * No doubtfully main class of HelpDesk mod
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemTickets extends TsBase {

	const CONTROLLER_NAME = 'tickets';

	public $allowedFields = array(
							'filter_t__id',
							'filter_t__status',
							'filter_t__priority',
							'filter_t__type',
							'filter_t__assign_agent',
							'filter_t__customer_id',
							'filter_t__group',
							'filter_t__provider',
							'filter_t__date_added',
							'filter_ta__id',
							'filter_ttt__tag_id',
							);

	public $imageMimeArray = array(
							'image/jpeg',
							'image/jpg',
							'image/png',
							'image/gif',
							);
	
	public function __construct($registry){
		$this->registry = $registry;
		$this->extendedClassCall = true;
		parent::__construct($registry);

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'modFolder' => 'ticketsystem',
						'defaultSort' => 't.id',
						'allowedFields' => $this->allowedFields,
					)
			);
		//it will work for email fetch from store saved email id's
		$this->TsLoader->TsFetchEmail->emailFetch();
	}
	
	/**
	 * Not Real Constructor, just dummy
	 * @return array loaded data from base controller
	 */
	public function _construct(){
		return $this->data;
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/tickets');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'tickets',
						'tplFile' => 'tickets_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_tickets');
		$data['text_list'] = $this->language->get('text_list_tickets');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = '';

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_tickets') => 'tickets',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/tickets/create', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['copy'] = $this->url->link('ticketsystem/tickets/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/tickets/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['ticketsResult'] = '';

		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];
			unset($this->session->data['error_warning']);
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$this->load->model('ticketsystem/status');
		$data['statuss'] = $this->model_ticketsystem_status->getStatuss(false);

		$this->load->model('ticketsystem/types');
		$data['types'] = $this->model_ticketsystem_types->getTypes(false);

		$this->load->model('ticketsystem/groups');
		$data['groups'] = $this->model_ticketsystem_groups->getGroups(false);

		$this->load->model('ticketsystem/agents');
		$data['agents'] = $this->model_ticketsystem_agents->getAgents(false);

		$this->load->model('ticketsystem/priority');
		$data['priorities'] = $this->model_ticketsystem_priority->getPriorities(false);

		$data['ts_roles'] = $this->agent['roles']['tickets'] ? $this->agent['roles']['tickets'] : array();

		/**
		 * set default sorting for tpl it will be updated from tpl js
		 */
		$order = 'desc';
		$data['sort_ticket_id'] = '&sort=t.id' . '&order='.$order;
		$data['sort_ticket_subject'] = '&sort=t.subject' . '&order='.$order;
		$data['sort_type'] = '&sort=t.type' . '&order='.$order;
		$data['sort_priority'] = '&sort=t.priority' . '&order='.$order;
		$data['sort_status'] = '&sort=t.status' . '&order='.$order;
		$data['sort_group'] = '&sort=t.group' . '&order='.$order;
		$data['sort_customer'] = '&sort=t.customer_id' . '&order='.$order;
		$data['sort_date_added'] = '&sort=t.date_added' . '&order='.$order;
		$data['sort_agent'] = '&sort=t.assign_agent' . '&order='.$order;

		$data['filterColumn'] = $this->load->controller('ticketsystem/ticketsfilter');
	
		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
		
	}

	/**
	 * Function used to return added Tickets in HelpDesk module
	 * @return json object / hmlt Tickets
	 */
	public function tickets() {
		$data = $this->_construct();

		$this->load->model('ticketsystem/tickets');

		$url = '';

		$ticketAccess = $this->getAgentTicketAccessData();

		$this->session->data['TsLocalStorage'] = $this->request->get;

		$overrideRequestData = [];
		foreach ($this->request->get as $getKey => $get) {
			$overrideRequestData[$getKey] = preg_match('/,/', $get) ? explode(',', $get) : $get;
		}

		$this->TsLoader->TsHelper->overrideRequestData($overrideRequestData, true);
		if(!isset($this->request->get['filter_t__assign_agent']))
			$this->TsLoader->TsHelper->overrideRequestData($ticketAccess, true);

		$total = $this->model_ticketsystem_tickets->getTotalTickets();

		$results = $this->model_ticketsystem_tickets->getTickets();

		$data['tickets'] = array();
		
		foreach ($results as $result) {
			$data['tickets'][] = array(
				'id' 		 	=> $result['id'],
				'subject'       => $result['subject'],
				'type'   		=> $result['typeName'],
				'status'   		=> $result['statusName'],
				'priority' 		=> $result['priorityName'],
				'group'   		=> $result['groupName'],
				'agent'   		=> $result['agentName'],
				'customer'   	=> stripslashes($result['customerName']),
				'customerEmail' => $result['customerEmail'],
				'date_added'   	=> $this->convertDateFormat($result['date_added']),
				'response_time' => $this->getDateDifference($result['response_time']),
				'resolve_time' => $this->getDateDifference($result['resolve_time']),
				'edit'       	=> $this->url->link('ticketsystem/tickets/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
			);
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		if(isset($this->request->get['page'])){
			$page = $this->request->get['page'];
		}else{
			$page = 1;
		}

		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = '{page}';

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total - $this->config->get('config_limit_admin'))) ? $total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total, ceil($total / $this->config->get('config_limit_admin')));
		
		if(version_compare(VERSION, '2.1.0.1', '<=')) {
			if(isset($this->request->server['HTTP_X_REQUESTED_WITH']) AND $this->request->server['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
				$this->response->setOutput($this->load->view('ticketsystem/tickets.tpl', $data));
			}else{
				return $this->load->view('ticketsystem/tickets.tpl', $data);
			}
		}else{
			if(isset($this->request->server['HTTP_X_REQUESTED_WITH']) AND $this->request->server['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
				$this->response->setOutput($this->load->view('ticketsystem/tickets.tpl', $data));
			}else{
				$this->response->setOutput($this->load->view('ticketsystem/tickets.tpl', $data));
			}
		}
	}

	/**
	 * Function return Time based on Agent and DB result and show values
	 * @param  string $date date passed from DB result
	 * @return string based on calculated result
	 */
	protected function getDateDifference($date){
		if(!$date)
			return false;

		$dateNow = date("Y-m-d H:i:s");
		$addTime = $this->getDateDifferences();
		if(($dateNowStrToTime = (strtotime($dateNow) + $this->getAgentOffset())) > ($dateStrToTime = strtotime($date) + $addTime)){
			$status = '-';
			//can add sla breach code here for manage data
		}else
			$status = '+';

		return $status.' '.$this->secondsToTime($dateNowStrToTime - $dateStrToTime);
	}

	/**
	 * Convert Seconds to Days, Hours, Min, Seconds
	 * @param  int $seconds seconds
	 * @return string  based on passed seconds
	 */
	protected function secondsToTime($seconds) {
	    $dtF = new DateTime("@0");
	    $dtT = new DateTime("@$seconds");
	    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
	}

public function create() {
		/**
		 * This TsBase function used to check that "Current Agent" has permission to this particular function
		 */
		$this->checkEventAccessPermission(array(
											'key'=> self::CONTROLLER_NAME, 
											'event'=> self::CONTROLLER_NAME.'.'.__FUNCTION__
											)
										);

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'tickets/create',
						'tplFile' => 'tickets_create',
					)
			);

		$this->document->setTitle($this->language->get('heading_tickets'));

		$this->load->model('ticketsystem/tickets');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateCreateForm()) {
			
			$this->TsLoader->TsService->model(array('model' => 'ticketsystem/tickets'));
			$this->TsLoader->TsService->model(array('model' => 'ticketsystem/customers'));

			$this->request->post['email'] = $this->request->post['ts_customer_mailid'] ? $this->request->post['ts_customer_mailid'] : $this->request->post['ts_customer_manual'];
			$this->request->post['receivers']['to'] = $this->request->post['email'];
			
			if($this->request->post['login_option'] && $this->request->post['login_option'] == 'list'){
				if(isset($this->request->post['ts_customer_mailid']) && $this->request->post['ts_customer_mailid']){

					$this->TsLoader->TsService->model(array('model' => 'account/customer', 'location' => 'catalog'));
					$customer_details = $this->model_account_customer->getCustomerByEmail($this->request->post['ts_customer_mailid']);
					if(isset($customer_details['firstname']) && $customer_details['firstname']){
						$this->request->post['name'] = $customer_details['firstname'].' '.$customer_details['lastname'];
					}else{
						$customers = $this->model_ticketsystem_customers->getCustomers(false, array('c.email' => $this->request->post['ts_customer_mailid']));
						$this->request->post['name'] = $customers[0]['name'];
					}
				}
			}else{
				if(isset($this->request->post['ts_customer_manual']) && $this->request->post['ts_customer_manual']){
					if(isset($this->request->post['ts_customer_manual_name']) && $this->request->post['ts_customer_manual_name'])
						$this->request->post['name'] = $this->request->post['ts_customer_manual_name'];
				}
			}
						
			$customers = $this->model_ticketsystem_customers->getCustomers(false, array('c.email' => $this->request->post['email']));
				
				if($customers)
					$customerId = $customers[0]['id'];
				else{
					//check if this email exists in OC
					$this->TsLoader->TsService->model(array('model' => 'account/customer', 'location' => 'catalog'));
					$customer = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
					if($customer)
						$ocCustomerId = $customer['customer_id'];
					else
						$ocCustomerId = 0;
					
					//create one in TS
					$customerData = array(
										'customer_id' => $ocCustomerId,
										'email' => $this->request->post['email'],
										'name' => $this->request->post['name'],
									);
					$customerId = $this->model_ticketsystem_customers->addCustomer($customerData);
				}
			

			// if(!isset($this->session->data['ts_customer']) AND !$this->customer->getId()){
			// 	$this->session->data['ts_customer'] = array(
			// 											'id' => $customerId,
			// 											'email' => $this->request->post['email'],
			// 											);
			// }

			$defaultDataToCreateTicket = array(
										'provider' => 'web', 
										'customer_id' => $customerId, 
										'agent_id' => $customerId, 
										'sender_type' => 'customer',
										'messagetype' => 'create',
									 );

			$postData = array_merge($this->request->post, $defaultDataToCreateTicket);

			$ticket_id = $this->model_ticketsystem_tickets->addTicket($postData);
						
			$this->session->data['success'] = $this->language->get('text_success_add');

			$url = '';

			$this->response->redirect($this->url->link('ticketsystem/tickets', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getCreate();
	}

	
	protected function getCreate(){
		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_create_'.self::CONTROLLER_NAME);

		$data['text_form'] = $this->language->get('text_generate_ticket');

	
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}		

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_tickets') => 'tickets',
					$data['text_form'] => 'tickets/create',
					)
			);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('ticketsystem/tickets/create', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('ticketsystem/tickets/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
		}		

		$data['cancel'] = $this->url->link('ticketsystem/tickets', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$config = array(
						'ts_fields',
						'ts_required_fields',
						'ts_fileupload_size',
						'ts_fileupload_ext',
						'ts_editor',
					);

		foreach($config as $value){
			$data[$value] = $this->config->get($value);
		}


		if($data['ts_editor']){
			$this->document->addScript('admin/view/javascript/summernote/summernote.js');
			$this->document->addStyle('admin/view/javascript/summernote/summernote.css');
		}

		$postArray = array(
					'login_option',
					'ts_customer_mailid',
					'ts_customer_manual',
					'ts_customer_manual_name',
					'subject',
					'message',
					'group',
					'agent',
					'status',
					'priority',
					'tickettype',
					'custom_field',
					'fileupload',
					);

		foreach($postArray as $value){
			if(isset($this->request->post[$value]))
				$data[$value] = $this->request->post[$value];
			else
				$data[$value] = false;
			if(isset($this->error[$value]))
				$data['error_'.$value] = $this->error[$value];
			else
				$data['error_'.$value] = false;
		}

		if($this->error)
			foreach($this->error as $keyError => $dataError){
				$data['error_'.$keyError] = $dataError;
			}

		if(isset($data['custom_field']['tickets']))
			$data['tickets'] = $data['custom_field']['tickets'];

		// $this->TsLoader->TsService->model(array('model' => 'ticketsystem/types'));

		if(is_array($data['ts_fields'])){
			if(in_array('group', $data['ts_fields'])){
				$data['groups'] = array();
				$this->TsLoader->TsService->model(array('model' => 'ticketsystem/groups'));
				$groups = $this->model_ticketsystem_groups->getGroups(false);
				foreach($groups as $group){
					// if($group['status'])
						$data['groups'][] = array(
												'id' => $group['id'],
												'name' => $group['name'],
											);
				}
			}
		
			if(in_array('agent', $data['ts_fields'])){
				$data['agents'] = array();
				$this->TsLoader->TsService->model(array('model' => 'ticketsystem/agents'));
				// $this->load->model('ticketsystem/agents');
				$agents = $this->model_ticketsystem_agents->getAgents(false);
				foreach($agents as $agent){
					// if($agent['status'])
						$data['agents'][] = array(
												'id' => $agent['id'],
												'name' => $agent['username'],
											);
				}
			}
			if(in_array('tickettype', $data['ts_fields'])){
				$data['types'] = array();
				$this->load->model('ticketsystem/types');
				$types = $this->model_ticketsystem_types->getTypes(false);
				foreach($types as $type){
					if($type['status'])
						$data['types'][] = array(
												'id' => $type['id'],
												'name' => $type['name'],
											);
				}
			}
			if(in_array('status', $data['ts_fields'])){
				$data['statuss'] = array();
				$this->TsLoader->TsService->model(array('model' => 'ticketsystem/status'));
				// $this->load->model('ticketsystem/status');
				$statuss = $this->model_ticketsystem_status->getStatuss(false);
				foreach($statuss as $status){
					if($status['status'])
						$data['statuss'][] = array(
												'id' => $status['id'],
												'name' => $status['name'],
											);
				}
			}
			if(in_array('priority', $data['ts_fields'])){
				$data['priorities'] = array();
				$this->TsLoader->TsService->model(array('model' => 'ticketsystem/priority'));
				// $this->load->model('ticketsystem/priority');
				$priorities = $this->model_ticketsystem_priority->getPriorities(false);
				foreach($priorities as $priority){
					if($priority['status'])
						$data['priorities'][] = array(
												'id' => $priority['id'],
												'name' => $priority['name'],
											);
				}
			}
		}else
			$data['ts_fields'] = array();

		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/customers'));
		$data['total_customers'] = $this->model_ticketsystem_customers->getAllCustomers(false);
		
		
		

		$data['custom_fields'] = array();
		
		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		//fetch custom fields
		$this->load->model('ticketsystem/types');
		$this->TsLoader->TsService->model(array('model' => 'customer/custom_field', 'location' => 'catalog'));
		$custom_fields = $this->model_customer_custom_field->getCustomFields($customer_group_id);
			
		// $this->load->model('ticketsystem/types');

		foreach ($custom_fields as $custom_field) { 
			if ($custom_field['location'] == 'tickets') {
				$custom_field['tstype'] = $this->model_ticketsystem_types->getCustomFieldType($custom_field['custom_field_id']);
				$data['custom_fields'][] = $custom_field;
			}
		}
	
		$data['token'] = $this->session->data['token'];
		
		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));

	}

	protected function validateCreateForm() {
	
		if(is_array($this->config->get('ts_required_fields'))){
			foreach($this->config->get('ts_required_fields') as $field){
				if(isset($this->request->post[$field]) AND !$this->request->post[$field])
					$this->error[$field] = $this->language->get('error_'.$field);
			}
		}

		if (isset($this->request->post['login_option']) && $this->request->post['login_option'] == '') {
			$this->error['login_option'] = $this->language->get('error_login_option');
		}

		if(isset($this->request->post['login_option']) && $this->request->post['login_option']){
			if($this->request->post['login_option'] == 'list'){
				if(!isset($this->request->post['ts_customer_mailid']) OR !$this->request->post['ts_customer_mailid'] OR !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['ts_customer_mailid'])){
					$this->error['ts_customer_mailid'] = $this->language->get('error_ts_customer_mailid');	
				}
			}else{
				if(!isset($this->request->post['ts_customer_manual']) OR !$this->request->post['ts_customer_manual'] OR !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['ts_customer_manual'])){
					$this->error['ts_customer_manual'] = $this->language->get('error_ts_customer_manual');	
				}
				if(!isset($this->request->post['ts_customer_manual_name']) OR !$this->request->post['ts_customer_manual_name'] OR (utf8_strlen(trim($this->request->post['ts_customer_manual_name'])) >100)){
					$this->error['ts_customer_manual_name'] = $this->language->get('error_ts_customer_manual_name');
				}

				
			}
		}
		
		
		if(isset($this->request->files['fileupload']))
			if($this->request->files['fileupload']['name'] || (is_array($this->config->get('ts_required_fields')) AND in_array('fileupload', $this->config->get('ts_required_fields'))))
				if($error = $this->TsLoader->TsFileUpload->fileUploadValidate($this->request->files['fileupload'])){
					$this->error['fileupload'] = $error;
				}

		// Customer Group
		if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->post['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		//fetch custom fields
		$this->load->model('ticketsystem/types');
		$this->TsLoader->TsService->model(array('model' => 'customer/custom_field', 'location' => 'catalog'));
		$custom_fields = $this->model_customer_custom_field->getCustomFields($customer_group_id);

		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/types'));
		// $this->load->model('ticketsystem/types');

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'tickets') {
				$custom_field['tstype'] = $this->model_ticketsystem_types->getCustomFieldType($custom_field['custom_field_id']);
				if(isset($this->request->post['tickettype']) AND $this->request->post['tickettype']==$custom_field['tstype']){
					if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
						if($custom_field['type']!='file')
							$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						else{
							if(isset($this->request->files['custom_field'.$custom_field['custom_field_id']])){
								if($error = $this->TsLoader->TsFileUpload->fileUploadValidate($this->request->files['custom_field'.$custom_field['custom_field_id']])){
									$this->error['custom_field'.$custom_field['custom_field_id']] = $error;
								}
							}
						}
					}elseif($custom_field['type']=='file'){
						if(isset($this->request->files['custom_field'.$custom_field['custom_field_id']])){
							if($this->request->files['custom_field'.$custom_field['custom_field_id']]['name'] AND $this->request->files['custom_field'.$custom_field['custom_field_id']]['tmp_name'])
								if($error = $this->TsLoader->TsFileUpload->fileUploadValidate($this->request->files['custom_field'.$custom_field['custom_field_id']])){
									$this->error['custom_field'.$custom_field['custom_field_id']] = $error;
								}
						}
					}
				}
			}
		}

		if($this->error)
			$this->error['error_warning'] = $this->language->get('error_warning');

		return !$this->error;
	}

	public function edit() {
		/**
		 * This TsBase function used to check that "Current Agent" has permission to this particular function
		 */
		$this->checkEventAccessPermission(array(
											'key'=>self::CONTROLLER_NAME, 
											'event'=> self::CONTROLLER_NAME.'.'.__FUNCTION__
											)
										);

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'tickets/edit',
						'tplFile' => 'tickets_form',
					)
			);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/tickets', 'token=' . $this->session->data['token'] . $url, 'SSL'));

		$this->document->setTitle($this->language->get('heading_tickets'));

		$this->load->model('ticketsystem/tickets');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			if(isset($this->request->post['reply']['submit'])){
				$data = array(
							'id' => $this->request->get['id'],
							'message' => $this->request->post['reply']['message'],
							'agent_id' => $this->agent['id'],
							'sender_type' => 'agent',
							'messagetype' => 'reply',
							'receivers' => isset($this->request->post['reply']['receivers']) ? $this->request->post['reply']['receivers'] : array(),
							);	

				$this->model_ticketsystem_tickets->addTicketThread($data);			
				$this->session->data['success'] = $this->language->get('text_success_reply');
			}elseif(isset($this->request->post['forward']['submit'])){
				$data = array(
							'id' => $this->request->get['id'],
							'message' => $this->request->post['forward']['message'],
							'agent_id' => $this->agent['id'],
							'sender_type' => 'agent',
							'messagetype' => 'forward',
							'receivers' => isset($this->request->post['forward']['receivers']) ? $this->request->post['forward']['receivers'] : array(),
							'checkAttachment' => true,
							);	
				$this->model_ticketsystem_tickets->addTicketThread($data);			

				$this->session->data['success'] = $this->language->get('text_success_forward');
			}elseif(isset($this->request->post['internal']['submit'])){
					$data = array(
							'id' => $this->request->get['id'],
							'message' => $this->request->post['internal']['message'],
							'agent_id' => $this->agent['id'],
							'sender_type' => 'agent',
							'messagetype' => 'note',
							);	
				$this->model_ticketsystem_tickets->addTicketThread($data);
				$this->session->data['success'] = $this->language->get('text_success_internal');
			}
			$this->response->redirect($this->url->link('ticketsystem/tickets/edit', 'token=' . $this->session->data['token'] . $url.'&id='.$this->request->get['id'], 'SSL'));
		}

		$this->getView();
	}

	protected function getView() {
		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_'.self::CONTROLLER_NAME);

		$data['text_form'] = $this->language->get('text_view_ticket').' #'.$this->request->get['id'];
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		foreach ($this->error as $errorName => $error) {
			$data['error_'.$errorName] = $error;
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['custom_field'])) {
			$data['error_custom_field'] = $this->error['custom_field'];
		} else {
			$data['error_custom_field'] = '';
		}

		if (isset($this->error['forward_to'])) {
			$data['error_forward_to'] = $this->error['forward_to'];
		} else {
			$data['error_forward_to'] = '';
		}
		
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_tickets') => 'tickets',
					$data['text_form'] => 'tickets/edit&id='.$this->request->get['id'] ,
					)
			);

		$data['token'] = $this->session->data['token'];
		$data['ticketId'] = $this->request->get['id'];

		$data['action'] = $this->url->link('ticketsystem/tickets/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['cancel'] = $this->url->link('ticketsystem/tickets', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$ticketAccess = $this->getAgentTicketAccessData();

		if($ticketAccess)
			$filterArray = array('t.id' => $this->request->get['id'], 't.assign_agent' => $ticketAccess['filter_ta__id']);
		else
			$filterArray = array('t.id' => $this->request->get['id']);

		$data['ticket_info'] = $this->model_ticketsystem_tickets->getTicket($filterArray);
		
		if($data['ticket_info']['sla']){
			$data['response_time'] = $this->getDateDifference($data['ticket_info']['sla']['response_time']);
			$data['resolve_time'] = $this->getDateDifference($data['ticket_info']['sla']['resolve_time']);
		}else{
			$data['response_time'] = $data['resolve_time'] = false;
		}

		if($data['ticket_info'])
			$data['ticket_info']['customerLink'] = $this->url->link('ticketsystem/customers','token=' . $this->session->data['token'] . '&filter_c__email='.$data['ticket_info']['customerEmail']);
		else{
			$this->session->data['error_warning'] = $this->language->get('error_access_permission');
			$this->response->redirect($this->url->link('ticketsystem/tickets', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
		
		//add this agent entry into viewing table
		$this->model_ticketsystem_tickets->addTicketViewAgent(array('ticket_id' => $this->request->get['id'], 'agent_id' => $this->agent['id']));

		$data['ticket_info']['date_added'] = $this->convertDateFormat($data['ticket_info']['date_added']);

		$data['tickets'] = unserialize($data['ticket_info']['custom_field']) ? unserialize($data['ticket_info']['custom_field'])['tickets'] : array();

		//fetch custom fields
		$this->load->model('ticketsystem/types');
		$this->TsLoader->TsService->model(array('model' => 'customer/custom_field', 'location' => 'catalog'));
		$customer_group_id = $this->config->get('config_customer_group_id');
		$custom_fields = $this->model_customer_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) { 
			if ($custom_field['location'] == 'tickets') {
				$custom_field['tstype'] = $this->model_ticketsystem_types->getCustomFieldType($custom_field['custom_field_id']);
				$data['custom_fields'][] = $custom_field;
			}
		}

		$data['ticket_create'] = array();

		$ticket_create = $this->model_ticketsystem_tickets->getTicketThreads(array('ticket_id' => $this->request->get['id'], 'type' => 'create'));

		foreach($ticket_create as $key => $ticket_thread){
			$ticket_thread_attachments = $this->model_ticketsystem_tickets->getTicketThreadAttachments($ticket_thread['id']);
			$ticket_thread['attachments'] = array();
			foreach ($ticket_thread_attachments as $attachment) {
				if(!$attachment['id'])
					continue;
				$path = $attachment['path'].$attachment['fakename'];
				if(in_array($attachment['mime'], $this->imageMimeArray)){
					if(file_exists(DIR_IMAGE.$path))
						$attachment['viewImage'] = $this->model_tool_image->resize($path,50,50);
					else
						$attachment['viewImage'] = false;
				}
				else
					$attachment['viewImage'] = false;
				$attachment['path'] = HTTP_CATALOG.'image/'.$path;
				$ticket_thread['attachments'][] = $attachment;
			}
			$ticket_thread_receivers = $this->model_ticketsystem_tickets->getTicketThreadReceivers($ticket_thread['id']);
			$ticket_thread['receivers'] = array();
			if($ticket_thread_receivers){
				$ticket_thread['receivers'] = unserialize($ticket_thread_receivers['receivers']);
			}
			$ticket_thread['date_added'] = $this->convertDateFormat($ticket_thread['date_added']);
			$ticket_thread['date_updated'] = $this->convertDateFormat($ticket_thread['date_updated']);
			if($ticket_thread['type'] == 'create'){
				$data['ticket_create'] = $ticket_thread;
				continue;
			}
		}

		$data['ticket_threads'] = $this->getTicketThreads();

		if($ticketAccess)
			$filterArray = array('tn.ticket_id' => $this->request->get['id'], 'tn.agent_id' => $ticketAccess['filter_ta__id']);
		else
			$filterArray = array('tn.ticket_id' => $this->request->get['id']);
		
		$data['ticket_notes'] = $this->model_ticketsystem_tickets->getTicketNotes($filterArray);

		$ticket_drafts = $this->model_ticketsystem_tickets->getTicketDrafts(array('agent_id' => $this->agent['id'], 'ticket_id' => $this->request->get['id']));

		foreach ($ticket_drafts as $draft) {
			$data['ticket_drafts'][$draft['type']] = $draft['message'];
		}

		$data['ticket_responses'] = array();

		$this->load->model('ticketsystem/responses');
		$responses = $this->model_ticketsystem_responses->getResponses(false, array('r.status' => 1));
		foreach ($responses as $response) {
			$validFor = unserialize($response['valid_for']);
			if((int)$validFor['value'] AND $validFor['value']==$this->agent['id']){
				$data['ticket_responses'][] = $response;
			}elseif($validFor['value']=='groups' AND in_array($data['ticket_info']['group'],$validFor['groups'])){
				$data['ticket_responses'][] = $response;
			}elseif($validFor['value']=='all'){
				$data['ticket_responses'][] = $response;
			}
		}

		$postData = array(
						'ticket_info',
						'reply',
						'forward',
						'internal',
					);

		if(isset($this->request->post['ticket_info']))
			$data['ticket_info'] = array_merge($this->request->post['ticket_info'], $data['ticket_info']);

		if(isset($this->request->post['reply']))
			$data['reply'] = $this->request->post['reply'];
		else
			$data['reply'] = array();

		if(isset($this->request->post['forward']))
			$data['forward'] = $this->request->post['forward'];
		else
			$data['forward'] = array();

		if(isset($this->request->post['internal']))
			$data['internal'] = $this->request->post['internal'];
		else
			$data['internal'] = array();
		
		$data['ts_roles'] = $this->agent['roles']['tickets'];

		$this->load->model('ticketsystem/status');
		$data['ts_statuss'] = $this->model_ticketsystem_status->getStatuss(false);

		$this->load->model('ticketsystem/types');
		$data['ts_types'] = $this->model_ticketsystem_types->getTypes(false);

		$this->load->model('ticketsystem/groups');
		$data['ts_groups'] = $this->model_ticketsystem_groups->getGroups(false);

		$this->load->model('ticketsystem/agents');
		$data['ts_agents'] = $this->model_ticketsystem_agents->getAgents(false);

		$this->load->model('ticketsystem/priority');
		$data['ts_priorities'] = $this->model_ticketsystem_priority->getPriorities(false); 
		
		$this->load->model('ticketsystem/tags');
		$data['ts_tags'] = $this->model_ticketsystem_tags->getTags(false); 

		$data['ts_save_draft_time'] = $this->config->get('ts_save_draft_time') ? $this->config->get('ts_save_draft_time') : 20000;

		$data['ts_ticket_view_time'] = $this->config->get('ts_ticket_view_time') ? $this->config->get('ts_ticket_view_time') : 50000;

		$data['ts_update_status_to_spam'] = is_array($this->config->get('ts_ticket_status')) ? $this->config->get('ts_ticket_status')['spam'] : false;

		$data['delete_form'] = $this->url->link('ticketsystem/tickets/delete', 'token=' . $this->session->data['token'], 'SSL');

		$data['action_custom_fields'] = $this->url->link('ticketsystem/tickets/customField', 'token=' . $this->session->data['token'].'&id='.$this->request->get['id'], 'SSL');

		$data['current_agent'] = array(
									'id' => $this->agent['id'],
									'name' => $this->agent['name_alias'] ? $this->agent['name_alias'] : $this->agent['username'],
									'signature' => $this->agent['signature'],
									'email' => $this->agent['email'],
									'image' => $this->agent['image'] ? $this->model_tool_image->resize($this->agent['image'],50,50) : false,
								);

		//for prev and next buttons
		$data['prevId'] = $data['nextId'] = 0;
		if(isset($this->session->data['TsLocalStorage'])){
			$overrideRequestData = [];

			foreach ($this->session->data['TsLocalStorage'] as $getKey => $get) {
				$overrideRequestData[$getKey] = preg_match('/,/', $get) ? explode(',', $get) : $get;
			}

			$this->TsLoader->TsHelper->overrideRequestData($overrideRequestData, true);

			if(!isset($this->session->data['TsLocalStorage']['filter_t__assign_agent']))
				$this->TsLoader->TsHelper->overrideRequestData($ticketAccess, true);

			$results = $this->model_ticketsystem_tickets->getTickets();

			foreach ($results as $key => $result) {
				if($result['id']==$this->request->get['id']){
					$data['prevId'] = isset($results[$key-1]) ? $this->url->link('ticketsystem/tickets/edit', 'token=' . $this->session->data['token'] . '&id=' . $results[$key-1]['id'] . $url, 'SSL') : 0;
					$data['nextId'] = isset($results[$key+1]) ? $this->url->link('ticketsystem/tickets/edit', 'token=' . $this->session->data['token'] . '&id=' . $results[$key+1]['id'] . $url, 'SSL') : 0;
				}
			}
		}

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	/**
	 * Get Ticket Threads based on Ticket id
	 * @param  array  $data 
	 * @return string html
	 */
	public function getTicketThreads($data = array()){
		$data = $this->_construct();

		$this->load->model('ticketsystem/tickets');

		$data['current_agent'] = array(
									'id' => $this->agent['id'],
									'name' => $this->agent['name_alias'] ? $this->agent['name_alias'] : $this->agent['username'],
									'signature' => $this->agent['signature'],
									'email' => $this->agent['email'],
									'image' => $this->agent['image'] ? $this->model_tool_image->resize($this->agent['image'],50,50) : false,
								);

		$total_threads = $this->model_ticketsystem_tickets->getTotalTicketThreads(array('ticket_id' => $this->request->get['id']));

		$neagtiveThreads = 0;

		$filter_array = array(
							'ticket_id' => $this->request->get['id'],
						);

		if(isset($this->request->get['limit'])){
			$limits = explode('|', str_replace('ticket-thread-', '', $this->request->get['limit']));
			$filter_array['ticket_before_id'] = $limits[2];
			$filter_array['start'] = (($limits[0]) ? ($limits[0]-1) : 0);
			$neagtiveThreads = $limits[0];
		}

		$ticket_threads = $this->model_ticketsystem_tickets->getTicketThreads($filter_array);

		$data['ticket_threads'] = array();

		foreach($ticket_threads as $key => $ticket_thread){
			$ticket_thread_attachments = $this->model_ticketsystem_tickets->getTicketThreadAttachments($ticket_thread['id']);
			$ticket_thread['attachments'] = array();
			foreach ($ticket_thread_attachments as $attachment) {
				$path = $attachment['path'].$attachment['fakename'];
				if(in_array($attachment['mime'], $this->imageMimeArray)){
					if(file_exists(DIR_IMAGE.$path))
						$attachment['viewImage'] = $this->model_tool_image->resize($path,50,50);
					else
						$attachment['viewImage'] = false;
				}
				else
					$attachment['viewImage'] = false;

				$attachment['path'] = HTTP_CATALOG.'image/'.$path;
				$ticket_thread['attachments'][] = $attachment;
			}
			$ticket_thread_receivers = $this->model_ticketsystem_tickets->getTicketThreadReceivers($ticket_thread['id']);
			$ticket_thread['receivers'] = array();
			if($ticket_thread_receivers){
				$ticket_thread['receivers'] = unserialize($ticket_thread_receivers['receivers']);
			}
			$ticket_thread['date_added'] = $this->convertDateFormat($ticket_thread['date_added']);
			$ticket_thread['date_updated'] = $this->convertDateFormat($ticket_thread['date_updated']);
			if($ticket_thread['type'] == 'create'){
				continue;
			}
			$neagtiveThreads++;
			$data['ticket_threads'][$ticket_thread['id']] = $ticket_thread;
		}

		sort($data['ticket_threads']);

		$data['ts_roles'] = $this->agent['roles']['tickets'];

		//-1 for create message
		$total_threads = ($total_threads) ? ($total_threads-1) : 1;

		$data['ticket_pagination_info_disabled'] = ($neagtiveThreads == $total_threads ? true : false);

		$data['ticket_pagination_info'] = $neagtiveThreads.'|'.$total_threads.'|'.(isset($filter_array['ticket_before_id']) ? $filter_array['ticket_before_id'] : ($data['ticket_threads'] ? $data['ticket_threads'][count($data['ticket_threads'])-1]['id'] : 0));


		if(isset($this->request->server['HTTP_X_REQUESTED_WITH']) AND $this->request->server['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
			$this->response->setOutput($this->load->view('ticketsystem/ticketThreads.tpl', $data));
		else
			return $this->load->view('ticketsystem/ticketThreads.tpl', $data);
	}

	/**
	 * At time of Bulk Operation, Ts Events Trigger will work for 1st condition only 
	 * @description Function update Ticket Fields
	 */
	public function update() {
		$this->isAjax = true;

		/**
		 * This TsBase function used to check that "Current Agent" has permission to this particular function
		 */
		$this->checkEventAccessPermission(array(
											'key'=>self::CONTROLLER_NAME, 
											'event'=> self::CONTROLLER_NAME.'.'.__FUNCTION__
											)
										);

		$this->language->load('ticketsystem/all');

		$json = array();

		if(!$this->accessPermissionErrorStatus){
			$this->load->model('ticketsystem/tickets');

			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

				if(is_array($this->request->post['id'])){
					$postIds = $this->request->post['id'];
				}else{
					$postIds = array($this->request->post['id']);
				}

				foreach ($postIds as $postId) {

					if(is_array($this->request->post['value'])){
						$valuesArray = $this->request->post['value'];
					}else{
						$valuesArray = array($this->request->post['value']);
					}

					foreach($valuesArray as $column => $value){
						if(!$value)
							continue;

						$data = array(
									 'id' => $postId,
									 'column' => is_integer($column) ? strtolower(str_replace(array('input-','ts'),'' ,$this->request->post['type'])) : $column,
									 'value' => $value,
									 'agent_id' => $this->agent['id'],
									);

						switch ($data['column']) {
							case 'assign_agent':
							case 'group':
							case 'status':
							case 'type':
							case 'priority':
								$this->model_ticketsystem_tickets->editTicket(array($data));
								break;
							case 'notes':
								$json['notesId'] = $this->model_ticketsystem_tickets->addTicketNotes($data);
								break;
							case (preg_match('/delete-note-/',$data['column']) ? true : false):
								$id = preg_replace('/[A-Z\-]/i', '', $data['column']);
								$this->model_ticketsystem_tickets->deleteTicketNotesById($id);
								break;
							case (preg_match('/note-/',$data['column']) ? true : false):
								$data['ticket_id'] = $data['id'];
								$data['id'] = preg_replace('/[A-Z\-]/i', '', $data['column']);
								$this->model_ticketsystem_tickets->updateTicketNotes($data);
								break;
							case 'tag-add':
								$data['value'] = $this->request->post['value'];
								$this->load->model('ticketsystem/tags');
								$json['tagsId'] = $tagId = $this->model_ticketsystem_tags->addTag(array('name' => $data['value']));
								$this->model_ticketsystem_tickets->addTicketTags(array('ticket_id' => $data['id'], 'tag_id' => $tagId));
								break;
							case 'tags':
								$data['value'] = $this->request->post['value'];
								$this->model_ticketsystem_tickets->deleteTicketTag($data['id']);
								foreach ($data['value'] as $tagId) {
									$this->model_ticketsystem_tickets->addTicketTags(array('ticket_id' => $data['id'], 'tag_id' => $tagId));
								}
								break;
							default:
								break;
						}
					}
				}

				$json['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_tickets'));
			}
		}else
			$json['warning'] = $this->language->get('error_permission');

		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Function merge multiple tickets and closed all merged tickets, not Primary one
	 * @return json object
	 */
	public function merge() {
		/**
		 * Set TsBase value so that it will not show "Error Permission" but set $accessPermissionErrorStatus so that we can check that agent has permission or not
		 * @var boolean
		 */
		$this->isAjax = true;

		/**
		 * This TsBase function used to check that "Current Agent" has permission to this particular function
		 */
		$this->checkEventAccessPermission(array(
											'key'=>self::CONTROLLER_NAME, 
											'event'=> self::CONTROLLER_NAME.'.'.__FUNCTION__
											)
										);

		$this->language->load('ticketsystem/all');

		$json = array();

		if(!$this->accessPermissionErrorStatus){

			$this->load->model('ticketsystem/tickets');

			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

				$primaryId = $this->request->post['primary'];

				if(is_array($this->request->post['id'])){
					$tickets = $this->request->post['id'];
				}else{
					$tickets = array($this->request->post['id']);
				}

				//remove primary value from array
				$tickets = array_diff($tickets, array($primaryId));

				$message = $this->language->get('text_ticket_merge_primary_message');
				foreach ($tickets as $ticket) {
					$data = array(
							't.id' => $ticket,
						);
					$ticketData = $this->model_ticketsystem_tickets->getTicket($data);
					if($ticketData){
						$data = array(
								'ticket_id' => $ticket,
								'type' => 'create',
							);
						$threadData = $this->model_ticketsystem_tickets->getTicketThreads($data);
						if($threadData){
							$message .= '<h4>##'.$ticketData['id'].'</h4>';
							$message .= '<h4>'.$this->language->get('text_subject').'- '.$ticketData['subject'].'</h4>';
							$message .= $this->language->get('text_message').'- '.$threadData[0]['message'];
							$message .= '</br></br>';
						}
					}
				}

				//add Private Note to this ticket
				$data = array(
							'id' => $primaryId,
							'message' => $message,
							'agent_id' => $this->agent['id'],
							'sender_type' => 'agent',
							'messagetype' => 'note',
						);

				$this->model_ticketsystem_tickets->addTicketThread($data);

				foreach ($tickets as $ticket) {
					//close ticket
					$data = array(
								 'id' => $ticket,
								 'column' => 'status',
								 'value' => (is_array($this->config->get('ts_ticket_status')) ? $this->config->get('ts_ticket_status')['closed'] : 0),
								 'agent_id' => $this->agent['id'],
								);
					$this->model_ticketsystem_tickets->editTicket(array($data));
					//transfer ticket threads to primary ticket
					$data = array(
								'ticket_id' => $primaryId,
								'old_ticket_id' => $ticket,
							);
					$this->model_ticketsystem_tickets->updateTicketThreadByTicket($data);
					//add Private Note to this ticket
					$data = array(
								'id' => $ticket,
								'message' => sprintf($this->language->get('text_ticket_merge_message'),$primaryId),
								'agent_id' => $this->agent['id'],
								'sender_type' => 'agent',
								'messagetype' => 'note',
							);
					$this->model_ticketsystem_tickets->addTicketThread($data);
					//clear Ticket Viewers
					$this->model_ticketsystem_tickets->clearExtraViewers(array('ticket_id' => $ticket));
					//clear Ticket Drafts
					$this->model_ticketsystem_tickets->deleteTicketDrafts(array('ticket_id' => $ticket));
				}

				$json['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_tickets'));
			}
		}else
			$json['warning'] = $this->language->get('error_permission');

		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Function will update Custom Fields for Ticket
	 * File type is not supported in this version :(
	 * @return $this->edit or Redirect
	 */
	public function customField() {
		/**
		 * This TsBase function used to check that "Current Agent" has permission to this particular function
		 */
		$this->checkEventAccessPermission(array(
											'key'=>self::CONTROLLER_NAME, 
											'event'=> self::CONTROLLER_NAME.'.update'
											)
										);

		$this->language->load('ticketsystem/all');

		if(!$this->accessPermissionErrorStatus){
			$this->load->model('ticketsystem/tickets');

			if (($this->request->server['REQUEST_METHOD'] == 'POST') AND $this->customFieldValidation()) {
				if(isset($this->request->post['custom_field'])){
					$data = array(
								 'id' => $this->request->get['id'],
								 'column' => 'custom_field',
								 'value' => serialize($this->request->post['custom_field']),
								 'agent_id' => $this->agent['id'],
								);

					$this->model_ticketsystem_tickets->editTicket(array($data));
				}
				$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_tickets'));
				$url = $this->TsLoader->TsHelper->getUrlData('default');

				$this->response->redirect($this->url->link('ticketsystem/tickets/edit', 'token=' . $this->session->data['token'].'&id='.$this->request->get['id'].$url , 'SSL'));
			}
		}else
			$this->error['warning'] = $this->language->get('error_permission');

		$this->edit();
	}

	/**
	 * Apply Selected Response to Ticket
	 * @call - ticketsystem/ticketactions/applyActionsOnRequest
	 * @return json object
	 */
	public function applyResponses() {
		$this->language->load('ticketsystem/actions');

		$json = array();

		if (isset($this->request->get['action_id']) AND isset($this->request->get['id'])) {
			$this->load->model('ticketsystem/responses');
			if($actions = $this->model_ticketsystem_responses->getResponse($this->request->get['action_id'])){
				$data = array(
							'ticket_id' => $this->request->get['id'],
							'agent_id' => $this->agent['id'],
							'actions' => unserialize($actions['actions']),
							);
				$json['message'] = $this->language->get('text_success').$this->load->controller('ticketsystem/ticketactions/applyActionsOnRequest', $data);
				$json['success'] = $this->language->get('text_success_apply_action');
			}else{
				$json['error'] = $this->language->get('error_something_went_wrong');
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Function controls Thread related Actions like Delete / Split & Ticket Draft and Ticket viewers
	 * @return json object
	 */
	public function threadActions() {
		/**
		 * Set TsBase value so that it will not show "Error Permission" but set $accessPermissionErrorStatus so that we can check that agent has permission or not
		 * @var boolean
		 */
		$this->isAjax = true;

		if(isset($this->request->post['event'])){
			if(!in_array($this->request->post['event'], array('reply[message]', 'forward[message]', 'internal[message]', 'getViewers')))
				$this->checkEventAccessPermission(array(
													'key'=>self::CONTROLLER_NAME, 
													'event'=> self::CONTROLLER_NAME.'.'.$this->request->post['event']
													)
												);

		}else{
			$this->accessPermissionErrorStatus = true;
		}

		$this->language->load('ticketsystem/all');

		$json = array();

		if(!$this->accessPermissionErrorStatus){
			$this->load->model('ticketsystem/tickets');

			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				$event = $this->request->post['event'];
				switch ($event) {
					case 'deletethread':
						$data = array(
							 'id' => $this->request->post['id'],
							 'thread_id' => str_replace(array('deletethread-','split-'), '', $this->request->post['threadId']),
							 'agent_id' => $this->agent['id'],
							);
						$this->model_ticketsystem_tickets->deleteTicketThread($data['thread_id']);
						$json['success'] = $this->language->get('text_success_delete_thread');
						break;
					case 'split':
						$data = array(
							 'id' => $this->request->post['id'],
							 'thread_id' => str_replace(array('deletethread-','split-'), '', $this->request->post['threadId']),
							 'agent_id' => $this->agent['id'],
							);
						$ticket_id = $this->model_ticketsystem_tickets->copyTicket($data['id']);
						$ticket_url = $this->url->link('ticketsystem/tickets/edit', 'token=' . $this->session->data['token'] .'&id='.$ticket_id, 'SSL');
						$this->model_ticketsystem_tickets->updateTicketThread(array('ticket_id' => $ticket_id, 'thread_id' => $data['thread_id']));
						$this->model_ticketsystem_tickets->addAgentTicket(array('ticket_id' => $ticket_id, 'agent_id' => $this->agent['id']));
						$json['success'] = sprintf($this->language->get('text_success_split_ticket'), $ticket_url, $ticket_id);
						break;
					case preg_match('/\[message\]/', $event) ? true: false:
						$data = array(
							 'ticket_id' => $this->request->post['id'],
							 'agent_id' => $this->agent['id'],
							 'type' => preg_replace('/\[message\]/', '', $event),
							 'message' => $this->request->post['html'],
							);
						$this->model_ticketsystem_tickets->updateTicketDrafts($data);
						$json['success'] = $this->language->get('text_success_draft_ticket');
						break;
					case 'getViewers':
						$data = array(
							 'ticket_id' => $this->request->post['id'],
							 'agent_id' => $this->agent['id'],
							 'type' => $event,
							);
						$this->model_ticketsystem_tickets->clearExtraViewers($data);
						$viewers = $this->model_ticketsystem_tickets->getTicketViewAgent($data['ticket_id']);
						$json['viewers'] = $agents = [];
						if($viewers){
							$count = 0;
							foreach ($viewers as $viewer) {
								if($viewer['agent_id'] != $this->agent['id']){
									$agent = $this->model_ticketsystem_agents->getAgent(array('a.id' => $viewer['agent_id']));
									if($agent)
										$agents[] = $agent['name_alias'] ? $agent['name_alias'] : $agent['username'];
									$count++;
								}
							}
							$json['viewers'] = array(
													'viewers' => implode(', ', $agents),
													'no' => $count,
													);
						}
						break;
					default:
						break;
				}
			}
		}else
			$json['warning'] = $this->language->get('error_permission');

		$this->response->setOutput(json_encode($json));
	}

	protected function validateForm() {
		$error = false;

		if (!$this->user->hasPermission('modify', 'ticketsystem/tickets')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if(isset($this->request->post['reply']['submit'])){
			$files = array();
			foreach ($this->request->files['reply']['name']['file'] as $key => $file) {
				if(!$file)
					continue;

				$entry = array(
								'name' => $file,
								'tmp_name' => $this->request->files['reply']['tmp_name']['file'][$key],
								'type' => $this->request->files['reply']['type']['file'][$key],
								'error' => $this->request->files['reply']['error']['file'][$key],
								'size' => $this->request->files['reply']['size']['file'][$key],
								);
				if($error = $this->TsLoader->TsFileUpload->fileUploadValidate($entry))
					break;
				$files[] = $entry;
			}
			if(!$error)
				$this->request->files = $files;
			else
				$this->error['reply_file'] = $error;

		}elseif(isset($this->request->post['forward']['submit'])){
			if(!isset($this->request->post['forward']['receivers']['to']) || !$this->request->post['forward']['receivers']['to'])
				$this->error['forward_to'] = $this->language->get('error_forward_to');

			$files = array();
			foreach ($this->request->files['forward']['name']['file'] as $key => $file) {
				if(!$file)
					continue;

				$entry = array(
								'name' => $file,
								'tmp_name' => $this->request->files['forward']['tmp_name']['file'][$key],
								'type' => $this->request->files['forward']['type']['file'][$key],
								'error' => $this->request->files['forward']['error']['file'][$key],
								'size' => $this->request->files['forward']['size']['file'][$key],
								);
				if($error = $this->TsLoader->TsFileUpload->fileUploadValidate($entry))
					break;
				$files[] = $entry;
			}
			if(!$error)
				$this->request->files = $files;
			else
				$this->error['forward_file'] = $error;

		}elseif(isset($this->request->post['internal']['submit'])){
				$this->request->files = array();
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	/**
	 * Validate Custom Field Files
	 * @return boolean
	 */
	public function customFieldValidation() {
		$error = false;
		if($this->request->files)
			foreach ($this->request->files as $key => $files) {
				if($files['name'] AND $files['tmp_name']){
					if($error = $this->TsLoader->TsFileUpload->fileUploadValidate($files)){
						$this->error[$key] = $error;
					}
				}
			}

		if($error)
			return false;
		return true;
	}

	public function delete() {
		/**
		 * This TsBase function used to check that "Current Agent" has permission to this particular function
		 */
		$this->checkEventAccessPermission(array(
											'key'=>self::CONTROLLER_NAME, 
											'event'=> self::CONTROLLER_NAME.'.'.__FUNCTION__
											)
										);

		$this->load->language('ticketsystem/all');

		$this->document->setTitle($this->language->get('heading_tickets'));

		$this->load->model('ticketsystem/tickets');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_tickets->deleteTicket($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_tickets'));
			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/tickets', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/tickets')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}