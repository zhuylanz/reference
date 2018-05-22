<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to show HelpDesk Events, which will be used in Module
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemEvents extends TsBase {

	const CONTROLLER_NAME = 'events';

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
						'defaultSort' => 'e.name',
						'allowedFields' => $this->allowedFields,
					)
			);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/events');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'events',
						'tplFile' => 'events_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_events');
		$data['text_list'] = $this->language->get('text_list_events');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_events') => 'events',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/events/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/events/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['events'] = array();

		$events_total = $this->model_ticketsystem_events->getTotalEvents();

		$results = $this->model_ticketsystem_events->getEvents();

		foreach ($results as $result) {
			$data['events'][] = array(
				'id' 		 	=> $result['id'],
				'status'       	=> $result['status'],
				'name'       	=> $result['name'],
				'description'   => $result['description'],
				'date_updated'  => $this->convertDateFormat($result['date_updated']),
				'edit'       => $this->url->link('ticketsystem/events/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
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

		$data['sort_name'] = $this->url->link('ticketsystem/events', 'token=' . $this->session->data['token'] . '&sort=e.name' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('ticketsystem/events', 'token=' . $this->session->data['token'] . '&sort=e.status' . $url, 'SSL');
		$data['sort_date_updated'] = $this->url->link('ticketsystem/events', 'token=' . $this->session->data['token'] . '&sort=e.date_updated' . $url, 'SSL');

		$data['resultTotal'] = $events_total;
		$data['addPagination'] = true;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function add() {
		$this->document->setTitle($this->language->get('heading_events'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'events/add',
						'tplFile' => 'events_form',
					)
			);

		$this->load->model('ticketsystem/events');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_ticketsystem_events->addEvent($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_events'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/events', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/events', 'token=' . $this->session->data['token'] . $url, 'SSL'));

		$this->document->setTitle($this->language->get('heading_events'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'events/edit',
						'tplFile' => 'events_form',
					)
			);

		$this->load->model('ticketsystem/events');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['id'] = $this->request->get['id'];
			$this->model_ticketsystem_events->editEvent($this->request->post);
			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_events'));

			$this->response->redirect($this->url->link('ticketsystem/events', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('ticketsystem/events');

		$this->document->setTitle($this->language->get('heading_events'));

		$this->load->model('ticketsystem/events');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_events->deleteEvent($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_events'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/events', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getForm() {
		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_'.self::CONTROLLER_NAME);

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add_events') : $this->language->get('text_edit_events');
		
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

		if (isset($this->error['error_events'])) {
			$data['error_events'] = $this->error['error_events'];
		} else {
			$data['error_events'] = array();
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

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_events') => 'events',
					$data['text_form'] => !isset($this->request->get['id']) ? 'events/add' : 'events/edit&id='.$this->request->get['id'] ,
					)
			);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('ticketsystem/events/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('ticketsystem/events/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('ticketsystem/events', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$event_info = $this->model_ticketsystem_events->getEvent($this->request->get['id']);
		}

		$data['token'] = $this->session->data['token'];
		$data['text_info_tevents'] = sprintf($this->language->get('text_info_tevents'), $this->language->get('heading_'.$this->route1));
		$data['text_info_actions'] = sprintf($this->language->get('text_info_actions'), $this->language->get('heading_'.$this->route1));

		$this->load->model('ticketsystem/agents');
		$data['tsAgents'] = $this->model_ticketsystem_agents->getAgents(false);

		$columnData = array(
						'id',
						'name',
						'description',
						'status',
						);

		foreach ($columnData as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (!empty($event_info)) 
				$data[$key] = $event_info[$key];
			else
				$data[$key] = '';
		}

		$columnDataArray = array(
								'conditions_all',
								'conditions_one',
								'performer',
								'actions',
								'events',
							);

		foreach ($columnDataArray as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (!empty($event_info)) 
				$data[$key] = unserialize($event_info[$key]);
			else
				$data[$key] = array();
		}

		if(!isset($data['performer']['agents']))
			$data['performer']['agents'] = array();

		/**
		 * This will call ticketactions class, which will manage actions completely.
		 * It will return Html based on passed $data and $error 
		 */
		$data['ticketAction'] = $this->load->controller('ticketsystem/ticketactions', array('action' => $data['actions'], 'error' => $data['error_action']));

		/**
		 * This will call ticketconditions class, which will manage conditions completely.
		 * It will return Html based on passed $data and $error 
		 * Id will show it's for One or All
		 */
		$data['ticketConditionsAll'] = $this->load->controller('ticketsystem/ticketconditions', array('conditions' => $data['conditions_all'], 'error' => $data['error_conditions_all'], 'id' => 'all'));
		$data['ticketConditionsOne'] = $this->load->controller('ticketsystem/ticketconditions', array('conditions' => $data['conditions_one'], 'error' => $data['error_conditions_one'], 'id' => 'one'));

		/**
		 * This will call ticketevents class, which will manage Events.
		 */
		$data['ticketEvents'] = $this->load->controller('ticketsystem/ticketevents', array('events' => $data['events'], 'error' => $data['error_events'], 'id' => 'all'));

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/events')) {
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

		/**
		 * Call ticketevents class function - validation, which will validation selected events and return error which we pass to same controller at time of calling 
		 */
		if(isset($this->request->post['events'])){
			if($errorEvents = $this->load->controller('ticketsystem/ticketevents/validation', $this->request->post['events']))
				$this->error['error_events'] = $errorEvents;
		}else{
			$this->error['error_events']['error_blank_event'] = $this->language->get('error_blank_event');
		}

		/**
		 * Call ticketconditions class function - validation, which will validation selected conditions and return error which we pass to same controller at time of calling 
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
		if (!$this->user->hasPermission('modify', 'ticketsystem/events')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}