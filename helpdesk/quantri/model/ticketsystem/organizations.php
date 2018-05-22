<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Organizations
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemOrganizations extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'name',
						'description',
						'domain',
						'note',
						'image',
						'customer_role',
						'customers',
						'groups',
						);

	public function getTotalOrganizations(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_organizations o WHERE 1 ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getOrganizations($useHelper = true){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_organizations o WHERE 1 ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();

		return $this->db->query($sql)->rows;
	}

	public function getOrganization($id){
		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_organizations o WHERE id = '".(int)$id."'")->row;

		if($result){
			$result['groups'] = $this->getOrganizationGroups($id);
			$result['customers'] = $this->getOrganizationCustomers($id);
		}

		return $result;
	}

	public function deleteOrganization($id){
		$this->event->trigger('pre.admin.ts.organizations.delete', $id);

		$this->deleteOrganizationCustomers($id);
		$this->deleteOrganizationGroups($id);
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_organizations WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.organizations.delete', $id);
	}

	public function addOrganization($data){
		$this->event->trigger('pre.admin.ts.organizations.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_organizations SET name = '".$this->db->escape($data['name'])."',
							description = '".$this->db->escape($data['description'])."',
							domain = '".$this->db->escape($data['domain'])."',
							note = '".$this->db->escape($data['note'])."',
							image = '".$this->db->escape($data['image'])."',
							customer_role = '".(int)$data['customer_role']."'
						");

		$organizationsId = $this->db->getLastId();

		if(is_array($data['customers']))
			$this->addOrganizationCustomers(array_merge($data, array('id' => $organizationsId)));

		if(is_array($data['groups']))
			$this->addOrganizationGroups(array_merge($data, array('id' => $organizationsId)));

		$this->event->trigger('post.admin.ts.organizations.add', $organizationsId);
	}	

	public function editOrganization($data){
		$this->event->trigger('pre.admin.ts.organizations.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_organizations SET name = '".$this->db->escape($data['name'])."',
						description = '".$this->db->escape($data['description'])."',
						domain = '".$this->db->escape($data['domain'])."',
						note = '".$this->db->escape($data['note'])."',
						image = '".$this->db->escape($data['image'])."',
						customer_role = '".(int)$data['customer_role']."',
						date_updated = NOW()
						WHERE id='".(int)$data['id']."'");

		$this->deleteOrganizationCustomers($data['id']);
		if(is_array($data['customers']))
			$this->addOrganizationCustomers($data);

		$this->deleteOrganizationGroups($data['id']);
		if(is_array($data['groups']))
			$this->addOrganizationGroups($data);

		$this->event->trigger('post.admin.ts.organizations.edit', $data);
	}

	public function deleteOrganizationCustomers($id){
		$this->event->trigger('pre.admin.ts.organization.customers.relation.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_organization_customers WHERE organization_id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.organization.customers.relation.delete', $id);
	}

	public function addOrganizationCustomers($data){
		$this->event->trigger('pre.admin.ts.organization.customers.relation.add', $data);

		//add this organization customer entry to ts customer
		$this->load->model('ticketsystem/customers');

		foreach($data['customers'] as $value){
			$this->db->query("INSERT INTO ".DB_PREFIX."ts_organization_customers SET customer_id = '".(int)$value."',
						organization_id = '".(int)$data['id']."'
						");
		}

		$this->event->trigger('post.admin.ts.organization.customers.relation.add', $data);
	}

	public function getOrganizationCustomers($id){
		return	$this->db->query("SELECT oc.*,c.name FROM ".DB_PREFIX."ts_organization_customers oc LEFT JOIN ".DB_PREFIX."ts_customers c ON (oc.customer_id = c.id) WHERE organization_id = '".(int)$id."'")->rows;
	}


	public function deleteOrganizationGroups($id){
		$this->event->trigger('pre.admin.ts.organizations.groups.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_organization_groups WHERE organization_id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.organizations.groups.delete', $id);
	}

	public function addOrganizationGroups($data){
		$this->event->trigger('pre.admin.ts.organizations.groups.add', $data);

		foreach($data['groups'] as $value){
			$this->db->query("INSERT INTO ".DB_PREFIX."ts_organization_groups SET group_id = '".(int)$value."',
						organization_id = '".(int)$data['id']."'
						");
		}

		$this->event->trigger('post.admin.ts.organizations.groups.add', $data);
	}

	public function getOrganizationGroups($id){
		return	$this->db->query("SELECT og.*,gd.name FROM ".DB_PREFIX."ts_organization_groups og LEFT JOIN ".DB_PREFIX."ts_group_descriptions gd ON (og.group_id = gd.group_id) WHERE organization_id = '".(int)$id."' AND gd.language_id = '".(int)$this->config->get('config_language_id')."'")->rows;
	}

}