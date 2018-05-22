<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to show Ticket Responses which will be used in Module
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemRoles extends TsBase {

	const CONTROLLER_NAME = 'roles';

	public $allowedFields = array(
							'filter_name',
							'filter_date_updated',
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
						'defaultSort' => 'r.name',
						'allowedFields' => $this->allowedFields,
					)
			);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/roles');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'roles',
						'tplFile' => 'roles_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_roles');
		$data['text_list'] = $this->language->get('text_list_roles');

		//extract shoting data
		// extract($this->TsLoader->TsHelper->getSortData());
		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_roles') => 'roles',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/roles/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['copy'] = $this->url->link('ticketsystem/roles/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/roles/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['roles'] = array();

		$roles_total = $this->model_ticketsystem_roles->getTotalRoles();

		$results = $this->model_ticketsystem_roles->getRoles();

		foreach ($results as $result) {
			$data['roles'][] = array(
				'id' 		 	=> $result['id'],
				'name'       	=> $result['name'],
				'description'   => $result['description'],
				'date_updated'    => $this->convertDateFormat($result['date_updated']),
				'edit'       => $this->url->link('ticketsystem/roles/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
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

		$data['sort_name'] = $this->url->link('ticketsystem/roles', 'token=' . $this->session->data['token'] . '&sort=r.name' . $url, 'SSL');
		$data['sort_date_updated'] = $this->url->link('ticketsystem/roles', 'token=' . $this->session->data['token'] . '&sort=r.date_updated' . $url, 'SSL');

		$data['resultTotal'] = $roles_total;
		$data['addPagination'] = true;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function add() {
		$this->document->setTitle($this->language->get('heading_roles'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'roles/add',
						'tplFile' => 'roles_form',
					)
			);

		$this->load->model('ticketsystem/roles');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_ticketsystem_roles->addRole($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_roles'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/roles', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/roles', 'token=' . $this->session->data['token'] . $url, 'SSL'));

		$this->document->setTitle($this->language->get('heading_roles'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'roles/edit',
						'tplFile' => 'roles_form',
					)
			);

		$this->load->model('ticketsystem/roles');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['id'] = $this->request->get['id'];
			$this->model_ticketsystem_roles->editRole($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_roles'));

			$this->response->redirect($this->url->link('ticketsystem/roles', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('ticketsystem/roles');

		$this->document->setTitle($this->language->get('heading_roles'));

		$this->load->model('ticketsystem/roles');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_roles->deleteRole($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_roles'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/roles', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getForm() {
		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_'.self::CONTROLLER_NAME);

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add_roles') : $this->language->get('text_edit_roles');
		
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

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = array();
		}

		if (isset($this->error['roles'])) {
			$data['error_roles'] = $this->error['roles'];
		} else {
			$data['error_roles'] = array();
		}

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_roles') => 'roles',
					$data['text_form'] => !isset($this->request->get['id']) ? 'roles/add' : 'roles/edit&id='.$this->request->get['id'] ,
					)
			);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('ticketsystem/roles/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('ticketsystem/roles/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('ticketsystem/roles', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$role_info = $this->model_ticketsystem_roles->getRole($this->request->get['id']);
		}

		$data['roles'] = $this->TsLoader->TsHelper->tsRoles;

		$data['token'] = $this->session->data['token'];

		$columnData = array(
						'name',
						'description',
						'role',
						);

		foreach ($columnData as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (!empty($role_info)) 
				$data[$key] = $role_info[$key];
			else
				$data[$key] = '';
		}

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/roles')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ((utf8_strlen($this->request->post['description']) < 3) || (utf8_strlen($this->request->post['description']) > 255)) {
			$this->error['description'] = $this->language->get('error_description');
		}

		if (!isset($this->request->post['roles'])) {
			$this->error['roles'] = $this->language->get('error_roles');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/roles')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}