<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class is installer for HelpDesk module
 */
class ControllerModuleTS extends Controller {

	private $error = array();

	public function install(){
		$this->load->model('ticketsystem/install');
		$this->model_ticketsystem_install->createTables();
		
		//to insert pre-added data into Helpdesk tables
		$this->model_ticketsystem_install->database();
	}

	public function index() {
		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'modFolder' => 'module',
						'controllerFile' => 'ts',
						'tplFile' => 'ts',
					)
			);

		$data = array_merge($this->load->language('ticketsystem/all'), $this->load->language('module/ts'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('ticketsystem/install');
			if(isset($this->request->post['ts_register_activity']))
				$this->model_ticketsystem_install->eventsEntry($this->request->post['ts_register_activity']);
			else
				$this->model_ticketsystem_install->eventsEntry(array());

			$this->model_setting_setting->editSetting('ts', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_title') => 'ts',
					)
			);

		$data['controllers'] = array(
								'agents',
								'groups',
								'roles',
								'tickets',
								'businesshours',
								'types',
								// 'customers',
								// 'customfields',
								// 'events',
								// 'level',
								// 'organizations',
								// 'priority',
								// 'responses',
								// 'rules',
								// 'sla',
								// 'status',
								// 'supportcenter',
								// 'emails',
								);

		$data['tsPriority'] = $this->TsLoader->TsDefault->getPriority();

		$this->load->model('ticketsystem/status');
		$data['tsStatus'] = $this->model_ticketsystem_status->getStatuss(false);

		$this->load->model('ticketsystem/types');
		$data['tsTypes'] = $this->model_ticketsystem_types->getTypes(false);

		$this->load->model('ticketsystem/groups');
		$data['tsGroups'] = $this->model_ticketsystem_groups->getGroups(false);

		$this->load->model('ticketsystem/priority');
		$data['tsPriorities'] = $this->model_ticketsystem_priority->getPriorities(false);

		$data['tsHeader'] = array(
								'supportcenter',
								'generatetickets',
								'tickets',
								'login'
								);

		$data['tsFields'] = array(
								'subject',
								'tickettype',
								'group',
								'priority',
								'status',
								'agent',
								'message',
								'fileupload',
								);

		$config_array = array(
							'ts_status',
							'ts_activity_limit',
							'ts_date_format',
							'ts_register_activity',
							'ts_action_level_delete',
							'ts_action_level_edit',
							'ts_header',
							'ts_information_limit',
							'ts_information_order',
							'ts_action_level_add',
							'ts_fields',
							'ts_required_fields',
							'ts_login',
							'ts_fileupload_no',
							'ts_fileupload_size',
							'ts_fileupload_ext',
							'ts_customer_delete_ticket',
							'ts_customer_delete_ticketthread',
							'ts_customer_update_status',
							'ts_customer_add_cc',
							'ts_editor',
							'ts_ticket_status',
							'ts_ticket_default',

							'ts_ticket_view_limit',
							'ts_save_draft_time',
							'ts_ticket_view_time',
							'ts_ticket_view_expire_time',
							'ts_add_internal_after_applying_actions',
						);

		foreach($config_array as $config){
			if(isset($this->request->post[$config]))
				$data[$config] = $this->request->post[$config];
			else
				$data[$config] = $this->config->get($config);				
		}

		$data['action'] = $this->url->link('module/ts', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/ts')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}