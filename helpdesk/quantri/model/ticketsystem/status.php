<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Ticket Status of HelpDesk module
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemStatus extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'status',
						'statuss',
						);

	public function getTotalStatuss(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_ticket_status ts LEFT JOIN ".DB_PREFIX."ts_ticket_status_description tsd ON (ts.id = tsd.status_id) WHERE tsd.language_id = '".$this->config->get('config_language_id')."' ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getStatuss($useHelper = true){
		$sql = "SELECT ts.*,tsd.name, tsd.description FROM ".DB_PREFIX."ts_ticket_status ts LEFT JOIN ".DB_PREFIX."ts_ticket_status_description tsd ON (ts.id = tsd.status_id) WHERE tsd.language_id = '".$this->config->get('config_language_id')."' ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();

		return $this->db->query($sql)->rows;
	}

	public function getStatus($id){
		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_ticket_status ts WHERE id = '".(int)$id."'")->row;

		if($result)
			$statusDescription = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_ticket_status_description tsd WHERE status_id = '".(int)$id."'")->rows;

			$result['statuss'] = array();

			if($statusDescription)
				foreach ($statusDescription as $key => $value) {
					$result['statuss'][$value['language_id']] = array(
													'name' => $value['name'],
													'description' => $value['description'],
													);
				}

		return $result;
	}

	public function deleteStatus($id){
		$this->event->trigger('pre.admin.ts.status.delete', $id);

		$this->deleteStatusDescription($id);
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_ticket_status WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.status.delete', $id);
	}

	public function deleteStatusDescription($id){
		$this->event->trigger('pre.admin.ts.status.description.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_ticket_status_description WHERE status_id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.status.description.delete', $id);
	}

	public function addStatus($data){
		$this->event->trigger('pre.admin.ts.status.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_ticket_status SET status = '".(int)$data['status']."'");

		$statusId = $this->db->getLastId();

		$this->addStatusDescription(array_merge($data, array('id' => $statusId)));

		$this->event->trigger('post.admin.ts.status.add', $statusId);
	}

	public function editStatus($data){
		$this->event->trigger('pre.admin.ts.status.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_ticket_status SET status = '".(int)$data['status']."' WHERE id='".(int)$data['id']."'");

		$this->deleteStatusDescription($data['id']);

		$this->addStatusDescription($data);

		$this->event->trigger('post.admin.ts.status.edit', $data);
	}

	public function addStatusDescription($data){
		$this->event->trigger('pre.admin.ts.status.description.add', $data);

		foreach($data['statuss'] as $language => $value){
			$this->db->query("INSERT INTO ".DB_PREFIX."ts_ticket_status_description SET name = '".$this->db->escape($value['name'])."',
										description = '".$this->db->escape($value['description'])."',
										language_id = '".(int)$language."',
										status_id = '".(int)$data['id']."'
									");
		}

		$this->event->trigger('post.admin.ts.status.description.add', $data);
	}
}