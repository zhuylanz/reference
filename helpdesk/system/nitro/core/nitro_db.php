<?php
class NitroDb {
    public static $singleton;
    public static $created_nitro_product_cache = false;
    private $link = false;
    
    public static function getInstance() {
        if (empty(self::$singleton)) self::$singleton = new NitroDb();
        return self::$singleton->getLink();
    }
    
    public function __construct() {
        $class = 'DB\\' . DB_DRIVER;

        if (!class_exists($class)) {
            $file = DIR_SYSTEM . 'library' . DIRECTORY_SEPARATOR . str_replace('\\', '/', strtolower($class)) . '.php';

            if (file_exists($file)) {
                require_once $file;
            }
        }

        if (class_exists($class)) {
            $this->link = new $class(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        }
    }
    
    public function getLink() { return $this->link; }
}
