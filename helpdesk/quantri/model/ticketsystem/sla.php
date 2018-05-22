<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Rules of HelpDesk module
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemSla extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'name',
						'description',
						'status',
						'sort_order',
						'conditions_all',
						'conditions_one',
						'priority',
						'respond_violation',
						'resolve_violation',
						);

	public function getTotalSLAs(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_sla s WHERE 1 ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getSLAs($useHelper = true, $data = array()){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_sla s WHERE 1 ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();

		if(isset($data['sort']))
			$sql .= ' ORDER BY '.$data['sort'];

		if(isset($data['order']))
			$sql .= ' '.$data['order'];

		return ($this->db->query($sql)->rows);
	}

	public function getSLA($id){
		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_sla s WHERE s.id = '".(int)$id."'")->row;

		if($result){
			$result['conditions_all'] = unserialize($result['conditions_all']);
			$result['conditions_one'] = unserialize($result['conditions_one']);
			$result['priority'] = $this->getSLAPriority($result['id']);
		}

		return $result;
	}

	public function deleteSLA($id){
		$this->event->trigger('pre.admin.ts.sla.delete', $id);

		$this->deleteSLAPriority($id);
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_sla WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.sla.delete', $id);
	}

	public function addSLA($data){
		$this->event->trigger('pre.admin.ts.sla.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_sla SET name = '".$this->db->escape($data['name'])."',
								description = '".$this->db->escape($data['description'])."',
								status = '".(int)$data['status']."',
								sort_order = '".(int)$data['sort_order']."',
								conditions_all = '".$this->db->escape(serialize($data['conditions_all']))."',
								conditions_one = '".$this->db->escape(serialize($data['conditions_one']))."'
			");

		$slaId = $this->db->getLastId();

		$this->addSLAPriority(array_merge($data, array('id' => $slaId)));

		$this->event->trigger('post.admin.ts.sla.add', $slaId);
	}

	public function editSLA($data){
		$this->event->trigger('pre.admin.ts.sla.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_sla SET name = '".$this->db->escape($data['name'])."',
								description = '".$this->db->escape($data['description'])."',
								status = '".(int)$data['status']."',
								sort_order = '".(int)$data['sort_order']."',
								conditions_all = '".$this->db->escape(serialize($data['conditions_all']))."',
								conditions_one = '".$this->db->escape(serialize($data['conditions_one']))."',
								date_updated = NOW()
								WHERE id='".(int)$data['id']."'");

		$this->deleteSLAPriority($data['id']);
		$this->addSLAPriority($data);

		$this->event->trigger('post.admin.ts.sla.edit', $data);
	}

	public function deleteSLAPriority($slaId){
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_sla_priority WHERE sla_id = '".(int)$slaId."'");
	}

	public function addSLAPriority($data){
		$this->event->trigger('pre.admin.ts.sla.priority.add', $data);

		if(!is_array($data['priority']))
			return;

		foreach($data['priority'] as $key => $value){
			$this->db->query("INSERT INTO ".DB_PREFIX."ts_sla_priority SET sla_id = '".(int)$data['id']."',
									priority_id = '".(int)$key."',
									respond_within = '".$this->db->escape(serialize($value['respond']))."',
									resolve_within = '".$this->db->escape(serialize($value['resolve']))."',
									hours_type = '".(int)$value['hours_type']."',
									status = '".(int)$value['status']."'
				");
		}

		$this->event->trigger('post.admin.ts.sla.priority.add', $data);
	}

	public function getSLAPriority($slaId){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_sla_priority WHERE sla_id = '".(int)$slaId."'";

		$results = $this->db->query($sql)->rows;

		$realResult = array();

		if($results){
			foreach ($results as $key => $result) {
				$results[$key]['resolve'] = unserialize($result['resolve_within']);
				$results[$key]['respond'] = unserialize($result['respond_within']);
				$realResult[$result['priority_id']] = $results[$key];
				unset($results[$key]);
			}
		}

		return $realResult;
	}
}