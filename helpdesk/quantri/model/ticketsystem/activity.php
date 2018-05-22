<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Activities of Helpdesk mod
 */
class ModelTicketSystemActivity extends Model {

	/**
	 * @method fetchOnlyValues, it converts passed array into index array and return
	 * @$this->TsLoader->TsService->model() Function can loads model from both sides - Admin / Catalog
	 * @params array [model-> Model name, location-> admin/ catalog @default admin]
	 */
	
	
	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'type',
						'performer',
						'performertype',
						'affected',
						'activity',
						'level',
						);
	/**
	 * @method getFilterData, function return query based on GET values
	 * @method createQueryUsingFields, function return query based on passed $data array
	 * @param  array  $data if we want to build query manually
	 */
	public function getTotalActivities($data = array()){
		$sql = "SELECT a.*,u.username FROM ".DB_PREFIX."ts_activity a LEFT JOIN ".DB_PREFIX."ts_agents ta ON (a.performer = ta.id) LEFT JOIN ".DB_PREFIX."user u ON (ta.user_id = u.user_id) WHERE 1 ";
		
		if(!$data)
			$sql .= $this->TsLoader->TsHelper->getFilterData(false);
		else
			$sql .= ' AND '.$this->TsLoader->TsHelper->createQueryUsingFields($data);

		return count($this->db->query($sql)->rows);
	}

	public function getActivities($useHelper = true, $data = array()){
		$sql = "SELECT a.*,CONCAT(u.firstname,' ',u.lastname) as username,u.email, tc.name as customerName, tc.email as customerEmail FROM ".DB_PREFIX."ts_activity a LEFT JOIN ".DB_PREFIX."ts_agents ta ON (a.performer = ta.id AND a.performertype='agent') LEFT JOIN ".DB_PREFIX."user u ON (ta.user_id = u.user_id) LEFT JOIN ".DB_PREFIX."ts_customers tc ON (a.performer = tc.id AND a.performertype='customer') WHERE 1 ";

		$this->TsLoader->TsHelper->setDefaultValues(
				array(
						'preLikeInfilterSql' => '%',
					)
			);

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData(true, true);
		elseif($data)
			$sql .= ' AND '.$this->TsLoader->TsHelper->createQueryUsingFields($data);

		return $this->db->query($sql)->rows;
	}

	public function getActivity($id){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_activity a WHERE id = '".(int)$id."'";

		return $this->db->query($sql)->row;
	}

	public function deleteActivity($id){
		$this->event->trigger('pre.admin.ts.activity.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_activity WHERE id = '".(int)$id."'");

		$this->event->trigger('pre.admin.ts.activity.delete', $id);
	}

	public function addActivity($data){
		$this->event->trigger('pre.admin.ts.activity.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_activity SET type = '".$this->db->escape($data['type'])."',
							performer = '".(int)$data['performer']."',
							performertype = '".$this->db->escape($data['performertype'])."',
							affected = '".(int)$data['affected']."',
							activity = '".$this->db->escape($data['activity'])."',
							level = '".$this->db->escape($data['level'])."'
							");

		$id = $this->db->getLastId();

		$this->event->trigger('post.admin.ts.activity.add', $id);
	}
}