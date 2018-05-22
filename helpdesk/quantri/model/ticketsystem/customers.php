<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on HelpDesk Customers.
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemCustomers extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'name',
						'email',
						'organization_id',
						'customer_id',
						);

	public function getTotalCustomers(){
		$sql = "SELECT c.*,CONCAT(oc.firstname,' ',oc.lastname) as oc_customer,o.id organization_id, o.name as organization FROM ".DB_PREFIX."ts_customers c LEFT JOIN ".DB_PREFIX."customer oc ON (c.customer_id = oc.customer_id) LEFT JOIN ".DB_PREFIX."ts_organization_customers toc ON (c.id = toc.customer_id) LEFT JOIN ".DB_PREFIX."ts_organizations o ON (toc.organization_id = o.id) WHERE 1 ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getCustomers($useHelper = true, $data = array()){
		
		$sql = "SELECT c.*,CONCAT(oc.firstname,' ',oc.lastname) as oc_customer,o.id organization_id,o.name as organization FROM ".DB_PREFIX."ts_customers c LEFT JOIN ".DB_PREFIX."customer oc ON (c.customer_id = oc.customer_id) LEFT JOIN ".DB_PREFIX."ts_organization_customers toc ON (c.id = toc.customer_id) LEFT JOIN ".DB_PREFIX."ts_organizations o ON (toc.organization_id = o.id) WHERE 1 ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();

		if($data)
			$sql .= 'AND '.$this->TsLoader->TsHelper->createQueryUsingFields($data);
		
		return $this->db->query($sql)->rows;
	}

	public function getCustomer($id){
		$result = $this->db->query("SELECT c.*,CONCAT(oc.firstname,' ',oc.lastname) as oc_customer,o.name as organization,toc.organization_id FROM ".DB_PREFIX."ts_customers c LEFT JOIN ".DB_PREFIX."customer oc ON (c.customer_id = oc.customer_id) LEFT JOIN ".DB_PREFIX."ts_organization_customers toc ON (c.id = toc.customer_id) LEFT JOIN ".DB_PREFIX."ts_organizations o ON (toc.organization_id = o.id) WHERE c.id = '".(int)$id."' ")->row;

		return $result;
	}

	public function getCustomerByOCId($id){
		$result = $this->db->query("SELECT c.*,CONCAT(oc.firstname,' ',oc.lastname) as oc_customer,o.name as organization,toc.organization_id FROM ".DB_PREFIX."ts_customers c LEFT JOIN ".DB_PREFIX."customer oc ON (c.customer_id = oc.customer_id) LEFT JOIN ".DB_PREFIX."ts_organization_customers toc ON (c.id = toc.customer_id) LEFT JOIN ".DB_PREFIX."ts_organizations o ON (toc.organization_id = o.id) WHERE c.customer_id = '".(int)$id."' ")->row;

		return $result;
	}

	/**
	 * [getCustomerByOCEmailId to for fatch the oc customer's details using their email id]
	 * @param  [integer] $email_id [email_id of customer]
	 * @return [array]           [details of customer]
	 */
	public function getCustomerByOCEmailId($email_id){
		$result = $this->db->query("SELECT c.*,CONCAT(oc.firstname,' ',oc.lastname) as oc_customer,o.name as organization,toc.organization_id FROM ".DB_PREFIX."ts_customers c LEFT JOIN ".DB_PREFIX."customer oc ON (c.customer_id = oc.customer_id) LEFT JOIN ".DB_PREFIX."ts_organization_customers toc ON (c.id = toc.customer_id) LEFT JOIN ".DB_PREFIX."ts_organizations o ON (toc.organization_id = o.id) WHERE c.customer_id = '".(int)$email_id."' ")->row;

		return $result;
	}

	public function deleteCustomer($id){
		$this->event->trigger('pre.admin.ts.customers.delete', $id);

		$this->deleteCustomerOrganizationEntry($id);
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_customers WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.customers.delete', $id);
	}

	public function addCustomer($data){
		$this->event->trigger('pre.admin.ts.customers.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_customers SET email = '".$this->db->escape($data['email'])."',
							name = '".$this->db->escape($data['name'])."',
							customer_id = '".(int)$data['customer_id']."'
						");

		$customerId = $this->db->getLastId();

		if($data['organization_id'])
			$this->setCustomerOrganization(array_merge($data, array('id' => $customerId)));

		$this->event->trigger('post.admin.ts.customers.add', $customerId);

		return $customerId;
	}

	public function editCustomer($data){
		$this->event->trigger('pre.admin.ts.customers.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_customers SET email = '".$this->db->escape($data['email'])."',
						name = '".$this->db->escape($data['name'])."',
						customer_id = '".(int)$data['customer_id']."',
						date_updated = NOW()
						WHERE id='".(int)$data['id']."'");

		$this->deleteCustomerOrganizationEntry($data['id']);
		if($data['organization_id'])
			$this->setCustomerOrganization($data);

		$this->event->trigger('post.admin.ts.customers.edit', $data);
	}

	public function deleteCustomerOrganizationEntry($id){
		$this->event->trigger('pre.admin.ts.customer.organization.relation.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_organization_customers WHERE customer_id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.customer.organization.relation.delete', $id);
	}

	public function setCustomerOrganization($data){
		$this->event->trigger('pre.admin.ts.customer.organization.relation.add', $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_organization_customers SET
								customer_id = '".(int)$data['id']."',
								organization_id = '".(int)$data['organization_id']."'
							");

		$this->event->trigger('post.admin.ts.customer.organization.relation.add', $data);
	}

	
	/**
	 * [getAllCustomers for get all unique customers details of OC and TS]
	 * @return [array] [unique array of customer's details ]
	 */
	public function getAllCustomers(){
		$ts_uniqueArray = array();
		$sql = "SELECT oc.email, CONCAT(oc.firstname,' ',oc.lastname) as name, oc.customer_id FROM ".DB_PREFIX."customer oc UNION SELECT c.email, c.name, c.customer_id FROM ".DB_PREFIX."ts_customers c ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);
		$result = $this->db->query($sql)->rows;
		foreach ($result as $key => $value) {
			$ts_uniqueArray[$value['email']] = $value;		
		}
	return $ts_uniqueArray;
		
	}

}