<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * This class store default values related to Ticket properties
 * Which will, we display to admin so that he/ she can select from these
 */
class TsTicket{

	//ticket events
	public $ticketEvents = array(
								'priority_updated',
								'type_updated',
								'status_updated',
								'group_updated',
								'agent_updated',
								'note_added',
								'reply_added',
								'ticket',
							);

	//use strtotime at time of calculation
	public $ticketSLATime = array(
								'+0 min'	=> 'immediately',
								'+30 mins'	=> '30_min',
								'+1 hour'	=> '1_hr',
								'+2 hours'	=> '2_hr',
								'+4 hours'	=> '4_hr',
								'+8 hours'	=> '8_hr',
								'+12 hours'	=> '12_hr',
								'+1 day'	=> '1_day',
								'+2 days'	=> '2_day',
								'+3 days'	=> '3_day',
								'+1 week'	=> '1_week',
								'+2 weeks'	=> '2_week',
								'+3 weeks'	=> '3_week',
								'+1 month'	=> '1_month',
							);

	public $source = array(
							'web',
							'mail',
						);

	public $ticketConditions = array(
								'mail' => array(
											'from_mail',
											'to_mail',
										),
								'ticket' => array(
											'subject',
											'description',
											'subject_or_description',
											'priority',
											'type',
											'status',
											'source',
											'created',
											'agent',
											'group',
										),

								'customer' => array(
											'customer_name',
											'customer_email',
										),

								'organization' => array(
											'organization_name',
											'organization_domain',
										),
							);

	public $ticketPlaceHolder = array(
							'id',
							'subject',
							'description',
							'threaddescription',
							'tags',
							'ticketnotes',
							'groupname',
							'agentname',
							'agentemail',

							'source',
							'status',
							'priority',
							'tickettype',

							'customername',
							'customeremail',
							'organization',
							);


	public $ticketAction = array(
								'priority',
								'type',
								'status',

								'tag',
								'cc',
								'bcc',
								'note',

								'assign_agent',
								'assign_group',

								'mail_agent',
								'mail_group',
								'mail_customer',

								'delete_ticket',
								'mark_spam',
							);

}