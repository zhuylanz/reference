<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to manage HelpDesk Customers, their listing etc
 *
 * Basic structure is same as Activity controller, more used function are explained here
 *
 * Helpdesk Customers are different from Opencart Customers, Ts Customers can or can't be OC customers.
 * In our mod we used our entry for customers, no big data just email, name and oc-customer-id if any
 */
class ControllerTicketSystemCustomers extends TsBase {

	const CONTROLLER_NAME = 'customers';

	public $allowedFields = array(
							'filter_c__name',
							'filter_c__email',
							'filter_o__name',
							'filter_oc__firstname',
							'filter_c__date_updated',
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
						'defaultSort' => 'c.email',
						'allowedFields' => $this->allowedFields,
					)
			);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/customers');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'customers',
						'tplFile' => 'customers_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_customers');
		$data['text_list'] = $this->language->get('text_list_customers');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_customers') => 'customers',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/customers/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/customers/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['customers'] = array();

		$customers_total = $this->model_ticketsystem_customers->getTotalCustomers();

		$results = $this->model_ticketsystem_customers->getCustomers();

		foreach ($results as $result) {
			$data['customers'][] = array(
				'id' 		 	=> $result['id'],
				'name'       	=> $result['name'],
				'email'       	=> $result['email'],
				'organization'  => '<a href="'.$this->url->link('ticketsystem/organizations', 'token=' . $this->session->data['token'] . '&filter_name=' . $result['organization'] , 'SSL').'">'.$result['organization'].'</a>',
				'oc_customer'  => '<a href="'.$this->url->link('sale/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'] , 'SSL').'">'.$result['oc_customer'].'</a>',
				'date_updated'  => $this->convertDateFormat($result['date_updated']),
				'edit'       => $this->url->link('ticketsystem/customers/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
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

		$data['sort_name'] = $this->url->link('ticketsystem/customers', 'token=' . $this->session->data['token'] . '&sort=c.name' . $url, 'SSL');
		$data['sort_email'] = $this->url->link('ticketsystem/customers', 'token=' . $this->session->data['token'] . '&sort=c.email' . $url, 'SSL');
		$data['sort_customers'] = $this->url->link('ticketsystem/customers', 'token=' . $this->session->data['token'] . '&sort=c.customer_id' . $url, 'SSL');
		$data['sort_organization'] = $this->url->link('ticketsystem/customers', 'token=' . $this->session->data['token'] . '&sort=toc.organization_id' . $url, 'SSL');
		$data['sort_date_updated'] = $this->url->link('ticketsystem/customers', 'token=' . $this->session->data['token'] . '&sort=c.date_updated' . $url, 'SSL');

		$data['resultTotal'] = $customers_total;
		$data['addPagination'] = true;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function add() {

		$this->document->setTitle($this->language->get('heading_customers'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'customers/add',
						'tplFile' => 'customers_form',
					)
			);

		$this->load->model('ticketsystem/customers');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_ticketsystem_customers->addCustomer($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_customers'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/customers', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/customers', 'token=' . $this->session->data['token'] . $url, 'SSL'));

		$this->document->setTitle($this->language->get('heading_customers'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'customers/edit',
						'tplFile' => 'customers_form',
					)
			);

		$this->load->model('ticketsystem/customers');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['id'] = $this->request->get['id'];
			$this->model_ticketsystem_customers->editCustomer($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_customers'));

			$this->response->redirect($this->url->link('ticketsystem/customers', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('ticketsystem/customers');

		$this->document->setTitle($this->language->get('heading_customers'));

		$this->load->model('ticketsystem/customers');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_customers->deleteCustomer($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_customers'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/customers', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}
	
	protected function getForm() {

		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_'.self::CONTROLLER_NAME);

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add_customers') : $this->language->get('text_edit_customers');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = array();
		}

		if (isset($this->error['customer_exists'])) {
			$data['error_customer_exists'] = $this->error['customer_exists'];
		} else {
			$data['error_customer_exists'] = array();
		}

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_customers') => 'customers',
					$data['text_form'] => !isset($this->request->get['id']) ? 'customers/add' : 'customers/edit&id='.$this->request->get['id'] ,
					)
			);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('ticketsystem/customers/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('ticketsystem/customers/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('ticketsystem/customers', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$customer_info = $this->model_ticketsystem_customers->getCustomer($this->request->get['id']);
		}

		$data['token'] = $this->session->data['token'];

		$columnData = array(
						'id',
						'name',
						'email',
						'customer_id',
						'organization_id',
						'oc_customer',
						'organization',
						);

		foreach ($columnData as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (!empty($customer_info)) 
				$data[$key] = $customer_info[$key];
			else
				$data[$key] = '';
		}

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/customers')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}else{
			$customers = $this->model_ticketsystem_customers->getCustomers(false, array('c.email' => $this->request->post['email']));
			foreach($customers as $customer){
				if(isset($this->request->get['id'])){
					if($customer['id']!=$this->request->get['id']){
						$this->error['email'] = $this->language->get('error_email_exists');
						break;
					}
				}else{
					$this->error['email'] = $this->language->get('error_email_exists');
					break;
				}
			}
		}

		if(isset($this->request->post['customer_id']) AND $this->request->post['customer_id']!=0){
			$customers = $this->model_ticketsystem_customers->getCustomers(false, array('c.customer_id' => $this->request->post['customer_id']));
			foreach($customers as $customer){
				if(isset($this->request->get['id'])){
					if($customer['id']!=$this->request->get['id']){
						$this->error['customer_exists'] = $this->language->get('error_customer_exists');
						break;
					}
				}else{
					$this->error['customer_exists'] = $this->language->get('error_customer_exists');
					break;
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/customers')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	/**
	 * This function is for Hepldesk Customers
	 * @return json object
	 */
	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_email'])) {
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_email'])) {
				$filter_email = $this->request->get['filter_email'];
			} else {
				$filter_email = '';
			}

			if (isset($this->request->get['filter_status'])) {
				$filter_status = $this->request->get['filter_status'];
			} else {
				$filter_status = 1;
			}

			if (isset($this->request->get['filter_organization'])) {
				$filter_organization = $this->request->get['filter_organization'];
			} else {
				$filter_organization = '';
			}

			$this->load->model('ticketsystem/customers');

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_email' => $filter_email,
				'filter_status' => $filter_status,
				'start'        => 0,
				'limit'        => 5
			);

			$filter_data = array(
				'filter_c__name'  => $filter_name,
			);
			
			$results = $this->model_ticketsystem_customers->getCustomers(false, $filter_data);

			foreach ($results as $result) {
				if($filter_organization){
					if($result['organization_id'] AND $result['organization_id']!=$filter_organization)
						continue;
				}

				if($result['customer_id'])
					$json[] = array(
						'id'       			=> $result['id'],
						'name'         		=> $result['name'],
						'email'             => $result['email'],
					);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * This function is for Opencart Customers
	 * @return json object
	 */
	public function autocompleteOcCustomers() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_email'])) {
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_email'])) {
				$filter_email = $this->request->get['filter_email'];
			} else {
				$filter_email = '';
			}

			if (isset($this->request->get['filter_status'])) {
				$filter_status = $this->request->get['filter_status'];
			} else {
				$filter_status = 1;
			}

			if (isset($this->request->get['filter_organization'])) {
				$filter_organization = $this->request->get['filter_organization'];
			} else {
				$filter_organization = '';
			}

			$this->load->model('ticketsystem/occustomers');
			$this->load->model('ticketsystem/customers');

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_email' => $filter_email,
				'filter_status' => $filter_status,
				'start'        => 0,
				'limit'        => 5
			);

			$results = $this->model_ticketsystem_occustomers->getCustomers($filter_data);

			foreach ($results as $result) {

				if($filter_organization){
					$customer = $this->model_ticketsystem_occustomers->getCustomer($result['customer_id']);
					if($customer AND $customer['organization_id'] AND $customer['organization_id']!=$filter_organization)
						continue;
				}

				if($result['customer_id'])
					$json[] = array(
						'customer_id'       => $result['customer_id'],
						'firstname'         => $result['firstname'],
						'lastname'          => $result['lastname'],
						'email'             => $result['email'],
					);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}