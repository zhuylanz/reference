<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Rules of HelpDesk module
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemRules extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'name',
						'description',
						'actions',
						'conditions_all',
						'conditions_one',
						'status',
						);

	public function getTotalRules(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_rules r WHERE 1 ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getRules($useHelper = true, $data = array()){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_rules r WHERE 1 ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();
		elseif($data)
			$sql .= ' AND '. $this->TsLoader->TsHelper->createQueryUsingFields($data);

		return $this->db->query($sql)->rows;
	}

	public function getRule($id){
		return $this->db->query("SELECT * FROM ".DB_PREFIX."ts_rules r WHERE r.id = '".(int)$id."'")->row;
	}

	public function deleteRule($id){
		$this->event->trigger('pre.admin.ts.rules.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_rules WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.rules.delete', $id);
	}

	public function addRule($data){
		$this->event->trigger('pre.admin.ts.rules.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_rules SET name = '".$this->db->escape($data['name'])."',
								description = '".$this->db->escape($data['description'])."',
								actions = '".$this->db->escape(serialize($data['actions']))."',
								conditions_one = '".$this->db->escape(serialize($data['conditions_one']))."',
								conditions_all = '".$this->db->escape(serialize($data['conditions_all']))."',
								status = '".(int)$data['status']."'
			");

		$rulesId = $this->db->getLastId();
		$this->event->trigger('post.admin.ts.rules.add', $rulesId);
	}

	public function editRule($data){
		$this->event->trigger('pre.admin.ts.rules.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_rules SET name = '".$this->db->escape($data['name'])."',
								description = '".$this->db->escape($data['description'])."',
								actions = '".$this->db->escape(serialize($data['actions']))."',
								conditions_one = '".$this->db->escape(serialize($data['conditions_one']))."',
								conditions_all = '".$this->db->escape(serialize($data['conditions_all']))."',
								status = '".(int)$data['status']."'
								WHERE id='".(int)$data['id']."'");

		$this->event->trigger('post.admin.ts.rules.edit', $data);
	}
}