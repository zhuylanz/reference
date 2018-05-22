<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Priority
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemPriority extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'status',
						'priority',
						);

	public function getTotalPriorities(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_ticket_priority tp LEFT JOIN ".DB_PREFIX."ts_ticket_priority_description tpd ON (tp.id = tpd.priority_id) WHERE tpd.language_id = '".$this->config->get('config_language_id')."' ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getPriorities($useHelper = true){
		$sql = "SELECT tp.*,tpd.name, tpd.description FROM ".DB_PREFIX."ts_ticket_priority tp LEFT JOIN ".DB_PREFIX."ts_ticket_priority_description tpd ON (tp.id = tpd.priority_id) WHERE tpd.language_id = '".$this->config->get('config_language_id')."' ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();

		return $this->db->query($sql)->rows;
	}

	public function getPriority($id){
		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_ticket_priority tp WHERE id = '".(int)$id."'")->row;

		if($result)
			$priorityDescription = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_ticket_priority_description tpd WHERE priority_id = '".(int)$id."'")->rows;

			$result['priority'] = array();

			if($priorityDescription)
				foreach ($priorityDescription as $key => $value) {
					$result['priority'][$value['language_id']] = array(
													'name' => $value['name'],
													'description' => $value['description'],
													);
				}

		return $result;
	}

	public function deletePriority($id){
		$this->event->trigger('pre.admin.ts.priority.delete', $id);

		$this->deletePriorityDescription($id);
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_ticket_priority WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.priority.delete', $id);
	}

	public function deletePriorityDescription($id){
		$this->event->trigger('pre.admin.ts.priority.description.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_ticket_priority_description WHERE priority_id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.priority.description.delete', $id);
	}

	public function addPriority($data){
		$this->event->trigger('pre.admin.ts.priority.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_ticket_priority SET status = '".(int)$data['status']."'");

		$priorityId = $this->db->getLastId();

		$this->addPriorityDescription(array_merge($data, array('id' => $priorityId)));

		$this->event->trigger('post.admin.ts.priority.add', $priorityId);
	}

	public function editPriority($data){
		$this->event->trigger('pre.admin.ts.priority.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_ticket_priority SET status = '".(int)$data['status']."', date_updated = NOW() WHERE id='".(int)$data['id']."'");

		$this->deletePriorityDescription($data['id']);

		$this->addPriorityDescription($data);

		$this->event->trigger('post.admin.ts.priority.edit', $data);
	}

	public function addPriorityDescription($data){
		$this->event->trigger('pre.admin.ts.priority.description.add', $data);

		foreach($data['priority'] as $language => $value){
			$this->db->query("INSERT INTO ".DB_PREFIX."ts_ticket_priority_description SET name = '".$this->db->escape($value['name'])."',
										description = '".$this->db->escape($value['description'])."',
										language_id = '".(int)$language."',
										priority_id = '".(int)$data['id']."'
									");
		}

		$this->event->trigger('post.admin.ts.priority.description.add', $data);
	}
}