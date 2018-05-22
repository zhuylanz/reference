<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * This class used to add Activity to HelpDesk mod, all functions call this in last
 */
class TsActivity extends TsRegistry{

	/**
	 * Default date format
	 */
	const DATE_FORMAT = 'l jS \of F Y h:i:s A';

	/**
	 * To match controllers
	 */
	const TS_AGENT = 'agents';
	const TS_BUSINESSHOURS = 'businesshours';
	const TS_CUSTOMER = 'customers';
	const TS_TICKET_CUSTOM_FIELDS = 'customfields';
	const TS_EVENTS = 'events';
	const TS_GROUP = 'groups';
	const TS_LEVELS = 'level';
	const TS_ORGANIZATION = 'organizations';
	const TS_PRIORITY = 'priority';
	const TS_RESPONSES = 'responses';
	const TS_ROLE = 'roles';
	const TS_RULES = 'rules';
	const TS_SLA = 'sla';
	const TS_STATUS = 'status';
	const TS_SUPPORTCENTER = 'supportcenter';
	const TS_TICKET = 'tickets';
	const TS_GENERATE_TICKET = 'generatetickets';
	const TS_TYPE = 'types';
	const TS_EMAIL = 'emails';

	/**
	 * To match events
	 */
	const TS_ACTION_DELETE = 'delete';
	const TS_ACTION_ADD = 'add';
	const TS_ACTION_EDIT = 'edit';
	//tickets
	const TS_ACTION_THREAD_ACTION = 'threadActions';

	/**
	 * $agent Agent Info
	 * @var array
	 */
	private $agent;

	/**
	 * setAgent Set Agent Info
	 * @param array $agent
	 */
	public function setAgent($agent){
		$this->agent = $agent;
	}

	/**
	 * convertDateFormat Convert Date format to admin added format
	 */
	protected function convertDateFormat($date = false){
		$offset = 0;
		//update date format based on user
		return date(($this->config->get('ts_date_format') ? $this->config->get('ts_date_format') : self::DATE_FORMAT), (strtotime($date ? $date : date('Y-m-d')) + $offset) );
	}

	/**
	 * initilize Initialize all basic requirement
	 */
	protected function initilize(){
		$TsService = new TsService($this->registry);

		$this->language->load('ticketsystem/activity');
		
		$TsService->model(array('model'=>'ticketsystem/activity'));
		$this->model_ticketsystem_activity = $this->registry->get('model_ticketsystem_activity');
	}

	/**
	 * add Add activity based on passed conditions and register that
	 * @param boolean $data all data related to function
	 * @param boolean $id   Ticket id else it will be in $data
	 */
	public function add($data = false, $id = false) {
		if(!$id AND !is_array($data))
			$id = $data;
		elseif(isset($this->request->post['id']))
			$id = $this->request->post['id'];
		elseif(isset($data['id']))
			$id = $data['id'];

		$this->initilize();

		$route = explode('/', $this->request->get['route']);

		if(!isset($route[2]) AND $route[0]!='ticketsystem')
			return;

		$entryArray = array();

		if(is_array($this->config->get('ts_register_activity')) AND !in_array($route[1],$this->config->get('ts_register_activity')))
			return;

		switch($route[1]){
			/**
			AGENT
			 */
			case self::TS_AGENT:
				switch($route[2]){
					case self::TS_ACTION_DELETE:
						$entryArray = array(
										'type' => self::TS_AGENT. ' - ' .self::TS_ACTION_DELETE,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => $id,
										'activity' => sprintf($this->language->get('text_agent_delete'), $this->agent['username'], $this->request->post['username'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_delete') ? $this->config->get('ts_action_level_delete') : $this->TsLoader->TsHelper->getTsActionLevels('delete'),
										);
						break;
					case self::TS_ACTION_ADD:
						$entryArray = array(
										'type' => self::TS_AGENT. ' - ' .self::TS_ACTION_ADD,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => $id,
										'activity' => sprintf($this->language->get('text_agent_add'), $this->agent['username'], $this->request->post['username'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_add') ? $this->config->get('ts_action_level_add') : $this->TsLoader->TsHelper->getTsActionLevels('add'),
										);
						break;
					case self::TS_ACTION_EDIT:
						$entryArray = array(
										'type' => self::TS_AGENT. ' - ' .self::TS_ACTION_EDIT,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => $id,
										'activity' => sprintf($this->language->get('text_agent_edit'), $this->agent['username'], $this->request->post['username'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_edit') ? $this->config->get('ts_action_level_edit') : $this->TsLoader->TsHelper->getTsActionLevels('edit'),
										);
						break;
				}
				break;

			/**
			GROUPS
			 */
			case self::TS_GROUP:
				switch($route[2]){
					case self::TS_ACTION_DELETE:
						$entryArray = array(
										'type' => self::TS_GROUP. ' - ' .self::TS_ACTION_DELETE,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => false,
										'activity' => sprintf($this->language->get('text_group_delete'), $this->agent['username'], $this->request->post['group'][$this->config->get('config_language_id')]['name'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_delete') ? $this->config->get('ts_action_level_delete') : $this->TsLoader->TsHelper->getTsActionLevels('delete'),
										);
						break;
					case self::TS_ACTION_ADD:
						$entryArray = array(
										'type' => self::TS_GROUP. ' - ' .self::TS_ACTION_ADD,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => false,
										'activity' => sprintf($this->language->get('text_group_add'), $this->agent['username'], $this->request->post['group'][$this->config->get('config_language_id')]['name'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_add') ? $this->config->get('ts_action_level_add') : $this->TsLoader->TsHelper->getTsActionLevels('add'),
										);
						break;
					case self::TS_ACTION_EDIT:
						$entryArray = array(
										'type' => self::TS_GROUP. ' - ' .self::TS_ACTION_EDIT,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => false,
										'activity' => sprintf($this->language->get('text_group_edit'), $this->agent['username'], $this->request->post['group'][$this->config->get('config_language_id')]['name'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_edit') ? $this->config->get('ts_action_level_edit') : $this->TsLoader->TsHelper->getTsActionLevels('edit'),
										);
						break;
				}
				break;

			/**
			ROLES
			 */
			case self::TS_ROLE:
				switch($route[2]){
					case self::TS_ACTION_DELETE:
						$entryArray = array(
										'type' => self::TS_ROLE. ' - ' .self::TS_ACTION_DELETE,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => false,
										'activity' => sprintf($this->language->get('text_role_delete'), $this->agent['username'], $this->request->post['name'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_delete') ? $this->config->get('ts_action_level_delete') : $this->TsLoader->TsHelper->getTsActionLevels('delete'),
										);
						break;
					case self::TS_ACTION_ADD:
						$entryArray = array(
										'type' => self::TS_ROLE. ' - ' .self::TS_ACTION_ADD,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => false,
										'activity' => sprintf($this->language->get('text_role_add'), $this->agent['username'], $this->request->post['name'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_add') ? $this->config->get('ts_action_level_add') : $this->TsLoader->TsHelper->getTsActionLevels('add'),
										);
						break;
					case self::TS_ACTION_EDIT:
						$entryArray = array(
										'type' => self::TS_ROLE. ' - ' .self::TS_ACTION_EDIT,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => false,
										'activity' => sprintf($this->language->get('text_role_edit'), $this->agent['username'], $this->request->post['name'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_edit') ? $this->config->get('ts_action_level_edit') : $this->TsLoader->TsHelper->getTsActionLevels('edit'),
										);
						break;
				}
				break;

			/**
			Tickets
			 */
			case (self::TS_TICKET || self::TS_GENERATE_TICKET):
				switch($route[2]){
					case self::TS_ACTION_DELETE:
						$entryArray = array(
										'type' => self::TS_TICKET. ' - ' .self::TS_ACTION_DELETE,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => !isset($this->agent['affected']) ? false : $this->agent['affected'],
										'activity' => sprintf($this->language->get('text_ticket_delete'), $this->agent['username'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_delete') ? $this->config->get('ts_action_level_delete') : $this->TsLoader->TsHelper->getTsActionLevels('delete'),
										);
						break;
					case self::TS_ACTION_ADD:
						$entryArray = array(
										'type' => self::TS_TICKET. ' - ' .self::TS_ACTION_ADD,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => !isset($this->agent['affected']) ? false : $this->agent['affected'],
										'activity' => sprintf($this->language->get('text_ticket_add'), $this->agent['username'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_add') ? $this->config->get('ts_action_level_add') : $this->TsLoader->TsHelper->getTsActionLevels('add'),
										);
						break;
					case self::TS_ACTION_EDIT:
						$entryArray = array(
										'type' => self::TS_TICKET. ' - ' .self::TS_ACTION_EDIT,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => !isset($this->agent['affected']) ? false : $this->agent['affected'],
										'activity' => sprintf($this->language->get('text_ticket_edit'), $this->agent['username'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_edit') ? $this->config->get('ts_action_level_edit') : $this->TsLoader->TsHelper->getTsActionLevels('edit'),
										);
						break;
					case self::TS_ACTION_THREAD_ACTION:
						$entryArray = array(
										'type' => self::TS_TICKET. ' - ' .self::TS_ACTION_THREAD_ACTION,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => !isset($this->agent['affected']) ? false : $this->agent['affected'],
										'activity' => sprintf($this->language->get('text_ticket_status'), $this->agent['username'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_edit') ? $this->config->get('ts_action_level_edit') : $this->TsLoader->TsHelper->getTsActionLevels('edit'),
										);
						break;
				}
				break;

			/**
			BUSINESSHOURS
			 */
			case self::TS_BUSINESSHOURS:
				switch($route[2]){
					case self::TS_ACTION_DELETE:
						$entryArray = array(
										'type' => self::TS_BUSINESSHOURS. ' - ' .self::TS_ACTION_DELETE,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => false,
										'activity' => sprintf($this->language->get('text_businesshour_delete'), $this->agent['username'], $this->request->post['name'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_delete') ? $this->config->get('ts_action_level_delete') : $this->TsLoader->TsHelper->getTsActionLevels('delete'),
										);
						break;
					case self::TS_ACTION_ADD:
						$entryArray = array(
										'type' => self::TS_BUSINESSHOURS. ' - ' .self::TS_ACTION_ADD,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => false,
										'activity' => sprintf($this->language->get('text_businesshour_add'), $this->agent['username'], $this->request->post['name'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_add') ? $this->config->get('ts_action_level_add') : $this->TsLoader->TsHelper->getTsActionLevels('add'),
										);
						break;
					case self::TS_ACTION_EDIT:
						$entryArray = array(
										'type' => self::TS_BUSINESSHOURS. ' - ' .self::TS_ACTION_EDIT,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => false,
										'activity' => sprintf($this->language->get('text_businesshour_edit'), $this->agent['username'], $this->request->post['name'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_edit') ? $this->config->get('ts_action_level_edit') : $this->TsLoader->TsHelper->getTsActionLevels('edit'),
										);
						break;
				}
				break;

			/**
			TICKET TYPE
			 */
			case self::TS_TYPE:
				switch($route[2]){
					case self::TS_ACTION_DELETE:
						$entryArray = array(
										'type' => self::TS_TYPE. ' - ' .self::TS_ACTION_DELETE,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => false,
										'activity' => sprintf($this->language->get('text_type_delete'), $this->agent['username'], $this->request->post['type'][$this->config->get('config_language_id')]['name'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_delete') ? $this->config->get('ts_action_level_delete') : $this->TsLoader->TsHelper->getTsActionLevels('delete'),
										);
						break;
					case self::TS_ACTION_ADD:
						$entryArray = array(
										'type' => self::TS_TYPE. ' - ' .self::TS_ACTION_ADD,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => false,
										'activity' => sprintf($this->language->get('text_type_add'), $this->agent['username'], $this->request->post['type'][$this->config->get('config_language_id')]['name'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_add') ? $this->config->get('ts_action_level_add') : $this->TsLoader->TsHelper->getTsActionLevels('add'),
										'level' => $this->config->get('ts_action_level_add') ? $this->config->get('ts_action_level_add') : $this->TsLoader->TsHelper->getTsActionLevels('add'),
										);
						break;
					case self::TS_ACTION_EDIT:
						$entryArray = array(
										'type' => self::TS_TYPE. ' - ' .self::TS_ACTION_EDIT,
										'performer' => $this->agent['id'],
										'performertype' => !isset($this->agent['performertype']) ? 'agent' : $this->agent['performertype'],
										'affected' => false,
										'activity' => sprintf($this->language->get('text_type_edit'), $this->agent['username'], $this->request->post['types'][$this->config->get('config_language_id')]['name'], $id, $this->convertDateFormat()),
										'level' => $this->config->get('ts_action_level_edit') ? $this->config->get('ts_action_level_edit') : $this->TsLoader->TsHelper->getTsActionLevels('edit'),
										);
						break;
				}
				break;

			default:
				break;
		}

		$entryArray ? $this->model_ticketsystem_activity->addActivity($entryArray) : false;
	}
}