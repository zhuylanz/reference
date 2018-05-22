<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on HelpDesk Mails.
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemEmails extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'name',
						'description',
						'email',
						'group',
						'priority',
						'type',
						'username',
						'password',
						'hostname',
						'port',
						'mailbox',
						'protocol',
						'fetch_time',
						'email_per_fetch',
						'actions',
						'status',
						);

	public function getTotalEmails(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_emails WHERE 1 ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getEmails($useHelper = true, $data = array()){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_emails WHERE 1 ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();
		elseif($data)
			$sql .= ' AND '.$this->TsLoader->TsHelper->createQueryUsingFields($data);

		return $this->db->query($sql)->rows;
	}

	public function getEmail($id){
		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_emails WHERE id = '".(int)$id."'")->row;

		return $result;
	}

	public function getEmailByEmail($email){
		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_emails WHERE username = '".$this->db->escape($email)."'")->row;

		return $result;
	}

	public function deleteEmail($id){
		$this->event->trigger('pre.admin.ts.email.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_emails WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.email.delete', $id);
	}

	public function addEmail($data){
		$this->event->trigger('pre.admin.ts.email.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_emails SET 
							name = '".$this->db->escape($data['name'])."',
							description = '".$this->db->escape($data['description'])."',
							email = '".$this->db->escape($data['email'])."',
							`group` = '".(int)$data['group']."',
							priority = '".(int)$data['priority']."',
							`type` = '".(int)$data['type']."',
							username = '".$this->db->escape($data['username'])."',
							password = '".$this->db->escape($data['password'])."',
							hostname = '".$this->db->escape($data['hostname'])."',
							mailbox = '".$this->db->escape($data['mailbox'])."',
							port = '".(int)$data['port']."',
							protocol = '".$this->db->escape($data['protocol'])."',
							fetch_time = '".(int)$data['fetch_time']."',
							email_per_fetch = '".(int)$data['email_per_fetch']."',
							actions = '".$this->db->escape(serialize($data['actions']))."',
							status = '".(int)$data['status']."'
				");

		$emailId = $this->db->getLastId();

		$this->event->trigger('post.admin.ts.email.add', $emailId);
	}

	public function editEmail($data){
		$this->event->trigger('pre.admin.ts.email.edit', $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_emails SET 
							name = '".$this->db->escape($data['name'])."',
							description = '".$this->db->escape($data['description'])."',
							email = '".$this->db->escape($data['email'])."',
							`group` = '".(int)$data['group']."',
							priority = '".(int)$data['priority']."',
							`type` = '".(int)$data['type']."',
							username = '".$this->db->escape($data['username'])."',
							password = '".$this->db->escape($data['password'])."',
							hostname = '".$this->db->escape($data['hostname'])."',
							mailbox = '".$this->db->escape($data['mailbox'])."',
							port = '".(int)$data['port']."',
							protocol = '".$this->db->escape($data['protocol'])."',
							fetch_time = '".(int)$data['fetch_time']."',
							email_per_fetch = '".(int)$data['email_per_fetch']."',
							actions = '".$this->db->escape(serialize($data['actions']))."',
							status = '".(int)$data['status']."',
							date_updated = NOW()
							WHERE id='".(int)$data['id']."'");

		$this->event->trigger('post.admin.ts.email.edit', $data);
	}

	/**
	Function used at time of Email Fetching
     */

	/**
	 * Email data 
	 */
	public function checkEmailUidExists($uid){
		return $this->db->query("SELECT * FROM ".DB_PREFIX."ts_ticket_thread_email WHERE uid = '".(int)$uid."'")->row;
	}

	public function checkEmailReferenceExists($reference){
		$singleReference = str_replace(' ',"','", $reference);

		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_ticket_thread_email WHERE message_id IN ('".$singleReference."')")->row;

		return $result ? $result['thread_id'] : null;
	}

	public function addEmailThread($data){
		$this->event->trigger('pre.admin.ts.email.thread.add', $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_ticket_thread_email SET message_id = '".$this->db->escape($data['message_id'])."',
							`email_id` = '".$this->db->escape($data['email_id'])."',
							`references` = '".$this->db->escape($data['references'])."',
							thread_id = '".(int)$data['thread_id']."',
							uid = '".(int)$data['uid']."'
						");
		
		return $result ? $result['thread_id'] : null;

		$this->event->trigger('pre.admin.ts.email.thread.add', $data);
	}

	public function addEmailFetchEntry($data){
		$this->event->trigger('pre.admin.ts.email.fetch.add', $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_email_fetch SET email_id = '".(int)$data['email_id']."',
								fetched_email = '".(int)$data['fetched_email']."'
						");

		$this->event->trigger('pre.admin.ts.email.fetch.add', $data);
	}

	public function getEmailFetchEntry($email_id){
		return $this->db->query("SELECT * FROM ".DB_PREFIX."ts_email_fetch WHERE email_id = '".(int)$email_id."' ORDER BY id DESC LIMIT 1")->row;
	}
}