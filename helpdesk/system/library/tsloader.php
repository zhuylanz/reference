<?php
// $this->registry->set('TicketSystem', new TicketSystem(array('HtmlGenerator') ,$this->registry));
// $HtmlGenerator = $this->registry->get('TicketSystem')->getRegisterClass('HtmlGenerator');
// $HtmlGenerator->htmlGeneratehere();

/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * This is Loader class of HelpDesk mod, it's added from -> engine/Front class ocmod injection
 * This class loads all classes used in HelpDesk module
 */
class TsLoader
{
	/**
	 * $registry Opencart Default Registry
	 * @var array
	 */
	protected $registry;

	/**
	 * $tsfolder Folder Name
	 * @var string
	 */
	protected $tsfolder = 'ticketsystem';

	/**
	 * $basefile basefile name
	 * @var string
	 */
	protected $basefile = 'TsDefault';

	/**
	 * [$data Data array
	 * @var array
	 */
	protected $data = array();

	/**
	 * __construct Register class autoloader
	 */
	public function __construct($registry) {	
		$this->registry = $registry;

		//add own autoloader
		spl_autoload_register(array(__CLASS__,'_ts_autoloader'));
		spl_autoload_register(array(__CLASS__,'_ts_autoloader_mail'));
	}

	/**
	 * __get Return object of calling class
	 * @param  string $file class name
	 * @return object
	 */
	public function __get($file){
		if(!isset($this->$file))
			$this->$file = new $file($this->registry);

		return $this->$file;
	}

	/**
	 * _ts_autoloader ticketsystem folder autoloader
	 * @param  string $class class name
	 */
	public function _ts_autoloader($class){
		//with namespace
		if(preg_match('/\\\/', $class)){
			$file = DIR_APPLICATION.strtolower(preg_replace('/\\\/','/',$class)).'.php';
		}else{
			$file = DIR_SYSTEM.'library/'.$this->tsfolder.'/'.strtolower($class).'.php';
		}

		if(file_exists($file)){
			include_once($file);
		}
	}

	/**
	 * _ts_autoloader_mail ticketsystem/Mail > folders autoloader
	 * @param  string $class class name
	 */
	public function _ts_autoloader_mail($class){
		$file = '';
		//with namespace
		if(preg_match('/\\\/', $class)){
			$file = DIR_SYSTEM.'library/ticketsystem/Mail/'.preg_replace('/\\\/','/',$class).'.php';
		}

		if($file AND file_exists($file)){
			include_once($file);
		}
	}

}