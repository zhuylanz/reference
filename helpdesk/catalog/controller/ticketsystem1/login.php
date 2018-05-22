<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Login Class is used to set login/ logout of customer
 */
class ControllerTicketSystemLogin extends Controller {

	const CONTROLLER_NAME = 'login';

	private $error = array();

	public function index() {
		if(!$this->config->get('ts_status'))
			$this->response->redirect($this->url->link('account/account','','SSL'));
		
		if (isset($this->session->data['ts_customer'])) {
			$this->response->redirect($this->url->link('ticketsystem/tickets', '', 'SSL'));
		}

		$data = $this->load->language('ticketsystem/login');
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->session->data['success'] = $this->language->get('text_success_login');
			$this->response->redirect($this->url->link('ticketsystem/tickets', '', 'SSL'));
		}

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'modFolder' => 'ticketsystem',
						'addTsHeader' => (is_array($this->config->get('ts_header')) AND in_array(self::CONTROLLER_NAME, $this->config->get('ts_header'))) ? true : false,
					)
			);

		$this->document->addStyle('catalog/view/javascript/ticketsystem/css/ticketsystem/ticketsystem.css');

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'login',
						'tplFile' => 'login',
					)
			);

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getCatalogBreadcrumbs(
				array(
					$this->language->get('heading_title') => 'login',
					)
			);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];
			unset($this->session->data['error_warning']);
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['ticketId'])) {
			$data['ticketId'] = $this->request->post['ticketId'];
		} else {
			$data['ticketId'] = '';
		}

		$data['action'] = $this->url->link('ticketsystem/login', '', 'SSL');

		$data['categories'] = array();

		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/supportcenter'));
		$results = $this->model_ticketsystem_supportcenter->getCategories(false);

		foreach($results as $category){
			$data['categories'][] = array(
									'id' => $category['id'],
									'name' => $category['name'],
									);
		}

		$this->response->setOutput($this->TsLoader->TsHelper->loadCatalogHtml($data));
	}

	protected function validate() {
		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'sqlStringEqual' => true,
					)
			);
		if(!trim($this->request->post['email']) AND !trim($this->request->post['ticketId'])){
			$this->error['warning'] = $this->language->get('error_login');
		}else{
			// Check if customer has been in helpdesk db.
			$this->TsLoader->TsService->model(array('model' => 'ticketsystem/tickets'));
			$filterArray = array(
							't.id' => $this->request->post['ticketId'],
							'tc.email' => $this->request->post['email']
						);
			if($ticketData = $this->model_ticketsystem_tickets->getTicket($filterArray)){
				$this->session->data['ts_customer'] = array(
														'id' => $ticketData['customer_id'],
														'email' => $this->request->post['email'],
														);
			}else{
				$this->error['warning'] = $this->language->get('error_login');
			}
		}

		return !$this->error;
	}

	public function logout() {
		if(!$this->config->get('ts_status'))
			$this->response->redirect($this->url->link('account/account','','SSL'));
		
		$this->load->language('ticketsystem/login');
		if(!$this->customer->getId()){
			unset($this->session->data['ts_customer']);
			$this->session->data['success'] = $this->language->get('text_success_logout');
			$this->response->redirect($this->url->link('ticketsystem/login', '', 'SSL'));
		}else{
			$this->session->data['error_warning'] = $this->language->get('error_oc_login');
			$this->response->redirect($this->url->link('ticketsystem/supportcenter', '', 'SSL'));
		}
	}
}