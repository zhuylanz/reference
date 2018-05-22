<?php
use Controller\TicketSystem\TsBase;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 *
 * This class is integration of Opencart Custom Fields and Ticket System. We internally called oc default class for our custom fields without guilt ;) so entry added by oc for ticket system will also work.
 * 
 * Basic structure is same as Activity controller, more used function are explained here
 */

class ControllerTicketSystemCustomFields extends TsBase {

	const CONTROLLER_NAME = 'customfields';

	public $allowedFields = array(
							'filter_name',
							'filter_location',
							'filter_type',
							);

	public function __construct($registry){
		$this->registry = $registry;
		$this->extendedClassCall = true;
		parent::__construct($registry);

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'modFolder' => 'ticketsystem',
						'defaultSort' => 'cfd.name',
						'allowedFields' => $this->allowedFields,
					)
			);
	}

	/**
	 * @functions - index, add, edit, delete
	 * These function calls internally Opencart custom field and represent those as Helpdesk
	 * @return html
	 */
	public function index() {
		$this->request->get['filter_location'] = 'tickets';
		
		$controller = new \Front($this->registry);
		$response = $this->registry->get('response');
		if(version_compare(VERSION, '2.0.1.1', '<=')) {
			$controller->dispatch(new \Action('sale/custom_field'), new \Action('error/not_found'));
			$html = $response->getOutput();
			$html = preg_replace('/sale\/custom_field/', 'ticketsystem/customfields', $html);
		}else{
			$controller->dispatch(new \Action('customer/custom_field'), new \Action('error/not_found'));
			$html = $response->getOutput();
			$html = preg_replace('/customer\/custom_field/', 'ticketsystem/customfields', $html);
		}		
		
		//add custom field info
		$html = preg_replace('/<div class="panel-body">/', '<div class="panel-body">'.$this->addHtml(), $html);

		echo $html.$this->addScript();
		exit;
	}

	public function add() {
		$controller = new \Front($this->registry);
		$response = $this->registry->get('response');
		if(version_compare(VERSION, '2.0.1.1', '<=')) {
			$controller->dispatch(new \Action('sale/custom_field/add'), new \Action('error/not_found'));
			$html = $response->getOutput();
			$html = preg_replace('/sale\/custom_field/', 'ticketsystem/customfields', $html);
		}else{
			$controller->dispatch(new \Action('customer/custom_field/add'), new \Action('error/not_found'));
			$html = $response->getOutput();
			$html = preg_replace('/customer\/custom_field/', 'ticketsystem/customfields', $html);
		}
		
		$html = preg_replace('/<option value="account">.*?<\/option>/', '', $html);
		$html = preg_replace('/<option value="address">.*?<\/option>/', '', $html);
		echo $html.$this->addScript();
		exit;
	}

	public function edit() {
		$controller = new \Front($this->registry);
		$response = $this->registry->get('response');
		$controller->dispatch(new \Action('sale/custom_field/edit'), new \Action('error/not_found'));
		
		$html = $response->getOutput();
		$html = preg_replace('/sale\/custom_field/', 'ticketsystem/customfields', $html);
		$html = preg_replace('/<option value="account">.*?<\/option>/', '', $html);
		$html = preg_replace('/<option value="address">.*?<\/option>/', '', $html);
		echo $html.$this->addScript();
		exit;
	}

	public function delete() {
		$controller = new \Front($this->registry);
		$response = $this->registry->get('response');
		$controller->dispatch(new \Action('sale/custom_field/delete'), new \Action('error/not_found'));
		
		$html = $response->getOutput();
		$html = preg_replace('/sale\/custom_field/', 'ticketsystem/customfields', $html);
		echo $html.$this->addScript();
		exit;
	}

	/**
	 * Using this function we added alert info to OC custom field html
	 */
	public function addHtml(){
		$this->language->load('ticketsystem/customfield');
		
		$html = <<<HTML
		<div class="alert alert-info"><i class="fa fa-check-circle"></i> {$this->language->get('text_ts_customefield_info')} <button type="button" class="close" data-dismiss="alert">×</button>
    	</div>
HTML;
 		//it must be in first column of line
		return $html;
	}

 	/**
 	 * it used to remove default custom-field highlight
 	 */
	public function addScript(){
		$script = <<<HTML
		<script>
			$(document).ready(function(){
				$('#sale').removeClass('active').removeClass('open');
				$('#sale ul').removeClass('in');
				$('#sale li').removeClass('active').removeClass('open');
			})
		</script>
HTML;
 		//it must be in first column of line
		return $script;
	}

}