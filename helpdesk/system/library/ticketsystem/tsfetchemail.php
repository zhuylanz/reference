<?php
if(!function_exists('helpDeskErrorHandler')){
	function helpDeskErrorHandler() {}
}

/**
 * Set Fake Error Handler to skip Opencart Errors
 */
set_error_handler("helpDeskErrorHandler");

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * This class is used to fetch Mails and generate ticket for HelpDesk module, from added emails
 */
class TsFetchEmail extends TsRegistry{

	/**
	 * $emailData Email data
	 * @var array
	 */
	protected $emailData;

	/**
	 * initilize initialize all requirement for this class
	 */
	protected function initilize(){
		$TsService = new TsService($this->registry);

		$this->language->load('ticketsystem/emails');
		
		/**
		 * This can loads model from Admin and Calatog both 
		 * @default admin
		 */
		$TsService->model(array('model'=>'ticketsystem/tickets'));
		$TsService->model(array('model'=>'ticketsystem/emails'));
		$TsService->model(array('model'=>'ticketsystem/customers'));
		if(version_compare(VERSION, '2.0.1.1', '<=')) {
			$TsService->model(array('model'=>'sale/customer'));
			$this->model_sale_customer = $this->registry->get('model_sale_customer');
		}else{
			$TsService->model(array('model'=>'customer/customer'));
			$this->model_customer_customer = $this->registry->get('model_customer_customer');
		}

		$this->model_ticketsystem_emails = $this->registry->get('model_ticketsystem_emails');
		$this->model_ticketsystem_tickets = $this->registry->get('model_ticketsystem_tickets');
		$this->model_ticketsystem_customers = $this->registry->get('model_ticketsystem_customers');
		
	}

	/**
	 * emailFetch Function is caller for this class
	 * @param  boolean $email if passed then fetched mail to only passes email
	 * @return array fetch mail progress 
	 */
	public function emailFetch($email = false) {
		$this->initilize();

		$emails = array();

		if($email)
			$emails[] = $this->model_ticketsystem_emails->getEmail($email);
		else{
			$emails = $this->model_ticketsystem_emails->getEmails(false, array('status' => 1));
		}

		$error = $success = $alertMessage = '';

		foreach ($emails as $data) {

			$emailsLastfetch = $this->model_ticketsystem_emails->getEmailFetchEntry($data['id']);

			if($emailsLastfetch){
				if( (strtotime('+'.$data['fetch_time'].'minutes', (strtotime($emailsLastfetch['date_added']) + $this->registry->get('TsBase')->getDateDifferences(true) ))) > strtotime(date("Y-m-d H:i:s"))){
					$alertMessage['error'] = $this->language->get('error_fetched_under_time');
					continue;
				}
			}

			$this->emailData = $data;

			$host = $data['hostname'];
			$port = $data['port'];
			$username = $data['username'];
			$password = $data['password'];
			$protocol = $data['protocol'];
			$fetch_time = $data['fetch_time'];
			$limit = $data['email_per_fetch'];
			$mailbox = $data['mailbox'];

			$messages = $alertMessage = array();

			$server = new \Fetch\Server($host, $port, $protocol);
			$server->setAuthentication($username, $password);

			try{
				if($mailbox)
					$server->setMailBox($mailbox);
				// $messages = $server->getOrderedMessages(SORTARRIVAL, 0, $limit);
				$messages = $server->getMessages($limit);
			}catch(\Exception $e){
				$error = $e->getMessage();
			}

			$messages = array_reverse($messages);

			if($messages){
				foreach ($messages as $key => $message) {
					if($returnMessage = $this->processMail($message, $server))
						$alertMessage[] = $returnMessage;
					else
						unset($messages[$key]);
				}
			}

			$server->expunge();

			$email_id = $data['id'];

			$data = array(
						'email_id' => $email_id,
						'fetched_email' => count($messages),
						);

			$this->model_ticketsystem_emails->addEmailFetchEntry($data);

			$success = sprintf($this->language->get('success_emails_fetched'), count($messages));
		}
		
		return array(
				'error' => $error,
				'success' => $success,
				'alertMessage' => $alertMessage,
				);
	}
	
	/**
	 * processMail Process fetched mails from email id
	 * @param  object $message Message Class object
	 * @param  object $server  Server Class object
	 * @return string message
	 */
	protected function processMail($message, $server){
		$isHtml = true;
		$alertMssage = array();

		/**
		 * If mail uid exits in system then skip this
		 */
		if($this->model_ticketsystem_emails->checkEmailUidExists($message->getUid()))
			return false;

		/**
		 * $customer_address Get mail addresses
		 * @var string
		 */
		$customer_address = explode(' <', str_replace('>', '', $message->getHeaders()->senderaddress));
		$email_address = $message->getHeaders()->to[0]->mailbox.'@'.$message->getHeaders()->to[0]->host;

		$sender_email_address = $message->getHeaders()->from[0]->mailbox.'@'.$message->getHeaders()->from[0]->host;
		
		$emailData = $this->emailData;

		$customers = $this->model_ticketsystem_customers->getCustomers(false, array('c.email' => $customer_address[1]));
		if($customers)
			$customerId = $customers[0]['id'];
		else{
			//check if this email exists in OC
			if(version_compare(VERSION, '2.0.1.1', '<=')) {
				$customer = $this->model_sale_customer->getCustomerByEmail($customer_address[1]);
			}else{
				$customer = $this->model_customer_customer->getCustomerByEmail($customer_address[1]);
			}
			
			if($customer)
				$ocCustomerId = $customer['customer_id'];
			else
				$ocCustomerId = 0;
			
			//create one in TS
			$customerData = array(
								'customer_id' => $ocCustomerId,
								'email' => $customer_address[1],
								'name' => $customer_address[0],
							);
			$customerId = $this->model_ticketsystem_customers->addCustomer($customerData);
		}

		$receivers = array(
						'to' => (isset($sender_email_address) ? $sender_email_address : array()),
						'cc' => (isset($message->getHeaders()->ccaddress) ? $message->getHeaders()->ccaddress  : array()), 
						'bcc' => (isset($message->getHeaders()->ccaddress) ? $message->getHeaders()->ccaddress : array())
						);

		/**
		 * $references Check if mail have references, it's mean, it can be thread of ticket not new ticket
		 * @var string
		 */
		$references = $message->getHeaders()->references ? $message->getHeaders()->references : false;
		
		/**
		 * check if references are in our DB tables
		 * @var string
		 */
		if(isset($message->getHeaders()->references) && ($thread_id = $this->model_ticketsystem_emails->checkEmailReferenceExists($message->getHeaders()->references))){
			
			if($threadData = $this->model_ticketsystem_tickets->getTicketThreads(array('thread_id' => $thread_id))){

				$alertMssage['thread'] = sprintf($this->language->get('success_thread_added'),$threadData['ticket_id']);
				$data = array(
							'id' => $threadData[0]['ticket_id'],
							'message' => $message->getMessageBody($isHtml),
							// 'message' => htmlentities($message->getMessageBody($isHtml)),
							'agent_id' => $customerId,
							'sender_type' => 'customer',
							'messagetype' => 'reply',
							'receivers' => $receivers,
							);	

				$thread_id = $this->model_ticketsystem_tickets->addTicketThread($data);
				$this->saveAttachments($message, $thread_id);

				//add message entry to ticket_mail table
				$data = array(
							'thread_id' => $thread_id,
							'uid' => $message->getUid(),
							'message_id' => $message->getHeaders()->message_id,
							'references' => $references,
							);
				$this->model_ticketsystem_emails->addEmailThread($data);
			}
		
		}elseif($ticket_id = $this->model_ticketsystem_tickets->checkEmailTicketReferenceExists($message->getHeaders()->references)){

			$data = array(
							'id' => $ticket_id,
							'message' => $message->getMessageBody($isHtml),
							// 'message' => htmlentities($message->getMessageBody($isHtml)),
							'agent_id' => $customerId,
							'sender_type' => 'customer',
							'messagetype' => 'reply',
							'receivers' => $receivers,
							);	

				$thread_id = $this->model_ticketsystem_tickets->addTicketThread($data);
				$this->saveAttachments($message, $thread_id);

				//add message entry to ticket_mail table
				$data = array(
							'thread_id' => $thread_id,
							'uid' => $message->getUid(),
							'message_id' => $message->getHeaders()->message_id,
							'references' => $references,
							);
				$this->model_ticketsystem_emails->addEmailThread($data);

		}else{			
	
			//add it to system as new ticket
			if($emailData){
				$data = array(	
							'subject' => $message->getSubject(),
							// 'subject' => htmlentities($message->getSubject()),
							'priority' => $emailData['priority'],
							'tickettype' => $emailData['type'],
							'group' => $emailData['group'],
							'provider' => 'mail',
							'custom_field' => array('tickets' => array()),
							'message' => $message->getMessageBody($isHtml),
							// 'message' => htmlentities($message->getMessageBody($isHtml)),
							'customer_id' => $customerId,
							'agent_id' => $customerId,
							'sender_type' => 'customer',
							'messagetype' => 'create',
							'receivers' => $receivers,
							);

				$thread_id = $this->model_ticketsystem_tickets->addTicket($data);

				$this->saveAttachments($message, $thread_id);

				//add message entry to ticket_mail table
				$data = array(
							'email_id' => $emailData['id'],
							'thread_id' => $thread_id,
							'uid' => $message->getUid(),
							'message_id' => $message->getHeaders()->message_id,
							'references' => $references,
							);

				$this->model_ticketsystem_emails->addEmailThread($data);

				$alertMssage['ticket'] = $this->language->get('success_ticket_added');
			}else{
				$alertMssage['error'] = $this->language->get('error_email_save_first');
			}
		}
		
		/**
		 * Perform Actions on Email set from admin side
		 */
		if(isset($emailData['actions']) AND $actions = unserialize($emailData['actions'])){
			switch($actions['action']){
				case 'nothing':
					break;
				case 'delete':
					$message->delete();
					break;
				case 'movetofolder':
					if(!$server->hasMailBox($actions['folder']))
						$server->createMailBox($actions['folder']);
					$message->moveToMailBox($actions['folder']);
					break;
				default:
					break;
			}
		}

		return $alertMssage;
	}

	/**
	 * saveAttachments Save Ticket Attachments with ticket id to system
	 * @param  object $message  Message Class object
	 * @param  integer $ticketThreadId Created Thread Id
	 */
	protected function saveAttachments($message, $ticketThreadId){
		if($message->getAttachments())
			foreach($message->getAttachments() as $attachment){
				$attachmentData = array(
									'name' => $attachment->getFileName(),
									'fakename' => $attachment->getFileName(),
									'size' => $attachment->getSize(),
									'mime' => $attachment->getMimeType(),
									'path' => $attachment->getSavePath(),
									);

				$attachmentId = $this->model_ticketsystem_tickets->addTicketThreadAttachments($attachmentData);
				
				$attachmentEntryData = array(
											'thread_id' => $ticketThreadId,
											'attachment_id' => $attachmentId,
										);
				$this->model_ticketsystem_tickets->addTicketThreadAttachmentsEntry($attachmentEntryData);
			}
	}
}