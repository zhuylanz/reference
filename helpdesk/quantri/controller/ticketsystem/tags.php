<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to show Tags added in Helpdesk
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */
class ControllerTicketSystemTags extends TsBase {

	const CONTROLLER_NAME = 'tags';

	public $allowedFields = array(
							'filter_name',
							'filter_date_updated',
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
						'defaultSort' => 'tt.name',
						'allowedFields' => $this->allowedFields,
					)
			);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_'.self::CONTROLLER_NAME));

		$this->load->model('ticketsystem/tags');

		$this->getList();
	}

	protected function getList() {
		$data = $this->_construct();

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controllerFile' => 'tags',
						'tplFile' => 'tags_list',
					)
			);

		$data['heading_title'] = $this->language->get('heading_tags');
		$data['text_list'] = $this->language->get('text_list_tags');

		$data = array_merge($this->TsLoader->TsHelper->getSortData(), $data);

		$url = $this->TsLoader->TsHelper->getUrlData('default');

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getAdminBreadcrumbs(
				array(
					$this->language->get('heading_tags') => 'tags',
					)
			);

		$data['delete'] = $this->url->link('ticketsystem/tags/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['tags'] = array();

		$tags_total = $this->model_ticketsystem_tags->getTotalTags();

		$results = $this->model_ticketsystem_tags->getTotalTags();
		
		if($results)
			foreach ($results as $result) {
				$data['tags'][] = array(
					'id' 		 	=> $result['id'],
					'name'       	=> $result['name'],
					'ticketCount'   => $result['ticketCount'],
					'date_updated'  => $this->convertDateFormat($result['date_updated']),
					'ticketsLink'   => $this->url->link('ticketsystem/tickets', 'token=' . $this->session->data['token'] . '&#filter_ttt__tag_id=' . $result['id'] , 'SSL')
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

		$data['sort_name'] = $this->url->link('ticketsystem/tags', 'token=' . $this->session->data['token'] . '&sort=tt.name' . $url, 'SSL');
		$data['sort_date_updated'] = $this->url->link('ticketsystem/tags', 'token=' . $this->session->data['token'] . '&sort=tt.date_updated' . $url, 'SSL');

		$data['resultTotal'] = $tags_total;
		$data['addPagination'] = true;

		$this->response->setOutput($this->TsLoader->TsHelper->loadHtml($data));
	}

	public function delete() {
		$this->load->language('ticketsystem/tags');

		$this->document->setTitle($this->language->get('heading_tags'));

		$this->load->model('ticketsystem/tags');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $id) {
				$this->model_ticketsystem_tags->deleteTag($id);
			}

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_tags'));

			$url = $this->TsLoader->TsHelper->getUrlData('default');

			$this->response->redirect($this->url->link('ticketsystem/tags', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ticketsystem/tags')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}