<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to show Ticket Priority, which will be used in Module
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemPriority extends TsBase {

	const CONTROLLER_NAME = 'priority';

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
						'defaultSort' => 'tpd.name',
						'allowedFields' => $this->allowedFields,
					)
			);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/priority');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'priority',
						'tplFile' => 'priority_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_priority');
		$data['text_list'] = $this->language->get('text_list_priority');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_priority') => 'priority',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/priority/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/priority/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['priority'] = array();

		$priority_total = $this->model_ticketsystem_priority->getTotalPriorities();

		$results = $this->model_ticketsystem_priority->getPriorities();

		foreach ($results as $result) {
			$data['priority'][] = array(
				'id' 		 	=> $result['id'],
				'status'       	=> $result['status'],
				'name'       	=> $result['name'],
				'description'   => $result['description'],
				'date_updated'  => $this->convertDateFormat($result['date_updated']),
				'edit'       => $this->url->link('ticketsystem/priority/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
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

		$data['sort_name'] = $this->url->link('ticketsystem/priority', 'token=' . $this->session->data['token'] . '&sort=tpd.name' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('ticketsystem/priority', 'token=' . $this->session->data['token'] . '&sort=tp.status' . $url, 'SSL');
		$data['sort_date_updated'] = $this->url->link('ticketsystem/priority', 'token=' . $this->session->data['token'] . '&sort=tp.date_updated' . $url, 'SSL');

		$data['resultTotal'] = $priority_total;
		$data['addPagination'] = true;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function add() {
		$this->document->setTitle($this->language->get('heading_priority'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'priority/add',
						'tplFile' => 'priority_form',
					)
			);

		$this->load->model('ticketsystem/priority');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_ticketsystem_priority->addPriority($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_priority'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/priority', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/priority', 'token=' . $this->session->data['token'] . $url, 'SSL'));

		$this->document->setTitle($this->language->get('heading_priority'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'priority/edit',
						'tplFile' => 'priority_form',
					)
			);

		$this->load->model('ticketsystem/priority');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['id'] = $this->request->get['id'];
			$this->model_ticketsystem_priority->editPriority($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_priority'));

			$this->response->redirect($this->url->link('ticketsystem/priority', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('ticketsystem/priority');

		$this->document->setTitle($this->language->get('heading_priority'));

		$this->load->model('ticketsystem/priority');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_priority->deletePriority($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_priority'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/priority', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getForm() {
		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_'.self::CONTROLLER_NAME);

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add_priority') : $this->language->get('text_edit_priority');
		
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

		if (isset($this->error['priority'])) {
			$data['error_priority'] = $this->error['priority'];
		} else {
			$data['error_priority'] = array();
		}

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_priority') => 'priority',
					$data['text_form'] => !isset($this->request->get['id']) ? 'priority/add' : 'priority/edit&id='.$this->request->get['id'] ,
					)
			);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('ticketsystem/priority/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('ticketsystem/priority/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('ticketsystem/priority', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$priority_info = $this->model_ticketsystem_priority->getPriority($this->request->get['id']);
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['token'] = $this->session->data['token'];

		$columnData = array(
						'id',
						'status',
						'priority',
						);

		foreach ($columnData as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (!empty($priority_info)) 
				$data[$key] = $priority_info[$key];
			else
				$data[$key] = '';
		}
		
		$data['priority'] = $data['priority'] ? $data['priority'] : array();

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/priority')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach($this->request->post['priority'] as $language => $value)
		if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 64)) {
			$this->error['name'][$language] = $this->language->get('error_name');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/priority')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}