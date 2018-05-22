<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Support Center of HelpDesk module
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemSupportCenter extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'status',
						'informations',
						'category',
						);

	public function getTotalCategories(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_category tc LEFT JOIN ".DB_PREFIX."ts_category_description tcd ON (tc.id = tcd.category_id) WHERE tcd.language_id = '".$this->config->get('config_language_id')."' ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getCategories($useHelper = true){
		$sql = "SELECT tc.*,tcd.name, tcd.description FROM ".DB_PREFIX."ts_category tc LEFT JOIN ".DB_PREFIX."ts_category_description tcd ON (tc.id = tcd.category_id) WHERE tcd.language_id = '".$this->config->get('config_language_id')."' ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();

		return $this->db->query($sql)->rows;
	}

	public function getCategoryInformationByCategory($id){
		return $this->db->query("SELECT information_id FROM ".DB_PREFIX."ts_category_information WHERE category_id = '".(int)$id."'")->rows;
	}

	public function getCategoryInformationByFiltering($data){
		$sql = "SELECT tci.information_id, id.title, id.description FROM ".DB_PREFIX."ts_category_information tci LEFT JOIN " .DB_PREFIX. "information i ON(tci.information_id = i.information_id) LEFT JOIN " .DB_PREFIX. "information_description id ON(i.information_id = id.information_id) WHERE id.language_id = '".(int)$this->config->get('config_language_id')."'";

		if($this->config->get('ts_information_order'))
			$data['order'] = $this->config->get('ts_information_order');

		$this->TsLoader->TsHelper->overrideRequestData($data, true);

		$sql .= $this->TsLoader->TsHelper->getFilterData(true, true);

		return $this->db->query($sql)->rows;
	}

	public function getCategory($id){
		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_category tc WHERE id = '".(int)$id."'")->row;

		if($result)
			$categoryDescription = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_category_description tcd WHERE category_id = '".(int)$id."'")->rows;

			$result['category'] = array();
			if($categoryDescription){
				foreach ($categoryDescription as $key => $value) {
					$result['category'][$value['language_id']] = array(
													'name' => $value['name'],
													'description' => $value['description'],
													);
				}

			$categoryDescription = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_category_description tcd WHERE category_id = '".(int)$id."'")->rows;

			$result['informations'] = TsService::fetchOnlyValues($this->getCategoryInformationByCategory($id));
		}

		return $result;
	}

	public function deleteCategory($id){
		$this->event->trigger('pre.admin.ts.supportcenter.category.delete', $id);

		$this->deleteCategoryInformation($id);
		$this->deleteCategoryDescription($id);
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_category WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.supportcenter.category.delete', $id);
	}

	public function deleteCategoryDescription($id){
		$this->event->trigger('pre.admin.ts.supportcenter.category.description.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_category_description WHERE category_id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.supportcenter.category.description.delete', $id);
	}

	public function deleteCategoryInformation($id){
		$this->event->trigger('pre.admin.ts.supportcenter.category.informations.delete', $id);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_category_information WHERE category_id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.supportcenter.category.informations.delete', $id);
	}

	public function addCategory($data){
		$this->event->trigger('pre.admin.ts.supportcenter.category.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("INSERT INTO ".DB_PREFIX."ts_category SET status = '".(int)$data['status']."'");

		$categoryId = $this->db->getLastId();

		$this->addCategoryDescription(array_merge($data, array('id' => $categoryId) ));
		$this->addCategoryInformations(array_merge($data, array('id' => $categoryId) ));

		$this->event->trigger('post.admin.ts.supportcenter.category.add', $categoryId);
	}

	public function editCategory($data){
		$this->event->trigger('pre.admin.ts.supportcenter.category.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_category SET status = '".(int)$data['status']."' WHERE id='".(int)$data['id']."'");

		$this->deleteCategoryDescription($data['id']);
		$this->addCategoryDescription($data);

		$this->deleteCategoryInformation($data['id']);
		$this->addCategoryInformations($data);

		$this->event->trigger('post.admin.ts.supportcenter.category.edit', $data);
	}

	public function addCategoryDescription($data){
		$this->event->trigger('pre.admin.ts.supportcenter.category.description.add', $data);

		foreach($data['category'] as $language => $value){
			$this->db->query("INSERT INTO ".DB_PREFIX."ts_category_description SET name = '".$this->db->escape($value['name'])."',
										description = '".$this->db->escape($value['description'])."',
										language_id = '".(int)$language."',
										category_id = '".(int)$data['id']."'
									");
		}

		$this->event->trigger('post.admin.ts.supportcenter.category.description.add', $data);
	}

	public function addCategoryInformations($data){
		$this->event->trigger('pre.admin.ts.supportcenter.category.informations.add', $data);

		foreach($data['informations'] as $value){
			$this->db->query("INSERT INTO ".DB_PREFIX."ts_category_information SET 
										information_id = '".(int)$value."',
										category_id = '".(int)$data['id']."'
									");
		}

		$this->event->trigger('post.admin.ts.supportcenter.category.informations.add', $data);
	}
}