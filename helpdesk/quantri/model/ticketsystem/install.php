<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is installer of HelpDesk Module.
 */
class ModelTicketSystemInstall extends Model {

	/**
	 * eventsEntry Function used to add/ update Activity entries selected from module, on which module will add a entry to TsActivity
	 * @param  array $addActivity Add these entries to Opencart Events Table
	 */
	public function eventsEntry($addActivity){
		$array = array(
					'agents' => array(
									//Agents
									'pre.admin.ts.agent.delete',
									'post.admin.ts.agent.add',
									'post.admin.ts.agent.edit',
								),

					'businesshours' => array(
									//Business Hours
									'pre.admin.ts.businesshours.delete',
									'post.admin.ts.businesshours.add',
									'post.admin.ts.businesshours.edit',
								),

					'groups' => array(
									//Group
									'pre.admin.ts.group.delete',
									'post.admin.ts.group.add',
									'post.admin.ts.group.edit',
								),

					'roles' => array(
									//Roles
									'pre.admin.ts.role.delete',
									'post.admin.ts.role.add',
									'post.admin.ts.role.edit',
								),

					'types' => array(
									//Ticket Types
									'pre.admin.ts.type.delete',
									'post.admin.ts.type.add',
									'post.admin.ts.stype.edit',
								),

					'tickets' => array(
									//Ticket
									'pre.admin.ts.ticket.delete',
									'pre.admin.ts.ticket.thread.delete',
									'pre.admin.ts.agent.ticket.delete',
									'post.admin.ts.ticket.add',
									'post.admin.ts.ticket.thread.add',
									'post.admin.ts.ticket.edit',
									'post.admin.ts.update.ticket.thread',
								),
					);

		$this->load->model('extension/event');
		$this->model_extension_event->deleteEvent('ts');

		foreach($array as $controller => $events){
			if(in_array($controller, $addActivity))
				foreach($events as $event){
					$this->model_extension_event->addEvent('ts', $event, 'ticketsystem/activity/add');
				}
		}
	}

	/**
	 * database Function used to store default data Helpdesk module from sql file
	 * @ignore It's just OC default function replica
	 */
	public function database() {
		$file = DIR_APPLICATION . 'tsHelpDesk.sql';

		if (!file_exists($file)) {
			exit('Could not load sql file: ' . $file);
		}

		$lines = file($file);

		if ($lines) {
			$sql = '';

			foreach($lines as $line) {
				if ($line && (substr($line, 0, 2) != '--') && (substr($line, 0, 1) != '#')) {
					$sql .= $line;

					if (preg_match('/;\s*$/', $line)) {
						$sql = str_replace("INSERT INTO `ticket_", "INSERT INTO `" . DB_PREFIX, $sql);

						$this->db->query($sql);

						$sql = '';
					}
				}
			}
		}

		//add current user entry to Helpdesk admin
		$this->load->model('ticketsystem/agents');
		$data = array(
						'user_id' => $this->user->getId(),
						'name_alias' => $this->user->getUserName(),
						'level' => 1,
						'scope' => 2,
						'signature' => 'Admin',
						'role' => array(1),
						'timezone' => 'Europe/London',
					);
		$this->model_ticketsystem_agents->addAgent($data);
	}

	/**
	 * createTables Crate HelpDesk tables
	 */
	public function createTables() {
		$this->removeTables();

		/**
		 * Mails
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_emails (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) NOT NULL,
			  `description` varchar(1000) NOT NULL,
			  `email` varchar(100) NOT NULL,

			  `group` int(100) NOT NULL,
			  `priority` int(100) NOT NULL,
			  `type` int(100) NOT NULL,

			  `username` varchar(200) NOT NULL,
			  `password` varchar(1000) NOT NULL,

			  `hostname` varchar(1000) NOT NULL,
			  `port` int(100) NOT NULL,
			  `mailbox` varchar(1000) NOT NULL,
			  `protocol` varchar(100) NOT NULL,
			  `fetch_time` int(100) NOT NULL,
			  `email_per_fetch` int(100) NOT NULL,
			  `actions` varchar(1000) NOT NULL,

			  `status` int(10) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Email Fetch
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_email_fetch (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `email_id` int(100) NOT NULL,
			  `fetched_email` int(100) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Email Templates
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_emailtemplates (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) NOT NULL,
			  `message` longtext NOT NULL,
			  `status` int(10) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Business Hours
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_business_hours (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) NOT NULL,
			  `description` varchar(1000) NOT NULL,
			  `timezone` varchar(200) NOT NULL,
			  `timings` varchar(1000) NOT NULL,
			  `sizes` varchar(1000) NOT NULL,
			  `positions` varchar(1000) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Business Hours
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_business_hour_timings (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `business_hour_id` int(11) NOT NULL,
			  `day` varchar(100) NOT NULL,
			  `time_start` time NOT NULL,
			  `time_end` time NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Holidays
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_holidays (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `business_hour_id` int(11) NOT NULL,
			  `name` varchar(100) NOT NULL,
			  `from_date` varchar(200) NOT NULL,
			  `to_date` varchar(200) NOT NULL, 
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Customers
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_customers (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(200) NOT NULL,
			  `email` varchar(200) NOT NULL,
			  `customer_id` int(100) NOT NULL,
			  `timezone` varchar(200) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Organization
		 *
		 * customer_role - can see own or company tickets
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_organizations (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(200) NOT NULL, 
			  `description` varchar(1000) NOT NULL,
			  `domain` varchar(200) NOT NULL, 
			  `note` varchar(2000) NOT NULL, 
			  `image` varchar(2000) NOT NULL, 
			  `customer_role` int(100) NOT NULL, 
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Organization Customers
		 *
		 * customer_id will be ts_customers - id
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_organization_customers (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `organization_id` int(100) NOT NULL,
			  `customer_id` int(100) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Organization Groups
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_organization_groups (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `organization_id` int(100) NOT NULL,
			  `group_id` int(100) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Attachments
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_attachments (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(200) NOT NULL, 
			  `fakename` varchar(200) NOT NULL, 
			  `size` varchar(200) NOT NULL, 
			  `mime` varchar(200) NOT NULL, 
			  `path` varchar(500) NOT NULL, 
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Tags
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_tags (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(500) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Activity
		 *
		 * if anything will happen (add/update/delete) then this will store that
		 *
		 * type - for performer type - client or agents or system
		 * performer - who did this
		 * affected - who will affect form this
		 * level - which level of activity happen
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_activity (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `type` varchar(100) NOT NULL,
			  `performer` int(100) NOT NULL,
			  `performertype` varchar(100) NOT NULL,
			  `affected` int(100) NOT NULL,
			  `activity` varchar(1000) NOT NULL,
			  `level` varchar(100) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Groups
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_groups (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `businesshour_id` int(100) NOT NULL,
			  `automatic_assign` int(100) NOT NULL,
			  `inform_time` varchar(100) NOT NULL,
			  `inform_agent` int(100) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Groups Description
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_group_descriptions (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `group_id` int(11) NOT NULL,
			  `name` varchar(200) NOT NULL,
			  `description` varchar(1000) NOT NULL,
			  `language_id` int(100) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Roles
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_roles (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(200) NOT NULL,
			  `description` varchar(1000) NOT NULL,
			  `role` varchar(5000) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Notes
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_notes (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `agent_id` int(100) NOT NULL,
			  `ticket_id` int(100) NOT NULL,
			  `note` varchar(1000) NOT NULL,
			  `completed` int(100) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Responses - Macros
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_responses (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) NOT NULL,
			  `description` varchar(1000) NOT NULL,
			  `actions` text NOT NULL,
			  `valid_for` varchar(100) NOT NULL,
			  `status` int(10) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_agents_responses (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `agent_id` int(100) NOT NULL,
			  `response_id` int(100) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;




		/**
		 * Agents
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_agents (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_id` int(200) NOT NULL,
			  `name_alias` varchar(200) NOT NULL,
			  `signature` varchar(2000) NOT NULL,
			  `level` int(100) NOT NULL,
			  `scope` int(100) NOT NULL,
			  `timezone` varchar(200) NOT NULL, 
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  `last_login` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Agents Roles
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_agent_roles (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `agent_id` int(100) NOT NULL,
			  `role_id` int(100) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Agents Groups
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_agent_groups (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `agent_id` int(100) NOT NULL,
			  `group_id` int(100) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Levels
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_agent_level (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `status` int(11) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Levels Description
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_agent_level_description (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `level_id` int(100) NOT NULL,
			  `name` varchar(500) NOT NULL,
			  `description` varchar(1000) NOT NULL,
			  `language_id` int(100) NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* Tickets 
		* 
		* provider for email / website
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_tickets (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `customer_id` int(100) NOT NULL,
			  `subject` varchar(500) NOT NULL,
			  `provider` varchar(100) NOT NULL,
			  `priority` int(100) NOT NULL,
			  `status` int(100) NOT NULL,
			  `type` int(100) NOT NULL,
			  `group` int(100) NOT NULL,
			  `assign_agent` int(100) NOT NULL,
			  `custom_field` text NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Tickets Receivers if any
		 *
		 * type for cc, bcc
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_ticket_receivers (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `ticket_id` int(100) NOT NULL, 
			  `to` varchar(500) NOT NULL,
			  `cc` varchar(500) NOT NULL,
			  `bcc` varchar(500) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* Tickets Threads
		* 
		* type will use to store activity like normal ticket threads (reply)
		* or internal note threads or forward, activities too like update, edit etc but don't show initially
		*
		* sender_id for agent / customer
		* sender_type for agent / customer which will filter threads
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_tickets_threads (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `ticket_id` int(100) NOT NULL, 
			  `sender_id` int(100) NOT NULL, 
			  `sender_type` varchar(100) NOT NULL, 
			  `type` varchar(100) NOT NULL DEFAULT 0,
			  `message` longtext NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Thread Receivers 
		 *
		 * type for reply, internal note, forward
		 * receivers for to:john@webkul.com, bcc:, forwardto:
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_thread_receivers (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `thread_id` int(100) NOT NULL, 
			  `type` varchar(100) NOT NULL,
			  `receivers` varchar(500) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* Tickets Attachments
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_tickets_attachments (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `thread_id` int(100) NOT NULL, 
			  `attachment_id` int(100) NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* Tickets Locks
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_tickets_locks (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `ticket_id` int(100) NOT NULL, 
			  `agent_id` int(100) NOT NULL, 
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_expire` timestamp NOT NULL DEFAULT '0000-00-00', 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* Tickets Drafts
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_tickets_drafts (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `ticket_id` int(100) NOT NULL, 
			  `agent_id` int(100) NOT NULL, 
			  `message` longtext NOT NULL,
			  `type` varchar(200) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Tickets Tags
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_tickets_tags (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `ticket_id` int(100) NOT NULL, 
			  `tag_id` int(100) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Types
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_ticket_types (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `status` int(11) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Types Description
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_ticket_types_description (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `type_id` int(100) NOT NULL,
			  `name` varchar(500) NOT NULL,
			  `description` varchar(1000) NOT NULL,
			  `language_id` int(100) NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Status
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_ticket_status (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `status` int(11) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Status Description
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_ticket_status_description (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `status_id` int(100) NOT NULL,
			  `name` varchar(500) NOT NULL,
			  `description` varchar(1000) NOT NULL,
			  `language_id` int(100) NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Priority
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_ticket_priority (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `status` int(11) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Priority Description
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_ticket_priority_description (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `priority_id` int(100) NOT NULL,
			  `name` varchar(500) NOT NULL,
			  `description` varchar(1000) NOT NULL,
			  `language_id` int(100) NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* Ticket Types and Custom field Relation
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_ticket_types_customfield (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `type_id` int(100) NOT NULL,
			  `custom_field` int(100) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* Ticket Agents
		* To store ticket id against agent id if agent created ticket behalf of any customer
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_ticket_agent_created (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `agent_id` int(100) NOT NULL,
			  `ticket_id` int(100) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* Ticket Un-Assign Mail
		* Send mail to agent if ticket is not assigned to anyone, based on group selected agent 
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_ticket_unassigned (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `ticket_id` int(100) NOT NULL,
			  `group_id` int(100) NOT NULL,
		 	  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* Ticket SLA
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_ticket_sla (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `ticket_id` int(100) NOT NULL,
			  `response_time` varchar(1000) NOT NULL,
			  `resolve_time` varchar(1000) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* Ticket Thread Email
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_ticket_thread_email (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `email_id` int(11) NOT NULL,
			  `thread_id` int(100) NOT NULL,
			  `message_id` varchar(1000) NOT NULL,
			  `references` varchar(1000) NOT NULL,
			  `uid` int(100) NOT NULL,
		 	  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* Ticket Fields -> will be in next version
		*/
		// $this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_ticket_fields (
		// 	  `id` int(11) NOT NULL AUTO_INCREMENT,
		// 	  `fixed_type` text NOT NULL,
		// 	  `custom_field` text NOT NULL,
		// 	  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  // `date_updated` timestamp NOT NULL, 
		// 	  PRIMARY KEY (`id`)
		// 	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		// ) ;


		/**
		* SLA 
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_sla (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(500) NOT NULL,
			  `description` varchar(1000) NOT NULL,
			  `status` int(11) NOT NULL,
			  `sort_order` int(100) NOT NULL,
			  `conditions_all` text NOT NULL,
			  `conditions_one` text NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* SLA Priorities
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_sla_priority (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `sla_id` int(100) NOT NULL,
			  `priority_id` int(100) NOT NULL,
			  `respond_within` varchar(500) NOT NULL,
			  `resolve_within` varchar(500) NOT NULL,
			  `hours_type` int(11) NOT NULL,
			  `status` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* SLA Respond Violation -> will be in next version
		*/
		// $this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_sla_respond_violation (
		// 	  `id` int(11) NOT NULL AUTO_INCREMENT,
		// 	  `sla_id` int(100) NOT NULL,
		// 	  `violation_time` varchar(100) NOT NULL ,
		// 	  `agents` varchar(500) NOT NULL,
		// 	  PRIMARY KEY (`id`)
		// 	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		// ) ;

		/**
		* SLA Respond Violation -> will be in next version
		*/
		// $this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_sla_resolved_violation (
		// 	  `id` int(11) NOT NULL AUTO_INCREMENT,
		// 	  `sla_id` int(100) NOT NULL,
		// 	  `violation_time` varchar(100) NOT NULL ,
		// 	  `agents` varchar(500) NOT NULL,
		// 	  PRIMARY KEY (`id`)
		// 	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		// ) ;

		/**
		* SLA Breach
		*
		* breach type - respond or resolve
		* description - text
		* customer_id - TS customer_id not OC customer_id
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_sla_breach (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `sla_id` int(100) NOT NULL,
			  `breach_type` varchar(100) NOT NULL,
			  `ticket_id` int(100) NOT NULL ,
			  `customer_id` int(100) NOT NULL,
			  `agent_id` int(100) NOT NULL ,
			  `description` varchar(1000) NOT NULL ,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;


		/**
		* Events and Triggers
		* We will store used added events in this table along with conditions and actions
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_events (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) NOT NULL,
			  `description` varchar(1000) NOT NULL,
			  `performer` varchar(1000) NOT NULL ,
			  `events` text NOT NULL,
			  `actions` text NOT NULL ,
			  `conditions_one` text NOT NULL ,
			  `conditions_all` text NOT NULL ,
			  `status` int(11) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* Ticket Rules
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_rules (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) NOT NULL,
			  `description` varchar(1000) NOT NULL,
			  `actions` text NOT NULL ,
			  `conditions_one` text NOT NULL ,
			  `conditions_all` text NOT NULL ,
			  `status` int(11) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;




		/**
		 * Support Center Category
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_category (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `status` int(11) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP, 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Support Center Category Description
		 */
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_category_description (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `category_id` int(100) NOT NULL,
			  `name` varchar(500) NOT NULL,
			  `description` varchar(1000) NOT NULL,
			  `language_id` int(100) NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		* Support Center Category and OC Information
		*/
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_category_information (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `category_id` int(100) NOT NULL,
			  `information_id` int(100) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;

		/**
		 * Service Policies -> will be in next version
		 */
		/*
		$this->db->query("CREATE TABLE IF NOT EXISTS ".DB_PREFIX."ts_policies (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) NOT NULL,
			  `description` varchar(2000) NOT NULL,
			  `date_added` timestamp DEFAULT CURRENT_TIMESTAMP), 
			  `date_updated` timestamp NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
		) ;
		*/
	}
	
	/**
	 * removeTables Remove HelpDesk tables
	 */
	public function removeTables(){
		$tables = array(
					'ts_business_hours',
					'ts_business_hour_timings',
					'ts_holidays',
					'ts_customers',

					'ts_organizations',
					'ts_organization_customers',
					'ts_organization_groups',

					'ts_attachments',
					'ts_tags',
					'ts_activity',
					'ts_groups',
					'ts_group_descriptions',
					'ts_roles',
					'ts_notes',
					'ts_responses',

					'ts_emails',

					'ts_agents',
					'ts_agent_roles',
					'ts_agent_groups',
					'ts_agent_level',
					'ts_agent_level_description',
					'ts_agents_responses',

					'ts_tickets',
					'ts_ticket_receivers',
					'ts_tickets_threads',
					'ts_thread_receivers',
					'ts_tickets_attachments',
					'ts_tickets_locks',
					'ts_tickets_drafts',
					'ts_tickets_tags',
					'ts_ticket_agent_created',
					'ts_ticket_unassigned',
					'ts_ticket_sla',

					'ts_ticket_types',
					'ts_ticket_types_description',
					'ts_ticket_status',
					'ts_ticket_status_description',
					'ts_ticket_priority',
					'ts_ticket_priority_description',
					'ts_ticket_types_customfield',

					'ts_sla',
					'ts_sla_priority',
					// 'ts_sla_respond_violation',
					// 'ts_sla_resolved_violation',
					'ts_sla_breach',
					
					'ts_events',
					'ts_rules',
					'ts_category',
					'ts_category_description',
					'ts_category_information',

					'ts_emailtemplates'
				);
	
		foreach($tables as $tabel){
			$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX .$tabel."`");
		}
	}
}