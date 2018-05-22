<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to manage HelpDesk Agents, their listing etc
 *
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemAgents extends TsBase {

	const CONTROLLER_NAME = 'agents';

	public $allowedFields = array(
							'filter_id',
							'filter_firstname',
							'filter_email',
							'filter_last_login',
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

		$this->load->model('ticketsystem/agents');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'agents',
						'tplFile' => 'agents_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_agents');
		$data['text_list'] = $this->language->get('text_list_agents');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_agents') => 'agents',
					)
			);

		$data['add'] = $this->url->link('ticketsystem/agents/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['copy'] = $this->url->link('ticketsystem/agents/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('ticketsystem/agents/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['agents'] = array();

		$result_total = $this->model_ticketsystem_agents->getTotalAgents();

		$results = $this->model_ticketsystem_agents->getAgents();

		foreach ($results as $result) {

				if($result['image'])
					$thumb = $this->model_tool_image->resize($result['image'], 75, 75);
				else
					$thumb = $this->model_tool_image->resize('placeholder.png', 75, 75);

			$data['agents'][] = array(
				'id' 		 	=> $result['id'],
				'name'       	=> $result['username'],
				'email'   		=> $result['email'],
				'image'       	=> $thumb,
				'last_login'    => $this->convertDateFormat($result['last_login']),
				'edit'       	=> $this->url->link('ticketsystem/agents/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
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

		$data['sort_username'] = $this->url->link('ticketsystem/agents', 'token=' . $this->session->data['token'] . '&sort=u.username' . $url, 'SSL');
		$data['sort_email'] = $this->url->link('ticketsystem/agents', 'token=' . $this->session->data['token'] . '&sort=u.email' . $url, 'SSL');
		$data['sort_lastlogin'] = $this->url->link('ticketsystem/agents', 'token=' . $this->session->data['token'] . '&sort=a.last_login' . $url, 'SSL');

		$data['resultTotal'] = $result_total;
		$data['addPagination'] = true;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function add() {
		/**
		 * This TsBase function used to check that "Current Agent" has permission to this particular function
		 */
		$this->checkEventAccessPermission(array(
											'key'=> self::CONTROLLER_NAME, 
											'event'=> self::CONTROLLER_NAME.'.'.__FUNCTION__
											)
										);

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'agents/add',
						'tplFile' => 'agents_form',
					)
			);

		$this->document->setTitle($this->language->get('heading_agents'));

		$this->load->model('ticketsystem/agents');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_ticketsystem_agents->addAgent($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_agents'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/agents', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		/**
		 * This TsBase function used to check that "Current Agent" has permission to this particular function
		 */
		$this->checkEventAccessPermission(array(
											'key'=>self::CONTROLLER_NAME, 
											'event'=> self::CONTROLLER_NAME.'.'.__FUNCTION__
											)
										);

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'agents/edit',
						'tplFile' => 'agents_form',
					)
			);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		if(!isset($this->request->get['id']))
			$this->response->redirect($this->url->link('ticketsystem/agents', 'token=' . $this->session->data['token'] . $url, 'SSL'));

		$this->document->setTitle($this->language->get('heading_agents'));

		$this->load->model('ticketsystem/agents');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['id'] = $this->request->get['id'];
			$this->model_ticketsystem_agents->editAgent($this->request->post);

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_agents'));

			$this->response->redirect($this->url->link('ticketsystem/agents', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		/**
		 * This TsBase function used to check that "Current Agent" has permission to this particular function
		 */
		$this->checkEventAccessPermission(array(
											'key'=>self::CONTROLLER_NAME, 
											'event'=> self::CONTROLLER_NAME.'.'.__FUNCTION__
											)
										);

		$this->load->language('ticketsystem/agents');

		$this->document->setTitle($this->language->get('heading_agents'));

		$this->load->model('ticketsystem/agents');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_agents->deleteAgent($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_agents'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/agents', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getForm() {

		$data = $this->_construct();

		$data['heading_title'] = $this->language->get('heading_'.self::CONTROLLER_NAME);

		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add_agents') : $this->language->get('text_edit_agents');

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

		if (isset($this->error['role'])) {
			$data['error_role'] = $this->error['role'];
		} else {
			$data['error_role'] = array();
		}

		if (isset($this->error['user'])) {
			$data['error_user'] = $this->error['user'];
		} else {
			$data['error_user'] = array();
		}

		if (isset($this->error['scope'])) {
			$data['error_scope'] = $this->error['scope'];
		} else {
			$data['error_scope'] = array();
		}

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_agents') => 'agents',
					$data['text_form'] => !isset($this->request->get['id']) ? 'agents/add' : 'agents/edit&id='.$this->request->get['id'] ,
					)
			);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('ticketsystem/agents/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('ticketsystem/agents/edit&id='.$this->request->get['id'], 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('ticketsystem/agents', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$user_info = $this->model_ticketsystem_agents->getAgent(array('a.id' => $this->request->get['id']));
		}

		/**
		 * fetch values from TsDefault class - static values
		 */
		$data['scopes'] = $this->TsLoader->TsHelper->scope;
		$data['timezones'] = $this->TsLoader->TsHelper->timezone;

		$this->load->model('ticketsystem/roles');
		$data['roles'] = $this->model_ticketsystem_roles->getRoles(false);
		
		$this->load->model('ticketsystem/level');
		$data['levels'] = $this->model_ticketsystem_level->getLevels(false);

		$data['token'] = $this->session->data['token'];

		$columnData = array(
						'name_alias',
						'level',
						'timezone',
						'signature',
						'role',
						'scope',
						'user_id',
						'groups',

						'username',
						'email',
						'image',
						);

		foreach ($columnData as $key) {
			if (isset($this->request->post[$key]))
				$data[$key] = $this->request->post[$key];
			elseif (isset($user_info[$key])) 
				$data[$key] = $user_info[$key];
			else
				$data[$key] = '';
		}
		
		if($data['user_id']){
			$this->load->model('user/user');
			$user_info = $this->model_user_user->getUser($data['user_id']);
			
			if ($user_info) {
				if($user_info['image'])
					$thumb = $this->model_tool_image->resize($user_info['image'], 125, 125);
				else
					$thumb = $this->model_tool_image->resize('placeholder.png', 125, 125);

				$data['email'] = $user_info['email'];
				$data['image'] = $thumb;
			}
		}else{
			$data['image'] = $this->model_tool_image->resize('placeholder.png', 125, 125);
		}

		$data['groups'] = $data['groups'] ? $data['groups'] : array();
		
		$this->load->model('ticketsystem/groups');
		if(!isset($data['groups'][0]['id']))
			foreach ($data['groups'] as $key => $group) {
				unset($data['groups'][$key]);
				if($groupInfo = $this->model_ticketsystem_groups->getGroup($group)){
					$data['groups'][$key]['groupid'] = $group;
					$data['groups'][$key]['groupname'] = $groupInfo['group'][$this->config->get('config_language_id')]['name'];
				}
			}

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function getUsers() {
		// $this->checkEventAccessPermission(array(
		// 									'key'=>self::CONTROLLER_NAME, 
		// 									'event'=> self::CONTROLLER_NAME.'.'.__FUNCTION__
		// 									)
		// 								);
		
		$json = array();

		$this->load->model('ticketsystem/user');

		$users_info = $this->model_ticketsystem_user->getUsers($this->request->get['user']);

		if ($users_info) {
			foreach ($users_info as $user) {

				if(isset($user['image']) && $user['image'])
					$image = $this->model_tool_image->resize($user['image'], 125, 125);
				else
					$image = $this->model_tool_image->resize('placeholder.png', 125, 125);

				$json[] = array(
					'id'        		=> $user['user_id'],
					'username'          => $user['firstname'].' '.$user['lastname'],
					'email'        		=> $user['email'],
					'image'        		=> $image,
				);
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validateForm() {

		if (!$this->user->hasPermission('modify', 'ticketsystem/agents')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!isset($this->request->post['scope'])){
			$this->error['scope'] = $this->language->get('error_scope');
		}

		if (!isset($this->request->post['role']) || !$this->request->post['role']) {
			$this->error['role'] = $this->language->get('error_roles');
		}

		if (!(int)$this->request->post['user_id']) {
			$this->error['user'] = $this->language->get('error_user');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/agents')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		$this->load->model('ticketsystem/agents');

		$agents_info = $this->model_ticketsystem_agents->getAgents();

		if ($agents_info) {
			foreach ($agents_info as $user) {
				$json[] = array(
					'id'        		=> $user['id'],
					'name'          	=> $user['username'],
					'email'        		=> $user['email'],
				);
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}