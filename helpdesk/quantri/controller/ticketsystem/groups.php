<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to show HelpDesk Groups, which will be used in Module
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemGroups extends TsBase {

	const CONTROLLER_NAME = 'groups';

	public $allowedFields = array(
							'filter_name',
							'filter_date_updated'
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
						'defaultSort' => 'g.id',
						'allowedFields' => $this->allowedFields,
					)
			);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/groups');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'groups',
						'tplFile' => 'groups_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_groups');
		$data['text_list'] = $this->language->get('text_list_groups');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_groups') => 'groups',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/groups/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/groups/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['groups'] = array();

		$groups_total = $this->model_ticketsystem_groups->getTotalGroups();

		$results = $this->model_ticketsystem_groups->getGroups();

		$this->load->model('ticketsystem/businesshours');

		foreach ($results as $result) {
			$data['groups'][] = array(
				'id' 		 	=> $result['id'],
				'name'       	=> $result['name'],
				'description'   => $result['description'],
				'date_updated'    => $this->convertDateFormat($result['date_updated']),
				'businesshour'  => @$this->model_ticketsystem_businesshours->getBusinessHour($result['businesshour_id'])['name'],
				'edit'       	=> $this->url->link('ticketsystem/groups/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
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

		$data['sort_name'] = $this->url->link('ticketsystem/groups', 'token=' . $this->session->data['token'] . '&sort=gd.name' . $url, 'SSL');
		$data['sort_business'] = $this->url->link('ticketsystem/groups', 'token=' . $this->session->data['token'] . '&sort=g.businesshour_id' . $url, 'SSL');
		$data['sort_date_updated'] = $this->url->link('ticketsystem/groups', 'token=' . $this->session->data['token'] . '&sort=g.date_updated' . $url, 'SSL');

		$data['resultTotal'] = $groups_total;
		$data['addPagination'] = true;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function add() {

		$this->document->setTitle($this->language->get('heading_groups'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'groups/add',
						'tplFile' => 'groups_form',
					)
			);

		$this->load->model('ticketsystem/groups');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_ticketsystem_groups->addGroup($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_groups'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/groups', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/groups', 'token=' . $this->session->data['token'] . $url, 'SSL'));

		$this->document->setTitle($this->language->get('heading_groups'));

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'groups/edit',
						'tplFile' => 'groups_form',
					)
			);

		$this->load->model('ticketsystem/groups');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['id'] = $this->request->get['id'];
			$this->model_ticketsystem_groups->editGroup($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_groups'));

			$this->response->redirect($this->url->link('ticketsystem/groups', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('ticketsystem/groups');

		$this->document->setTitle($this->language->get('heading_groups'));

		$this->load->model('ticketsystem/groups');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_groups->deleteGroup($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_groups'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/groups', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getForm() {

		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_'.self::CONTROLLER_NAME);

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add_groups') : $this->language->get('text_edit_groups');
		
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

		if (isset($this->error['businesshours'])) {
			$data['error_businesshours'] = $this->error['businesshours'];
		} else {
			$data['error_businesshours'] = '';
		}

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_groups') => 'groups',
					$data['text_form'] => !isset($this->request->get['id']) ? 'groups/add' : 'groups/edit&id='.$this->request->get['id'] ,
					)
			);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('ticketsystem/groups/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('ticketsystem/groups/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('ticketsystem/groups', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$group_info = $this->model_ticketsystem_groups->getGroup($this->request->get['id']);
		}
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('ticketsystem/businesshours');
		$data['businesshours'] = $this->model_ticketsystem_businesshours->getBusinessHours(false);

		$this->load->model('ticketsystem/agents');
		$data['agentsList'] = $this->model_ticketsystem_agents->getAgents(false);

		$data['informTimes'] = array(
									30 => '30 Min',
									60 => '1 Hour',
									120 => '2 Hour',
									240 => '4 Hour',
									480 => '8 Hour',
									720 => '12 Hour',
									1440 => '1 Day',
									2880 => '2 Day',
								);		

		$data['token'] = $this->session->data['token'];

		$columnData = array(
						'group',
						'businesshour_id',
						'automatic_assign',
						'inform_time',
						'inform_agent',
						'agents',
						);

		foreach ($columnData as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (!empty($group_info)) 
				$data[$key] = $group_info[$key];
			else
				$data[$key] = '';
		}

		$data['group'] = $data['group'] ? $data['group'] : array();
		$data['agents'] = $data['agents'] ? $data['agents'] : array();

		if(!isset($data['agents'][0]['id']))
			foreach ($data['agents'] as $key => $agent) {
				unset($data['agents'][$key]);
				if($agentInfo = $this->model_ticketsystem_agents->getAgent(array('a.id' => $agent))){
					$data['agents'][$key]['id'] = $agentInfo['id'];
					$data['agents'][$key]['name'] = ($agentInfo['name_alias'] ? $agentInfo['name_alias'] : $agentInfo['username']).' - '.$agentInfo['email'];
				}
			}

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/groups')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach($this->request->post['group'] as $language => $value){
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 64)) {
				$this->error['name'][$language] = $this->language->get('error_name');
			}
		}

		if (!$this->request->post['businesshour_id']) {
			$this->error['businesshours'] = $this->language->get('error_businesshours');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/groups')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		$this->load->model('ticketsystem/groups');

		$groups_info = $this->model_ticketsystem_groups->getGroups();

		if ($groups_info) {
			foreach ($groups_info as $group) {
				$json[] = array(
					'id'        		=> $group['id'],
					'name'          	=> $group['name'],
				);
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}