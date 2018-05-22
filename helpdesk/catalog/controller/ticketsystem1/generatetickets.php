<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Generate Tickets Class is used to genearte tickets on Helpdesk mod
 */
class ControllerTicketSystemGenerateTickets extends Controller {

	const CONTROLLER_NAME = 'generatetickets';
	const SET_LIMIT = 15;

	private $error;
	private $isLogin;
	protected $allowedFields = array(
							'filter_name',
							'filter_date_updated',
							);

	protected function customerValidate(){
		//customer must be login
		if($this->config->get('ts_login')){
			if(!$this->customer->getId()){
				if(!isset($this->session->data['ts_customer'])){
					$this->session->data['redirect'] = $this->url->link('ticketsystem/generatetickets','','SSL');
					$this->response->redirect($this->url->link('ticketsystem/login','','SSL'));
				}
			}
		}
	}

	public function index() {
		if(!$this->config->get('ts_status'))
			$this->response->redirect($this->url->link('account/account','','SSL'));

		$this->customerValidate();

		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		$this->document->addStyle('catalog/view/javascript/ticketsystem/css/ticketsystem/ticketsystem.css');

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controller' => 'ticketsystem',
						'controllerFile' => 'generateticket',
						'tplFile' => 'generateticket',
						'allowedFields' => $this->allowedFields,
						'addTsHeader' => (is_array($this->config->get('ts_header')) AND in_array(self::CONTROLLER_NAME, $this->config->get('ts_header'))) ? true : false,
						
						//to get categories						
						'defaultSort' => 'tcd.name',
					)
			);

		$data = $this->load->language('ticketsystem/generatetickets');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getCatalogBreadcrumbs(
				array(
					$this->language->get('heading_title') => 'generatetickets',
					)
			);

		if(isset($this->error['error_warning'])){
			$data['error_warning'] = $this->error['error_warning'];
		}else{
			$data['error_warning'] = '';
		}

		if(isset($this->session->data['success'])){
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}else{
			$data['success'] = '';
		}

		$data['text_login_info'] = sprintf($this->language->get('text_login_info'), $this->url->link('account/login', '', 'SSL'));

		$data['action'] = $this->url->link('ticketsystem/generatetickets/add', '', 'SSL');

		$data['isLogin'] = ($this->customer->getId() || isset($this->session->data['ts_customer']));

		$data['categories'] = array();

		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/supportcenter', 'location' => 'admin'));
		// $this->load->model('ticketsystem/supportcenter');
		$results = $this->model_ticketsystem_supportcenter->getCategories();

		foreach($results as $category){
			$data['categories'][] = array(
									'id' => $category['id'],
									'name' => $category['name'],
									);
		}

		$config = array(
						'ts_fields',
						'ts_required_fields',
						'ts_login',
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
						'subject',
						'message',
						'group',
						'agent',
						'status',
						'priority',
						'tickettype',
						'custom_field',
						'email', //if not login and not required login
						'name', //if not login and not required login

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

		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/types'));

		if(is_array($data['ts_fields'])){
			if(in_array('group', $data['ts_fields'])){
				$data['groups'] = array();
				$this->TsLoader->TsService->model(array('model' => 'ticketsystem/groups'));
				// $this->load->model('ticketsystem/groups');
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
				// $this->load->model('ticketsystem/types');
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

		$data['custom_fields'] = array();

		//fetch custom fields
		$this->load->model('account/custom_field');
		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);
		
		// $this->load->model('ticketsystem/types');

		foreach ($custom_fields as $custom_field) { 
			if ($custom_field['location'] == 'tickets') {
				$custom_field['tstype'] = $this->model_ticketsystem_types->getCustomFieldType($custom_field['custom_field_id']);
				$data['custom_fields'][] = $custom_field;
			}
		}

		$this->response->setOutput($this->TsLoader->TsHelper->loadCatalogHtml($data));
	}

	public function add(){
		if(!$this->config->get('ts_status'))
			$this->response->redirect($this->url->link('account/account','','SSL'));

		$this->customerValidate();

		$this->load->language('ticketsystem/generatetickets');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->TsLoader->TsService->model(array('model' => 'ticketsystem/tickets'));
			$this->TsLoader->TsService->model(array('model' => 'ticketsystem/customers'));

			if($this->customer->getId()){
				$customer = $this->model_ticketsystem_customers->getCustomerByOCId($this->customer->getId());
				if(!$customer){
					//create one in TS
					$customerData = array(
										'customer_id' => $this->customer->getId(),
										'email' => $this->customer->getEmail(),
										'name' => $this->customer->getFirstName(),
									);
					$customerId = $this->model_ticketsystem_customers->addCustomer($customerData);
				}else
					$customerId = $customer['id'];
			}elseif(isset($this->session->data['ts_customer'])){
				$customerId = $this->session->data['ts_customer']['id'];
			}else{
				$customers = $this->model_ticketsystem_customers->getCustomers(false, array('c.email' => $this->request->post['email']));
				if($customers)
					$customerId = $customers[0]['id'];
				else{
					//check if this email exists in OC
					$this->load->model('account/customer');
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
			}

			if(!isset($this->session->data['ts_customer']) AND !$this->customer->getId()){
				$this->session->data['ts_customer'] = array(
														'id' => $customerId,
														'email' => $this->request->post['email'],
														);
			}

			$defaultDataToCreateTicket = array(
										'provider' => 'web', 
										'customer_id' => $customerId, 
										'agent_id' => $customerId, 
										'sender_type' => 'customer',
										'messagetype' => 'create',
									 );

			$postData = array_merge($this->request->post, $defaultDataToCreateTicket);
			
			$this->model_ticketsystem_tickets->addTicket($postData);
			$this->load->language('ticketsystem/generatetickets');
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('ticketsystem/generatetickets', '' , 'SSL'));
		}

		$this->index();
	}

	protected function validate() {
		if(is_array($this->config->get('ts_required_fields'))){
			foreach($this->config->get('ts_required_fields') as $field){
				if(isset($this->request->post[$field]) AND !$this->request->post[$field])
					$this->error[$field] = $this->language->get('error_'.$field);
			}
		}

		if(!$this->customer->getId() AND !isset($this->session->data['ts_customer'])){
			if(!isset($this->request->post['email']) OR !$this->request->post['email'] OR !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email']))
				$this->error['email'] = $this->language->get('error_email');
			if(!isset($this->request->post['name']) OR !$this->request->post['name'] OR (utf8_strlen(trim($this->request->post['name'])) >100))
				$this->error['name'] = $this->language->get('error_name');

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

		// Custom field validation
		$this->load->model('account/custom_field');
		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

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

	public function customfield() {
		if(!$this->config->get('ts_status'))
			$this->response->redirect($this->url->link('account/account','','SSL'));
		
		$json = array();

		$this->load->model('account/custom_field');

		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			$json[] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required'        => $custom_field['required']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}