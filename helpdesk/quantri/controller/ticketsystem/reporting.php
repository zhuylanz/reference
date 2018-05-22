<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to show HelpDesk Reporting, which will be used in Module
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemReporting extends TsBase {

	const CONTROLLER_NAME = 'reporting';

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
						'defaultSort' => 'ttd.name',
						'allowedFields' => $this->allowedFields,
					)
			);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'reporting',
						'tplFile' => 'reporting_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_reporting');
		$data['text_list'] = $this->language->get('text_list_reporting');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_reporting') => 'reporting',
					)
			);

		$data['reporting'] = array(
								// 'text_quick_view' => array(
								// 		'customer_a_glance' => $this->language->get('text_customer_a_glance'),
								// 		'agent_a_glance' => $this->language->get('text_agent_a_glance'),
								// 		'group_a_glance' => $this->language->get('text_group_a_glance'),
								// 		),
								'text_ticket_summary' => array(
										'agent_ticket_summary' => $this->language->get('text_agent_ticket_summary'),
										'customer_ticket_summary' => $this->language->get('text_customer_ticket_summary'),
										),
								// 'agent_comparison' => $this->language->get('text_agent_comparison'),
								// 'group_comparison' => $this->language->get('text_group_comparison'),
								// 'helpdesk_load_management' => $this->language->get('text_helpdesk_load_management'),
							);

		$data['reporting_view'] =  $this->url->link('ticketsystem/reporting/view', 'token=' . $this->session->data['token'] . $url.'&view=', 'SSL');

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

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function view() {
		if(!isset($this->request->get['view'])){
			$this->response->redirect($this->url->link('ticketsystem/reporting', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->document->setTitle($this->language->get('text_heading_'.$this->request->get['view']));
		
		$this->load->model('ticketsystem/reporting');

		if(method_exists($this, $this->request->get['view']))
			$this->{$this->request->get['view']}();
		else
			$this->response->redirect($this->url->link('ticketsystem/reporting', 'token=' . $this->session->data['token'], 'SSL'));
	}

	protected function agent_ticket_summary(){
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'reporting/view',
						'tplFile' => 'reporting_form_agent',
					)
			);

		$data['results'] = $this->model_ticketsystem_reporting->getTicketReportsSummaryByAgents();

		$data['heading_title'] = $data['text_form'] = $this->language->get('text_heading_'.$this->request->get['view']);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_reporting') => 'reporting',
					$data['text_form'] => 'reporting/view&view='.$this->request->get['view'] ,
				)
			);

		$data['cancel'] = $this->url->link('ticketsystem/reporting', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['token'] = $this->session->data['token'];

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	protected function customer_ticket_summary(){
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'reporting/view',
						'tplFile' => 'reporting_form_customer',
					)
			);

		$data['results'] = $this->model_ticketsystem_reporting->getTicketReportsSummaryByCustomers();

		$data['heading_title'] = $data['text_form'] = $this->language->get('text_heading_'.$this->request->get['view']);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_reporting') => 'reporting',
					$data['text_form'] => 'reporting/view&view='.$this->request->get['view'] ,
				)
			);

		$data['cancel'] = $this->url->link('ticketsystem/reporting', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['token'] = $this->session->data['token'];

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}
}