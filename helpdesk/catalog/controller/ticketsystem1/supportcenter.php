<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * SupportCenter Class is used to display information categories for customer
 */
class ControllerTicketSystemSupportCenter extends Controller {

	const CONTROLLER_NAME = 'supportcenter';
	const SET_LIMIT = 15;

	public $allowedFields = array(
							'filter_name',
							'filter_date_updated',
							);

	public function index() {
		if(!$this->config->get('ts_status'))
			$this->response->redirect($this->url->link('account/account','','SSL'));
		
		$this->document->addStyle('catalog/view/javascript/ticketsystem/css/ticketsystem/ticketsystem.css');

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'controller' => 'ticketsystem',
						'controllerFile' => 'supportcenter',
						'tplFile' => 'supportcenter',
						'filtetLimit' => $this->config->get('config_product_limit') ? $this->config->get('config_product_limit') : self::SET_LIMIT,
						'defaultSort' => 'tcd.name',
						'allowedFields' => $this->allowedFields,
						'addTsHeader' => (is_array($this->config->get('ts_header')) AND in_array(self::CONTROLLER_NAME, $this->config->get('ts_header'))) ? true : false,
					)
			);

		$data = $this->load->language('ticketsystem/supportcenter');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = $this->TsLoader->TsHelper->getCatalogBreadcrumbs(
				array(
					$this->language->get('heading_title') => 'supportcenter',
					)
			);

		if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];
			unset($this->session->data['error_warning']);
		}else{
			$data['error_warning'] = '';
		}
		
		$data['categories'] = array();

		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/supportcenter'));
		// $this->load->model('ticketsystem/supportcenter');
		$results = $this->model_ticketsystem_supportcenter->getCategories();

		if($results){
			$this->TsLoader->TsHelper->setDefaultValues(
					array(
							'allowedFields' => array('id__title', 'tci__category_id', 'tci__information_id'),
							'filtetLimit' => $this->config->get('ts_information_limit') ? $this->config->get('ts_information_limit') : 3,
							'preLikeInfilterSql' => '%',
							'defaultSort' => 'tci.id',
						)
				);

			foreach($results as $category){
				$informations = $this->model_ticketsystem_supportcenter->getCategoryInformationByFiltering(array('tci__category_id' => $category['id']));
				foreach ($informations as $key => $information) {
					$informations[$key]['href'] = $this->url->link('information/information&information_id='.$information['information_id'], '', 'SSL');
				}

				$data['categories'][] = array(
										'id' => $category['id'],
										'name' => $category['name'],
										'description' => $category['description'],
										'date_updated' => $category['date_updated'],
										'informations' => $informations,
										);
			}
		}

		$this->response->setOutput($this->TsLoader->TsHelper->loadCatalogHtml($data));
	}
}