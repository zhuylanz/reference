<?php
namespace Controller\TicketSystem;

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Class is base class for all/max Helpdesk classes
 * No doubtfully it's main class of HelpDesk mod
 *
 * It's functions are used every where in module
 */
class TsBase extends \Controller {

	/**
	 * Date Format which will display to viewer, if admin not added any
	 */
	const DATE_FORMAT = 'l jS \of F Y h:i:s A';

	/**
	 * $registry Base Opencart Registry
	 * @var array
	 */
	protected $registry;

	/**
	 * $agent Current Agent Details
	 * @var array
	 */
	protected $agent;

	/**
	 * $roles Current Agent Roles (Unserialized)
	 * @var array
	 */
	protected $roles;

	/**
	 * $accessPermissionErrorStatus Show if Agent has Access Permission or not
	 * @var boolean
	 */
	protected $accessPermissionErrorStatus = false;

	/**
	 * $alreadyExecuted If function executed already
	 * @var boolean
	 */
	protected $alreadyExecuted;

	/**
	 * $extendedClassCall calling from Extended class or Any controller is checking agent permission
	 * @var boolean
	 */
	protected $extendedClassCall = false;

	/**
	 * $falsePassedRoute set it to define and we skip error
	 * @var string
	 */
	protected $falsePassedRoute = 'webkul/webkul';

	/**
	 * $error used to store error
	 * @var array
	 */
	protected $error = array();

	/**
	 * $data used to store all data
	 * @var array
	 */
	protected $data = array();

	/**
	 * $base set base path
	 * @var string
	 */
	protected $base = 'ControllerTicketSystem';

	/**
	 * $isAjax Used to know that calling function is ajax or not so that we can display error page or not
	 * @var boolean
	 */
	protected $isAjax;

	/**
	 * $route1 Main controller not folder or any function
	 * @var string
	 */
	protected $route1;
	
	public function __construct($registry) {
		$this->registry = $registry;
		parent::__construct($registry);

		//load basic css
		$this->document->addStyle('view/stylesheet/ticketsystem/bootstrap-select.min.css');
		$this->document->addStyle('view/stylesheet/ticketsystem/ticketsystem.css');

		//load basic js
		$this->document->addScript('view/javascript/ticketsystem/bootstrap-select.js');
		$this->document->addScript('view/javascript/ticketsystem/common.js');

		//add all language to data
		$this->data = array_merge($this->load->language('ticketsystem/all'), $this->data);

		//load basic models
		$this->load->model('tool/image');

		//check access permission
		$this->checkAccessPermission();
		
		$this->registry->set('TsBase', $this);
	}

	/**
	 * checkAccessPermission Function check if current agent has permission to access controller or not and display error
	 * @return boolean or exit
	 */
	public function checkAccessPermission(){
		/**
		 * if module is not enabled than skip error
		 */
		if(!$this->config->get('ts_status')){
			$this->accessPermissionErrorStatus = true;
			return false;
		}

		$this->load->model('ticketsystem/agents');

		$agent = $this->model_ticketsystem_agents->getAgent(array('a.user_id' => $this->user->getId()));

		if(!$agent){
			$this->addErrorResponse();
		}else{
			$this->agent = $agent;

			//set agent timezone
			// date_default_timezone_set($agent['timezone']);

			$access = false;
			$route = $this->extendedClassCall ? explode('/', $this->request->get['route']) : explode('/', $this->falsePassedRoute);

			if($route[1]=='dashboard')
				return;

			$this->route1 = $route[1];

			$roles = $this->model_ticketsystem_agents->getAgentRoles($agent['id']);

			$this->load->model('ticketsystem/roles');
			if($roles)
				foreach ($roles as $key => $role) {
					$roleAccess = $this->model_ticketsystem_roles->getRole($roles[$key]['role_id']);
					$this->roles = $this->agent['roles'] = unserialize($roleAccess['role']);
					if(in_array($route[1], array_keys($this->roles)) || (isset($this->roles['admin']) AND in_array('admin.'.$route[1], $this->roles['admin']))){
						$access = true;
						break;
					}
				}

			if(!$access){
				$this->addErrorResponse();
				return;
			}
		}
	}

	/**
	 * setFalsePassedRoute If permission checking is from outer controller, just checking than set it to false
	 * @param noting $route False Route
	 */
	public function setFalsePassedRoute($route){
		$this->accessPermissionErrorStatus = false;
		$this->falsePassedRoute = $route;
	}

	/**
	 * getRoles Return Agent Roles
	 * @return array Agent Roles
	 */
	public function getRoles(){
		return $this->roles;
	}

	/**
	 * getAccessPermissionErrorStatus If Agent has access permission
	 * @return boolean status
	 */
	public function getAccessPermissionErrorStatus(){
		return $this->accessPermissionErrorStatus;
	}

	/**
	 * checkEventAccessPermission This method check if agent has permission for passed method/event
	 * @param  string $event it method
	 * @return boolean if success else exit from addErrorResponse()
	 */
	public function checkEventAccessPermission($event){
		if(isset($this->roles[$event['key']]) AND in_array($event['event'],$this->roles[$event['key']]))
			return true;

		$this->addErrorResponse();
		return false;
	}

	/**
	 * addErrorResponse Function add Error Page to current controller if Agent don't have access right
	 */
	public function addErrorResponse(){
		$this->accessPermissionErrorStatus = true;

		if(!$this->extendedClassCall || $this->isAjax)
			return false;

		$controller = new \Front($this->registry);
		$response = $this->registry->get('response');
		$controller->dispatch(new \Action('error/permission'), new \Action('error/not_found'));
		$response->output();
		exit;
	}

	/**
	 * convertDateFormat Convert Date Format to Admin define Date Format
	 * @param  boolean/ string $date if not passed than use current date
	 * @return string converted date
	 */
	public function convertDateFormat($date = false){
		$addTime = $this->getDateDifferences();

		//update date format based on user
		return date(($this->config->get('ts_date_format') ? $this->config->get('ts_date_format') : self::DATE_FORMAT), (strtotime(($date & $date!= '00-00-00 00:00:00') ? $date : date('Y-m-d')) + $addTime) );
	}

	/**
	 * getDateDifferences Function used to manage Mysql and Php date difference 
	 * @return integer seconds which is difference between mysql and php
	 */
	public function getDateDifferences($withoutOffset = false){
		// $mysqlTimeZone = $this->db->query("select timediff(now(),convert_tz(now(),@@session.time_zone,'+00:00')) as dateDiff")->row;
		// $mysqlTimeZone = $this->db->query("SELECT IF(@@session.time_zone = 'SYSTEM', @@system_time_zone, @@session.time_zone)")->row;
		$mysqlTimeZone = $this->db->query("SELECT TIMEDIFF(NOW(), UTC_TIMESTAMP) as dateDiff")->row;

		$addTime = 0;
		
		$offset = $this->getAgentOffset();

		if($offset != ($mysqlTimeSeconds = $this->parseDateTime($mysqlTimeZone['dateDiff'])) AND !$withoutOffset)
			$addTime = $offset - $mysqlTimeSeconds;
		else
			$addTime = $mysqlTimeSeconds;

		return $addTime;
	}

	/**
	 * getAgentOffset calculate Agent Timezone and Current Server time difference
	 * @return integer seconds
	 *
	 * @method timezone_offset_get return time difference between timezone_open() and current server timezone in seconds
	 */
	public function getAgentOffset(){
		$offset = 0;
		if($this->agent['timezone'])
			$offset = timezone_offset_get(timezone_open($this->agent['timezone']), new \DateTime());
		return $offset;
	}

	/**
	 * parseDateTime Parse passed time to seconds
	 * @return integer seconds
	 */
	protected function parseDateTime($time){
		$parsed = date_parse($time);
		$seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
		return $seconds;
	}

	/**
	 * getAgentTicketAccessData Function Used to calculate current agent access level base on tickets which is added in his/her groups 
	 * @return array access array which will use in DB query to get tickets
	 */
	protected function getAgentTicketAccessData(){
		$ticketAccess = array();

		if(($scope = $this->TsLoader->TsHelper->getScope($this->agent['scope'])) == 'own'){
			$ticketAccess = array('filter_ta__id' => $this->agent['id']);
		}elseif($scope == 'group'){
			$this->load->model('ticketsystem/agentsGroups');
			$group = $this->model_ticketsystem_agentsGroups->getAgentsGroupByFilter("ag.agent_id = '".(int)$this->agent['id']."'");
			$agents = $newAgents = array();
			if($group){
				if(count($group)>1){
					$sql = '(';
					$newGrp = array();
					foreach ($group as $grp) {
						$newGrp[] = $grp['groupid'];
					}
					$sql .= implode(',',$newGrp).')';
					$agents = $this->model_ticketsystem_agentsGroups->getAgentsGroupByFilter("ag.group_id IN ".$sql."");
				}else
					$agents = $this->model_ticketsystem_agentsGroups->getAgentsGroupByFilter("ag.group_id = '".(int)$group[0]['groupid']."'");
			}
			else
				$ticketAccess = array('filter_ta__id' => $this->agent['id']);

			if($agents){
				foreach ($agents as $agent) {
					$newAgents[$agent['agentId']] = $agent['agentId'];
				}
				$ticketAccess = array('filter_ta__id' => $newAgents);
			}
		}

		return $ticketAccess;
	}
}