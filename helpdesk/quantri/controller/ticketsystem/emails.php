<?php
use Controller\TicketSystem\TsBase;

/**
 * name suggested it's fake and blank error handler
 * @return false
 */
function helpDeskErrorHandlerFake() {}

/**
 * It's used to stop oc default handler so that we can display error to Admin instead of exit with error, coming from Mail server
 */
set_error_handler("helpDeskErrorHandlerFake");

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to show Email list and add, edit etc
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemEmails extends TsBase {

	const CONTROLLER_NAME = 'emails';

	public $allowedFields = array(
							'filter_name',
							'filter_email',
							'filter_date_updated',
							'filter_status',
							);


	public $mailProtocols = array(
							'IMAP',
							// 'IMAP/SSL',
							'POP',
							// 'POP/SSL',
							);

	/**
	 * Not Real Constructor, just dummy
	 * @return array loaded data from base controller
	 */
	public function _construct(){
		return $this->data;
	}

	public function __construct($registry){
		$this->registry = $registry;
		$this->extendedClassCall = true;
		parent::__construct($registry);

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'modFolder' => 'ticketsystem',
						'defaultSort' => 'id',
						'allowedFields' => $this->allowedFields,
					)
			);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/emails');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'emails',
						'tplFile' => 'emails_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_emails');
		$data['text_list'] = $this->language->get('text_list_emails');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_emails') => 'emails',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/emails/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/emails/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['emails'] = array();

		$emails_total = $this->model_ticketsystem_emails->getTotalEmails();

		$results = $this->model_ticketsystem_emails->getEmails();

		foreach ($results as $result) {
			$data['emails'][] = array(
				'id' 		 	=> $result['id'],
				'name'       	=> $result['name'],
				'description'   => $result['description'],
				'email'       	=> $result['email'],
				'status'       	=> $result['status'],
				'date_updated'  => $this->convertDateFormat($result['date_updated']),
				'edit'       => $this->url->link('ticketsystem/emails/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
			);
		}

		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = $this->TsLoader->TsHelper->getUrlData('sort');

		$data['sort_name'] = $this->url->link('ticketsystem/emails', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_email'] = $this->url->link('ticketsystem/emails', 'token=' . $this->session->data['token'] . '&sort=email' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('ticketsystem/emails', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$data['sort_date_updated'] = $this->url->link('ticketsystem/emails', 'token=' . $this->session->data['token'] . '&sort=date_updated' . $url, 'SSL');

		$data['resultTotal'] = $emails_total;
		$data['addPagination'] = true;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function add() {

		$this->document->setTitle($this->language->get('heading_emails'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'emails/add',
						'tplFile' => 'emails_form',
					)
			);

		$this->load->model('ticketsystem/emails');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_ticketsystem_emails->addEmail($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_emails'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/emails', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/emails', 'token=' . $this->session->data['token'] . $url, 'SSL'));

		$this->document->setTitle($this->language->get('heading_emails'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'emails/edit',
						'tplFile' => 'emails_form',
					)
			);

		$this->load->model('ticketsystem/emails');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['id'] = $this->request->get['id'];
			$this->model_ticketsystem_emails->editEmail($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_emails'));

			$this->response->redirect($this->url->link('ticketsystem/emails', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('ticketsystem/all');

		$this->document->setTitle($this->language->get('heading_emails'));

		$this->load->model('ticketsystem/emails');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_emails->deleteEmail($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_emails'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/emails', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getForm() {

		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_'.self::CONTROLLER_NAME);

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add_emails') : $this->language->get('text_edit_emails');
		
		$errorArray = array(
							'warning',
							'name',
							'username',
							'password',
							'hostname',
							'port',
							'fetch_time',
							'email_per_fetch',
							'email_action',
							'email_action_folder',
							);

		foreach ($errorArray as $error) {
			if (isset($this->error[$error]))
				$data['error_'.$error] = $this->error[$error];
			else
				$data['error_'.$error] = '';
		}

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_emails') => 'emails',
					$data['text_form'] => !isset($this->request->get['id']) ? 'emails/add' : 'emails/edit&id='.$this->request->get['id'] ,
					)
			);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('ticketsystem/emails/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('ticketsystem/emails/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('ticketsystem/emails', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$email_info = $this->model_ticketsystem_emails->getEmail($this->request->get['id']);
		}

		$data['token'] = $this->session->data['token'];

		$columnData = array(
						'id',
						'name',
						'description',
						'email',
						'group',
						'priority',
						'type',
						'username',
						'password',
						'hostname',
						'port',
						'mailbox',
						'protocol',
						'fetch_time',
						'email_per_fetch',
						'actions',
						'status',
						);

		foreach ($columnData as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (!empty($email_info)) 
				$data[$key] = $email_info[$key];
			else
				$data[$key] = '';
		}
		
		$data['actions'] = is_array($data['actions']) ? $data['actions'] : unserialize($data['actions']);

		$data['mailProtocols'] = $this->mailProtocols;

		$this->load->model('ticketsystem/types');
		$data['types'] = $this->model_ticketsystem_types->getTypes(false);

		$this->load->model('ticketsystem/groups');
		$data['groups'] = $this->model_ticketsystem_groups->getGroups(false);

		$this->load->model('ticketsystem/priority');
		$data['priorities'] = $this->model_ticketsystem_priority->getPriorities(false);

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/emails')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ((utf8_strlen($this->request->post['username']) < 1) || (utf8_strlen($this->request->post['username']) > 64)) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if ((utf8_strlen($this->request->post['password']) < 1) || (utf8_strlen($this->request->post['password']) > 64)) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if ((utf8_strlen($this->request->post['hostname']) < 1) || (utf8_strlen($this->request->post['hostname']) > 100)) {
			$this->error['hostname'] = $this->language->get('error_hostname');
		}

		if (!(int)$this->request->post['port']) {
			$this->error['port'] = $this->language->get('error_port');
		}

		if (!(int)$this->request->post['fetch_time']) {
			$this->error['fetch_time'] = $this->language->get('error_fetch_time');
		}

		if (!(int)$this->request->post['email_per_fetch']) {
			$this->error['email_per_fetch'] = $this->language->get('error_email_per_fetch');
		}

		if (!isset($this->request->post['actions']['action'])) {
			$this->error['email_action'] = $this->language->get('error_email_action');
		}

		if (isset($this->request->post['actions']['action']) AND $this->request->post['actions']['action']=='movetofolder' AND !$this->request->post['actions']['folder']) {
			$this->error['email_action_folder'] = $this->language->get('error_email_action_folder');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/emails')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	/**
	 * Function used to validate current data connection on ajax request
	 * @return json object
	 */
	public function emailValidate() {
		$json = array();

		$this->language->load('ticketsystem/emails');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {

			$data = $this->request->post;

			$host = $data['host'];
			$port = $data['port'];
			$username = $data['username'];
			$password = $data['password'];
			$service = $data['service'];
			$mailbox = $data['mailbox'];

			/**
			 * Fetch Server class used to Mail Pining
			 */
			$server = new \Fetch\Server($host, $port, $service);

			$server->setAuthentication($username, $password);

			try{
				$server->setMailBox($mailbox);
			}catch(\Exception $e){
				$this->error['error'] = $e->getMessage();
			}

			if(isset($this->error['error']))
				$json['error'] = $this->error['error'];
			else
				$json['success'] = $this->language->get('success_connection_established');
		}

		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Function used to fetch mails from calling Mail id using TsEmail class
	 * @return json object
	 */
	public function emailFetch() {
		$json = array();

		$this->language->load('ticketsystem/emails');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' AND isset($this->request->post['id'])) {
			try{
				$json = $this->TsLoader->TsFetchEmail->emailFetch($this->request->post['id']);
			}catch(\Exception $e){
				$json['error'] = $e->getMessage();
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}