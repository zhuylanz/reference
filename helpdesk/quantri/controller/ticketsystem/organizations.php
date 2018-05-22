<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to show Customer Organization, which will be used in Module
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemOrganizations extends TsBase {

	const CONTROLLER_NAME = 'organizations';

	public $allowedFields = array(
							'filter_name',
							'filter_date_updated',
							'filter_domain',
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
						'defaultSort' => 'o.name',
						'allowedFields' => $this->allowedFields,
					)
			);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/organizations');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'organizations',
						'tplFile' => 'organizations_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_organizations');
		$data['text_list'] = $this->language->get('text_list_organizations');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_organizations') => 'organizations',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/organizations/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/organizations/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['organizations'] = array();

		$organizations_total = $this->model_ticketsystem_organizations->getTotalOrganizations();

		$results = $this->model_ticketsystem_organizations->getOrganizations();

		foreach ($results as $result) {
			$data['organizations'][] = array(
				'id' 		 	=> $result['id'],
				'domain'       	=> $result['domain'],
				'name'       	=> $result['name'],
				'customers'     => '<a href="'.$this->url->link('ticketsystem/customers', 'token=' . $this->session->data['token'] . '&filter_o__name=' . $result['name'], 'SSL').'">'.count($this->model_ticketsystem_organizations->getOrganizationCustomers($result['id'])).'</a>',
				'description'   => $result['description'],
				'date_updated'  => $this->convertDateFormat($result['date_updated']),
				'edit'       => $this->url->link('ticketsystem/organizations/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
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

		$data['sort_name'] = $this->url->link('ticketsystem/organizations', 'token=' . $this->session->data['token'] . '&sort=o.name' . $url, 'SSL');
		$data['sort_domain'] = $this->url->link('ticketsystem/organizations', 'token=' . $this->session->data['token'] . '&sort=o.domain' . $url, 'SSL');
		$data['sort_date_updated'] = $this->url->link('ticketsystem/organizations', 'token=' . $this->session->data['token'] . '&sort=o.date_updated' . $url, 'SSL');

		$data['resultTotal'] = $organizations_total;
		$data['addPagination'] = true;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function add() {
		$this->document->setTitle($this->language->get('heading_organizations'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'organizations/add',
						'tplFile' => 'organizations_form',
					)
			);

		$this->load->model('ticketsystem/organizations');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_ticketsystem_organizations->addOrganization($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_organizations'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/organizations', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/organizations', 'token=' . $this->session->data['token'] . $url, 'SSL'));

		$this->document->setTitle($this->language->get('heading_organizations'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'organizations/edit',
						'tplFile' => 'organizations_form',
					)
			);

		$this->load->model('ticketsystem/organizations');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['id'] = $this->request->get['id'];
			$this->model_ticketsystem_organizations->editOrganization($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_organizations'));

			$this->response->redirect($this->url->link('ticketsystem/organizations', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('ticketsystem/organizations');

		$this->document->setTitle($this->language->get('heading_organizations'));

		$this->load->model('ticketsystem/organizations');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_organizations->deleteOrganization($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_organizations'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/organizations', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getForm() {
		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_'.self::CONTROLLER_NAME);

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add_organizations') : $this->language->get('text_edit_organizations');
		
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
					$this->language->get('heading_organizations') => 'organizations',
					$data['text_form'] => !isset($this->request->get['id']) ? 'organizations/add' : 'organizations/edit&id='.$this->request->get['id'] ,
					)
			);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('ticketsystem/organizations/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('ticketsystem/organizations/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('ticketsystem/organizations', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$organization_info = $this->model_ticketsystem_organizations->getOrganization($this->request->get['id']);
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['organizations'] = $this->TsLoader->TsHelper->tsorganizations;

		$data['token'] = $this->session->data['token'];

		$columnData = array(
						'id',
						'name',
						'description',
						'note',
						'domain',
						'customer_role',
						'customers',
						'groups',
						);

		foreach ($columnData as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (!empty($organization_info)) 
				$data[$key] = $organization_info[$key];
			else
				$data[$key] = '';
		}
		
		$data['customers'] = $data['customers'] ? $data['customers'] : array();
		$data['groups'] = $data['groups'] ? $data['groups'] : array();

		if(!isset($data['groups'][0]['id'])){
			$this->load->model('ticketsystem/groups');
			foreach ($data['groups'] as $key => $group) {
				unset($data['groups'][$key]);
				if($groupInfo = $this->model_ticketsystem_groups->getGroup($group)){
					$data['groups'][$key]['id'] = $groupInfo['id'];
					$data['groups'][$key]['name'] = $groupInfo['group'][$this->config->get('config_language_id')]['name'];
				}
			}
		}

		if(!isset($data['customers'][0]['id'])){
			$this->load->model('ticketsystem/customers');
			foreach ($data['customers'] as $key => $customer) {
				unset($data['customers'][$key]);
				if($customerInfo = $this->model_ticketsystem_customers->getCustomer($customer)){
					$data['customers'][$key]['id'] = $customerInfo['id'];
					$data['customers'][$key]['name'] = $customerInfo['name'];
				}
			}
		}

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/organizations')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/organizations')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		$this->load->model('ticketsystem/organizations');

		$organizations_info = $this->model_ticketsystem_organizations->getOrganizations();

		if ($organizations_info) {
			foreach ($organizations_info as $organization) {
				$json[] = array(
					'id'        		=> $organization['id'],
					'name'          	=> $organization['name'],
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}