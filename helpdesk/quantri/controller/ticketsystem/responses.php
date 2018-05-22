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
class ControllerTicketSystemResponses extends TsBase {

	const CONTROLLER_NAME = 'responses';

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
						'defaultSort' => 'r.name',
						'allowedFields' => $this->allowedFields,
					)
			);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/responses');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'responses',
						'tplFile' => 'responses_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_responses');
		$data['text_list'] = $this->language->get('text_list_responses');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_responses') => 'responses',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/responses/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/responses/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['responses'] = array();

		$responses_total = $this->model_ticketsystem_responses->getTotalResponses();

		$results = $this->model_ticketsystem_responses->getResponses();

		foreach ($results as $result) {
			$data['responses'][] = array(
				'id' 		 	=> $result['id'],
				'status'       	=> $result['status'],
				'name'       	=> $result['name'],
				'date_updated'  => $this->convertDateFormat($result['date_updated']),
				'edit'       => $this->url->link('ticketsystem/responses/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
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

		$data['sort_name'] = $this->url->link('ticketsystem/responses', 'token=' . $this->session->data['token'] . '&sort=r.name' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('ticketsystem/responses', 'token=' . $this->session->data['token'] . '&sort=r.status' . $url, 'SSL');
		$data['sort_date_updated'] = $this->url->link('ticketsystem/responses', 'token=' . $this->session->data['token'] . '&sort=r.date_updated' . $url, 'SSL');

		$data['resultTotal'] = $responses_total;
		$data['addPagination'] = true;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function add() {
		$this->document->setTitle($this->language->get('heading_responses'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'responses/add',
						'tplFile' => 'responses_form',
					)
			);

		$this->load->model('ticketsystem/responses');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['valid_for']['value']=='me' ? $this->request->post['valid_for']['value']=$this->agent['id'] : false;
			$this->model_ticketsystem_responses->addResponse($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_responses'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/responses', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/responses', 'token=' . $this->session->data['token'] . $url, 'SSL'));

		$this->document->setTitle($this->language->get('heading_responses'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'responses/edit',
						'tplFile' => 'responses_form',
					)
			);

		$this->load->model('ticketsystem/responses');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['id'] = $this->request->get['id'];
			$this->request->post['valid_for']['value']=='me' ? $this->request->post['valid_for']['value']=$this->agent['id'] : false;
			$this->model_ticketsystem_responses->editResponse($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_responses'));

			$this->response->redirect($this->url->link('ticketsystem/responses', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('ticketsystem/responses');

		$this->document->setTitle($this->language->get('heading_responses'));

		$this->load->model('ticketsystem/responses');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_responses->deleteResponse($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_responses'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/responses', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getForm() {
		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_'.self::CONTROLLER_NAME);

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add_responses') : $this->language->get('text_edit_responses');
		
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

		if (isset($this->error['error_action'])) {
			$data['error_action'] = $this->error['error_action'];
		} else {
			$data['error_action'] = array();
		}

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_responses') => 'responses',
					$data['text_form'] => !isset($this->request->get['id']) ? 'responses/add' : 'responses/edit&id='.$this->request->get['id'] ,
					)
			);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('ticketsystem/responses/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('ticketsystem/responses/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('ticketsystem/responses', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$response_info = $this->model_ticketsystem_responses->getResponse($this->request->get['id']);
		}

		$data['text_info_actions'] = sprintf($this->language->get('text_info_actions'), $this->language->get('heading_'.$this->route1));
		$data['token'] = $this->session->data['token'];

		$columnData = array(
						'id',
						'name',
						'description',
						'valid_for',
						'status',
						'actions',
						);

		foreach ($columnData as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (!empty($response_info)) 
				$data[$key] = $response_info[$key];
			else
				$data[$key] = '';
		}

		if (!empty($response_info)) {
			$data['actions'] = unserialize($data['actions']);
			$data['valid_for'] = unserialize($data['valid_for']);
		}
		else{
			$data['actions'] = $data['actions'] ? $data['actions'] : array();
		}
		
		$data['valid_for']['groups'] = isset($data['valid_for']['groups']) ? $data['valid_for']['groups'] : array();

		/**
		 * This will call ticketactions class, which will manage actions completely.
		 * It will return Html based on passed $data and $error 
		 */
		$data['ticketAction'] = $this->load->controller('ticketsystem/ticketactions', array('action' => $data['actions'], 'error' => $data['error_action']));

		$this->load->model('ticketsystem/groups');

		foreach ($data['valid_for']['groups'] as $key => $group) {
			unset($data['valid_for']['groups'][$key]);
			if($groupInfo = $this->model_ticketsystem_groups->getGroup($group)){
				$data['valid_for']['groups'][$key]['group_id'] = $groupInfo['id'];
				$data['valid_for']['groups'][$key]['name'] = $groupInfo['group'][$this->config->get('config_language_id')]['name'];
			}
		}

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/responses')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		/**
		 * Call ticketactions class function - validation, which will validation selected actions and return error which we pass to same controller at time of calling 
		 */
		if(isset($this->request->post['actions'])){
			if($errorAction = $this->load->controller('ticketsystem/ticketactions/validation', $this->request->post['actions']))
				$this->error['error_action'] = $errorAction;
		}else{
			$this->error['error_action']['error_responses'] = $this->language->get('error_response_action');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/responses')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}