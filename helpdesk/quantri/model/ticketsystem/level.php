<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on HelpDesk Levels.
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemLevel extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'status',
						'levels',
						);

	public function getTotalLevels(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_agent_level al LEFT JOIN ".DB_PREFIX."ts_agent_level_description ald ON (al.id = ald.level_id) WHERE ald.language_id = '".$this->config->get('config_language_id')."' ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getLevels($useHelper = true){
		$sql = "SELECT al.*,ald.name, ald.description FROM ".DB_PREFIX."ts_agent_level al LEFT JOIN ".DB_PREFIX."ts_agent_level_description ald ON (al.id = ald.level_id) WHERE ald.language_id = '".$this->config->get('config_language_id')."' ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();

		return $this->db->query($sql)->rows;
	}

	public function getLevel($id){
		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_agent_level al WHERE id = '".(int)$id."'")->row;

		if($result)
			$levelDescription = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_agent_level_description ald WHERE level_id = '".(int)$id."'")->rows;

			$result['levels'] = array();

			if($levelDescription)
				foreach ($levelDescription as $key => $value) {
					$result['levels'][$value['language_id']] = array(
													'name' => $value['name'],
													'description' => $value['description'],
													);
				}

		return $result;
	}

	public function deleteLevel($id){
		$this->event->trigger('pre.admin.ts.level.delete', $id);

		$this->deleteLevelDescription($id);
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_agent_level WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.level.delete', $id);
	}

	public function deleteLevelDescription($id){
		$this->event->trigger('pre.admin.ts.level.description.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_agent_level_description WHERE level_id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.level.description.delete', $id);
	}

	public function addLevel($data){
		$this->event->trigger('pre.admin.ts.level.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_agent_level SET status = '".(int)$data['status']."'");

		$levelId = $this->db->getLastId();

		$this->addLevelDescription(array_merge($data, array('id' => $levelId)));

		$this->event->trigger('post.admin.ts.level.add', $levelId);
	}

	public function editLevel($data){
		$this->event->trigger('pre.admin.ts.level.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_agent_level SET status = '".(int)$data['status']."' WHERE id='".(int)$data['id']."'");

		$this->deleteLevelDescription($data['id']);

		$this->addLevelDescription($data);

		$this->event->trigger('post.admin.ts.level.edit', $data);
	}

	public function addLevelDescription($data){
		$this->event->trigger('pre.admin.ts.level.description.add', $data);

		foreach($data['levels'] as $language => $value){
			$this->db->query("INSERT INTO ".DB_PREFIX."ts_agent_level_description SET name = '".$this->db->escape($value['name'])."',
										description = '".$this->db->escape($value['description'])."',
										language_id = '".(int)$language."',
										level_id = '".(int)$data['id']."'
									");
		}

		$this->event->trigger('post.admin.ts.level.description.add', $data);
	}
}