<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on HelpDesk Mails Templates.
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemEmailTemplates extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'name',
						'message',
						'status',
						);

	public function getTotalEmailTemplates(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_emailtemplates WHERE 1 ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getEmailTemplates($useHelper = true, $data = array()){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_emailtemplates WHERE 1 ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();
		elseif($data)
			$sql .= ' AND '.$this->TsLoader->TsHelper->createQueryUsingFields($data);

		return $this->db->query($sql)->rows;
	}

	public function getEmailTemplate($id){
		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_emailtemplates WHERE id = '".(int)$id."'")->row;

		return $result;
	}

	public function deleteEmailTemplate($id){
		$this->event->trigger('pre.admin.ts.emailtemplates.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_emailtemplates WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.emailtemplates.delete', $id);
	}

	public function addEmailTemplate($data){
		$this->event->trigger('pre.admin.ts.emailtemplates.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_emailtemplates SET 
							name = '".$this->db->escape($data['name'])."',
							message = '".$this->db->escape($data['message'])."',
							status = '".(int)$data['status']."'
				");

		$EmailTemplateId = $this->db->getLastId();

		$this->event->trigger('post.admin.ts.emailtemplates.add', $EmailTemplateId);
	}

	public function editEmailTemplate($data){
		$this->event->trigger('pre.admin.ts.emailtemplates.edit', $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_emailtemplates SET 
							name = '".$this->db->escape($data['name'])."',
							message = '".$this->db->escape($data['message'])."',
							status = '".(int)$data['status']."',
							date_updated = NOW()
							WHERE id='".(int)$data['id']."'");

		$this->event->trigger('post.admin.ts.emailtemplates.edit', $data);
	}
}