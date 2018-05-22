<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class used to display filter column in Tickets Controller
 */
class ControllerTicketSystemTicketsFilter extends controller {

	public function index() {
		$data = $this->language->load('ticketsystem/all');

		$this->load->model('ticketsystem/status');
		$data['statuss'] = $this->model_ticketsystem_status->getStatuss(false);

		$this->load->model('ticketsystem/types');
		$data['types'] = $this->model_ticketsystem_types->getTypes(false);

		$this->load->model('ticketsystem/groups');
		$data['groups'] = $this->model_ticketsystem_groups->getGroups(false);

		$this->load->model('ticketsystem/agents');
		$data['agents'] = $this->model_ticketsystem_agents->getAgents(false);

		$this->load->model('ticketsystem/priority');
		$data['priorities'] = $this->model_ticketsystem_priority->getPriorities(false);

		$this->load->model('ticketsystem/tags');
		$data['tags'] = $this->model_ticketsystem_tags->getTags(false);

		$this->load->model('ticketsystem/customers');
		$data['customers'] = $this->model_ticketsystem_customers->getCustomers(false);

		$data['source'] = array('web', 'mail');

		return $this->load->view('ticketsystem/ticketsfilter.tpl', $data);
	}

}