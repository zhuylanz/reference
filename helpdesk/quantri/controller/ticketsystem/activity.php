<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class is used to view activities of Helpdesk module and generate them for admin side, last function
 */
class ControllerTicketSystemActivity extends TsBase {

	const CONTROLLER_NAME = 'activity';

	/**
	 * Allowed fields will allowed in db query filtering which will generate by our TsHelper Class
	 * @var array
	 */
	public $allowedFields = array(
							'filter_a__activity',
							'filter_a__level',
							'filter_a__type',
							'filter_u__firstname',
							'filter_a__date_added',
							);

	/**
	 * Not Real Constructor, just dummy
	 * @return array loaded data from base controller
	 */
	public function _construct(){
		return $this->data;
	}

	/**
	 * Call parent constructor on creation and then automatically check, agent/ user is allowed for this controller or not
	 * @param array $registry - OC default registry
	 */
	public function __construct($registry){
		$this->registry = $registry;

		/**
		 * This will notify TsBase controller that it is class call for inheritance not just checking access so that TsBase can show "Permission Denied" page.
		 * @var boolean
		 */
		$this->extendedClassCall = true;
		parent::__construct($registry);

		/**
		 * This will set default values to TsHelper Class on which class will work
		 * like module folder - 
		 * default sort will be for db query
		 * allowed fields, declared upper
		 * addTsColumnLeft, it will add column left like front module but not so much good so i didn't used it either
		 */
		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'modFolder' => 'ticketsystem',
						'defaultSort' => 'a.id',
						'allowedFields' => $this->allowedFields,
						'addTsColumnLeft' => true,
					)
			);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/activity');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'activity',
						'tplFile' => 'activity_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_activity');
		$data['text_list'] = $this->language->get('text_list_activity');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		/**
		 * Using this we can get url data from TsHelper Class so no need to write all $this->request->get blah blah, just pass what type of url data you want and kBoom
		 * @var string
		 */
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		/**
		 * Just call TsHelper function to create breadcrumbs with 
		 * 'keys' => 'value'
		 * 'your language text' => 'link'
		 * link => TsHelper will get folder from upper and and add this link "controller" name to url and then generate breadcrumbs
		 */
		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_activity') => 'activity',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/activity/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/activity/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['activity'] = array();

		$results = $this->model_ticketsystem_activity->getActivities();
		$activity_total = $this->model_ticketsystem_activity->getTotalActivities();

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
		
		/**
		 * Get priority from TsDefault class
		 */
		$data['tsPriority'] = $this->TsLoader->TsDefault->getPriority();

		$data['tsType'] = array('delete', 'add', 'edit');

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

		/**
		 * same url fetcher based on sorting
		 * @var string
		 */
		$url = $this->TsLoader->TsHelper->getUrlData('sort');

		$data['sort_activity'] = $this->url->link('ticketsystem/activity', 'token=' . $this->session->data['token'] . '&sort=a.id' . $url, 'SSL');
		$data['sort_type'] = $this->url->link('ticketsystem/activity', 'token=' . $this->session->data['token'] . '&sort=a.type' . $url, 'SSL');
		$data['sort_user'] = $this->url->link('ticketsystem/activity', 'token=' . $this->session->data['token'] . '&sort=a.performer' . $url, 'SSL');
		$data['sort_level'] = $this->url->link('ticketsystem/activity', 'token=' . $this->session->data['token'] . '&sort=a.level' . $url, 'SSL');
		$data['sort_date_added'] = $this->url->link('ticketsystem/activity', 'token=' . $this->session->data['token'] . '&sort=a.date_added' . $url, 'SSL');

		$data['resultTotal'] = $activity_total;

		/**
		 * To add pagination to this controller enable this variable and TsHelper will add this
		 */
		$data['addPagination'] = true;

		/**
		 * LoadHtml will generate all repetitive work for us
		 */
		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function delete() {
		$this->load->language('ticketsystem/activity');

		$this->document->setTitle($this->language->get('heading_activity'));

		$this->load->model('ticketsystem/activity');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_activity->deleteActivity($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_activity'));

			/**
			 * same url fetcher from TsHelper based on "default"
			 */
			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/activity', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/activity')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	/**
	 * This function is used to add entry in Activity
	 * @param boolean $data array, on which library function will work
	 * @param boolean $id  based on condition - any id
	 */
	public function add($data = false, $id = false) {
		$this->TsLoader->TsActivity->setAgent($this->agent);
		$this->TsLoader->TsActivity->add($data, $id);
	}
}