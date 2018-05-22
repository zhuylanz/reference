<?php
/**
 * validate html of ticket and remove extra tags
 */
require_once(DIR_SYSTEM.'library/ticketsystem/HtmlFilter/htmlfilter.php');

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Tickets of Helpdesk mod.
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemTickets extends Model {

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
						'id' => 0,
						'customer_id' => 0,
						'subject' => 0,
						'message' => 0,
						'provider' => 0,
						'priority' => 0,
						'status' => 0,
						'tickettype' => 0,
						'group' => 0,
						'agent' => 0,
						'custom_field' => array(),
						);

	/**
	 * setDefaultData Function set default Ticket values to Ticket data if not set by sender
	 */
	public function setDefaultData(&$data){
		if(is_array($this->config->get('ts_ticket_default'))){
			if(!$data['priority'])
				$data['priority'] = $this->config->get('ts_ticket_default')['priority'];
			if(!$data['status'])
				$data['status'] = $this->config->get('ts_ticket_default')['status'];
			if(!$data['tickettype'])
				$data['tickettype'] = $this->config->get('ts_ticket_default')['type'];
			if(!$data['group'])
				$data['group'] = $this->config->get('ts_ticket_default')['group'];
		}
	}

	/**
	 * @method getFilterData, function return query based on GET values
	 * @method createQueryUsingFields, function return query based on passed $data array
	 * @param  array  $data if we want to build query manually
	 */
	public function getTotalTickets(){
		$sql = "SELECT DISTINCT t.id,t.*,ttd.name as typeName,tsd.name as statusName,tpd.name as priorityName,tgd.name as groupName FROM ".DB_PREFIX."ts_tickets t LEFT JOIN ". DB_PREFIX ."ts_ticket_types_description ttd ON(t.type = ttd.type_id) LEFT JOIN ". DB_PREFIX ."ts_ticket_status_description tsd ON(t.status = tsd.status_id) LEFT JOIN ". DB_PREFIX ."ts_ticket_priority_description tpd ON(t.priority = tpd.priority_id) LEFT JOIN ". DB_PREFIX ."ts_group_descriptions tgd ON(t.group = tgd.group_id) LEFT JOIN ". DB_PREFIX ."ts_agents ta ON(t.assign_agent = ta.id) LEFT JOIN ".DB_PREFIX."ts_tickets_tags ttt ON(t.id = ttt.ticket_id) WHERE 1 ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false, true);

		return count($this->db->query($sql)->rows);
	}
	
	public function getTickets($useHelper = true){
		$sql = "SELECT DISTINCT t.id,t.*,ttd.name as typeName,tsd.name as statusName,tpd.name as priorityName,tgd.name as groupName, ta.name_alias as agentAliasName,CONCAT(u.firstname,' ',u.lastname) agentName, tc.name as customerName, tc.email as customerEmail,tts.response_time, tts.resolve_time FROM ".DB_PREFIX."ts_tickets t LEFT JOIN ". DB_PREFIX ."ts_ticket_types_description ttd ON(t.type = ttd.type_id) LEFT JOIN ". DB_PREFIX ."ts_ticket_status_description tsd ON(t.status = tsd.status_id) LEFT JOIN ". DB_PREFIX ."ts_ticket_priority_description tpd ON(t.priority = tpd.priority_id) LEFT JOIN ". DB_PREFIX ."ts_group_descriptions tgd ON(t.group = tgd.group_id) LEFT JOIN ". DB_PREFIX ."ts_agents ta ON(t.assign_agent = ta.id) LEFT JOIN ". DB_PREFIX ."ts_customers tc ON(t.customer_id = tc.id)  LEFT JOIN ".DB_PREFIX."user u ON(ta.user_id = u.user_id) LEFT JOIN ".DB_PREFIX."ts_tickets_tags ttt ON(t.id = ttt.ticket_id) LEFT JOIN ".DB_PREFIX."ts_ticket_sla tts ON(t.id = tts.ticket_id) WHERE 1 ";

		if($useHelper){
			$sql .= $this->TsLoader->TsHelper->getFilterData(true, true);
		}

		return $this->db->query($sql)->rows;
	}

	public function getTicket($data = array()){
		$sql = "SELECT t.*,tc.name as customerName,tc.email as customerEmail,torg.name as organizationName,torg.domain as organizationDomain,tsd.name as statusName,tpd.name as priorityName,tgd.name as groupName,ttd.name as typeName, ta.name_alias as agentAliasName,CONCAT(u.firstname,' ',u.lastname) agentName,u.email as agentEmail FROM ".DB_PREFIX."ts_tickets t LEFT JOIN ".DB_PREFIX."ts_customers tc ON(t.customer_id = tc.id) LEFT JOIN ".DB_PREFIX."ts_organization_customers toc ON(tc.id = toc.customer_id) LEFT JOIN ".DB_PREFIX."ts_organizations torg ON(toc.organization_id = torg.id) LEFT JOIN ".DB_PREFIX."ts_ticket_status_description tsd ON(t.status = tsd.status_id) LEFT JOIN ". DB_PREFIX ."ts_ticket_priority_description tpd ON(t.priority = tpd.priority_id) LEFT JOIN ". DB_PREFIX ."ts_group_descriptions tgd ON(t.group = tgd.group_id) LEFT JOIN ". DB_PREFIX ."ts_ticket_types_description ttd ON(t.type = ttd.type_id) LEFT JOIN ". DB_PREFIX ."ts_agents ta ON(t.assign_agent = ta.id) LEFT JOIN ".DB_PREFIX."user u ON(ta.user_id = u.user_id) WHERE 1 ";

		if($data)
			$sql .= 'AND '. $this->TsLoader->TsHelper->createQueryUsingFields($data);

		$result = $this->db->query($sql)->row;

		if($result){
			$result['tags'] = TsService::fetchOnlyValues($this->db->query("SELECT ttt.tag_id, tt.name FROM " .DB_PREFIX. "ts_tickets_tags ttt LEFT JOIN ".DB_PREFIX."ts_tags tt ON(ttt.tag_id = tt.id) WHERE ttt.ticket_id = '".(int)$result['id']."'")->rows);
			$result['sla'] = $this->getTicketSLA($data);
		}
		
		return $result;
	}

	public function getTicketSLA($data){
		return $this->db->query("SELECT * FROM ".DB_PREFIX."ts_ticket_sla where ticket_id = '".(int)$data['t.id']."'")->row;
	}

	public function getTicketDrafts($data){
		return $this->db->query("SELECT * FROM ".DB_PREFIX."ts_tickets_drafts where agent_id = '".(int)$data['agent_id']."' AND	ticket_id = '".(int)$data['ticket_id']."'")->rows;
	}

	public function getTicketViewAgent($ticket_id){
		return $this->db->query("SELECT * FROM ".DB_PREFIX."ts_tickets_locks WHERE ticket_id = '".(int)$ticket_id."'")->rows;
	}

	public function getTicketNotes($data = array()){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_notes tn WHERE 1 ";

		if($data)
			$sql .= ' AND '. $this->TsLoader->TsHelper->createQueryUsingFields($data);

		return $this->db->query($sql)->rows;
	}

	public function getTicketTagById($data = array()){
		return $this->db->query("SELECT * FROM ".DB_PREFIX."ts_tickets_tags WHERE ticket_id = '".(int)$data['ticket_id']."' AND tag_id = '".(int)$data['tag_id']."'")->row;
	}

	public function getTicketReceiver($data){
		return $this->db->query("SELECT * FROM ".DB_PREFIX."ts_ticket_receivers WHERE ticket_id = '".(int)$data['ticket_id']."'")->rows;
	}

	public function getTotalTicketThreads($data = array()){
		$sql = "SELECT ttt.*,tc.name as customerName,ta.name_alias as agentAliasName,CONCAT(u.firstname,' ',u.lastname) agentName FROM ".DB_PREFIX."ts_tickets_threads ttt LEFT JOIN ".DB_PREFIX."ts_customers tc ON(ttt.sender_id = tc.id AND ttt.sender_type = 'customer') LEFT JOIN ".DB_PREFIX."ts_agents ta ON(ttt.sender_id = ta.id AND ttt.sender_type = 'agent') LEFT JOIN ".DB_PREFIX."user u ON(ta.user_id = u.user_id) WHERE 1 ";

		$implode = array();

		if(isset($data['ticket_id']))
			$implode[] = "ttt.ticket_id = '".(int)$data['ticket_id']."'";

		if(isset($data['type']))
			$implode[] = "ttt.type = '".$this->db->escape($data['type'])."'";

		if(isset($data['ticket_before_id']))
			$implode[] = "ttt.id < ".(int)$data['ticket_before_id']."";

		if(isset($data['thread_id']))
			$implode[] = "ttt.id = ".(int)$data['thread_id']."";

		$sql .= $implode ? (' AND '.implode(' AND ', $implode)." ORDER BY ttt.id DESC ") : false;

		return count($this->db->query($sql)->rows);
	}

	public function getTicketThreads($data = array()){
		$sql = "SELECT ttt.*,tc.name as customerName,ta.name_alias as agentAliasName,CONCAT(u.firstname,' ',u.lastname) agentName, u.image as agentImage FROM ".DB_PREFIX."ts_tickets_threads ttt LEFT JOIN ".DB_PREFIX."ts_customers tc ON(ttt.sender_id = tc.id AND ttt.sender_type = 'customer') LEFT JOIN ".DB_PREFIX."ts_agents ta ON(ttt.sender_id = ta.id AND ttt.sender_type = 'agent') LEFT JOIN ".DB_PREFIX."user u ON(ta.user_id = u.user_id) WHERE 1 ";

		$implode = array();

		if(isset($data['ticket_id']))
			$implode[] = "ttt.ticket_id = '".(int)$data['ticket_id']."'";

		if(isset($data['type']))
			$implode[] = "ttt.type = '".$this->db->escape($data['type'])."'";

		if(isset($data['ticket_before_id']))
			$implode[] = "ttt.id < ".(int)$data['ticket_before_id']."";

		if(isset($data['thread_id']))
			$implode[] = "ttt.id = ".(int)$data['thread_id']."";

		$sql .= $implode ? (' AND '.implode(' AND ', $implode)." ORDER BY ttt.id DESC ") : false;

		$limit = ($this->config->get('ts_ticket_view_limit') ? $this->config->get('ts_ticket_view_limit') : 5);

		if(isset($data['start']))
			$sql .= " LIMIT ".(int)$data['start'].",".$limit;
		else
			$sql .= " LIMIT 0,".$limit;

		return $this->db->query($sql)->rows;
	}

	public function getTicketThreadAttachments($id){
		return $this->db->query("SELECT ta.*,tta.thread_id FROM ".DB_PREFIX."ts_tickets_attachments tta LEFT JOIN " .DB_PREFIX. "ts_attachments ta ON(tta.attachment_id = ta.id) WHERE tta.thread_id = '".(int)$id."'")->rows;
	}

	public function getTicketThreadReceivers($id){
		return $this->db->query("SELECT * FROM ".DB_PREFIX."ts_thread_receivers WHERE thread_id = '".(int)$id."'")->row;
	}

	/**
	Delete
	 */	
	public function deleteTicket($id){
		//for TsEvents
		$this->TsLoader->TsEvents->checkEventsOnRequest(array('event' => 'deleted', 'data' =>$id ));

		$this->event->trigger('pre.admin.ts.ticket.delete', $id);

		//remove ticket threads with attachments
		foreach ($this->getTicketThreads(array('ticket_id'=>$id)) as $thread) {
			$this->deleteTicketThread($thread['id']);
			if(is_dir(DIR_IMAGE.'helpdesk/'.$id.'/'.$thread['id']))
				@rmdir(DIR_IMAGE.'helpdesk/'.$id.'/'.$thread['id']);
		}
		$this->deleteTicketNotes($id);
		$this->deleteTicketTag($id);
		$this->deleteTicketSLA($id);
		$this->deleteTicketDrafts(array('ticket_id' => $id));
		$this->deleteTicketAgentViewEntry(array('ticket_id' => $id));
		$this->deleteAgentTicket(array('ticket_id' => $id));

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_tickets WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.ticket.delete', $id);
	}

	public function deleteTicketThread($id){
		$this->event->trigger('pre.admin.ts.ticket.thread.delete', $id);

		$this->deleteTicketThreadReceivers($id);
		$this->deleteTicketThreadAttachmentsEntry($id);
		$this->deleteTicketThreadEmailEntry($id);
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_tickets_threads WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.ticket.thread.delete', $id);
	}

	public function deleteTicketThreadAttachmentsEntry($id){
		$ticketAttachments = $this->getTicketThreadAttachments($id);

		if($ticketAttachments)
			foreach($ticketAttachments as $ticketAttachment){
				$path = DIR_IMAGE.$ticketAttachment['path'].$ticketAttachment['fakename'];
				if(file_exists($path)){
					@unlink($path);
					@unlink(DIR_IMAGE.$ticketAttachment['path']);
				}
				$this->deleteTicketThreadAttachments($ticketAttachment['id']);
			}
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_tickets_attachments WHERE thread_id = '".(int)$id."'");
	}

	public function deleteTicketSLA($id){
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_ticket_sla WHERE ticket_id = '".(int)$id."'");
	}

	public function deleteTicketThreadAttachments($id){
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_attachments WHERE id = '".(int)$id."'");
	}

	public function deleteTicketNotes($id){
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_notes WHERE ticket_id = '".(int)$id."'");
	}

	public function deleteTicketNotesById($id){
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_notes WHERE id = '".(int)$id."'");
	}

	public function deleteTicketTag($id){
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_tickets_tags WHERE ticket_id = '".(int)$id."'");
	}

	public function deleteTagById($id){
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_tickets_tags WHERE tag_id = '".(int)$id."'");
	}

	public function deleteTicketReceivers($id){
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_tickets_receivers WHERE ticket_id = '".(int)$id."'");
	}

	public function deleteTicketThreadReceivers($id){
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_thread_receivers WHERE thread_id = '".(int)$id."'");
	}

	public function deleteTicketThreadEmailEntry($id){
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_ticket_thread_email WHERE thread_id = '".(int)$id."'");
	}
	
	/**
	 * Use Single Time in all app else it will calculate buggy data, which will be hard to find
	 * like time() is now manipulating with mysql NOW(), so wrong calculation is showing, $this->TsBase->getDateDifferences() corrected issue
	 */
	public function clearExtraViewers($data){
		foreach ($this->getTicketViewAgent($data['ticket_id']) as $viewer) {
			if(strtotime($viewer['date_expire']) < (strtotime(date('Y-m-d H:i:s') + $this->TsBase->getDateDifferences()))){
				$this->deleteTicketAgentViewEntry($data);
			}
		}
	}

	public function deleteTicketAgentViewEntry($data){
		$this->event->trigger('pre.admin.ts.ticket.lock.delete', $data);
		
		$sql = "DELETE FROM ".DB_PREFIX."ts_tickets_locks WHERE 1 ";

		$implode = array();

		if(isset($data['ticket_id']))
			$implode[] = "ticket_id = '".(int)$data['ticket_id']."'";

		if(isset($data['agent_id']))
			$implode[] = "agent_id = '".(int)$data['agent_id']."'";

		$sql .=  $implode ? (' AND '.implode(' AND ', $implode)) : false;

		$this->db->query($sql);

		$this->event->trigger('pre.admin.ts.ticket.lock.delete', $data);
	}

	public function deleteTicketDrafts($data){
		$this->event->trigger('pre.admin.ts.ticket.draft.delete', $data);
		
		$sql = "DELETE FROM ".DB_PREFIX."ts_tickets_drafts WHERE 1 ";

		$implode = array();

		if(isset($data['ticket_id']))
			$implode[] = "ticket_id = '".(int)$data['ticket_id']."'";

		if(isset($data['agent_id']))
			$implode[] = "agent_id = '".(int)$data['agent_id']."'";

		if(isset($data['type']))
			$implode[] = "`type` = '".$this->db->escape($data['type'])."'";
	
		$sql .=  $implode ? (' AND '.implode(' AND ', $implode)) : false;

		$this->db->query($sql);

		$this->event->trigger('post.admin.ts.ticket.draft.delete', $data);
	}

	public function deleteAgentTicket($data){
		$this->event->trigger('pre.admin.ts.agent.ticket.delete', $data);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_ticket_agent_created WHERE ticket_id = '".(int)$data['ticket_id']."'");

		$this->event->trigger('post.admin.ts.agent.ticket.delete', $data);
	}


	/**
	Add / Update
	 */	
public function addTicket($data){
	
		$this->event->trigger('pre.admin.ts.ticket.add', $data);

		$data = array_merge($this->allowedColums, $data);
		$this->setDefaultData($data);

		/**
		 *  Add assign ticket code based on group of ticket.
		 */
		$entryInUnassignTable = false;
		if($data['group'] AND !$data['agent']){
			$this->TsLoader->TsService->model(array('model' => 'ticketsystem/agentsGroups'));
			$agents = $this->model_ticketsystem_agentsGroups->getAgentsGroupByFilter("ag.group_id = '".(int)$data['group']."'");

			$agentTicket = array();

			foreach ($agents as $key => $agent) {
				$count = $this->db->query("SELECT count(t.id) count FROM ".DB_PREFIX."ts_tickets t WHERE t.assign_agent = '".(int)$agent['id']."' AND t.status NOT IN (".(is_array($this->config->get('ts_ticket_status')) ? ($this->config->get('ts_ticket_status')['closed'].','.$this->config->get('ts_ticket_status')['spam']) : 0).")")->row;

				if($count)
					$agentTicket[$agent['id']] = $count['count'];
			}

			if($agentTicket)
				$data['agent'] = current(array_keys($agentTicket, min($agentTicket)));
			else{
				$entryInUnassignTable = true;
			}
		}

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_tickets SET customer_id = '".(int)$data['customer_id']."',
							`subject` = '".$this->db->escape($this->removeParentTagsFromMessage($data['subject']))."',
							`provider` = '".$this->db->escape($data['provider'])."',
							`priority` = '".(int)$data['priority']."',
							`status` = '".(int)$data['status']."',
							`type` = '".(int)$data['tickettype']."',
							`group` = '".(int)$data['group']."',
							`assign_agent` = '".(int)$data['agent']."',
							`custom_field` = '".$this->db->escape(serialize($data['custom_field']))."'
							");

		$ticketId = $this->db->getLastId();

		if($ticketId && isset($data['receivers']) && $data['receivers']) {
			$data['ticket_id'] = $ticketId;
			$data['to'] = array($data['receivers']['to']);
			$data['cc'] = array();
			$data['bcc'] = array();
			$this->addReceiverEntry($data);
		}
		

		/**
		 *  If ticket is not assigned to any agent than add it's entry to a table to send information mail to respected agent.
		 */
		if($entryInUnassignTable)
			$this->db->query("INSERT INTO ".DB_PREFIX."ts_ticket_unassigned SET ticket_id = '".(int)$ticketId."', group_id = '".$data['group']."'");

		$thread_id = $data['thread_id'] = $this->addTicketThread(array_merge($data, array('id' => $ticketId)), true);

		//for TsEvents
		$this->TsLoader->TsEvents->checkEventsOnRequest(array('event' => 'created', 'data' => array_merge($data, array('id' => $ticketId))));
		
		$this->event->trigger('post.admin.ts.ticket.add', $ticketId);

		return $thread_id;
	}

	public function addReceiverEntry($data = array()){
		if(isset($data['ticket_id']) && $data['ticket_id']){

			$getTicketId = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_ticket_receivers WHERE ticket_id = '".(int)$data['ticket_id']."' ")->row;
			if(isset($getTicketId) && $getTicketId){
				$this->db->query("UPDATE ".DB_PREFIX."ts_ticket_receivers SET `to` = '".$this->db->escape(serialize($data['to']))."', `cc` = '".$this->db->escape(serialize($data['cc']))."', `bcc` = '".$this->db->escape(serialize($data['bcc']))."' WHERE ticket_id = '".(int)$data['ticket_id']."'");

			}else{
				$this->addTicketReceiver($data);
			}
		}
	}

	public function checkEmailTicketReferenceExists($data = false){
		$getTicketId = explode(' ', $data);
		
		if(isset($getTicketId[0]) && $getTicketId[0]){
			$ticket_id = intval(preg_replace('/[^0-9]+/', '', $getTicketId[0]), 10);
			
			$result = $this->db->query("SELECT id FROM ".DB_PREFIX."ts_tickets WHERE id = '".(int)$ticket_id."'")->row;
			if(isset($result) && $result){
				return $result['id'];
			}else{
				return null;
			}
		}
	}

	public function addTicketThread($data, $conditionCheck = false){
		//convert placeholder to their respective values
		$data['message'] = $this->convertPlaceHoldersToData($data);

		$this->event->trigger('pre.admin.ts.ticket.thread.add', $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_tickets_threads SET ticket_id = '".(int)$data['id']."',
							message = '".$this->db->escape($data['message'])."',
							sender_id = '".(int)$data['agent_id']."',
							sender_type = '".$this->db->escape($data['sender_type'])."',
							type = '".$data['messagetype']."'
							");

		$ticketThreadId = $this->db->getLastId();

		$this->deleteTicketDrafts(array('ticket_id' => $data['id'], 'type' => $data['messagetype'], 'agent_id' => $data['agent_id']));

		$data = array_merge($data, array('thread_id' => $ticketThreadId));

		if((isset($data['receivers']) AND $data['receivers'])){
			$this->addTicketThreadRecievers($data);
		}

		if($this->request->files)
			foreach($this->request->files as $files){
				if(!$files['name'] || !$files['tmp_name'])
					continue;

				$file = $files['name'] . '.' . md5(mt_rand());

				$path = 'helpdesk/'.$data['id'].'/'.$ticketThreadId.'/';
				if(!file_exists(DIR_IMAGE.$path))
					mkdir(DIR_IMAGE.$path,0777,true);

				move_uploaded_file($files['tmp_name'], DIR_IMAGE.$path . $file);

				$attachmentData = array(
									'name' => $files['name'],
									'fakename' => $file,
									'size' => $files['size'],
									'mime' => $files['type'],
									'path' => $path,
									);
				$attachmentId = $this->addTicketThreadAttachments($attachmentData);

				$attachmentEntryData = array(
											'ticket_id' => $data['id'],
											'thread_id' => $ticketThreadId,
											'attachment_id' => $attachmentId,
										);
				$this->addTicketThreadAttachmentsEntry($attachmentEntryData);
			}

		if(isset($data['checkAttachment']) AND isset($this->request->post['forward']['attachment']) AND $this->request->post['forward']['attachment']){
			foreach ($this->request->post['forward']['attachment'] as $attachmentId) {
				$attachmentEntryData = array(
											'ticket_id' => $data['id'],
											'thread_id' => $ticketThreadId,
											'attachment_id' => $attachmentId,
										);
				$this->addTicketThreadAttachmentsEntry($attachmentEntryData);
			}
		}

		/**
		 * for TsEvents
		 * This call check events for this function call and work any any match found
		 */
		$this->TsLoader->TsEvents->checkEventsOnRequest(array('event' => 'message', 'data' => $data));
		
		/**
		 * Ticket Rule Code
		 */
		if($conditionCheck){
			$this->TsLoader->TsService->model(array('model'=>'ticketsystem/rules'));
			$rules = $this->model_ticketsystem_rules->getRules(false, array('status' => 1));
			$this->TsLoader->TsConditions->checkConditionsOnRequest(array('conditions' => $rules,'ticket_id' => $data['id'], 'thread_id' => $ticketThreadId));
		}

		/**
		 * for TsSLA 
		 * This call check SLA for this Ticket and Thread
		 */
		$this->TsLoader->TsSLA->manageSLA($data);

		$this->event->trigger('post.admin.ts.ticket.thread.add', $data);

		return $ticketThreadId;
	}

	/**
	 * to replace placeholder function need only ticket id
	 * @param  array $data array with message
	 * @return update message        
	 */
	public function convertPlaceHoldersToData($data){
		$ticketPlaceHolders = $this->TsLoader->TsTicket->ticketPlaceHolder;

		$placeHolders = array();
		foreach ($ticketPlaceHolders as $ticketPlaceHolder) {
			$placeHolders[] = "{{ticket.$ticketPlaceHolder}}";
		}

		$ticketData = $this->getTicket(array('t.id' => $data['id']));

		if(!$ticketData)
			return;

		$threadData = $this->getTicketThreads(array('t.ticket_id' => $data['id'], 'type' => 'create'));

		$replaceArray = array(
							'{{ticket.id}}' => $ticketData['id'],
							'{{ticket.subject}}' => $ticketData['subject'],
							'{{ticket.description}}' => isset($threadData['message']) ? $threadData['message']: '',
							'{{ticket.threaddescription}}' => isset($data['threaddescription']) ? $data['threaddescription']: '',
							'{{ticket.tags}}' => implode(', ', $ticketData['tags']),
							'{{ticket.notes}}' => implode(', ', TsService::fetchOnlyValues($this->getTicketNotes(array('ticket_id' => $ticketData['id'])))),
							'{{ticket.groupname}}' => $ticketData['groupName'],
							'{{ticket.agentname}}' => $ticketData['agentAliasName'] ? $ticketData['agentAliasName'] : $ticketData['agentAliasName'],
							'{{ticket.agentemail}}' => $ticketData['agentEmail'],
							'{{ticket.source}}' => $ticketData['provider'],
							'{{ticket.status}}' => $ticketData['statusName'],
							'{{ticket.priority}}' => $ticketData['priorityName'],
							'{{ticket.tickettype}}' => $ticketData['typeName'],
							'{{ticket.customername}}' => $ticketData['customerName'],
							'{{ticket.customeremail}}' => $ticketData['customerEmail'],
							'{{ticket.organization}}' => $ticketData['organizationName'],
						);

		return str_replace($placeHolders, $replaceArray, $this->removeParentTagsFromMessage($data['message']));
	}

	/**
	 * Remove Html, Body Tag from message
	 * @return message string
	 */
	public function removeParentTagsFromMessage($string){
		//function is in including file --> see top
		$string = HTMLFilter($string, '');
		return $string;

		//use DOMDocument to get body elements
		$dom = new DOMDocument;
		$dom->loadHTML($string);
		$bodies = $dom->getElementsByTagName('body');
		assert($bodies->length === 1);
		$newString = '';
		foreach ($bodies as $body) {
		     $newString .= $body->nodeValue;
		}
		return $newString;
	}

	public function addTicketThreadRecievers($data){
		$this->event->trigger('pre.admin.ts.thread.receivers.add', $data);

		$this->db->query("INSERT INTO " .DB_PREFIX. "ts_thread_receivers SET thread_id = '".(int)$data['thread_id']."',
							 receivers = '".$this->db->escape(serialize($data['receivers']))."',
							 type = '".$this->db->escape($data['messagetype'])."'
							 ");

		$this->event->trigger('pre.admin.ts.thread.receivers.add', $data);
	}

	public function addTicketThreadAttachments($data){
		$this->db->query("INSERT INTO ".DB_PREFIX."ts_attachments SET name = '".$this->db->escape($data['name'])."',
							fakename = '".$this->db->escape($data['fakename'])."',
							size = '".(int)$data['size']."',
							mime = '".$this->db->escape($data['mime'])."',
							path = '".$this->db->escape($data['path'])."'
							");

		return $this->db->getLastId();
	}

	public function addTicketThreadAttachmentsEntry($data){
		$this->db->query("INSERT INTO ".DB_PREFIX."ts_tickets_attachments SET thread_id = '".(int)$data['thread_id']."',
							attachment_id = '".(int)$data['attachment_id']."'
							");
	}

	public function addTicketNotes($data = array()){
		$this->event->trigger('pre.admin.ts.ticket.notes.add', $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_notes SET ticket_id = '".(int)$data['id']."', agent_id = '".(int)$data['agent_id']."', `note` = '".$this->db->escape($data['value'])."'");

		$this->event->trigger('post.admin.ts.ticket.notes.add', $data);

		return $this->db->getLastId();
	}

	public function updateTicketNotes($data = array()){
		$this->event->trigger('pre.admin.ts.ticket.notes.update', $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_notes SET `completed` = '".$this->db->escape($data['value'])."' WHERE ticket_id = '".(int)$data['ticket_id']."' AND agent_id = '".(int)$data['agent_id']."' AND id = '".(int)$data['id']."'");

		$this->event->trigger('post.admin.ts.ticket.notes.update', $data);
	}

	public function addTicketTags($data = array()){
		$this->event->trigger('pre.admin.ts.ticket.tags.add', $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_tickets_tags SET ticket_id = '".(int)$data['ticket_id']."', tag_id = '".(int)$data['tag_id']."'");

		$this->event->trigger('post.admin.ts.ticket.tags.add', $data);

		return $this->db->getLastId();
	}

	public function addTicketReceiver($data){
		$this->event->trigger('pre.admin.ts.ticket.add.receivers', $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_ticket_receivers SET `to` = '".$this->db->escape(serialize($data['to']))."', `cc` = '".$this->db->escape(serialize($data['cc']))."', `bcc` = '".$this->db->escape(serialize($data['bcc']))."', ticket_id = '".(int)$data['ticket_id']."'");

		$this->event->trigger('post.admin.ts.ticket.add.receivers', $data);
	}

	public function updateTicketReceiver($data){
		if(!$data)
			return;

		$this->event->trigger('pre.admin.ts.ticket.update.receivers', $data);

		$sql = "UPDATE ".DB_PREFIX."ts_ticket_receivers ";

		$implode = array();
		if(isset($data['cc']))
		 	$implode[] = "`cc` = '".$this->db->escape(serialize($data['cc']))."'";

		if(isset($data['bcc'])) 
		 	$implode[] = "`bcc` = '".$this->db->escape(serialize($data['bcc']))."'";

		$sql .= $implode ? ("SET ".implode(',', $implode)) : false ." WHERE ticket_id = '".(int)$data['ticket_id']."'";

		$this->db->query($sql);

		$this->event->trigger('post.admin.ts.ticket.update.receivers', $data);
	}

	public function copyTicket($ticket_id){
		$this->event->trigger('pre.admin.ts.ticket.copy', $ticket_id);

		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_tickets WHERE id = '".(int)$ticket_id."'")->row;

		$ticketId = false;

		if($result){
			$this->db->query("INSERT INTO ".DB_PREFIX."ts_tickets SET customer_id = '".(int)$result['customer_id']."',
								subject = '".$this->db->escape($result['subject'])."',
								provider = '".(int)$result['provider']."',
								priority = '".(int)$result['priority']."',
								status = '".(int)$result['status']."',
								type = '".(int)$result['type']."',
								`group` = '".(int)$result['group']."',
								assign_agent = '".(int)$result['assign_agent']."',
								custom_field = '".$this->db->escape($result['custom_field'])."'
							");
			$ticketId = $this->db->getLastId();
		}

		$this->event->trigger('post.admin.ts.ticket.copy', $ticketId);
		
		return $ticketId;
	}

	/**
	 * used to update ticket, basically it's used to update
	 * @param  column and values of ticket $data 
	 * @return none
	 */
	public function editTicket(array $data){
		if(!$data)
			return;

		//for TsEvents
		$this->TsLoader->TsEvents->checkEventsOnRequest(array('event' => 'updated', 'data' => $data));

		$this->event->trigger('pre.admin.ts.ticket.edit', $data);
		
		$sql = "UPDATE ".DB_PREFIX."ts_tickets SET ";
		
		$implode = array();

		foreach ($data as $value) {
			if((int)$value['value'])
				$implode[] = "`".$value['column']."`". "='".(int)$value['value']."' ";
			else
				$implode[] = "`".$value['column']."`". "='".$this->db->escape($value['value'])."' ";
		}

		$sql .= implode(',', $implode). " , date_updated = NOW() WHERE id = '".(int)$value['id']."'";

		$this->callSLAForTicket($data);
		$this->db->query($sql);

		$this->event->trigger('post.admin.ts.ticket.edit', $data);
	}

	public function updateTicketThread($data){
		$this->event->trigger('pre.admin.ts.update.ticket.thread', $data);
		
		$this->db->query("UPDATE ".DB_PREFIX."ts_tickets_threads SET ticket_id = '".(int)$data['ticket_id']."', type = 'create' WHERE id = '".(int)$data['thread_id']."'");

		$this->event->trigger('post.admin.ts.update.ticket.thread', $data);
	}

	public function updateTicketThreadByTicket($data){
		$this->event->trigger('pre.admin.ts.update.ticket.thread.byTicket', $data);
		
		$this->db->query("UPDATE ".DB_PREFIX."ts_tickets_threads SET ticket_id = '".(int)$data['ticket_id']."' WHERE ticket_id = '".(int)$data['old_ticket_id']."' AND `type`!='create'");

		$this->event->trigger('post.admin.ts.update.ticket.thread.byTicket', $data);
	}

	public function updateTicketDrafts($data){
		$this->event->trigger('pre.admin.ts.ticket.draft.add', $data);

		$this->deleteTicketDrafts($data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_tickets_drafts SET agent_id = '".(int)$data['agent_id']."',
							ticket_id = '".(int)$data['ticket_id']."',
							message = '".$this->db->escape($data['message'])."',
							type = '".$this->db->escape($data['type'])."'
						");

		$this->event->trigger('post.admin.ts.ticket.draft.add', $data);
	}

	public function addTicketViewAgent($data){
		$this->event->trigger('pre.admin.ts.ticket.lock.add', $data);

		$this->clearExtraViewers($data);
		$this->deleteTicketAgentViewEntry($data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_tickets_locks SET agent_id = '".(int)$data['agent_id']."',
							ticket_id = '".(int)$data['ticket_id']."',
							date_expire = ADDTIME(NOW(), '".($this->config->get('ts_ticket_view_expire_time') ? $this->config->get('ts_ticket_view_expire_time') : '1:00:00.00')."')");

		$this->event->trigger('post.admin.ts.ticket.lock.add', $data);
	}

	public function addAgentTicket($data){
		$this->event->trigger('pre.admin.ts.agent.ticket.add', $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_ticket_agent_created SET agent_id = '".(int)$data['agent_id']."',
							ticket_id = '".(int)$data['ticket_id']."'
						");

		$this->event->trigger('post.admin.ts.agent.ticket.add', $data);
	}

	/**
	 * addTicketSLA Function add SLA entry for Ticket
	 * @param array $data - [response_time-> Complete String with DATEADD(NOW() and time for SLA) <-resolve_time]
	 */
	public function addTicketSLA($data){
		// $this->db->query("INSERT INTO ".DB_PREFIX."ts_ticket_sla SET ticket_id = '".(int)$data['ticket_id']."', response_time = DATE_ADD(NOW(),INTERVAL ".$data['response_time']."), resolve_time = DATE_ADD(NOW(),INTERVAL ".$data['resolve_time'].")");
		$this->db->query("INSERT INTO ".DB_PREFIX."ts_ticket_sla SET ticket_id = '".(int)$data['ticket_id']."', response_time = ".$data['response_time'].", resolve_time = ".$data['resolve_time']."");
	}

	public function updateTicketSLA($data){
		$sql = "UPDATE ".DB_PREFIX."ts_ticket_sla SET ";

		$impolde = array();

		if(isset($data['response_time']))
			$impolde[] = "response_time = ".$data['response_time']."";

		if(isset($data['resolve_time']))
			$impolde[] = "resolve_time = ".$data['resolve_time']."";

		$sql .= implode(', ',$impolde)." WHERE ticket_id = '".(int)$data['ticket_id']."'";

		$this->db->query($sql);
	}

	/**
	 * callSLAForTicket If Ticket is updated this function call and check is it resolved or not
	 * @param  array $data Ticket data -> Ticket Id
	 */
	public function callSLAForTicket($data){
		$slaData = array();

		$configResolved = is_array($this->config->get('ts_ticket_status')) ? $this->config->get('ts_ticket_status')['solved'] : 0;
		if(!$configResolved)
			return;
		//sent from action function
		if(isset($data['status']) AND $data['status']==$configResolved){
			$slaData = $data;
		//sent from edit function
		}elseif(isset($data[0])){ 
			foreach($data as $value){
				if($value['column']=='status' AND $value['value']==$configResolved){
					$slaData = array(
									'ticket_id' => $value['id'],
									'thread_id' => 0,
								);
				}
			}
		}

		//for TsSLA
		if($slaData)
			// $this->TsLoader->TsSLA->manageSLA($slaData);
			//or
			//delete ticket from sla table beacuse resolved
			$this->deleteTicketSLA($slaData['ticket_id']);
	}




	/**
	Apply Actions 
	*These functions are called from Library TsAction class
	*/
	public function actionTicketPriority($data){
		$this->event->trigger('pre.admin.ts.ticket.action.priority', $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_tickets SET `priority` = '".(int)$data['priority']."' WHERE id = '".(int)$data['ticket_id']."'");

		$this->event->trigger('post.admin.ts.ticket.action.priority', $data);
	}

	public function actionTicketType($data){
		$this->event->trigger('pre.admin.ts.ticket.action.type', $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_tickets SET `type` = '".(int)$data['type']."' WHERE id = '".(int)$data['ticket_id']."'");

		$this->event->trigger('post.admin.ts.ticket.action.type', $data);
	}

	public function actionTicketStatus($data){
		$this->event->trigger('pre.admin.ts.ticket.action.status', $data);

		$this->callSLAForTicket($data);
		$this->db->query("UPDATE ".DB_PREFIX."ts_tickets SET `status` = '".(int)$data['status']."' WHERE id = '".(int)$data['ticket_id']."'");

		$this->event->trigger('post.admin.ts.ticket.action.status', $data);
	}

	public function actionTicketTag($data){
		$this->event->trigger('pre.admin.ts.ticket.action.tag', $data);

		if($data['tag'])
			foreach ($data['tag'] as $tag) {
				if(!$this->getTicketTagById(array('ticket_id' => $data['ticket_id'], 'tag_id' => $tag)))
					$this->addTicketTags(array('ticket_id' => $data['ticket_id'], 'tag_id' => $tag));
			}

		$this->event->trigger('post.admin.ts.ticket.action.tag', $data);
	}

	public function actionTicketReceiverCC($data){
		$this->event->trigger('pre.admin.ts.ticket.action.receivers.cc', $data);

		if($this->getTicketReceiver($data)){
			$this->updateTicketReceiver($data);
		}else{
			$this->addTicketReceiver(array_merge($data, array('bcc' => array())));
		}

		$this->event->trigger('post.admin.ts.ticket.action.receivers.cc', $data);
	}

	public function actionTicketReceiverBCC($data){
		$this->event->trigger('pre.admin.ts.ticket.action.receivers.bcc', $data);

		if($this->getTicketReceiver($data)){
			$this->updateTicketReceiver($data);
		}else{
			$this->addTicketReceiver(array_merge($data, array('cc' => array())));
		}

		$this->event->trigger('post.admin.ts.ticket.action.receivers.bcc', $data);
	}

	public function actionTicketNote($data){
		$this->event->trigger('pre.admin.ts.ticket.action.note', $data);

		$this->addTicketThread($data);

		$this->event->trigger('post.admin.ts.ticket.action.note', $data);
	}

	public function actionTicketAgent($data){
		$this->event->trigger('pre.admin.ts.ticket.action.agent', $data);

		if(isset($this->user) AND $this->user->getId() AND $data['agent_id']=='current'){
			if($agent = $this->model_ticketsystem_agents->getAgent(array('a.user_id' => $this->user->getId()))){
				$data['agent_id'] = $agent['email'];
			}
		}

		$this->db->query("UPDATE ".DB_PREFIX."ts_tickets SET `assign_agent` = '".(int)$data['agent_id']."' WHERE id = '".(int)$data['ticket_id']."'");

		$this->event->trigger('post.admin.ts.ticket.action.agent', $data);
	}

	public function actionTicketGroup($data){
		$this->event->trigger('pre.admin.ts.ticket.action.group', $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_tickets SET `group` = '".(int)$data['group']."' WHERE id = '".(int)$data['ticket_id']."'");

		$this->event->trigger('post.admin.ts.ticket.action.group', $data);
	}

	/**
	 * actionTicketSendMailToCustomer Send mail to Customer
	 * @param  array $data Ticket Details
	 */
	public function actionTicketSendMailToCustomer($data){
		$this->event->trigger('pre.admin.ts.ticket.action.sendMailToCustomer', $data);

		//load mail sender model and send mail from here
		if($data['mail_customer']['emailtemplate']){
			$this->TsLoader->TsService->model(array('model'=>'ticketsystem/emailtemplates'));
			$mailData = $this->model_ticketsystem_emailtemplates->getEmailTemplate($data['mail_customer']['emailtemplate']);
			if(!$mailData)
				return;
			$subject = $mailData['name'];
			$message = $mailData['message'];
		}else{
			$subject = $data['mail_customer']['subject'];
			$message = $data['mail_customer']['message'];
		}

		$thread_id = isset($data['thread_id']) ? $data['thread_id'] : 0;

		$ticketData = $this->getTicket(array('t.id' => $data['ticket_id']));
		$threadData = $this->getTicketThreads(array('thread_id' => $thread_id));

		$ticketReceivers = $this->getTicketReceiver(array('ticket_id' => $data['ticket_id']));

		$to = $cc = $bcc = $attachments = array();
		if($ticketReceivers){
			foreach($ticketReceivers as $ticketReceiver){
				if($arrayTo = unserialize($ticketReceiver['to']))
					$to = array_merge($to, $arrayTo);
				if($arrayCC = unserialize($ticketReceiver['cc']))
					$cc = array_merge($cc, $arrayCC);
				if($arrayBCC = unserialize($ticketReceiver['bcc']))
					$bcc = array_merge($bcc, $arrayBCC);
			}
		}

		$threaddescription = '';
		if($threadData){
			$threaddescription = $threadData[0]['message'];
			$ticketThreadRecievers = $this->getTicketThreadReceivers($thread_id);
			if($ticketThreadRecievers AND ($threadRecievers = unserialize($ticketThreadRecievers['receivers'])))
				foreach($threadRecievers as $kind => $threadReciever){
					if(is_array($threadReciever))
						$$kind = array_merge($$kind, $threadReciever);
				}

			$ticketThreadAttachments = $this->getTicketThreadAttachments($thread_id);

			if($ticketThreadAttachments)
				foreach($ticketThreadAttachments as $ticketThreadAttachment){
					if(file_exists($attachment = DIR_IMAGE.$ticketThreadAttachment['path'].$ticketThreadAttachment['fakename']))
						$attachments[] = array(
												'attachment' => $attachment,
												'name' => $ticketThreadAttachment['name']
											);
				}
		}

		$customHeaders =  array(
								array(
									'name' => 'wTicketId',
									'value' => $data['ticket_id'],
									),
								array(
									'name' => 'helpDeskReply',
									'value' => 1,
									)
							);
		if ($this->config->get('config_mail_protocol') == 'mail') {
			$setFrom = array('email' => $this->config->get('config_email'), 'name' => $this->config->get('config_name'));
		} else {
			$setFrom = array('email' => $this->config->get('config_mail_smtp_username'), 'name' => $this->config->get('config_name'));
		}
		
		$replyTo = array('email' => $this->config->get('config_email'), 'name' => $this->config->get('config_name'));

		if($ticketEmailData = $this->getTicketEmailMessageId($data['ticket_id'])){			
			$customHeaders[] = array(
									'name' => 'References',
									'value' => $ticketEmailData[0]['message_id'],
								);
			$replyTo = 	array(
							array(
								'email' => $ticketEmailData[0]['username'], 
								'name' => $this->config->get('config_name')
							)
						);
		}

		/**
		 * @call TsEmail class to send mail (Based on PHPMailer)
		 * @method sendMail
		 */
		$this->TsLoader->TsEmail->sendMail(
						array(
							'subject' => $this->convertPlaceHoldersToData(array('id' => $data['ticket_id'], 'message' => $subject, 'threaddescription' => $threaddescription)),
							'message' => $this->convertPlaceHoldersToData(array('id' => $data['ticket_id'], 'message' => $message, 'threaddescription' => $threaddescription)),
							'address' => $to,
							'cc' => $cc,
							'bcc' => $bcc,
							'setFrom' => $setFrom,
							'replyTo' => $replyTo,
							'attachments' => $attachments,
							'customHeaders' => $customHeaders,
						)
					);
		
		$this->event->trigger('post.admin.ts.ticket.action.sendMailToCustomer', $data);
	}

	/**
	 * actionTicketSendMailToAgent Send mail to Agent
	 * @param  array $data Ticket Details
	 */
	public function actionTicketSendMailToAgent($data){
		$this->event->trigger('pre.admin.ts.ticket.action.sendMailToAgent', $data);

		//load mail sender model and send mail from here
		if($data['mail_agent']['emailtemplate']){
			$this->TsLoader->TsService->model(array('model'=>'ticketsystem/emailtemplates'));
			$mailData = $this->model_ticketsystem_emailtemplates->getEmailTemplate($data['mail_agent']['emailtemplate']);
			if(!$mailData)
				return;
			$subject = $mailData['name'];
			$message = $mailData['message'];
		}else{
			$subject = $data['mail_agent']['subject'];
			$message = $data['mail_agent']['message'];
		}

		$thread_id = isset($data['thread_id']) ? $data['thread_id'] : 0;

		$ticketData = $this->getTicket(array('t.id' => $data['ticket_id']));
		$threadData = $this->getTicketThreads(array('thread_id' => $thread_id));

		$ticketReceivers = $this->getTicketReceiver(array('ticket_id' => $data['ticket_id']));

		$to = $cc = $bcc = $attachments = array();
		if($ticketReceivers){
			foreach($ticketReceivers as $ticketReceiver){
				if($arrayTo = unserialize($ticketReceiver['to']))
					$to = array_merge($to, $arrayTo);
				if($arrayCC = unserialize($ticketReceiver['cc']))
					$cc = array_merge($cc, $arrayCC);
				if($arrayBCC = unserialize($ticketReceiver['bcc']))
					$bcc = array_merge($bcc, $arrayBCC);
			}
		}

		$threaddescription = '';
		if($threadData){
			$threaddescription = $threadData[0]['message'];
			$ticketThreadRecievers = $this->getTicketThreadReceivers($thread_id);
			if($ticketThreadRecievers AND ($threadRecievers = unserialize($ticketThreadRecievers['receivers'])))
				foreach($threadRecievers as $kind => $threadReciever){
					if(is_array($threadReciever))
						$$kind = array_merge($$kind, $threadReciever);
				}

			$ticketThreadAttachments = $this->getTicketThreadAttachments($thread_id);

			if($ticketThreadAttachments)
				foreach($ticketThreadAttachments as $ticketThreadAttachment){
					if(file_exists($attachment = DIR_IMAGE.$ticketThreadAttachment['path'].$ticketThreadAttachment['fakename']))
						$attachments[] = array(
												'attachment' => $attachment,
												'name' => $ticketThreadAttachment['name']
											);
				}
		}

		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/agents'));
		if($mailAgent = $this->model_ticketsystem_agents->getAgent(array('a.id' => (int)$data['mail_agent']['agent']))){
			$to = array_merge($to, array($mailAgent['email']));
		}elseif(isset($this->user) AND $this->user->getId() AND $data['mail_agent']['agent']=='current'){
			if($mailAgent = $this->model_ticketsystem_agents->getAgent(array('a.user_id' => $this->user->getId()))){
				$to = array_merge($to, array($mailAgent['email']));
			}
		}

		if ($this->config->get('config_mail_protocol') == 'mail') {
			$setFrom = array('email' => $this->config->get('config_email'), 'name' => $this->config->get('config_name'));
		} else {
			$setFrom = array('email' => $this->config->get('config_mail_smtp_username'), 'name' => $this->config->get('config_name'));
		}
		
		$replyTo = array($setFrom);

		$this->TsLoader->TsEmail->sendMail(
						array(
							'subject' => $this->convertPlaceHoldersToData(array('id' => $data['ticket_id'], 'message' => $subject, 'threaddescription' => $threaddescription)),
							'message' => $this->convertPlaceHoldersToData(array('id' => $data['ticket_id'], 'message' => $message, 'threaddescription' => $threaddescription)),
							'address' => $to,
							'cc' => $cc,
							'bcc' => $bcc,
							'setFrom' => $setFrom,
							'replyTo' => $replyTo,
							'attachments' => $attachments,
						)
					);

		$this->event->trigger('post.admin.ts.ticket.action.sendMailToAgent', $data);
	}

	/**
	 * actionTicketSendMailToGroup Send mail to Group
	 * @param  array $data Ticket Details
	 */
	public function actionTicketSendMailToGroup($data){
		$this->event->trigger('pre.admin.ts.ticket.action.sendMailToGroup', $data);

		//load mail sender model and send mail from here
		if($data['mail_group']['emailtemplate']){
			$this->TsLoader->TsService->model(array('model'=>'ticketsystem/emailtemplates'));
			$mailData = $this->model_ticketsystem_emailtemplates->getEmailTemplate($data['mail_group']['emailtemplate']);
			if(!$mailData)
				return;
			$subject = $mailData['name'];
			$message = $mailData['message'];
		}else{
			$subject = $data['mail_group']['subject'];
			$message = $data['mail_group']['message'];
		}

		$thread_id = isset($data['thread_id']) ? $data['thread_id'] : 0;

		$ticketData = $this->getTicket(array('t.id' => $data['ticket_id']));
		$threadData = $this->getTicketThreads(array('thread_id' => $thread_id));

		$ticketReceivers = $this->getTicketReceiver(array('ticket_id' => $data['ticket_id']));

		$to = $cc = $bcc = $attachments = array();
		if($ticketReceivers){
			foreach($ticketReceivers as $ticketReceiver){
				if($arrayTo = unserialize($ticketReceiver['to']))
					$to = array_merge($to, $arrayTo);
				if($arrayCC = unserialize($ticketReceiver['cc']))
					$cc = array_merge($cc, $arrayCC);
				if($arrayBCC = unserialize($ticketReceiver['bcc']))
					$bcc = array_merge($bcc, $arrayBCC);
			}
		}

		$threaddescription = '';
		if($threadData){
			$threaddescription = $threadData[0]['message'];
			$ticketThreadRecievers = $this->getTicketThreadReceivers($thread_id);
			if($ticketThreadRecievers AND ($threadRecievers = unserialize($ticketThreadRecievers['receivers'])))
				foreach($threadRecievers as $kind => $threadReciever){
					if(is_array($threadReciever))
						$$kind = array_merge($$kind, $threadReciever);
				}

			$ticketThreadAttachments = $this->getTicketThreadAttachments($thread_id);

			if($ticketThreadAttachments)
				foreach($ticketThreadAttachments as $ticketThreadAttachment){
					if(file_exists($attachment = DIR_IMAGE.$ticketThreadAttachment['path'].$ticketThreadAttachment['fakename']))
						$attachments[] = array(
												'attachment' => $attachment,
												'name' => $ticketThreadAttachment['name']
											);
				}
		}

		$this->TsLoader->TsService->model(array('model' => 'ticketsystem/agentsGroups'));
		if($mailAgents = $this->model_ticketsystem_agentsGroups->getAgentsGroupByFilter("ag.group_id = '".(int)$data['mail_group']['group']."'")){
			foreach($mailAgents as $mailAgent){
				$to[] = $mailAgent['agentEmail'];
			}
		}

		if ($this->config->get('config_mail_protocol') == 'mail') {
			$setFrom = array('email' => $this->config->get('config_email'), 'name' => $this->config->get('config_name'));
		} else {
			$setFrom = array('email' => $this->config->get('config_mail_smtp_username'), 'name' => $this->config->get('config_name'));
		}
		
		$replyTo = array($setFrom);

		$this->TsLoader->TsEmail->sendMail(
						array(
							'subject' => $this->convertPlaceHoldersToData(array('id' => $data['ticket_id'], 'message' => $subject, 'threaddescription' => $threaddescription)),
							'message' => $this->convertPlaceHoldersToData(array('id' => $data['ticket_id'], 'message' => $message, 'threaddescription' => $threaddescription)),
							'address' => $to,
							'cc' => $cc,
							'bcc' => $bcc,
							'setFrom' => $setFrom,
							'replyTo' => $replyTo,
							'attachments' => $attachments,
						)
					);

		$this->event->trigger('post.admin.ts.ticket.action.sendMailToGroup', $data);
	}

	public function actionTicketDelete($data){
		$this->event->trigger('pre.admin.ts.ticket.action.delete', $data);

		$this->deleteTicket($data['ticket_id']);

		$this->event->trigger('post.admin.ts.ticket.action.delete', $data);
	}

	public function actionTicketMarkSpam($data){
		$this->event->trigger('pre.admin.ts.ticket.action.markSpam', $data);

		if(is_array($this->config->get('ts_ticket_status')) AND isset($this->config->get('ts_ticket_status')['spam']))
			$this->db->query("UPDATE ".DB_PREFIX."ts_tickets SET `status` = '".(int)$this->config->get('ts_ticket_status')['spam']."' WHERE id = '".(int)$data['ticket_id']."'");

		$this->event->trigger('post.admin.ts.ticket.action.markSpam', $data);
	}

	/**
	 * getTicketEmailMessageId Function returns Mail details of ticket if exits
	 * @call in sendMail.. functions
	 */
	public function getTicketEmailMessageId($ticket_id){
		return $this->db->query("SELECT tte.*,te.email,te.username FROM ".DB_PREFIX."ts_ticket_thread_email tte LEFT JOIN ".DB_PREFIX."ts_tickets_threads tt ON(tt.id = tte.thread_id) LEFT JOIN ".DB_PREFIX."ts_emails te ON(tte.email_id = te.id) WHERE tt.ticket_id = '".(int)$ticket_id."' ORDER BY id ASC")->rows;
	}
}