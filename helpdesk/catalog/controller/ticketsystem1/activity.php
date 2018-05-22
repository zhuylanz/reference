<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Activity Class is used to register Activities on Helpdesk mod
 */
class ControllerTicketSystemActivity extends Controller{

	const NO_CUSTOMER_NAME = 'Unknown';

	private $customerDetails;
	
	public function __construct($registry){		
		parent::__construct($registry);

		if(!$this->config->get('ts_status'))
			return false;

		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/customers'));
		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/tickets'));

		$this->setTsCustomer();
	}

	public function add($data = false, $id = false) {
		if(!$this->config->get('ts_status'))
			return false;

		$affected = false;
		if(isset($data['id']) AND ($ticketDetails = $this->model_ticketsystem_tickets->getTicket(array('t.id' => $data['id'])))){
			$affected = $ticketDetails['assign_agent'];
		}

		$this->TsLoader->TsActivity->setAgent(
				array(
						'id' => isset($this->customerDetails['id']) ? $this->customerDetails['id'] : 0,
						'username' => isset($this->customerDetails['username']) ? $this->customerDetails['username'] : self::NO_CUSTOMER_NAME,
						'performertype' => 'customer',
						'affected' => $affected,
					)
				);

		$this->TsLoader->TsActivity->add($data, $id);
	}

	protected function setTsCustomer(){
		if($this->customer->getEmail()){
			$this->customerDetails = $this->model_ticketsystem_customers->getCustomerByOCId($this->customer->getId());
		}elseif(isset($this->session->data['ts_customer'])){
			$this->customerDetails = $this->model_ticketsystem_customers->getCustomer($this->session->data['ts_customer']['id']);
		}
	}
}