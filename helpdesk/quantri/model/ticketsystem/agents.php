<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Agents of Helpdesk mod.
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemAgents extends Model {

	/**
	 * @method fetchOnlyValues, it converts passed array into index array and return
	 * @$this->TsLoader->TsService->model() Function can loads model from both sides - Admin / Catalog
	 * @params array [model-> Model name, location-> admin/ catalog @default admin]
	 */

	
	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'groups',
						'id',
						'user_id',
						'name_alias',
						'level',
						'timezone',
						'signature',
						'role',
						'scope',
						);

	public function getTotalAgents(){
		$sql = "SELECT a.*,CONCAT(u.firstname,' ',u.lastname) username,u.email,u.image FROM ".DB_PREFIX."ts_agents a LEFT JOIN ".DB_PREFIX."user u ON(a.user_id = u.user_id) WHERE 1 ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getAgents($useHelper = true){
		$sql = "SELECT a.*,CONCAT(u.firstname,' ',u.lastname) username,u.email,u.image FROM ".DB_PREFIX."ts_agents a LEFT JOIN ".DB_PREFIX."user u ON(a.user_id = u.user_id) WHERE 1 ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();

		return $this->db->query($sql)->rows;
	}

	public function getAgent($data){
		$sql = "SELECT a.*,CONCAT(u.firstname,' ',u.lastname) username,u.email,u.image FROM ".DB_PREFIX."ts_agents a LEFT JOIN ".DB_PREFIX."user u ON(a.user_id = u.user_id) LEFT JOIN ".DB_PREFIX."ts_agent_roles ar ON(a.id = ar.agent_id)";

		if($data)
			$sql .= 'WHERE '. $this->TsLoader->TsHelper->createQueryUsingFields($data);

		$result = $this->db->query($sql)->row;

		if($result AND isset($data['a.id'])){
			$result['role'] = TsService::fetchOnlyValues($this->getAgentRoles($data['a.id']));

			$this->TsLoader->TsService->model(array('model'=>'ticketsystem/agentsGroups'));
			$result['groups'] = $this->model_ticketsystem_agentsGroups->getAgentsGroupByFilter("ag.agent_id = '".(int)$data['a.id']."'");
		}

		return $result;
	}

	public function getAgentRoles($id){
		return $this->db->query("SELECT role_id FROM ".DB_PREFIX."ts_agent_roles WHERE agent_id = '".(int)$id."'")->rows;
	}

	public function deleteAgent($id){
		$this->event->trigger('pre.admin.ts.agent.delete', $id);

		$this->load->model('ticketsystem/agentsGroups');
		$this->model_ticketsystem_agentsGroups->deleteEntryByAgent($id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_agents WHERE id = '".(int)$id."'");
		$this->updateAgentRole(array(), $id);

		$this->event->trigger('post.admin.ts.agent.delete', $id);
	}

	public function addAgent($data){
		$this->event->trigger('pre.admin.ts.agent.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_agents SET user_id = '".(int)$data['user_id']."',
							name_alias = '".$this->db->escape($data['name_alias'])."',
							level = '".(int)$data['level']."',
							timezone = '".$this->db->escape($data['timezone'])."',
							signature = '".$this->db->escape($data['signature'])."',
							scope = '".(int)$data['scope']."'
							");

		$agentId = $this->db->getLastId();

		$this->updateAgentRole(array_merge($data, array('id' => $agentId)));

		$this->load->model('ticketsystem/agentsGroups');
		$this->model_ticketsystem_agentsGroups->addGroupsToAgent(array_merge($data, array('id' => $agentId)));

		$this->event->trigger('post.admin.ts.agent.add', $agentId);

	}

	public function editAgent($data){
		$this->event->trigger('pre.admin.ts.agent.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);
		
		$this->db->query("UPDATE ".DB_PREFIX."ts_agents SET user_id = '".(int)$data['user_id']."',
							name_alias = '".$this->db->escape($data['name_alias'])."',
							level = '".(int)$data['level']."',
							timezone = '".$this->db->escape($data['timezone'])."',
							signature = '".$this->db->escape($data['signature'])."',
							scope = '".(int)$data['scope']."',
							date_updated = NOW()
							WHERE id = '".(int)$data['id']."'
							");

		$this->updateAgentRole($data);

		if(is_array($data['groups'])){
			$this->load->model('ticketsystem/agentsGroups');
			$this->model_ticketsystem_agentsGroups->addGroupsToAgent($data);
		}

		$this->event->trigger('post.admin.ts.agent.edit', $data);
	}

	public function updateAgentRole($data){

		$this->event->trigger('pre.admin.ts.agent.role.add', $data);

		$agent_id = $data['id'];

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_agent_roles WHERE agent_id = '".(int)$agent_id."'");

		$this->event->trigger('post.admin.ts.agent.role.delete', $agent_id);

		foreach ($data['role'] as $key => $role) {
			$this->db->query("INSERT INTO ".DB_PREFIX."ts_agent_roles SET agent_id = '".(int)$agent_id."' ,role_id = '".(int)$role."'");
		}

		$this->event->trigger('post.admin.ts.agent.role.add', $data);
	}

}