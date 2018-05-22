<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to show Email Templates, which will be used in Actions
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemEmailTemplates extends TsBase {

	const CONTROLLER_NAME = 'emailtemplates';

	public $allowedFields = array(
							'filter_name',
							'filter_date_updated',
							'filter_status',
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

		$this->load->model('ticketsystem/emailtemplates');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'emailtemplates',
						'tplFile' => 'emailtemplates_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_emailtemplates');
		$data['text_list'] = $this->language->get('text_list_emailtemplates');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_emailtemplates') => 'emailtemplates',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/emailtemplates/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/emailtemplates/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['emailtemplates'] = array();

		$emailtemplates_total = $this->model_ticketsystem_emailtemplates->getTotalEmailTemplates();

		$results = $this->model_ticketsystem_emailtemplates->getEmailTemplates();

		foreach ($results as $result) {
			$data['emailtemplates'][] = array(
				'id' 		 	=> $result['id'],
				'name'       	=> $result['name'],
				'message'   	=> strip_tags(html_entity_decode($result['message'])),
				'status'       	=> $result['status'],
				'date_updated'  => $this->convertDateFormat($result['date_updated']),
				'edit'       => $this->url->link('ticketsystem/emailtemplates/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
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

		$data['sort_name'] = $this->url->link('ticketsystem/emailtemplates', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('ticketsystem/emailtemplates', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$data['sort_date_updated'] = $this->url->link('ticketsystem/emailtemplates', 'token=' . $this->session->data['token'] . '&sort=date_updated' . $url, 'SSL');

		$data['resultTotal'] = $emailtemplates_total;
		$data['addPagination'] = true;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function add() {
		$this->document->setTitle($this->language->get('heading_emailtemplates'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'emailtemplates/add',
						'tplFile' => 'emailtemplates_form',
					)
			);

		$this->load->model('ticketsystem/emailtemplates');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_ticketsystem_emailtemplates->addEmailTemplate($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_emailtemplates'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/emailtemplates', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/emailtemplates', 'token=' . $this->session->data['token'] . $url, 'SSL'));

		$this->document->setTitle($this->language->get('heading_emailtemplates'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'emailtemplates/edit',
						'tplFile' => 'emailtemplates_form',
					)
			);

		$this->load->model('ticketsystem/emailtemplates');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['id'] = $this->request->get['id'];
			$this->model_ticketsystem_emailtemplates->editEmailTemplate($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_emailtemplates'));

			$this->response->redirect($this->url->link('ticketsystem/emailtemplates', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('ticketsystem/all');

		$this->document->setTitle($this->language->get('heading_emailtemplates'));

		$this->load->model('ticketsystem/emailtemplates');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_emailtemplates->deleteEmailTemplate($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_emailtemplates'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/emailtemplates', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getForm() {
		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_'.self::CONTROLLER_NAME);

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add_emailtemplates') : $this->language->get('text_edit_emailtemplates');
		
		$errorArray = array(
							'warning',
							'name',
							'message',
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
					$this->language->get('heading_emailtemplates') => 'emailtemplates',
					$data['text_form'] => !isset($this->request->get['id']) ? 'emailtemplates/add' : 'emailtemplates/edit&id='.$this->request->get['id'] ,
					)
			);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('ticketsystem/emailtemplates/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('ticketsystem/emailtemplates/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('ticketsystem/emailtemplates', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$email_info = $this->model_ticketsystem_emailtemplates->getEmailTemplate($this->request->get['id']);
		}

		$data['token'] = $this->session->data['token'];

		$columnData = array(
						'id',
						'name',
						'message',
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
		
		$data['ticketPlaceHolder'] = $this->TsLoader->TsTicket->ticketPlaceHolder;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/emailtemplates')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ((utf8_strlen(strip_tags(html_entity_decode($this->request->post['message']))) < 1)) {
			$this->error['message'] = $this->language->get('error_message');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/emailtemplates')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}