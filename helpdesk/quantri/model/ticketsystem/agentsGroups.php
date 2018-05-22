<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Agents Groups of Helpdesk mod.
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemAgentsGroups extends Model {

	public function deleteEntryByAgent($agentId){
		$this->event->trigger('pre.admin.ts.agent.group.delete', $agentId);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_agent_groups WHERE agent_id = '".(int)$agentId."'");

		$this->event->trigger('post.admin.ts.agent.group.delete', $agentId);
	}
	
	public function deleteEntryByGroup($groupId){
		$this->event->trigger('pre.admin.ts.group.agent.delete', $groupId);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_agent_groups WHERE group_id = '".(int)$groupId."'");

		$this->event->trigger('post.admin.ts.group.agent.delete', $groupId);
	}

	/**
	 * Add Entry in Agent Groups based on group
	 * @param array  $data agents
	 * @param interger $id  group
	 */
	public function addAgentsToGroup($data){
		$this->event->trigger('pre.admin.ts.group.agent.add', $data);

		$this->deleteEntryByGroup($data['id']);

		if(isset($data['agents']) AND is_array($data['agents']))
			foreach ($data['agents'] as $value) {
				$this->db->query("INSERT INTO ".DB_PREFIX."ts_agent_groups SET group_id = '".(int)$data['id']."',
								agent_id = '".(int)$value."'
								");
			}

		$this->event->trigger('post.admin.ts.group.agent.add', $data);
	}

	/**
	 * Add Entry in Agent Groups based on Agent
	 * @param array  $data groups
	 * @param interger $id  agent
	 */
	public function addGroupsToAgent($data){
		$this->event->trigger('pre.admin.ts.agent.group.add', $data);

		$this->deleteEntryByAgent($data['id']);

		if(isset($data['groups']) AND is_array($data['groups']))
			foreach ($data['groups'] as $value) {
				$this->db->query("INSERT INTO ".DB_PREFIX."ts_agent_groups SET group_id = '".(int)$value."',
								agent_id = '".(int)$data['id']."'
								");
			}

		$this->event->trigger('post.admin.ts.agent.group.add', $data);
	}

	public function getAgentsGroupByFilter($sql){
		return $this->db->query("SELECT a.id, CONCAT(u.firstname, ' ' , u.lastname, ' - ', u.email) as name,ag.agent_id as agentId, gd.group_id groupid, gd.name as groupname,u.email as agentEmail FROM ".DB_PREFIX."ts_agent_groups ag LEFT JOIN ".DB_PREFIX."ts_group_descriptions gd ON (ag.group_id = gd.group_id) LEFT JOIN ".DB_PREFIX."ts_agents a ON (ag.agent_id = a.id) LEFT JOIN ".DB_PREFIX."user u ON (a.user_id = u.user_id) WHERE $sql AND gd.language_id = '".(int)$this->config->get('config_language_id')."'")->rows;
	}

}