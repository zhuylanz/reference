<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Tags of HelpDesk module
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemTags extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'name',
						);

	public function getTotalTags(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_tags tt WHERE 1";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getTags($useHelper = true){
		$sql = "SELECT tt.*, (SELECT COUNT(ttt.id) FROM " .DB_PREFIX. "ts_tickets_tags ttt WHERE ttt.tag_id = tt.id) as ticketCount FROM ".DB_PREFIX."ts_tags tt WHERE 1";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();

		return $this->db->query($sql)->rows;
	}

	public function deleteTag($id){
		$this->event->trigger('pre.admin.ts.tag.delete', $id);

		$this->load->model('ticketsystem/tickets');
		$this->model_ticketsystem_tickets->deleteTagById($id);
		
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_tags WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.tag.delete', $id);
	}

	public function addTag($data){
		$this->event->trigger('pre.admin.ts.tag.add', $data);
		
		$this->db->query("INSERT INTO ".DB_PREFIX."ts_tags SET name = '".$this->db->escape($data['name'])."'");

		$this->event->trigger('post.admin.ts.tag.add', $data);

		return $this->db->getLastId();
	}
}