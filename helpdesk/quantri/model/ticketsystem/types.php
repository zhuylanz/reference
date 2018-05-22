<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Types of HelpDesk module
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemtypes extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'status',
						'types',
						);

	public function getTotalTypes(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_ticket_types tt LEFT JOIN ".DB_PREFIX."ts_ticket_types_description ttd ON (tt.id = ttd.type_id) WHERE ttd.language_id = '".$this->config->get('config_language_id')."' ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getTypes($useHelper = true){
		$sql = "SELECT tt.*,ttd.name, ttd.description FROM ".DB_PREFIX."ts_ticket_types tt LEFT JOIN ".DB_PREFIX."ts_ticket_types_description ttd ON (tt.id = ttd.type_id) WHERE ttd.language_id = '".$this->config->get('config_language_id')."' ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();

		return $this->db->query($sql)->rows;
	}

	public function getType($id){
		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_ticket_types tt WHERE id = '".(int)$id."'")->row;

		if($result)
			$typeDescription = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_ticket_types_description ttd WHERE type_id = '".(int)$id."'")->rows;

			$result['types'] = array();

			if($typeDescription)
				foreach ($typeDescription as $key => $value) {
					$result['types'][$value['language_id']] = array(
													'name' => $value['name'],
													'description' => $value['description'],
													);
				}

		return $result;
	}

	public function deleteType($id){
		$this->event->trigger('pre.admin.ts.type.delete', $id);

		$this->deleteTypeDescription($id);
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_ticket_types WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.type.delete', $id);
	}

	public function deleteTypeDescription($id){
		$this->event->trigger('pre.admin.ts.type.description.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_ticket_types_description WHERE type_id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.type.description.delete', $id);
	}

	public function addType($data){
		$this->event->trigger('pre.admin.ts.type.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_ticket_types SET status = '".(int)$data['status']."'");

		$typeId = $this->db->getLastId();

		$this->addTypeDescription(array_merge($data, array('id' => $typeId) ));

		$this->event->trigger('post.admin.ts.type.add', $typeId);
	}

	public function editType($data){
		$this->event->trigger('pre.admin.ts.type.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_ticket_types SET status = '".(int)$data['status']."' WHERE id='".(int)$data['id']."'");

		$this->deleteTypeDescription($data['id']);

		$this->addTypeDescription($data);

		$this->event->trigger('post.admin.ts.type.edit', $data);
	}

	public function addTypeDescription($data){
		$this->event->trigger('pre.admin.ts.type.description.add', $data);

		foreach($data['types'] as $language => $value){
			$this->db->query("INSERT INTO ".DB_PREFIX."ts_ticket_types_description SET name = '".$this->db->escape($value['name'])."',
										description = '".$this->db->escape($value['description'])."',
										language_id = '".(int)$language."',
										type_id = '".(int)$data['id']."'
									");
		}

		$this->event->trigger('post.admin.ts.type.description.add', $data);
	}

	public function saveCustomFieldType($data){
		$this->db->query("DELETE FROM " . DB_PREFIX . "ts_ticket_types_customfield WHERE custom_field = '".(int)$data['custom_field_id']."'");

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_ticket_types_customfield SET type_id='".(int)$data['type_id']."', custom_field = '".(int)$data['custom_field_id']."'");
	}

	public function getCustomFieldType($id){
		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_ticket_types_customfield ttc WHERE custom_field = '".(int)$id."'")->row;

		if($result)
			return $result['type_id'];
		else
			return false;
	}
}