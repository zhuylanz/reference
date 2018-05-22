<?php 

function getSupportedCookies() {
	$supportedCookies = explodeTrim("\n", getNitroPersistence('PageCache.SupportedCookies'));
    return $supportedCookies;
}

function getIgnoredRoutes() {
	$ignoredRoutes = explodeTrim("\n", getNitroPersistence('PageCache.IgnoredRoutes'));
	
	$predefinedIgnoredRoutes = array(
		'checkout/cart', 
		'checkout/checkout',
		'checkout/success',
		'account/register',
		'account/login',
		'account/edit',
		'account/account',
		'account/password',
		'account/address',
		'account/address/update',
		'account/address/delete',
		'account/wishlist',
		'account/order',
		'account/download',
		'account/return',
		'account/return/insert',
		'account/reward',
		'account/voucher',
		'account/transaction',
		'account/newsletter',
		'account/logout',
		'affiliate/login',
		'affiliate/register',
		'affiliate/account',
		'affiliate/edit',
		'affiliate/password',
		'affiliate/payment',
		'affiliate/tracking',
		'affiliate/transaction',
		'affiliate/logout',
		'information/contact',
		'product/compare',
		'error/not_found'
	);
	
	$ignoredRoutes = array_merge($predefinedIgnoredRoutes, $ignoredRoutes);

	return $ignoredRoutes;
}

function nitroGetVersion() {
    $index_contents = file_get_contents(dirname(DIR_APPLICATION) . DIRECTORY_SEPARATOR . 'index.php');

    $matches = array();

    preg_match("/VERSION\'\s*,\s*\'(.*?)\'/", $index_contents, $matches);

    if (!empty($matches[1])) {
        return $matches[1];
    } else {
        return null;
    }
}

function isCustomerLogged() {
    nitroEnableSession();

    $session = &nitroGetSession();
	return !empty($session['customer_id']);
}

function isItemsInCart() {
    if (isset($GLOBALS['nitroIsItemsInCart'])) return $GLOBALS['nitroIsItemsInCart'];

    if (!defined('VERSION')) {
        define('VERSION', nitroGetVersion());
    }

    nitroEnableSession();
    
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Hidden IP';
    
    $persistent_cart_settings = getOpenCartSetting('PersistentCart');

    if (!empty($persistent_cart_settings)) {
        $folder = dirname(dirname(dirname(dirname(__FILE__)))).'/vendors/persistentcart/'.$ip.'.txt';

        $persistent_cart_settings = (VERSION < '2.1') ? unserialize($persistent_cart_settings) : json_decode($persistent_cart_settings, true);

        if ($persistent_cart_settings['Enabled'] == 'yes' && file_exists($folder)) {
            $GLOBALS['nitroIsItemsInCart'] = true;
            return true;
        }
    }
    
    if (VERSION > '2.0.3.1') {
        $db = NitroDb::getInstance();

        $session_id = !empty($_COOKIE['default']) ? $_COOKIE['default'] : session_id();

        $cart_query = $db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND session_id = '" . $session_id . "'");
        //if (!$cart_query->num_rows) {
        //    $cart_query = $db->query("SELECT * FROM " . DB_PREFIX . "quotationc WHERE customer_id = '0' AND session_id = '" . $session_id . "'");
        //}
        $GLOBALS['nitroIsItemsInCart'] = $cart_query->num_rows > 0;
        return $cart_query->num_rows > 0;
    } else {
        $session = &nitroGetSession();
        $GLOBALS['nitroIsItemsInCart'] = (!empty($session['cart']) || !empty($session['persistent_cart']));
        return (!empty($session['cart']) || !empty($session['persistent_cart']));
    }
}

function isWishlistAdded() {
    nitroEnableSession();

    $session = &nitroGetSession();
	return !empty($session['wishlist']);
}

function isAJAXRequest() { 
	return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function isPOSTRequest() { 
	return !empty($_POST) || (!empty($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST');
}

function pageRefresh() {
	echo '<script type="text/javascript">document.location = document.location;</script>'; exit;	
}

function isPreCacheRequest() {
	if (!function_exists('getallheaders')) { 
    function getallheaders() { 
      $headers = array(); 
      foreach ($_SERVER as $name => $value) { 
        if (substr($name, 0, 5) == 'HTTP_') { 
          $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
        } 
      } 

      return $headers; 
    } 
	}

	$headers = getallheaders();

	return !empty($headers['Nitro-Precache']);
}

function isYMM() {
    nitroEnableSession();
    
    $session = &nitroGetSession();
    return !empty($session['ymm']);
}

function passesPageCacheValidation() {
    if (!empty($_COOKIE['nonitro'])) {
        return false;
    }
    
	if (NITRO_IGNORE_AJAX_REQUESTS && isAJAXRequest()) {
		return false;	
	}

	if (NITRO_IGNORE_POST_REQUESTS && isPOSTRequest()) {
		return false;	
	}
	
	if (isItemsInCart() || isCustomerLogged() || isWishlistAdded() || (isAdminLogged() && NITRO_DISABLE_FOR_ADMIN) || isYMM()) {
		return false;	
	}
	
	$ignoredRoutes = getIgnoredRoutes();

	global $registry;

	if (!empty($registry)) {
		$current_route = !empty($registry->get('request')->get['route']) ? $registry->get('request')->get['route'] : "common/home";
	}

	if (
		(!empty($_GET['route']) && in_array($_GET['route'], $ignoredRoutes)) || 
		(!empty($current_route) && in_array($current_route, $ignoredRoutes))
	) {
		return false;
	}

	if(areWeInIgnoredUrl()) {
		return false;
	}

	return true;
}

function decideToShowFrontWidget() {
	if (!getNitroPersistence('PageCache.Enabled')) return false;

    $store_front_widget = getNitroPersistence('PageCache.StoreFrontWidget');

    $session = &nitroGetSession();
    if (empty($session['NitroRenderTime']) || empty($_GET['cachefile'])) return false;

	switch ($store_front_widget) {
		case 'showOnlyWhenAdminIsLogged' : return isAdminLogged(); break;
		case 'showAlways': return true; break;
	}

	return false;
}

function serveCacheIfNecessary() {
	nitroEnableSession();
	
	if (passesPageCacheValidation() == false) {
		return false;	
	}
	
	$nitrocache_time = getPageCacheTime();

	$cachefile = NITRO_PAGECACHE_FOLDER . generateNameOfCacheFile();

	if (file_exists($cachefile) && @filemtime($cachefile) && (time() - $nitrocache_time) < filemtime($cachefile)) {
        $cache_filemtime = filemtime($cachefile);

        $quick_refresh_file = getQuickCacheRefreshFilename(false);

        $parts = explode(DS, dirname($cachefile));
        $count_parts_pagecache_dir = count(explode(DS, rtrim(NITRO_PAGECACHE_FOLDER, DS)));

        while(count($parts) >= $count_parts_pagecache_dir) {
            $quick_refresh_path = implode(DS, $parts) . DS . $quick_refresh_file;
            if (file_exists($quick_refresh_path)) {
                if (filemtime($quick_refresh_path) > $cache_filemtime) {
                    return false;
                }
            }
            array_pop($parts);
        }

		$before = microtime(true);
		usleep(1);
		header('Content-type: text/html; charset=utf-8');
		
		serveBrowserCacheHeadersIfNecessary($cache_filemtime);
		serveSpecialHeadersIfNecessary($cache_filemtime);
		
		if (loadGzipHeadersIfNecessary()) {
			$cachefile = $cachefile . '.gz';	
		}

		readfile($cachefile);

		$after = microtime(true);

        nitroEnableSession();

        $session = &nitroGetSession();
		$session['NitroRenderTime'] = $after - $before;

		exit;
	}
}

function serveBrowserCacheHeadersIfNecessary($filemtime) {
	if (headers_sent()) {
		return;
	}

    $startTime = microtime(true);
	
    nitroEnableSession();
    
    $session = &nitroGetSession();
	if (!empty($session['NitroSwitchLanguage'])) {
		unset($session['NitroSwitchLanguage']);
		return;
	}

	if (!empty($session['NitroSwitchCurrency'])) {
		unset($session['NitroSwitchCurrency']);
		return;
	}

    if (empty($session['NitroCookiesToPage'])) {
        $session['NitroCookiesToPage'] = array();
    }

    if (empty($_SERVER['HTTP_HOST']) || empty($_SERVER['REQUEST_URI'])) {
        return;
    }

    $page_key = md5($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    $cookie_string = nitroGetLanguage() . nitroGetCurrency() . getSupportedCookiesPrefix();

    if (empty($session['NitroCookiesToPage'][$page_key])) {
        $session['NitroCookiesToPage'][$page_key] = $cookie_string;
        return;
    } else if ($session['NitroCookiesToPage'][$page_key] != $cookie_string) {
        $session['NitroCookiesToPage'][$page_key] = $cookie_string;
        return;
    }

	header('Nitro-Cache: Enabled');
	
	$userAgent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'FOOBAR';
	$isIE = (stripos($userAgent, 'MSIE ') !== false);

	$code304 = false;
	
    if (getNitroPersistence('BrowserCache.Enabled')) {
        if (getNitroPersistence('BrowserCache.Headers.Pages.CacheControl') && !$isIE) {
            header('Cache-Control:public, max-age=31536000');
        }

        if (getNitroPersistence('BrowserCache.Headers.Pages.Expires')) {
            header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + getPageCacheTime()));
            $code304 = true;
        }

        if (getNitroPersistence('BrowserCache.Headers.Pages.LastModified')) {
            header('Last-Modified: '.gmdate('D, d M Y H:i:s \G\M\T', $filemtime));
            $code304 = true;
        }
        
        if ($code304 && !empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $filemtime) {
            require_once NITRO_CORE_FOLDER . 'http_response_code.php';
            http_response_code(304);
            $session['NitroRenderTime'] = microtime(true) - $startTime;

            exit;
        }
    }
}


function serveSpecialHeadersIfNecessary($filemtime) {
	$headers_file = NITRO_HEADERS_FOLDER . generateNameOfCacheFile();

	if (file_exists($headers_file) && filemtime($headers_file) >= $filemtime) {
		$headers = explode("\n", file_get_contents($headers_file));
		foreach ($headers as $header) {
			header($header, true);
		}
	}
}


function minifyHtmlIfNecessary($html) {
	if (NITRO_HTML_MINIFICATION_LEVEL == 0 && isNitroEnabled() && getNitroPersistence('Mini.Enabled') && getNitroPersistence('Mini.HTML')) {	
		return minifyHTML($html);
	}

	return $html;
}

function loadGzipHeadersIfNecessary() {
	if (getNitroPersistence('Compress.Enabled') && getNitroPersistence('Compress.HTML')) {
		$headers = array();

		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)) {
			$encoding = 'gzip';
		} 
	
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)) {
			$encoding = 'x-gzip';
		}
	
		if (!isset($encoding)) {
			return false;
		}

		if (headers_sent()) {
			return false;
		}
	
		if (connection_status()) { 
			return false;
		}
		
		header('Content-Encoding: ' . $encoding);

		return true;
	}

	return false;
}

function applyCloudFlareFix() {
	if (getNitroPersistence('CDNCloudFlare.Enabled')) {
		if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
		  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		}
	}
}

function open_nitro() {
	if (session_id()) {
        $session = &nitroGetSession();
		if (isset($session['nitro_ftp_persistence'])) unset($session['nitro_ftp_persistence']);
		if (isset($session['nitro_persistence'])) unset($session['nitro_persistence']);
	}

	if (isset($_POST['cacheFileToClear']) && count($_POST) == 1) {
		if (file_exists(NITRO_PAGECACHE_FOLDER . $_POST['cacheFileToClear'])) {
			unlink(NITRO_PAGECACHE_FOLDER . $_POST['cacheFileToClear']);
		}

		if (file_exists(NITRO_PAGECACHE_FOLDER . $_POST['cacheFileToClear'] . ".gz")) {
			unlink(NITRO_PAGECACHE_FOLDER . $_POST['cacheFileToClear'] . ".gz");
		}

		pageRefresh();
	}

	if (isNitroEnabled()) {
		applyCloudFlareFix();
		serveCacheIfNecessary();
	}

	$GLOBALS['nitro.start.time'] = microtime(true);

	ob_start(); // Start the output buffer
}
