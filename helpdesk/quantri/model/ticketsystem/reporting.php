<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to display Reports of HelpDesk module
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemReporting extends Model {

	public function getTicketReportsSummaryByAgents(){
		$sql = "SELECT ta.name_alias as agentAliasName,CONCAT(u.firstname,' ',u.lastname) agentName, ta.id FROM ".DB_PREFIX."ts_agents ta LEFT JOIN ".DB_PREFIX."user u ON(ta.user_id = u.user_id) WHERE 1 ";

		$agents = $this->db->query($sql)->rows;

		foreach($agents as $key => $agent){
			$total_tickets = $this->db->query("SELECT COUNT(t.id) AS total_tickets FROM ".DB_PREFIX."ts_tickets t WHERE t.assign_agent = '".(int)$agent['id']."'")->row;
			$agents[$key]['total_tickets'] = $total_tickets['total_tickets'];

			$configResolved = is_array($this->config->get('ts_ticket_status')) ? $this->config->get('ts_ticket_status')['solved'] : 0;
			$resolved_tickets = $this->db->query("SELECT COUNT(t.id) AS resolved_tickets FROM ".DB_PREFIX."ts_tickets t WHERE t.assign_agent = '".(int)$agent['id']."' AND `status` = '".(int)$configResolved."'")->row;
			$agents[$key]['resolved_tickets'] = $resolved_tickets['resolved_tickets'];

			$first_response_time = $this->db->query("SELECT AVG(TIME_TO_SEC(TIMEDIFF(ttt.date_added, (SELECT ttt2.date_added FROM ".DB_PREFIX."ts_tickets_threads ttt2 WHERE ttt2.type='create' AND ttt2.ticket_id = ttt.ticket_id)))) AS first_response_time FROM ".DB_PREFIX."ts_tickets_threads ttt WHERE ttt.sender_id = '".(int)$agent['id']."' AND ttt.sender_type = 'agent' AND type='reply'")->row;
			$agents[$key]['first_response_time'] = $first_response_time['first_response_time'];
			
			$avg_response_time = $this->db->query("SELECT AVG(TIME_TO_SEC(TIMEDIFF(ttt.date_added, (SELECT ttt2.date_added FROM ".DB_PREFIX."ts_tickets_threads ttt2 WHERE ttt2.type='reply' AND ttt2.ticket_id = ttt.ticket_id AND ttt2.id = ttt.id-1)))) AS avg_response_time FROM ".DB_PREFIX."ts_tickets_threads ttt WHERE ttt.sender_id = '".(int)$agent['id']."' AND ttt.sender_type = 'agent' AND type='reply'")->row;
			$agents[$key]['avg_response_time'] = $avg_response_time['avg_response_time'];
		}

		return $agents;
	}

	public function getTicketReportsSummaryByCustomers(){
		$sql = "SELECT c.name as customername, c.id FROM ".DB_PREFIX."ts_customers c WHERE 1 ";

		$customers = $this->db->query($sql)->rows;

		foreach($customers as $key => $customer){
			$total_tickets = $this->db->query("SELECT COUNT(t.id) AS total_tickets FROM ".DB_PREFIX."ts_tickets t WHERE t.customer_id = '".(int)$customer['id']."'")->row;
			$customers[$key]['total_tickets'] = $total_tickets['total_tickets'];

			$configResolved = is_array($this->config->get('ts_ticket_status')) ? $this->config->get('ts_ticket_status')['solved'] : 0;
			$resolved_tickets = $this->db->query("SELECT COUNT(t.id) AS resolved_tickets FROM ".DB_PREFIX."ts_tickets t WHERE t.customer_id = '".(int)$customer['id']."' AND `status` = '".(int)$configResolved."'")->row;
			$customers[$key]['resolved_tickets'] = $resolved_tickets['resolved_tickets'];

			$first_quert_time = $this->db->query("SELECT AVG(TIME_TO_SEC(TIMEDIFF(ttt.date_added, (SELECT ttt2.date_added FROM ".DB_PREFIX."ts_tickets_threads ttt2 WHERE ttt2.type='reply' AND ttt2.ticket_id = ttt.ticket_id ORDER BY ttt.id ASC LIMIT 1)))) AS first_quert_time FROM ".DB_PREFIX."ts_tickets_threads ttt WHERE ttt.sender_id = '".(int)$customer['id']."' AND ttt.sender_type = 'customer' AND type='reply'")->row;
			$customers[$key]['first_quert_time'] = $first_quert_time['first_quert_time'];
			
			$avg_query_time = $this->db->query("SELECT AVG(TIME_TO_SEC(TIMEDIFF((SELECT ttt2.date_added FROM ".DB_PREFIX."ts_tickets_threads ttt2 WHERE ttt2.type='reply' AND ttt2.ticket_id = ttt.ticket_id AND ttt2.id = ttt.id+1 ORDER BY ttt.id ASC LIMIT 1), ttt.date_added))) AS avg_query_time FROM ".DB_PREFIX."ts_tickets_threads ttt WHERE ttt.sender_id = '".(int)$customer['id']."' AND ttt.sender_type = 'customer' AND type='reply'")->row;
			$customers[$key]['avg_query_time'] = $avg_query_time['avg_query_time'];
		}

		return $customers;
	}
}