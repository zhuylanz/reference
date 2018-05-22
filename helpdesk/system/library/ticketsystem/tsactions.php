<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * This class used to apply actions to tickets
 */
class TsActions extends TsRegistry{

	/**
	 * applyActionsOnRequest Apply passed actions to passes ticket id
	 * @param  array  $data actions and id
	 * @return string actions message
	 */
	public function applyActionsOnRequest($data = array()){
		if(!isset($data['actions']) || !isset($data['ticket_id']))
			return;

		if(!isset($data['thread_id']))
			$data['thread_id'] = 0;

		/**
		 * create TsService object and load tickets model
		 */
		(new TsService($this->registry))->model(array('model'=>'ticketsystem/tickets'));
		$this->model_ticketsystem_tickets = $this->registry->get('model_ticketsystem_tickets');

		$this->language->load('ticketsystem/actions');
		$message = '';
		
		if($data['actions']){
			foreach ($data['actions'] as $action) {
				$newData = array(
								'ticket_id' => $data['ticket_id'],
								'thread_id' => $data['thread_id'],
								'agent_id' => $data['agent_id'],
								$action['type'] => (isset($action['action']) ? $action['action'] : false),
							);
				switch($action['type']){
					case 'status' :
						$this->model_ticketsystem_tickets->actionTicketStatus($newData);
						$message .= $this->language->get('text_updated_status');
						break;

					case 'type' :
						$this->model_ticketsystem_tickets->actionTicketType($newData);
						$message .= $this->language->get('text_updated_type');
						break;

					case 'priority' :
						$this->model_ticketsystem_tickets->actionTicketPriority($newData);
						$message .= $this->language->get('text_updated_priority');
						break;

					case 'tag' :
						$this->model_ticketsystem_tickets->actionTicketTag($newData);
						$message .= $this->language->get('text_updated_tag');
						break;

					case 'cc' :
						$this->model_ticketsystem_tickets->actionTicketReceiverCC($newData);
						$message .= $this->language->get('text_updated_cc');
						break;

					case 'bcc' :
						$this->model_ticketsystem_tickets->actionTicketReceiverBCC($newData);
						$message .= $this->language->get('text_updated_bcc');
						break;

					case 'note' :
						$newData['id'] = $newData['ticket_id'];
						$newData['sender_type']	= 'agent';
						$newData['message']	= $newData['note']['note'];
						if(isset($newData['note']['isPrivate']) AND $newData['note']['isPrivate']){
							$newData['messagetype']	= 'note';
							$message .= $this->language->get('text_updated_note');
						}else{
							$newData['messagetype']	= 'reply';
							$message .= $this->language->get('text_updated_reply_note');
						}
						$this->model_ticketsystem_tickets->actionTicketNote($newData);
						break;

					case 'assign_agent':
						$newData['agent_id'] = $newData['assign_agent'];
						$this->model_ticketsystem_tickets->actionTicketAgent($newData);
						$message .= $this->language->get('text_updated_agent');
						break;

					case 'assign_group' :
						$newData['group'] = $newData['assign_group'];
						$this->model_ticketsystem_tickets->actionTicketGroup($newData);
						$message .= $this->language->get('text_updated_group');
						break;

					case 'mail_agent' :
						$this->model_ticketsystem_tickets->actionTicketSendMailToAgent($newData);
						$message .= $this->language->get('text_updated_mail_to_agent');
						break;

					case 'mail_group' :
						$this->model_ticketsystem_tickets->actionTicketSendMailToGroup($newData);
						$message .= $this->language->get('text_updated_mail_to_group');
						break;

					case 'mail_customer' :
						$this->model_ticketsystem_tickets->actionTicketSendMailToCustomer($newData);
						$message .= $this->language->get('text_updated_mail_to_customer');
						break;

					case 'mark_spam' :
						$this->model_ticketsystem_tickets->actionTicketMarkSpam($newData);
						$message .= $this->language->get('text_updated_mail_to_customer');
						break;
						
					case 'delete_ticket' :
						$this->model_ticketsystem_tickets->actionTicketDelete($newData);
						$message .= $this->language->get('text_updated_delete');
						break;

					default :
						break;
				}	

			}
		}

		return $message;
	}
}