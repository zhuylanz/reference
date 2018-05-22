<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on HelpDesk Events & Triggers.
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemEvents extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'name',
						'description',
						'performer',
						'events',
						'actions',
						'conditions_all',
						'conditions_one',
						'status',
						);

	public function getTotalEvents(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_events e WHERE 1 ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getEvents($useHelper = true, $data = array()){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_events e WHERE 1 ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();
		elseif($data)
			$sql .= ' AND '. $this->TsLoader->TsHelper->createQueryUsingFields($data);

		return $this->db->query($sql)->rows;
	}

	public function getEvent($id){
		return $this->db->query("SELECT * FROM ".DB_PREFIX."ts_events e WHERE e.id = '".(int)$id."'")->row;
	}

	public function deleteEvent($id){
		$this->event->trigger('pre.admin.ts.events.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_events WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.events.delete', $id);
	}

	public function addEvent($data){
		$this->event->trigger('pre.admin.ts.events.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_events SET name = '".$this->db->escape($data['name'])."',
								description = '".$this->db->escape($data['description'])."',
								actions = '".$this->db->escape(serialize($data['actions']))."',
								events = '".$this->db->escape(serialize($data['events']))."',
								conditions_one = '".$this->db->escape(serialize($data['conditions_one']))."',
								conditions_all = '".$this->db->escape(serialize($data['conditions_all']))."',
								performer = '".$this->db->escape(serialize($data['performer']))."',
								status = '".(int)$data['status']."'
			");

		$eventsId = $this->db->getLastId();
		$this->event->trigger('post.admin.ts.events.add', $eventsId);
	}

	public function editEvent($data){
		$this->event->trigger('pre.admin.ts.events.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_events SET name = '".$this->db->escape($data['name'])."',
								description = '".$this->db->escape($data['description'])."',
								actions = '".$this->db->escape(serialize($data['actions']))."',
								events = '".$this->db->escape(serialize($data['events']))."',
								conditions_one = '".$this->db->escape(serialize($data['conditions_one']))."',
								conditions_all = '".$this->db->escape(serialize($data['conditions_all']))."',
								performer = '".$this->db->escape(serialize($data['performer']))."',
								status = '".(int)$data['status']."',
								date_updated = NOW()
								WHERE id='".(int)$data['id']."'");

		$this->event->trigger('post.admin.ts.events.edit', $data['id']);
	}
}