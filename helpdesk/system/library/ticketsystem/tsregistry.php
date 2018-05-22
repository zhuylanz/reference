<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * Registry :)
 * This class sets Opencart registries
 */
class TsRegistry{

	public function __construct($registry) {
		$this->registry = $registry;
		$this->db = $registry->get('db');
		$this->config = $registry->get('config');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->language = $registry->get('language');
		$this->url = $registry->get('url');
		$this->request = $registry->get('request');
		$this->load = $registry->get('load');
		$this->TsLoader = $registry->get('TsLoader');
	}
}