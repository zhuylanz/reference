<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Tickets Class is used to work on Tickets
 */
class ControllerTicketSystemTickets extends Controller {

	const CONTROLLER_NAME = 'tickets';
	const SET_LIMIT = 15;
	const DATE_FORMAT = 'l jS \of F Y h:i:s A';

	private $error = array();
	private $customerDetails;

	public $allowedFields = array(
							'filter_t__id',
							'filter_t__customer_id',
							'filter_t__status',
							'filter_t__priority',
							'filter_t__date_added',
							);

	public $imageMimeArray = array(
							'image/jpeg',
							'image/jpg',
							'image/png',
							'image/gif',
							);

	public function __construct($registry){
		parent::__construct($registry);

		if(!$this->config->get('ts_status'))
			$this->response->redirect($this->url->link('account/account','','SSL'));
		
		$this->customerValidate();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'modFolder' => 'ticketsystem',
						'filtetLimit' => 10 ? 10 : self::SET_LIMIT,
						'defaultSort' => 't.id',
						'allowedFields' => $this->allowedFields,
						'addTsHeader' => (is_array($this->config->get('ts_header')) AND in_array(self::CONTROLLER_NAME, $this->config->get('ts_header'))) ? true : false,
					)
			);

		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/tickets'));
		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/status'));
		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/priority'));
		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/customers'));
		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/organizations'));
		$this->load->model('tool/image');

		$this->setTsCustomer();

		$this->load->language('ticketsystem/tickets');
		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		$this->document->addStyle('catalog/view/javascript/ticketsystem/css/ticketsystem/ticketsystem.css');
	}

	protected function setTsCustomer(){
		if($this->customer->getEmail()){
			$this->customerDetails = $this->model_ticketsystem_customers->getCustomerByOCId($this->customer->getId());
		}elseif(isset($this->session->data['ts_customer'])){
			$this->customerDetails = $this->model_ticketsystem_customers->getCustomer($this->session->data['ts_customer']['id']);
		}
	}

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

	protected function convertDateFormat($date = false){
		$offset = 0;
		// if($this->agent['timezone'])
			// $offset = timezone_offset_get( timezone_open( $this->agent['timezone']), new \DateTime() );
		//update date format based on user
		return date(($this->config->get('ts_date_format') ? $this->config->get('ts_date_format') : self::DATE_FORMAT), (strtotime($date ? $date : date('Y-m-d')) + $offset) );
	}

	public function index() {
		$this->getList();
	}

	protected function getList() {
		$data = $this->load->language('ticketsystem/tickets');

		$config = array(
						'ts_customer_delete_ticket',
					);

		foreach($config as $value){
			$data[$value] = $this->config->get($value);
		}

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'tickets',
						'tplFile' => 'tickets_list',
					)
			);

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getCatalogBreadcrumbs(
				array(
					$this->language->get('heading_title') => 'tickets',
					)
			);

		$url = '';

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$data['add'] = $this->url->link('ticketsystem/tickets/add', $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/tickets/delete', $url, 'SSL');

		$data['ticketsResult'] = '';

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

		$data['statuss'] = $this->model_ticketsystem_status->getStatuss(false);

		$data['priorities'] = $this->model_ticketsystem_priority->getPriorities(false);

		$order = 'desc';

		$data['sort_ticket_id'] = '&sort=t.id' . '&order='.$order;
		$data['sort_ticket_subject'] = '&sort=t.subject' . '&order='.$order;
		$data['sort_type'] = '&sort=t.type' . '&order='.$order;
		$data['sort_priority'] = '&sort=t.priority' . '&order='.$order;
		$data['sort_status'] = '&sort=t.status' . '&order='.$order;
		$data['sort_date_added'] = '&sort=t.date_added' . '&order='.$order;

		$data['categories'] = array();

		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/supportcenter', 'location' => 'admin'));
		$results = $this->model_ticketsystem_supportcenter->getCategories(false);

		foreach($results as $category){
			$data['categories'][] = array(
									'id' => $category['id'],
									'name' => $category['name'],
									);
		}

		$this->response->setOutput($this->TsLoader->TsHelper->loadCatalogHtml($data));
	}

	public function tickets() {
		$data = $this->load->language('ticketsystem/tickets');

		$url = '';

		$overrideRequestData = [];

		$this->session->data['TsFrontLocalStorage'] = $this->request->get;

		foreach ($this->request->get as $getKey => $get) {
			$overrideRequestData[$getKey] = preg_match('/,/', $get) ? explode(',', $get) : $get;
		}

		$this->TsLoader->TsHelper->overrideRequestData($overrideRequestData, true);
		$this->TsLoader->TsHelper->overrideRequestData($this->getCustomerTicketAccessData(), true);

		$total = $this->model_ticketsystem_tickets->getTotalTickets();

		$results = $this->model_ticketsystem_tickets->getTickets();

		$data['tickets'] = array();
		
		foreach ($results as $result) {
			$data['tickets'][] = array(
				'id' 		 	=> $result['id'],
				'subject'       => $result['subject'],
				'type'   		=> $result['typeName'],
				'statusId'   	=> $result['status'],
				'status'   		=> $result['statusName'],
				'priority' 		=> $result['priorityName'],
				'group'   		=> $result['groupName'],
				'agent'   		=> $result['agentName'],
				'customer'   	=> stripslashes($result['customerName']),
				'customerEmail' => $result['customerEmail'],
				'date_added'   	=> $this->convertDateFormat($result['date_added']),
				'edit'       	=> $this->url->link('ticketsystem/tickets/edit', 'id=' . $result['id'] . $url, 'SSL')
			);
		}

		if(is_array($this->config->get('ts_ticket_status'))){
			$data['ts_update_status_to_delete'] = $this->config->get('ts_ticket_status')['closed'];
			$data['ts_update_status_to_spam'] = $this->config->get('ts_ticket_status')['spam'];
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
		$pagination->limit = 10;
		$pagination->url = '{page}';

		$data['pagination'] = $pagination->render();
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($total - 10)) ? $total : ((($page - 1) * 10) + 10), $total, ceil($total / 10));

		if(version_compare(VERSION, '2.2.0.0', '<')) {
			if(isset($this->request->server['HTTP_X_REQUESTED_WITH']) AND $this->request->server['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
				$this->response->setOutput($this->load->view('default/template/ticketsystem/tickets.tpl', $data));
			else
				return $this->load->view('default/template/ticketsystem/tickets.tpl', $data);

		}else{
			
			if(isset($this->request->server['HTTP_X_REQUESTED_WITH']) AND $this->request->server['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
				$this->response->setOutput($this->load->view('ticketsystem/tickets', $data));
			else
				return $this->load->view('ticketsystem/tickets', $data);
		}

		
	}

	public function edit() {
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/tickets', $url , 'SSL'));

		$this->request->post['selected'] = array($this->request->get['id']);

		if(!$this->validateTicketAccess()){
			$this->session->data['error_warning'] = $this->language->get('error_access_permission');
			$this->response->redirect($this->url->link('ticketsystem/tickets', '' , 'SSL'));
		}

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'tickets/edit',
						'tplFile' => 'tickets_form',
					)
			);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			if(isset($this->request->post['reply']['submit'])){
				isset($this->request->post['reply']['receivers']['cc']) ? ($this->request->post['reply']['receivers']['cc'] ? ($this->request->post['reply']['receivers']['cc'] = explode(',', $this->request->post['reply']['receivers']['cc'])) : false) : false;
				$receivers = isset($this->request->post['reply']['receivers']) ? $this->request->post['reply']['receivers'] : array();
				$data = array(
							'id' => $this->request->get['id'],
							'message' => $this->request->post['reply']['message'],
							'agent_id' => $this->customerDetails['id'],
							'sender_type' => 'customer',
							'messagetype' => 'reply',
							'receivers' => $receivers,
							);	

				$this->model_ticketsystem_tickets->addTicketThread($data);			
				$this->session->data['success'] = $this->language->get('text_success_reply');
			}
			$this->response->redirect($this->url->link('ticketsystem/tickets/edit', $url.'&id='.$this->request->get['id'], 'SSL'));
		}

		$this->getView();
	}

	protected function getView() {
		$data = $this->language->load('ticketsystem/tickets');

		$config = array(
						'ts_editor',
						'ts_customer_delete_ticket',
						'ts_customer_update_status',
						'ts_customer_delete_ticketthread',
						'ts_customer_add_cc',
					);

		foreach($config as $value){
			$data[$value] = $this->config->get($value);
		}

		$data['text_fileupload_info'] = sprintf($data['text_fileupload_info'], $this->config->get('ts_fileupload_no'), $this->config->get('ts_fileupload_size'));

		if(is_array($this->config->get('ts_ticket_status'))){
			$data['ts_update_status_to_delete'] = $this->config->get('ts_ticket_status')['closed'];
			$data['ts_update_status_to_spam'] = $this->config->get('ts_ticket_status')['spam'];
		}

		if($data['ts_editor']){
			$this->document->addScript('admin/view/javascript/summernote/summernote.js');
			$this->document->addStyle('admin/view/javascript/summernote/summernote.css');
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = $this->language->get('text_view_ticket').' #'.$this->request->get['id'];
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if(is_array($this->error))
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

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getCatalogBreadcrumbs(
				array(
					$this->language->get('heading_title') => 'tickets',
					$data['text_form'] => 'tickets/edit&id='.$this->request->get['id'] ,
					)
			);

		$data['ticketId'] = $this->request->get['id'];

		$data['action'] = $this->url->link('ticketsystem/tickets/edit&id='.$this->request->get['id'], $url, 'SSL');

		$data['cancel'] = $this->url->link('ticketsystem/tickets', $url, 'SSL');

		$filterArray = array_merge($this->getCustomerTicketAccessData(), array('t.id' => $this->request->get['id']));

		$data['ticket_info'] = $this->model_ticketsystem_tickets->getTicket($filterArray);
		
		$data['ticket_info']['date_added'] = $this->convertDateFormat($data['ticket_info']['date_added']);

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
				$attachment['path'] = HTTP_SERVER.'image/'.$path;
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

		$postData = array(
						'ticket_info',
						'reply',
					);

		if(isset($this->request->post['ticket_info']))
			$data['ticket_info'] = array_merge($this->request->post['ticket_info'], $data['ticket_info']);

		if(isset($this->request->post['reply']))
			$data['reply'] = $this->request->post['reply'];
		else
			$data['reply'] = array();

		//for prev and next buttons
		$data['prevId'] = $data['nextId'] = 0;

		if(isset($this->session->data['TsLocalStorage'])){
			$overrideRequestData = [];

			foreach ($this->session->data['TsLocalStorage'] as $getKey => $get) {
				$overrideRequestData[$getKey] = preg_match('/,/', $get) ? explode(',', $get) : $get;
			}

			$this->TsLoader->TsHelper->overrideRequestData($overrideRequestData, true);
			$this->TsLoader->TsHelper->overrideRequestData($this->getCustomerTicketAccessData(), true);

			$results = $this->model_ticketsystem_tickets->getTickets();

			foreach ($results as $key => $result) {
				if($result['id']==$this->request->get['id']){
					$data['prevId'] = isset($results[$key-1]) ? $this->url->link('ticketsystem/tickets/edit', 'id=' . $results[$key-1]['id'] . $url, 'SSL') : 0;
					$data['nextId'] = isset($results[$key+1]) ? $this->url->link('ticketsystem/tickets/edit', 'id=' . $results[$key+1]['id'] . $url, 'SSL') : 0;
				}
			}
		}

		$data['categories'] = array();

		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/supportcenter', 'location' => 'admin'));
		$results = $this->model_ticketsystem_supportcenter->getCategories(false);

		foreach($results as $category){
			$data['categories'][] = array(
									'id' => $category['id'],
									'name' => $category['name'],
									);
		}

		$this->response->setOutput($this->TsLoader->TsHelper->loadCatalogHtml($data));
	}

	public function getTicketThreads($data = array()){
		$data = $this->load->language('ticketsystem/tickets');

		$config = array(
						'ts_customer_delete_ticketthread',
					);

		foreach($config as $value){
			$data[$value] = $this->config->get($value);
		}

		$neagtiveThreads = 0;

		$filter_array = array(
							'ticket_id' => $this->request->get['id'],
							'type' => 'reply'
						);

		$total_threads = $this->model_ticketsystem_tickets->getTotalTicketThreads($filter_array);

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

				$attachment['path'] = HTTP_SERVER.'image/'.$path;
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
			$ticket_thread['agentImage'] = $ticket_thread['agentImage'] ? $this->model_tool_image->resize($ticket_thread['agentImage'],50,50) : false;
			$data['ticket_threads'][$ticket_thread['id']] = $ticket_thread;
		}

		sort($data['ticket_threads']);

		$data['ticket_pagination_info_disabled'] = ($neagtiveThreads == $total_threads ? true : false);

		$data['ticket_pagination_info'] = $neagtiveThreads.'|'.$total_threads.'|'.(isset($filter_array['ticket_before_id']) ? $filter_array['ticket_before_id'] : ($data['ticket_threads'] ? $data['ticket_threads'][count($data['ticket_threads'])-1]['id'] : 0));
		if(version_compare(VERSION, '2.2.0.0', '<')) {
			if(isset($this->request->server['HTTP_X_REQUESTED_WITH']) AND $this->request->server['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
				$this->response->setOutput($this->load->view('default/template/ticketsystem/ticketThreads.tpl', $data));
			else
				return $this->load->view('default/template/ticketsystem/ticketThreads.tpl', $data);
		}else{
			if(isset($this->request->server['HTTP_X_REQUESTED_WITH']) AND $this->request->server['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
				$this->response->setOutput($this->load->view('ticketsystem/ticketThreads', $data));
			else
				return $this->load->view('ticketsystem/ticketThreads', $data);
		}
		
	}

	public function threadActions() {
		$json = array();

		if(!$this->accessPermissionErrorStatus){
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				$event = $this->request->post['event'];
				switch ($event) {
					case 'deletethread':
						if(!$this->config->get('ts_customer_delete_ticketthread'))
							break;
						$data = array(
							 'id' => $this->request->post['id'],
							 'thread_id' => str_replace(array('deletethread-','split-'), '', $this->request->post['threadId']),
							 'agent_id' => $this->customerDetails['id'],
							);
						$this->model_ticketsystem_tickets->deleteTicketThread($data['thread_id']);
						$json['success'] = $this->language->get('text_success_delete_thread');
						break;
					case 'split':
						$data = array(
							 'id' => $this->request->post['id'],
							 'thread_id' => str_replace(array('deletethread-','split-'), '', $this->request->post['threadId']),
							 'agent_id' => $this->customerDetails['id'],
							);
						$ticket_id = $this->model_ticketsystem_tickets->copyTicket($data['id']);
						$ticket_url = $this->url->link('ticketsystem/tickets/edit', 'id='.$ticket_id, 'SSL');
						$this->model_ticketsystem_tickets->updateTicketThread(array('ticket_id' => $ticket_id, 'thread_id' => $data['thread_id']));
						$json['success'] = sprintf($this->language->get('text_success_split_ticket'), $ticket_url, $ticket_id);
						break;
					case 'status':
						if(!$this->config->get('ts_customer_update_status'))
							break;
						$data = array(
									 'id' => $this->request->post['id'],
									 'column' => 'status',
									 'value' => $this->request->post['threadId'],
									 'agent_id' => $this->customerDetails['id'],
									);
						$this->model_ticketsystem_tickets->editTicket(array($data));
						$json['success'] = sprintf($this->language->get('text_success_closed_ticket'), $this->request->post['id']);
						break;
					default:
						break;
				}
			}
		}else
			$json['warning'] = $this->language->get('error_access_permission');

		$this->response->setOutput(json_encode($json));
	}

	protected function validateForm() {
		$error = false;
		
		if(isset($this->request->post['reply']['submit'])){
			$files = array();
			if(count($this->request->files['reply']['name']['file']) > $this->config->get('ts_fileupload_no')){
				$error = $this->language->get('error_too_much_images');
			}else{
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
			}
			if(!$error)
				$this->request->files = $files;
			else
				$this->error['reply_file'] = $error;
			if(!trim($this->request->post['reply']['message']) || !trim(strip_tags(html_entity_decode($this->request->post['reply']['message']))))
				$this->error['reply_message'] = $this->language->get('error_message');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	public function delete() {
		$this->load->language('ticketsystem/tickets');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->post['selected']) && $this->config->get('ts_customer_delete_ticket') && $this->validateTicketAccess()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_tickets->deleteTicket($id);
			}

			$this->session->data['success'] = $this->language->get('text_success');
			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/tickets', $url, 'SSL'));
		}else{
			$this->error['warning'] = $this->language->get('error_warning');
		}

		$this->getList();
	}

	protected function validateTicketAccess() {
		$status = true;
		foreach ($this->request->post['selected'] as $id) {
			if(!$this->model_ticketsystem_tickets->getTicket(array_merge($this->getCustomerTicketAccessData(),array('t.id' => $id)))){
				$status = false;				
			}
		}

		if(!$status)
			$this->error['warning'] = $this->language->get('error_warning');

		return $status;
	}

	protected function getCustomerTicketAccessData(){
		$customers = array();

		if($customerDetails = $this->customerDetails){
			if($customerDetails['organization_id']){
				if($organization = $this->model_ticketsystem_organizations->getOrganization($customerDetails['organization_id'])){
					if($organization['customer_role']){
						$organizationCustomers = $this->model_ticketsystem_organizations->getOrganizationCustomers($customerDetails['organization_id']);
						foreach ($organizationCustomers as $organizationCustomer) {
							$customers[] = $organizationCustomer['customer_id'];
						}
					}
				}
			}

			if(!$customers)
				$customers[] = $customerDetails['id'];
		}else{
			$customers = array(0);
		}

		return array('filter_t__customer_id' => $customers);
	}
}