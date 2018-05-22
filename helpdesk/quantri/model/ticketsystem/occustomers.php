<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to Fetch Opencart Customers
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemOcCustomers extends Model {

	public function getCustomers($data){
		$sql = "SELECT c.* FROM ".DB_PREFIX."customer c WHERE 1 ";

		if(isset($data['filter_name']) AND $data['filter_name'])
			$sql .= " AND CONCAT(c.firstname, ' ',c.lastname) LIKE '%".$data['filter_name']."%'";

		if(isset($data['filter_email']) AND $data['filter_email'])
			$sql .= " AND c.email LIKE '%".$data['filter_email']."%'";

		return $this->db->query($sql)->rows;
	}

	public function getCustomer($id){
		$result = $this->db->query("SELECT c.*,CONCAT(oc.firstname,' ',oc.lastname) as oc_customer,o.name as organization,toc.organization_id FROM ".DB_PREFIX."ts_customers c LEFT JOIN ".DB_PREFIX."customer oc ON (c.customer_id = oc.customer_id) LEFT JOIN ".DB_PREFIX."ts_organization_customers toc ON (c.customer_id = toc.customer_id) LEFT JOIN ".DB_PREFIX."ts_organizations o ON (toc.organization_id = o.id) WHERE oc.customer_id = '".(int)$id."' ")->row;

		return $result;
	}

}