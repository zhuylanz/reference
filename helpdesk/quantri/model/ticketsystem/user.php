<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to fetch Opencart Users
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemUser extends Model{

	public function getUsers($user) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` u WHERE CONCAT(u.firstname,' ',u.lastname) LIKE '" . $this->db->escape($user) . "%' AND status = 1 AND user_id NOT IN (SELECT user_id FROM ".DB_PREFIX."ts_agents)");

		return $query->rows;
	}

	public function getUserById($id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE user_id = '" . (int)$id . "' AND status = 1 ");

		return $query->row;
	}

}