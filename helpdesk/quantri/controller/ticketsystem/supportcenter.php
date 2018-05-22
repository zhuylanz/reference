<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to show SupportCenter Category which will be used in Module
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemSupportCenter extends TsBase {

	const CONTROLLER_NAME = 'supportcenter';

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
						'defaultSort' => 'tcd.name',
						'allowedFields' => $this->allowedFields,
					)
			);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/supportcenter');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'supportcenter',
						'tplFile' => 'category_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_supportcenter');
		$data['text_list'] = $this->language->get('text_list_supportcenter');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_supportcenter') => 'supportcenter',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/supportcenter/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/supportcenter/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['category'] = array();

		$supportcenter_total = $this->model_ticketsystem_supportcenter->getTotalCategories();

		$results = $this->model_ticketsystem_supportcenter->getCategories();

		foreach ($results as $result) {
			$data['category'][] = array(
				'id' 		 	=> $result['id'],
				'status'       	=> $result['status'],
				'name'       	=> $result['name'],
				'description'   => $result['description'],
				'date_updated'  => $this->convertDateFormat($result['date_updated']),
				'edit'       => $this->url->link('ticketsystem/supportcenter/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
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

		$data['sort_name'] = $this->url->link('ticketsystem/supportcenter', 'token=' . $this->session->data['token'] . '&sort=tcd.name' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('ticketsystem/supportcenter', 'token=' . $this->session->data['token'] . '&sort=tc.status' . $url, 'SSL');
		$data['sort_date_updated'] = $this->url->link('ticketsystem/supportcenter', 'token=' . $this->session->data['token'] . '&sort=tc.date_updated' . $url, 'SSL');

		$data['resultTotal'] = $supportcenter_total;
		$data['addPagination'] = true;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function add() {
		$this->document->setTitle($this->language->get('heading_supportcenter'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'supportcenter/add',
						'tplFile' => 'category_form',
					)
			);

		$this->load->model('ticketsystem/supportcenter');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_ticketsystem_supportcenter->addCategory($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_supportcenter'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/supportcenter', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/supportcenter', 'token=' . $this->session->data['token'] . $url, 'SSL'));

		$this->document->setTitle($this->language->get('heading_supportcenter'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'supportcenter/edit',
						'tplFile' => 'category_form',
					)
			);

		$this->load->model('ticketsystem/supportcenter');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['id'] = $this->request->get['id'];
			$this->model_ticketsystem_supportcenter->editCategory($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_supportcenter'));

			$this->response->redirect($this->url->link('ticketsystem/supportcenter', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('ticketsystem/supportcenter');

		$this->document->setTitle($this->language->get('heading_supportcenter'));

		$this->load->model('ticketsystem/supportcenter');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_supportcenter->deleteCategory($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_supportcenter'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/supportcenter', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getForm() {
		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_'.self::CONTROLLER_NAME);

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add_supportcenter') : $this->language->get('text_edit_supportcenter');
		
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

		if (isset($this->error['supportcenter'])) {
			$data['error_supportcenter'] = $this->error['supportcenter'];
		} else {
			$data['error_supportcenter'] = array();
		}

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_supportcenter') => 'supportcenter',
					$data['text_form'] => !isset($this->request->get['id']) ? 'supportcenter/add' : 'supportcenter/edit&id='.$this->request->get['id'] ,
					)
			);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('ticketsystem/supportcenter/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('ticketsystem/supportcenter/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('ticketsystem/supportcenter', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$category_info = $this->model_ticketsystem_supportcenter->getCategory($this->request->get['id']);
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('catalog/information');
		$data['ocInformation'] = $this->model_catalog_information->getInformations();

		$data['token'] = $this->session->data['token'];

		$columnData = array(
						'id',
						'status',
						);

		foreach ($columnData as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (!empty($category_info)) 
				$data[$key] = $category_info[$key];
			else
				$data[$key] = '';
		}

		$columnDataArray = array(
						'category',
						'informations',
						);

		foreach ($columnDataArray as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (!empty($category_info)) 
				$data[$key] = $category_info[$key];
			else
				$data[$key] = array();
		}
		
		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/supportcenter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach($this->request->post['category'] as $language => $value)
		if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 64)) {
			$this->error['name'][$language] = $this->language->get('error_name');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/supportcenter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}