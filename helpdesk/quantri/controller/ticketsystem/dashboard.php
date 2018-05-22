<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to show Ticket Status with Ticket No. assign to current Agent and Activity related to agent
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */

class ControllerTicketSystemDashboard extends TsBase {

	const CONTROLLER_NAME = 'dashboard';

	public $allowedFields = array(
							'filter_a__activity',
							'filter_a__level',
							'filter_a__type',
							'filter_u__username',
							'filter_a__date_added',
							'filter_t__status',
							'filter_ta__id'
							);

	public function __construct($registry){
		$this->registry = $registry;
		$this->extendedClassCall = true;
		parent::__construct($registry);

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'modFolder' => 'ticketsystem',
						'defaultSort' => 'a.id',
						'allowedFields' => $this->allowedFields,
						'addTsColumnLeft' => false,
					)
			);
	}

	/**
	 * Not Real Constructor, just dummy
	 * @return array loaded data from base controller
	 */
	public function _construct(){
		return $this->data;
	}

	public function index() {
		$this->load->language('ticketsystem/all');

		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/activity');
		$this->load->model('ticketsystem/tickets');
		$this->load->model('ticketsystem/status');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'dashboard',
						'tplFile' => 'dashboard_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_dashboard');
		$data['text_list'] = $this->language->get('text_list_dashboard');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_dashboard') => 'dashboard',
					)
			);

		$data['activity'] = array();

		$filterArray = array(
							'affected' => $this->agent['id'],
							'type' => 'tickets',
						);

		$results = $this->model_ticketsystem_activity->getActivities(false, $filterArray);
		$activity_total = $this->model_ticketsystem_activity->getTotalActivities($filterArray);

		foreach ($results as $result) {
			$data['activity'][] = array(
				'id' 		 	=> $result['id'],
				'type'       	=> $result['type'],
				'performer'     => $result['performer'],
				'performertype' => $result['performertype'],
				'username'      => ($result['username'] ? $result['username'] : $result['customerName']),
				'email'      	=> ($result['email'] ? $result['email'] : $result['customerEmail']),
				'performerLink' => ($result['username'] ? $this->url->link('ticketsystem/agents', 'token=' . $this->session->data['token'] . '&filter_id='.$result['performer'], 'SSL') : $this->url->link('ticketsystem/customers', 'token=' . $this->session->data['token'] . '&filter_id='.$result['performer'], 'SSL')),
				'affected'      => $result['affected'],
				'activity'      => $result['activity'],
				'level'       	=> $this->language->get('text_'.$result['level']),
				'date_added'  	=> $this->convertDateFormat($result['date_added']),
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

		$url = $this->TsLoader->TsHelper->getUrlData('sort');

		$data['resultTotal'] = $activity_total;
		$data['addPagination'] = true;

		//for ticket
		$data['tile'] = array();

		/**
		 * This TsHelper "overrideRequestData" function used to override current set data in helper class on which class will work after some time
		 */
		$this->TsLoader->TsHelper->overrideRequestData($this->getAgentTicketAccessData(), true);
		foreach ($this->model_ticketsystem_status->getStatuss(false) as $status) {
			if($status['status']){
				$this->TsLoader->TsHelper->overrideRequestData(array('filter_t__status' => $status['id']), true);
				$data['tile'][] = array(
								'name' => $status['name'],
								'total' => $this->model_ticketsystem_tickets->getTotalTickets(),
								'link' => $this->url->link('ticketsystem/tickets', 'token=' . $this->session->data['token'] . '#&filter_t__status='.$status['id'], 'SSL'),
							);
			}
		}

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

}