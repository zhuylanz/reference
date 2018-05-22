<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Model Class is used to work on Business Hours of Helpdesk mod.
 *
 * All Explained in Activity Model Class, extra explanation is here
 */
class ModelTicketSystemBusinessHours extends Model {

	/**
	 * $allowedColums These array keys will be merged to add / edit function so that we don't need to add isset condition and TsHelper Query Builder will create all query
	 * @var array
	 */
	public $allowedColums = array(
						'id',
						'name',
						'description',
						'timezone',
						'holiday',
						);

	public function getTotalBusinessHours(){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_business_hours b WHERE 1 ";
		
		$sql .= $this->TsLoader->TsHelper->getFilterData(false);

		return count($this->db->query($sql)->rows);
	}

	public function getBusinessHours($useHelper = true){
		$sql = "SELECT * FROM ".DB_PREFIX."ts_business_hours b WHERE 1 ";

		if($useHelper)
			$sql .= $this->TsLoader->TsHelper->getFilterData();

		return $this->db->query($sql)->rows;
	}

	public function getBusinessHour($id){
		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."ts_business_hours r WHERE id = '".(int)$id."'")->row;

		if($result)
			$result['holiday'] = $this->getHolidays($id);

		return $result;
	}

	public function deleteBusinessHour($id){
		$this->event->trigger('pre.admin.ts.businesshours.delete', $id);

		$this->deleteHolidays($id);
		
		$this->db->query("DELETE FROM ".DB_PREFIX."ts_business_hours WHERE id = '".(int)$id."'");

		$this->event->trigger('post.admin.ts.businesshours.delete', $id);
	}

	public function addBusinessHour($data){
		$this->event->trigger('pre.admin.ts.businesshours.add', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$sql = "INSERT INTO ".DB_PREFIX."ts_business_hours SET name = '".$this->db->escape($data['name'])."',
							description = '".$this->db->escape($data['description'])."',
							timezone = '".$this->db->escape($data['timezone'])."',
							timings = '".$this->db->escape(serialize($data['businesshours']['days']))."',
							positions = '".$this->db->escape(serialize($data['businesshours']['position']))."',
							sizes = '".$this->db->escape(serialize($data['businesshours']['size']))."'
							";

		$this->db->query($sql);

		$businesshoursId = $this->db->getLastId();

		$this->addHolidays(array_merge($data, array('id' => $businesshoursId)));

		$this->event->trigger('post.admin.ts.businesshours.add', $businesshoursId);
	}

	public function editBusinessHour($data){
		$this->event->trigger('pre.admin.ts.businesshours.edit', $data);

		$data = array_merge(array_flip($this->allowedColums), $data);

		$this->db->query("UPDATE ".DB_PREFIX."ts_business_hours SET name = '".$this->db->escape($data['name'])."',
							description = '".$this->db->escape($data['description'])."',
							timezone = '".$this->db->escape($data['timezone'])."',
							timings = '".$this->db->escape(serialize($data['businesshours']['days']))."',
							positions = '".$this->db->escape(serialize($data['businesshours']['position']))."',
							sizes = '".$this->db->escape(serialize($data['businesshours']['size']))."',
							date_updated = NOW()
							WHERE id = '".(int)$data['id']."'
							");

		$this->deleteHolidays($data['id']);

		$this->addHolidays($data);

		$this->event->trigger('post.admin.ts.businesshours.edit', $data);
	}

	public function addHolidays($data){
		$this->event->trigger('pre.admin.ts.businesshours.holiday.add', $data);

		if(is_array($data['holiday']))
			foreach ($data['holiday'] as $key => $value) {
				$this->db->query("INSERT INTO ".DB_PREFIX."ts_holidays SET 
								business_hour_id = '".(int)$data['id']."',
								name = '".$this->db->escape($value['name'])."',
								from_date = '".$this->db->escape($value['from_date'])."',
								to_date = '".$this->db->escape($value['to_date'])."',
								date_updated = NOW()
							");
			}

		$this->event->trigger('post.admin.ts.businesshours.holiday.add', $data);
	}

	public function deleteHolidays($businessHourId){
		$this->event->trigger('pre.admin.ts.businesshours.holiday.delete', $businessHourId);

		$this->db->query("DELETE FROM ".DB_PREFIX."ts_holidays WHERE business_hour_id = '".(int)$businessHourId."'");

		$this->event->trigger('post.admin.ts.businesshours.holiday.delete', $businessHourId);
	}

	public function getHolidays($businessHourId){
		return $this->db->query("SELECT * FROM ".DB_PREFIX."ts_holidays WHERE business_hour_id = '".(int)$businessHourId."'")->rows;
	}

}