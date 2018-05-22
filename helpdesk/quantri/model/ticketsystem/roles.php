<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Roles of HelpDesk module
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemRoles extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'name',
						'description',
						'roles',
						);

	public function getTotalRoles(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_roles r WHERE 1 ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getRoles($useHelper = true){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_roles r WHERE 1 ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();

		return $this->db->query($sql)->rows;
	}

	public function getRole($id){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_roles r WHERE id = '".(int)$id."'";

		return $this->db->query($sql)->row;
	}

	public function deleteRole($id){
		$this->event->trigger('pre.admin.ts.role.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_roles WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.role.delete', $id);
	}

	public function addRole($data){
		$this->event->trigger('pre.admin.ts.role.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$sql = "INSERT INTO ".DB_PREFIX."ts_roles SET name = '".$this->db->escape($data['name'])."',
							description = '".$this->db->escape($data['description'])."',
							role = '".$this->db->escape(serialize($data['roles']))."'
							";

		$this->db->query($sql);

		$roleId = $this->db->getLastId();

		$this->event->trigger('post.admin.ts.role.add', $roleId);
	}

	public function editRole($data){
		$this->event->trigger('pre.admin.ts.role.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$sql = "UPDATE ".DB_PREFIX."ts_roles SET name = '".$this->db->escape($data['name'])."',
							description = '".$this->db->escape($data['description'])."',
							role = '".$this->db->escape(serialize($data['roles']))."',
							date_updated = NOW()
							WHERE id = '".(int)$data['id']."'
							";

		$this->db->query($sql);

		$this->event->trigger('post.admin.ts.role.edit', $data);
	}

}