<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to manage HelpDesk Business Hours, their listing etc
 *
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemBusinessHours extends TsBase {

	const CONTROLLER_NAME = 'businesshours';

	public $allowedFields = array(
							'filter_name',
							'filter_timezone',
							'filter_date_updated',
							);

	public function __construct($registry){
		$this->registry = $registry;
		$this->extendedClassCall = true;
		parent::__construct($registry);

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'modFolder' => 'ticketsystem',
						'defaultSort' => 'b.name',
						'allowedFields' => $this->allowedFields,
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
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/businesshours');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'businesshours',
						'tplFile' => 'businesshours_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_businesshours');
		$data['text_list'] = $this->language->get('text_list_businesshours');

		//extract shoting data
		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_businesshours') => 'businesshours',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/businesshours/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['copy'] = $this->url->link('ticketsystem/businesshours/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/businesshours/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['businesshours'] = array();

		$businesshours_total = $this->model_ticketsystem_businesshours->getTotalBusinessHours();

		$results = $this->model_ticketsystem_businesshours->getBusinessHours();

		foreach ($results as $result) {
			$data['businesshours'][] = array(
				'id' 		 	=> $result['id'],
				'name'       	=> $result['name'],
				'description'   => $result['description'],
				'timezone'   	=> $result['timezone'],
				'date_updated'   => $this->convertDateFormat($result['date_updated']),
				'edit'       => $this->url->link('ticketsystem/businesshours/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
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

		$data['sort_name'] = $this->url->link('ticketsystem/businesshours', 'token=' . $this->session->data['token'] . '&sort=b.name' . $url, 'SSL');
		$data['sort_timezone'] = $this->url->link('ticketsystem/businesshours', 'token=' . $this->session->data['token'] . '&sort=b.timezone' . $url, 'SSL');
		$data['sort_date_updated'] = $this->url->link('ticketsystem/businesshours', 'token=' . $this->session->data['token'] . '&sort=b.date_updated' . $url, 'SSL');

		$data['resultTotal'] = $businesshours_total;
		$data['addPagination'] = true;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function add() {

		$this->document->setTitle($this->language->get('heading_businesshours'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'businesshours/add',
						'tplFile' => 'businesshours_form',
					)
			);

		$this->load->model('ticketsystem/businesshours');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_ticketsystem_businesshours->addBusinessHour($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_businesshours'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/businesshours', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/businesshours', 'token=' . $this->session->data['token'] . $url, 'SSL'));

		$this->document->setTitle($this->language->get('heading_businesshours'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'businesshours/edit',
						'tplFile' => 'businesshours_form',
					)
			);

		$this->load->model('ticketsystem/businesshours');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['id'] = $this->request->get['id'];
			$this->model_ticketsystem_businesshours->editBusinessHour($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_businesshours'));

			$this->response->redirect($this->url->link('ticketsystem/businesshours', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('ticketsystem/businesshours');

		$this->document->setTitle($this->language->get('heading_businesshours'));

		$this->load->model('ticketsystem/businesshours');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_businesshours->deleteBusinessHour($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_businesshours'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/businesshours', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getForm() {

		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_'.self::CONTROLLER_NAME);

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add_businesshours') : $this->language->get('text_edit_businesshours');
		
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

		if (isset($this->error['businesshours'])) {
			$data['error_businesshours'] = $this->error['businesshours'];
		} else {
			$data['error_businesshours'] = array();
		}

		if (isset($this->error['holiday'])) {
			foreach ($this->error['holiday'] as $key => $value) {
				$data['error_holiday'][$key] = $value;
			}
		} else {
			$data['error_holiday'] = array();
		}

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_businesshours') => 'businesshours',
					$data['text_form'] => !isset($this->request->get['id']) ? 'businesshours/add' : 'businesshours/edit&id='.$this->request->get['id'] ,
					)
			);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('ticketsystem/businesshours/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('ticketsystem/businesshours/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('ticketsystem/businesshours', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$businessHour_info = $this->model_ticketsystem_businesshours->getBusinessHour($this->request->get['id']);
		}

		$data['token'] = $this->session->data['token'];

		$data['timezones'] = $this->TsLoader->TsHelper->timezone;
		$data['weekDays'] = $this->TsLoader->TsHelper->weekDays;
		$data['timings'] = $this->TsLoader->TsHelper->timings;

		$columnData = array(
						'name',
						'description',
						'timezone',
						'holiday'
						);

		foreach ($columnData as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (!empty($businessHour_info) AND isset($businessHour_info[$key])) 
				$data[$key] = $businessHour_info[$key];
			else
				$data[$key] = '';
		}

		$data['daysTimings'] = $data['daysPositions'] = $data['daysSizes'] = array();

		if(isset($this->request->post['businesshours'])){
			$data['daysTimings'] = $this->request->post['businesshours']['days'];
			$data['daysPositions'] = $this->request->post['businesshours']['position'];
			$data['daysSizes'] = $this->request->post['businesshours']['size'];
		}elseif (!empty($businessHour_info)){
			$data['daysTimings'] = unserialize($businessHour_info['timings']);
			$data['daysPositions'] = unserialize($businessHour_info['positions']);
			$data['daysSizes'] = unserialize($businessHour_info['sizes']);
		}

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/businesshours')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ((utf8_strlen($this->request->post['description']) < 3) || (utf8_strlen($this->request->post['description']) > 255)) {
			$this->error['description'] = $this->language->get('error_description');
		}
		
		if (!isset($this->request->post['businesshours'])) {
			$this->error['businesshours'] = $this->language->get('error_businesshours');
		}else{
			$error = true;

			foreach ($this->request->post['businesshours']['days'] as $key => $days) {
				if(isset(array_values($days)[0]) AND $days[0]){
					$error = false;
					break;
				}
			}
			if($error)
				$this->error['businesshours'] = $this->language->get('error_businesshours');
		}

		if (isset($this->request->post['holiday'])) {
			$error = true;
			foreach ($this->request->post['holiday'] as $key => $value) {
				if(!$value['name'] || !$value['from_date'] || !$value['to_date'])
					$this->error['holiday'][$key] = $this->language->get('error_holiday_empty');
				elseif($value['from_date'] < $value['to_date'])
					$this->error['holiday'][$key] = $this->language->get('error_holiday_date');
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/businesshours')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}