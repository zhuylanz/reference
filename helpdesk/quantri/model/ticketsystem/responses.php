<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Responses of HelpDesk module
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemResponses extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'name',
						'description',
						'actions',
						'valid_for',
						'status',
						);

	public function getTotalResponses(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_responses r WHERE 1 ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getResponses($useHelper = true, $data = array()){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_responses r WHERE 1 ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();

		if($data)
			$sql .= ' AND '.$this->TsLoader->TsHelper->createQueryUsingFields($data);

		return $this->db->query($sql)->rows;
	}

	public function getResponse($id){
		return $this->db->query("SELECT * FROM ".DB_PREFIX."ts_responses r WHERE r.id = '".(int)$id."'")->row;
	}

	public function deleteResponse($id){
		$this->event->trigger('pre.admin.ts.responses.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_responses WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.responses.delete', $id);
	}

	public function addResponse($data){
		$this->event->trigger('pre.admin.ts.responses.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_responses SET name = '".$this->db->escape($data['name'])."',
								description = '".$this->db->escape($data['description'])."',
								actions = '".$this->db->escape(serialize($data['actions']))."',
								valid_for = '".$this->db->escape(serialize($data['valid_for']))."',
								status = '".(int)$data['valid_for']."'
			");

		$responsesId = $this->db->getLastId();

		$this->event->trigger('post.admin.ts.responses.add', $responsesId);
	}

	public function editResponse($data){
		$this->event->trigger('pre.admin.ts.responses.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_responses SET name = '".$this->db->escape($data['name'])."',
								description = '".$this->db->escape($data['description'])."',
								actions = '".$this->db->escape(serialize($data['actions']))."',
								valid_for = '".$this->db->escape(serialize($data['valid_for']))."',
								date_updated = NOW(),
								status = '".(int)$data['valid_for']."'
								WHERE id='".(int)$data['id']."'");

		$this->event->trigger('post.admin.ts.responses.edit', $data);
	}
}