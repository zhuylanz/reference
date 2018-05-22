<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Service :)
 * This class provide services to HelpDesk module
 */
class TsService{
	/**
	 * $registry OC Registry
	 * @var array
	 */
	private $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}

	/**
	 * Function create Model based on recieved folder
	 * Not valid if folder name will different from default but i will add valid one ;)
	 */
	public function model($data = array('model' => '', 'location' => 'admin')) {
		$model = $data['model'];
		$location = !isset($data['location']) ? 'admin' : $data['location'];
		if($location == 'admin')
			$file = preg_replace('/catalog/','quantri', DIR_APPLICATION) . 'model/' . $model . '.php';
		else
			$file = preg_replace('/admin/','catalog', DIR_APPLICATION) . 'model/' . $model . '.php';

		$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);

		if (file_exists($file)) {
			include_once($file);

			$this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
		} else {
			trigger_error('Error: Could not load model ' . $file . '!');
			exit();
		}
	}

	/**
	 * Function returns values from recieved Array
	 * @param  array  $data any array with key, value pair
	 * @return array  with only values       
	 */
	public static function fetchOnlyValues(array $data){
		$result = array();
		foreach ($data as $key => $value) {
			if(is_array($value))
				$result = array_merge(self::fetchOnlyValues($value), $result);
			else
				$result[] = $value;
		}

		return $result;
	}

}