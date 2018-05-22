<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Header Class is used to display header on Helpdesk conrollers
 */
class ControllerTicketSystemHeader extends Controller {

	public function index($categories) {
		$this->document->addScript('catalog/view/javascript/ticketsystem/scrollspy.js');
		$this->document->addStyle('catalog/view/javascript/ticketsystem/css/ticketsystem/ticketsystem.css');

		$data = $this->load->language('ticketsystem/header');

		$data['categories'] = $categories;
		$data['supportLink'] = $this->url->link('ticketsystem/supportcenter','', 'SSL');
		$data['generateTicketLink'] = $this->url->link('ticketsystem/generatetickets','', 'SSL');
		$data['ticketsLink'] = $this->url->link('ticketsystem/tickets','', 'SSL');

		if($this->customer->getId() OR isset($this->session->data['ts_customer'])){
			$data['text_text_link'] = $this->language->get('text_logout');
			$data['text_link_link'] = $this->url->link('ticketsystem/login/logout','', 'SSL');
		}else{
			$data['text_text_link'] = $this->language->get('text_login');
			$data['text_link_link'] = $this->url->link('ticketsystem/login','', 'SSL');
		}

		if(version_compare(VERSION, '2.1.0.1', '<=')) {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/ticketsystem/header.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/ticketsystem/header.tpl', $data);
			} else {
				return $this->load->view('default/template/ticketsystem/header.tpl', $data);
			}
		}else{
			return $this->load->view('ticketsystem/header', $data);
		}
		
	}

	public function informationAutoComplete(){
		$json = array();

		$this->language->load('ticketsystem/header');

		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/supportcenter'));
		// $this->load->model('ticketsystem/supportcenter');

		$this->TsLoader->TsHelper->setDefaultValues(
					array(
							'allowedFields' => array('id__title', 'tci__category_id', 'tci__information_id'),
							'preLikeInfilterSql' => '%',
							'defaultSort' => 'tci.id',
						)
				);
		
		$informations_info = $this->model_ticketsystem_supportcenter->getCategoryInformationByFiltering($this->request->get);

		if ($informations_info) {
			foreach ($informations_info as $result) {
				$json[] = array(
					'id'        		=> $result['information_id'],
					'title'          	=> $result['title'],
					'description'       => substr(strip_tags(html_entity_decode($result['description'])),0,200),
					'href'        		=> $this->url->link('information/information&information_id='.$result['information_id'],'', 'SSL'),
				);
			}
		}

		if(!$json)
			$json[] = array(
							'title'          	=> $this->language->get('text_no_results'),
							'href'          	=> '',
						);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}