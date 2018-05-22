<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to show Ticket SLA which will be used in Module
 * These SLA rules will work at time of ticket creation, reply , resolved etc
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemSla extends TsBase {

	const CONTROLLER_NAME = 'sla';

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
						'defaultSort' => 's.name',
						'allowedFields' => $this->allowedFields,
						// 'addTsColumnLeft' => true,
					)
			);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/sla');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'sla',
						'tplFile' => 'sla_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_sla');
		$data['text_list'] = $this->language->get('text_list_sla');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_sla') => 'sla',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/sla/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/sla/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['sla'] = array();

		$sla_total = $this->model_ticketsystem_sla->getTotalSLAs();

		$results = $this->model_ticketsystem_sla->getSLAs();

		foreach ($results as $result) {
			$data['sla'][] = array(
				'id' 		 	=> $result['id'],
				'status'       	=> $result['status'],
				'name'       	=> $result['name'],
				'description'   => nl2br($result['description']),
				'date_updated'  => $this->convertDateFormat($result['date_updated']),
				'edit'       => $this->url->link('ticketsystem/sla/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
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

		$data['sort_name'] = $this->url->link('ticketsystem/sla', 'token=' . $this->session->data['token'] . '&sort=s.name' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('ticketsystem/sla', 'token=' . $this->session->data['token'] . '&sort=s.status' . $url, 'SSL');
		$data['sort_date_updated'] = $this->url->link('ticketsystem/sla', 'token=' . $this->session->data['token'] . '&sort=s.date_updated' . $url, 'SSL');

		$data['resultTotal'] = $sla_total;
		$data['addPagination'] = true;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function add() {
		$this->document->setTitle($this->language->get('heading_sla'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'sla/add',
						'tplFile' => 'sla_form',
					)
			);

		$this->load->model('ticketsystem/sla');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_ticketsystem_sla->addSLA($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_sla'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/sla', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/sla', 'token=' . $this->session->data['token'] . $url, 'SSL'));

		$this->document->setTitle($this->language->get('heading_sla'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'sla/edit',
						'tplFile' => 'sla_form',
					)
			);

		$this->load->model('ticketsystem/sla');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['id'] = $this->request->get['id'];
			$this->model_ticketsystem_sla->editSLA($this->request->post);
			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_sla'));

			$this->response->redirect($this->url->link('ticketsystem/sla', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('ticketsystem/sla');

		$this->document->setTitle($this->language->get('heading_sla'));

		$this->load->model('ticketsystem/sla');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_sla->deleteSLA($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_sla'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/sla', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getForm() {
		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_'.self::CONTROLLER_NAME);

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add_sla') : $this->language->get('text_edit_sla');
		
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

		if (isset($this->error['error_priority'])) {
			$data['error_priority'] = $this->error['error_priority'];
		} else {
			$data['error_priority'] = array();
		}

		if (isset($this->error['error_priority_blank'])) {
			$data['error_priority_blank'] = $this->error['error_priority_blank'];
		} else {
			$data['error_priority_blank'] = array();
		}

		if (isset($this->error['error_conditions'])) {
			$data['error_conditions'] = $this->error['error_conditions'];
		} else {
			$data['error_conditions'] = array();
		}

		if (isset($this->error['error_conditions_all'])) {
			$data['error_conditions_all'] = $this->error['error_conditions_all'];
		} else {
			$data['error_conditions_all'] = array();
		}

		if (isset($this->error['error_conditions_one'])) {
			$data['error_conditions_one'] = $this->error['error_conditions_one'];
		} else {
			$data['error_conditions_one'] = array();
		}

		if (isset($this->error['error_resolve_violation'])) {
			$data['error_resolve_violation'] = $this->error['error_resolve_violation'];
		} else {
			$data['error_resolve_violation'] = array();
		}

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_sla') => 'sla',
					$data['text_form'] => !isset($this->request->get['id']) ? 'sla/add' : 'sla/edit&id='.$this->request->get['id'] ,
					)
			);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('ticketsystem/sla/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('ticketsystem/sla/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('ticketsystem/sla', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$sla_info = $this->model_ticketsystem_sla->getSLA($this->request->get['id']);
		}

		$data['token'] = $this->session->data['token'];

		$this->load->model('ticketsystem/priority');
		$data['tsPriority'] = $this->model_ticketsystem_priority->getPriorities(false);

		$this->load->model('ticketsystem/agents');
		$data['tsAgents'] = $this->model_ticketsystem_agents->getAgents(false);

		$data['tstime'] = $this->TsLoader->TsTicket->ticketSLATime;

		$columnData = array(
						'id',
						'name',
						'description',
						'status',
						'sort_order',
						);

		foreach ($columnData as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (!empty($sla_info)) 
				$data[$key] = $sla_info[$key];
			else
				$data[$key] = '';
		}

		$columnDataArray = array(
						'conditions_all',
						'conditions_one',
						'priority',
						);

		foreach ($columnDataArray as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (!empty($sla_info)) 
				$data[$key] = $sla_info[$key];
			else
				$data[$key] = array();
		}

		/**
		 * This will call ticketconditions class, which will manage conditions completely.
		 * It will return Html based on passed $data and $error 
		 * Id will show it's for One or All
		 */
		$data['ticketConditionsAll'] = $this->load->controller('ticketsystem/ticketconditions', array('conditions' => $data['conditions_all'], 'error' => $data['error_conditions_all'], 'id' => 'all'));
		$data['ticketConditionsOne'] = $this->load->controller('ticketsystem/ticketconditions', array('conditions' => $data['conditions_one'], 'error' => $data['error_conditions_one'], 'id' => 'one'));

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/sla')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if(isset($this->request->post['priority']) AND is_array($this->request->post['priority'])){
			$entry = false;
			foreach($this->request->post['priority'] as $key => $priority){
				if($priority['status']){
					$entry = true;
					if(!$priority['resolve']['time'])
						$this->error['error_priority'][$key]['error_resolve_time'] = $this->language->get('error_resolve_time');
					if(!$priority['respond']['time'])
						$this->error['error_priority'][$key]['error_respond_time'] = $this->language->get('error_respond_time');
				}
			}
			if(!$entry)
				$this->error['error_priority_blank'] = $this->language->get('error_select_priority');
		}else
			$this->error['error_priority_blank'] = $this->language->get('error_blank_priority');

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		/**
		 * Call ticketevents class function - validation, which will validation selected events and return error which we pass to same controller at time of calling 
		 */
		if(isset($this->request->post['conditions_all'])){
			if($errorCondition = $this->load->controller('ticketsystem/ticketconditions/validation', array('condition' => $this->request->post['conditions_all'], 'key' => 'conditions_all')))
				$this->error['error_conditions_all'] = $errorCondition;
		}

		if(isset($this->request->post['conditions_one'])){
			if($errorCondition = $this->load->controller('ticketsystem/ticketconditions/validation', array('condition' => $this->request->post['conditions_one'], 'key' => 'conditions_one')))
				$this->error['error_conditions_one'] = $errorCondition;
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/sla')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

};