<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on HelpDesk Events & Triggers.
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemGroups extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'automatic_assign',
						'inform_time',
						'inform_agent',
						'group',
						'businesshour_id',
						);

	public function getTotalGroups(){
		$sql = "SELECT g.*,gd.name, gd.description FROM ".DB_PREFIX."ts_groups g LEFT JOIN ".DB_PREFIX."ts_group_descriptions gd ON (g.id = gd.group_id) WHERE gd.language_id = '".(int)$this->config->get('config_language_id')."'";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getGroups($useHelper = true){
		$sql = "SELECT g.*,gd.name, gd.description FROM ".DB_PREFIX."ts_groups g LEFT JOIN ".DB_PREFIX."ts_group_descriptions gd ON (g.id = gd.group_id) WHERE gd.language_id = '".(int)$this->config->get('config_language_id')."'";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();

		return $this->db->query($sql)->rows;
	}

	public function getGroup($id){
		$sql = "SELECT g.* FROM ".DB_PREFIX."ts_groups g WHERE g.id = '".(int)$id."'";

		$group = $this->db->query($sql)->row;

		if($group){
			$groupDescriptions = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_group_descriptions gd WHERE gd.group_id = '".(int)$id."'")->rows;

			if($groupDescriptions)
				foreach($groupDescriptions as $groupDescription){
					$group['group'][$groupDescription['language_id']] = array(
																			'name' => $groupDescription['name'],
																			'description' => $groupDescription['description'],
																		);
				}
			else
				$group['group'] = array();

			$this->TsLoader->TsService->model(array('model'=>'ticketsystem/agentsGroups'));
			$group['agents'] = $this->model_ticketsystem_agentsGroups->getAgentsGroupByFilter("ag.group_id = '".(int)$id."'");
		}

		return $group;
	}

	public function deleteGroup($id){
		$this->event->trigger('pre.admin.ts.group.delete', $id);

		$this->load->model('ticketsystem/agentsGroups');
		$this->model_ticketsystem_agentsGroups->deleteEntryByGroup($id);
		
		$this->deleteGroupDescription($id);
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_groups WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.group.delete', $id);
	}

	public function deleteGroupDescription($id){
		$this->event->trigger('pre.admin.ts.grocup.description.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_group_descriptions WHERE group_id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.group.description.delete', $id);
	}

	public function addGroup($data){
		$this->event->trigger('pre.admin.ts.group.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_groups SET automatic_assign = '".(int)$data['automatic_assign']."',
							inform_time = '".(int)$data['inform_time']."',
							businesshour_id = '".(int)$data['businesshour_id']."',
							inform_agent = '".(int)$data['inform_agent']."'
							");

		$groupId = $this->db->getLastId();

		$this->addDescription(array_merge($data, array('id' => $groupId)));

		$this->load->model('ticketsystem/agentsGroups');
		$this->model_ticketsystem_agentsGroups->addAgentsToGroup(array_merge($data, array('id' => $groupId)));

		$this->event->trigger('post.admin.ts.group.add', $groupId);
	}

	public function editGroup($data){
		$this->event->trigger('pre.admin.ts.group.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_groups SET automatic_assign = '".(int)$data['automatic_assign']."',
							inform_time = '".(int)$data['inform_time']."',
							businesshour_id = '".(int)$data['businesshour_id']."',
							inform_agent = '".(int)$data['inform_agent']."',
							date_updated = NOW()
							WHERE id = '".(int)$data['id']."'
							");

		$this->deleteGroupDescription($data['id']);

		$this->addDescription($data);

		$this->load->model('ticketsystem/agentsGroups');
		$this->model_ticketsystem_agentsGroups->addAgentsToGroup($data);

		$this->event->trigger('post.admin.ts.group.edit', $data);
	}

	public function addDescription($data){
		$this->event->trigger('pre.admin.ts.group.description.add', $data);
		foreach ($data['group'] as $language => $value) {
			$this->db->query("INSERT INTO ".DB_PREFIX."ts_group_descriptions SET group_id = '".(int)$data['id']."',
							name = '".$this->db->escape($value['name'])."',
							description = '".$this->db->escape($value['description'])."',
							language_id = '".(int)$language."'
							");
		}

		$this->event->trigger('post.admin.ts.group.description.add', $data);
	}

}