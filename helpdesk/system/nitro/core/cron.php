<?php
error_reporting(E_ALL ^ E_WARNING);
//date_default_timezone_set("Europe/Sofia");

if ($argc >= 2) {
    $_SERVER["HTTP_HOST"] = $argv[1];
    $_SERVER["SERVER_NAME"] = $argv[1];
    putenv("SERVER_NAME=". $argv[1]);
} else {
    $_SERVER["HTTP_HOST"] = "localhost";
    $_SERVER["SERVER_NAME"] = "localhost";
}

$current_dir = dirname(__FILE__);
$now = time();

chdir(dirname(dirname(dirname($current_dir))));

require_once realpath('./config.php');
require_once realpath($current_dir . DIRECTORY_SEPARATOR . 'core.php');

if (!defined('VERSION')) {
    define('VERSION', nitroGetVersion());
}

// Kick out intruders
if (!isCli()) exit;

// We expect this to always be on, but just in case...
if (!getNitroPersistence('CRON.Local.Status')) exit;

if (!getNitroPersistence('CRON.Local.PreCache') && !getNitroPersistence('CRON.Local.Delete')) exit;

$tasks = array();

if (getNitroPersistence('CRON.Local.Delete')) {
  $period = getNitroPersistence('PageCache.ExpireTime');
  $period = !empty($period) ? $period : NITRO_PAGECACHE_TIME;
  
  $tasks[] = '- Delete files older than ' . date('Y-m-d H:i:s', $now - $period);

  cleanNitroCacheFolders('index.html', $period);

  //Delete cache for products which specials or discounts have just expired
  $yesterday = date("Y-m-d", strtotime("-1 day"));
  $db = NitroDb::getInstance();

  $special_results = $db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE date_end = '" . $db->escape($yesterday) . "'");
  foreach ($special_results->rows as $res) {
      clearProductCache($res["product_id"]);
  }

  $discount_results = $db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE date_end = '" . $db->escape($yesterday) . "'");
  foreach ($discount_results->rows as $res) {
      clearProductCache($res["product_id"]);
  }
}

if (getNitroPersistence('CRON.Local.PreCache')) {
  $precache_progress = '- Precache sitemap files.';
  
  $token = getNitroPersistence('CRON.Remote.Token');
  $url = HTTP_SERVER . 'index.php?route=tool/nitro/get_pagecache_stack&cron_token=' . $token;

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  $data = curl_exec($ch);

  curl_close($ch);

  if (!empty($data)) {
    $urls = json_decode($data);

    $total = count($urls);

    $currency = strtoupper(getOpenCartSetting('config_currency'));
    $language = getOpenCartSetting('config_language');

    foreach ($urls as $target) {
      $ch = curl_init($target);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Nitro-Precache');
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      $headers = array(
        'Nitro-Precache: 1',
        'Cache-Control: no-cache',
        'Pragma: no-cache',
        'Connection: keep-alive'
      );

      $cookie = 'currency=' . $currency . '; language=' . $language . ';';

      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_COOKIE, $cookie);
      curl_exec($ch);
      curl_close($ch);
    }

    $precache_progress .= ' Precached ' . $total . ' pages';
  } else {
    $precache_progress .=  ' Pre-cache data not found. URL: ' . $url . PHP_EOL;
  }

  $tasks[] = $precache_progress;
}

if (getNitroPersistence('CRON.Local.SendEmail')) {
  $subject =  'NitroPack Local CRON job';
  $message =  'Time of execution: ' . date('Y-m-d H:i:s', $now) . PHP_EOL . PHP_EOL;
  $message .= 'Executed tasks: ' . PHP_EOL . implode(PHP_EOL, $tasks) . PHP_EOL . PHP_EOL;
  
  sendNitroMail(getOpenCartSetting('config_email'), $subject, $message);
}
