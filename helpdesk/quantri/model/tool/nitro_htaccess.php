<?php

class ModelToolNitroHtaccess extends ModelToolNitro {

    private function getStringBetween($var1 = "", $var2 = "", $pool){
        $temp1 = strpos($pool, $var1);
        $result = substr($pool, $temp1, strlen($pool));
        $dd = strpos($result,$var2);

        if($dd == 0){
            $dd = strlen($result);
        }

        return substr($result, 0, $dd + strlen($var2));
    }

    private function extractNitrocodeFromHtaccessFile($htaccessContent) {
        $nitrocode = $this->getStringBetween('# STARTNITRO', '# ENDNITRO', $htaccessContent);

        if (strpos($nitrocode, 'STARTNITRO') == false) {
            $nitrocode = '';
        }

        return $nitrocode;
    }

    private function getHtaccessFileContent() {
        $htaccessFile = NITRO_SITE_ROOT . '.htaccess';

        if (!file_exists($htaccessFile)) {
            touch($htaccessFile);
        }

        return trim(file_get_contents($htaccessFile));
    }

    private function restoreHtaccess() {
        $htaccessFileContent = $this->getHtaccessFileContent();

        $old_content = $this->extractNitrocodeFromHtaccessFile($htaccessFileContent);

        if ((string)$old_content != '') {
            $newHtaccessFileContent = str_replace($old_content . PHP_EOL, '', $htaccessFileContent);
            $this->setHtaccessFileContent($newHtaccessFileContent);
        }
    }

    private function restoreHtaccessCompress() {
        $htaccessFileContent = $this->getHtaccessFileContent();

        $old_content = $this->extractNitrocodeCompressFromHtaccessFile($htaccessFileContent);

        if ((string)$old_content != '') {
            $newHtaccessFileContent = str_replace($old_content . PHP_EOL, '', $htaccessFileContent);
            $this->setHtaccessFileContent($newHtaccessFileContent);
        }
    }

    private function restoreHtaccessCdn() {
        $htaccessFileContent = $this->getHtaccessFileContent();

        $old_content = $this->extractNitrocodeCdnFromHtaccessFile($htaccessFileContent);

        if ((string)$old_content != '') {
            $newHtaccessFileContent = str_replace($old_content . PHP_EOL, '', $htaccessFileContent);
            $this->setHtaccessFileContent($newHtaccessFileContent);
        }
    }

    private function restoreHtaccessCookie() {
        $htaccessFileContent = $this->getHtaccessFileContent();

        $old_content = $this->extractNitrocodeCookieFromHtaccessFile($htaccessFileContent);

        if ((string)$old_content != '') {
            $newHtaccessFileContent = str_replace($old_content . PHP_EOL, '', $htaccessFileContent);
            $this->setHtaccessFileContent($newHtaccessFileContent);
        }
    }

    private function extractNitrocodeCompressFromHtaccessFile($htaccessContent) {
        $nitrocode = $this->getStringBetween('# STARTCOMPRESSNITRO', '# ENDCOMPRESSNITRO', $htaccessContent);
        if (strpos($nitrocode,'STARTCOMPRESSNITRO') == false) {
            return '';
        }
        return $nitrocode;
    }

    private function extractNitrocodeCdnFromHtaccessFile($htaccessContent) {
        $nitrocode = $this->getStringBetween('# STARTCDNNITRO', '# ENDCDNNITRO', $htaccessContent);
        if (strpos($nitrocode,'STARTCDNNITRO') == false) {
            return '';
        }
        return $nitrocode;
    }

    private function extractNitrocodeCookieFromHtaccessFile($htaccessContent) {
        $nitrocode = $this->getStringBetween('# STARTCOOKIENITRO', '# ENDCOOKIENITRO', $htaccessContent);
        if (strpos($nitrocode,'STARTCOOKIENITRO') == false) {
            return '';
        }
        return $nitrocode;
    }

    private function isCurrentUserFileOwner($filename) {
        $nitro_temp = NITRO_FOLDER . 'temp';
        if (file_exists($nitro_temp) && is_writable($nitro_temp) && function_exists('posix_getpwuid')) {
            $test_file = $nitro_temp . DIRECTORY_SEPARATOR . 'nitro_usercheck';
            if (@touch($test_file)) {
                $currentUserInfo = posix_getpwuid(fileowner($test_file));
                $htaccessUserInfo = posix_getpwuid(fileowner($filename));
                return ($currentUserInfo['name'] == $htaccessUserInfo['name']);
            }
        }
        return false;
    }

    private function getHeaders($url) {
        loadNitroLib('browser');
        $browser = new NitroBrowser($url);
        try {
            $browser->fetch(true, "HEAD");
            $headers_ = $browser->getHeaders();

            $headers = array();

            foreach ($headers_ as $name => $value) {
                $headers[] = $name . ($value ? ': ' . $value : '');
            }
        } catch (Exception $e) {
            if (NITRO_DEBUG_MODE) {
                $this->log->write('[NitroBrowser]: ' . $e->getMessage());
            }
            return false;
        }
        return $headers;
    }

    private function isAdministrationAccessible() {
        if (NITRO_DEFAULT_ADMIN_ACCESSIBLE) return true;

        $headers = ini_get('allow_url_fopen') ? @get_headers(HTTP_SERVER) : false;

        if (!$headers) {
            $headers = $this->getHeaders(HTTP_SERVER);

            if (!$headers) {
                return false;
            }
        }

        preg_match('/(\d{3})/', $headers[0], $matches);
        
        if ($matches) {
            $code = (int)$matches[1];
            if ($code >= 500) {
                return false;
            }
        }
        
        return true;
    }

    private function setHtaccessFileContent($newcontent) {
        $htaccessFile = NITRO_SITE_ROOT . '.htaccess';
        $htaccessFileBackup = NITRO_SITE_ROOT . '.htaccess-backup';

        if (!is_writable($htaccessFile)) {
            if (function_exists('chmod')) {
                if ($this->isCurrentUserFileOwner($htaccessFile)) {
                    chmod($htaccessFile, 0644);
                } else {
                    $this->session->data['error'] = 'Your PHP user does not have write permissions for the .htaccess file. Please set write permissions or contact your hosting provider to do it.';  
                    return false;
                }
            }
        }

        if (!file_exists($htaccessFile)) {
            touch($htaccessFile);
        }

        if (!file_exists($htaccessFileBackup)) {
            if (!copy($htaccessFile, $htaccessFileBackup)) {
                $this->session->data['error'] = 'Your PHP user does not have permission to create the .htaccess-backup file. Please create it manually and for content set the current content of your .htaccess file.'; 
                return false;
            }
        }

        if (is_writable($htaccessFile)) {
            $backupContent = file_get_contents($htaccessFile);
            file_put_contents($htaccessFile, trim($newcontent));
            if (!$this->isAdministrationAccessible()) {
                file_put_contents($htaccessFile, $backupContent);
                $this->session->data['error'] = 'Applying <i>.htaccess</i> rules for compression/browser cache is not compatible with your server setup. Please make sure you have the following Apache modules enabled: <b>mod_deflate, mod_expires, mod_headers, mod_mime, mod_rewrite</b>.'; 
                return false;
            }
        }

        return true;
    }

    public function applyHtaccessCdnRules() {
        $this->loadCore();

        $this->restoreHtaccessCdn();

        if (!getNitroPersistence('Enabled') || !getNitroPersistence('CDNStandard.GenericURL')) {
            return false;
        }

        $htrules = '# STARTCDNNITRO'.PHP_EOL;
        $htrules .= '<IfModule mod_headers.c>'.PHP_EOL;
        $htrules .= 'Header set Access-Control-Allow-Origin "*"'.PHP_EOL;
        $htrules .= '</IfModule>'.PHP_EOL;
        $htrules .= '# ENDCDNNITRO'.PHP_EOL;

        $newHtaccessFileContent = $htrules . $this->getHtaccessFileContent();

        return $this->setHtaccessFileContent($newHtaccessFileContent);
    }

    public function applyHtaccessCookieRules() {
        $this->loadCore();

        $this->restoreHtaccessCookie();

        if (!getNitroPersistence('Enabled') || !getNitroPersistence('ImageCookies.Enabled')) {
            return false;
        }

        $htrules = '# STARTCOOKIENITRO'.PHP_EOL;
        $htrules .= '<FilesMatch "\.(jpg|jpeg|png|gif|swf|svg|JPG|JPEG|PNG|GIF|SWF|SVG)$">'.PHP_EOL;
        $htrules .= 'Header unset Cookie'.PHP_EOL;
        $htrules .= 'Header unset Set-Cookie'.PHP_EOL;
        $htrules .= '</FilesMatch>'.PHP_EOL;
        $htrules .= '# ENDCOOKIENITRO'.PHP_EOL;

        $newHtaccessFileContent = $htrules . $this->getHtaccessFileContent();

        return $this->setHtaccessFileContent($newHtaccessFileContent);
    }

    public function applyHtaccessRules() {
        $this->loadCore();

        $this->restoreHtaccess();

        if (!getNitroPersistence('Enabled') || !getNitroPersistence('BrowserCache.Enabled')) {
            return false;
        }

        $htrules = '# STARTNITRO'.PHP_EOL;
        if (
            getNitroPersistence('BrowserCache.CSSJS.Period') != 'no-cache' || 
            getNitroPersistence('BrowserCache.Images.Period') != 'no-cache' || 
            getNitroPersistence('BrowserCache.Icons.Period') != 'no-cache'
        ) {
            //$htrules .= '<IfModule mod_expires>'.PHP_EOL;
            $htrules .= 'ExpiresActive On'.PHP_EOL;
            //$htrules .= '</IfModule>'.PHP_EOL;
        }

        if (getNitroPersistence('BrowserCache.CSSJS.Period') != 'no-cache') {
            $maxage = getNitroPersistence('BrowserCache.CSSJS.Period');
            $htrules .= PHP_EOL;
            $htrules .= '#CSS JS XML TXT - '.strtoupper($maxage).PHP_EOL;
            $htrules .= '<FilesMatch "\.(xml|txt|css|js)$">'.PHP_EOL;
            //$htrules .= '<IfModule mod_headers>'.PHP_EOL;
            $htrules .= 'Header set Cache-Control "max-age='.(string)(strtotime($maxage)-time()).', public"'.PHP_EOL;
            //$htrules .= '</IfModule>'.PHP_EOL;
            //$htrules .= '<IfModule mod_expires>'.PHP_EOL;
            $htrules .= 'ExpiresDefault "access plus '.$maxage.'"'.PHP_EOL;
            //$htrules .= '</IfModule>'.PHP_EOL;
            $htrules .= '</FilesMatch>'.PHP_EOL;
        }

        if (getNitroPersistence('BrowserCache.Images.Period') != 'no-cache') {
            $maxage = getNitroPersistence('BrowserCache.Images.Period');
            $htrules .= PHP_EOL;
            $htrules .= '#JPG JPEG PNG GIF SWF SVG - '.strtoupper($maxage).PHP_EOL;
            $htrules .= '<FilesMatch "\.(jpg|jpeg|png|gif|swf|svg|JPG|JPEG|PNG|GIF|SWF|SVG)$">'.PHP_EOL;
            //$htrules .= '<IfModule mod_headers>'.PHP_EOL;
            $htrules .= 'Header set Cache-Control "max-age='.(string)(strtotime($maxage)-time()).', public"'.PHP_EOL;
            //$htrules .= '</IfModule>'.PHP_EOL;
            //$htrules .= '<IfModule mod_expires>'.PHP_EOL;
            $htrules .= 'ExpiresDefault "access plus '.$maxage.'"'.PHP_EOL;
            $htrules .= 'Header set Last-Modified "Wed, 05 Jun 2009 06:40:46 GMT"'.PHP_EOL;
            //$htrules .= '</IfModule>'.PHP_EOL;
            $htrules .= '</FilesMatch>'.PHP_EOL;
        }

        if (getNitroPersistence('BrowserCache.Icons.Period') != 'no-cache') {
            $maxage = getNitroPersistence('BrowserCache.Icons.Period');
            $htrules .= PHP_EOL;
            $htrules .= '#OTF WOFF TTF ICO PDF FLV - '.strtoupper($maxage).PHP_EOL;
            $htrules .= '<FilesMatch "\.(otf|ico|pdf|flv|woff|ttf)$">'.PHP_EOL;
            //$htrules .= '<IfModule mod_headers>'.PHP_EOL;
            $htrules .= 'Header set Cache-Control "max-age='.(string)(strtotime($maxage)-time()).', public"'.PHP_EOL;
            //$htrules .= '</IfModule>'.PHP_EOL;
            //$htrules .= '<IfModule mod_expires>'.PHP_EOL;
            $htrules .= 'ExpiresDefault "access plus '.$maxage.'"'.PHP_EOL;
            //$htrules .= '</IfModule>'.PHP_EOL;
            $htrules .= '</FilesMatch>'.PHP_EOL;
        }

        $htrules .= '# ENDNITRO'.PHP_EOL;

        $newHtaccessFileContent = $htrules . $this->getHtaccessFileContent();

        return $this->setHtaccessFileContent($newHtaccessFileContent);
    }

    public function applyHtaccessCompressionRules() {
        $this->loadCore();

        $this->restoreHtaccessCompress();

        if (!getNitroPersistence('Enabled') || !getNitroPersistence('Compress.Enabled')) {
            return false;
        }

        if (
            getNitroPersistence('BrowserCache.Enabled') &&
            getNitroPersistence('BrowserCache.CSSJS.Period') != 'no-cache'
        ) {
            switch(getNitroPersistence('BrowserCache.CSSJS.Period')) {
                case '1 week' : {
                    $browser_cache = 7 * 24 * 3600;
                } break;
                case '1 month' : {
                    $browser_cache = 30 * 24 * 3600;
                } break;
                case '6 months' : {
                    $browser_cache = 6 * 30 * 24 * 3600;
                } break;
                case '1 year' : {
                    $browser_cache = 365 * 24 * 3600;
                } break;
                default : {
                    $browser_cache = 0;
                }
            }
        }

        $htrules = '# STARTCOMPRESSNITRO'.PHP_EOL;

        if (
            getNitroPersistence('Compress.CSS') && (int)getNitroPersistence('Compress.CSSLevel') > 0 && !NITRO_ALTERNATIVE_CSS_COMPRESS ||
            getNitroPersistence('Compress.JS') && (int)getNitroPersistence('Compress.CSSLevel') > 0 && !NITRO_ALTERNATIVE_JS_COMPRESS
        ) {
            $htrules .= 'RewriteEngine On'.PHP_EOL;
            //$htrules .= '<IfModule mod_rewrite>'.PHP_EOL;
            $htrules .= 'RewriteRule .* - [E=HTTP_IF_MODIFIED_SINCE:%{HTTP:If-Modified-Since}]'.PHP_EOL;
            //$htrules .= '</IfModule>'.PHP_EOL;
        }


        if (getNitroPersistence('Compress.CSS') && (int)getNitroPersistence('Compress.CSSLevel') > 0 && !NITRO_ALTERNATIVE_CSS_COMPRESS) {
            $htrules .= PHP_EOL;
            //$htrules .= '<IfModule mod_rewrite.c>'.PHP_EOL;
            $htrules .= 'RewriteCond %{SCRIPT_FILENAME} !-d'.PHP_EOL;
            $htrules .= 'RewriteRule ^(\/?((catalog)|(assets)).+)\.css$ assets/style.php?l=' . getNitroPersistence('Compress.CSSLevel') . '&p=$1' . (!empty($browser_cache) ? '&c=' . $browser_cache : '') . ' [NC,L]'.PHP_EOL;
            //$htrules .= '</IfModule>'.PHP_EOL;
        }

        if (getNitroPersistence('Compress.JS') && (int)getNitroPersistence('Compress.JSLevel') > 0 && !NITRO_ALTERNATIVE_JS_COMPRESS) {
            $htrules .= PHP_EOL;
            //$htrules .= '<IfModule mod_rewrite.c>'.PHP_EOL;
            $htrules .= 'RewriteCond %{SCRIPT_FILENAME} !-d'.PHP_EOL;
            $htrules .= 'RewriteRule ^(\/?((catalog)|(assets)).+)\.js$ assets/script.php?l=' . getNitroPersistence('Compress.JSLevel') . '&p=$1' . (!empty($browser_cache) ? '&c=' . $browser_cache : '') . ' [NC,L]'.PHP_EOL;
            //$htrules .= '</IfModule>'.PHP_EOL;
        }

        if (NITRO_USE_SVG_COMPRESSION) {
            $htrules .= 'AddType image/svg+xml .svg' . PHP_EOL;
            $htrules .= 'AddOutputFilterByType DEFLATE image/svg+xml' . PHP_EOL;
        }

        if (NITRO_USE_WOFF_COMPRESSION) {
            $htrules .= 'AddType application/x-font-woff .woff' . PHP_EOL;
            $htrules .= 'AddOutputFilterByType DEFLATE application/x-font-woff' . PHP_EOL;
        }

        if (NITRO_ALTERNATIVE_JS_COMPRESS) {
            $htrules .= 'AddType application/javascript .js' . PHP_EOL;
            $htrules .= 'AddOutputFilterByType DEFLATE application/javascript' . PHP_EOL;
        }

        if (NITRO_ALTERNATIVE_CSS_COMPRESS) {
            $htrules .= 'AddType text/css .css' . PHP_EOL;
            $htrules .= 'AddOutputFilterByType DEFLATE text/css' . PHP_EOL;
        }

        $htrules .= '# ENDCOMPRESSNITRO'.PHP_EOL;

        $newHtaccessFileContent = $htrules . $this->getHtaccessFileContent();

        return $this->setHtaccessFileContent($newHtaccessFileContent);
    }

}
