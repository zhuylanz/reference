<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'core.php';//core.php includes top.php

function optimizeCSS($styles, $excludes = array(), $excludes_meta = array()) {
    if (!isNitroEnabled() || !getNitroPersistence('Mini.Enabled')) {
        return $styles;
    }

    global $registry;

    $oc_root = str_replace('/', DS, dirname(DIR_APPLICATION));

    require_once NITRO_CORE_FOLDER . 'cdn.php';

    $css_groups = array();
    $mergeMedias = getNitroPersistence("Mini.MergeCSSMedia");

    foreach($styles as $hash=>$style) {
        $style["media"] = strtolower($style["media"]);

        if ($mergeMedias && $style["media"] == "screen") {
            $style["media"] = "all";
        }

        if (empty($css_groups[$style['rel'].'/nitro_divider/'.$style['media']])) $css_groups[$style['rel'].'/nitro_divider/'.$style['media']] = array();
        $css_groups[$style['rel'].'/nitro_divider/'.$style['media']][$hash] = $style['href'];
    }

    if (getNitroPersistence('Mini.CSS')) {
        foreach ($css_groups as $key=>$files) {
            $css_groups[$key] = minify('css', $files, $excludes, $excludes_meta);
        }
    }

    if (getNitroPersistence('Mini.CSSCombine')) {
        foreach ($css_groups as $key=>$files) {
            $css_groups[$key] = combine('css', $files, $excludes, $excludes_meta);
        }
    }

    $styles = array();

    foreach ($css_groups as $key=>$files) {
        list($rel, $media) = explode('/nitro_divider/', $key);
        foreach ($files as $file) {
            $styles[md5($file)] = array(
                'rel' => $rel,
                'media' => $media,
                'href' => $file
            );
        }
    }

    return nitroCDNResolve($styles);
}

function optimizeJS($scripts, $excludes = array(), $excludes_meta = array()) {
    if (!isNitroEnabled() || !getNitroPersistence('Mini.Enabled')) {
        return $scripts;
    }

    global $registry;
    $oc_root = str_replace('/', DS, dirname(DIR_APPLICATION));
    $cache = NULL;
    $cachefile = NULL;
    $filename = NULL;

    //load NitroCache
    require_once NITRO_CORE_FOLDER . 'cdn.php';

    $nitroSettings = getNitroPersistence();

    if (getNitroPersistence('Mini.JS')) {
        $scripts = minify('js', $scripts, $excludes, $excludes_meta);
    }

    if (getNitroPersistence('Mini.JSCombine')) {
        $scripts = combine('js', $scripts, $excludes, $excludes_meta);
    }

    return nitroCDNResolve($scripts);
}

function is_url(&$string) {
    $standard_match = preg_match('@^(https?:)?//.*$@', $string);

    if (!$standard_match) {
        $trimmed = trim($string, '/');

        //if (stripos($trimmed, 'index.php?') === 0 || stripos($trimmed, 'stylesheet/cssminify1.php?') !== FALSE || preg_match('~stylesheet\/theme[0-9]+\.php\?~', $trimmed) !== FALSE) {
        if (preg_match('~\.php\?~', $trimmed) != FALSE) {
            $string = HTTP_SERVER . $trimmed;
            $standard_match = true;
        }
    }

    return !!$standard_match;//this converts anything to bool, so don't remove the double negation pls
}

function clean_file_paths(&$files, &$excludes) {
    $oc_root = str_replace('/', DS, dirname(DIR_APPLICATION));

    $autoExclude = getNitroPersistence('Mini.AutoExclude');

    $webshopUrl = preg_replace('@^(//\w)@', 'http:$1', getWebshopUrl());
    $cdnURL = preg_replace('@^(//\w)@', 'http:$1', getNitroPersistence('CDNStandard.GenericURL'));

    $localhost = !empty($webshopUrl) ? parse_url('http://' . preg_replace('/^(https?:)?\/\//', '', $webshopUrl), PHP_URL_HOST) : '';
    $cdnHost = !empty($cdnURL) ? parse_url('http://' . preg_replace('/^(https?:)?\/\//', '', $cdnURL), PHP_URL_HOST) : '';
    $localpath = !empty($webshopUrl) ? parse_url('http://' . preg_replace('/^(https?:)?\/\//', '', $webshopUrl), PHP_URL_PATH) : '';

    if (!$localpath || $localpath == '.') {
        $localpath = '/';
    }

    foreach($files as $hash=>$file) {
        $local_file_path_test = ltrim(str_replace(array('/', '\\'), DS, $file), DS);
        if (is_readable($local_file_path_test)) {
            $files[$hash] = str_replace($oc_root, "", $local_file_path_test);
            continue;
        } else if (is_readable(DS . $local_file_path_test)) {
            $files[$hash] = str_replace($oc_root, "", DS . $local_file_path_test);
            continue;
        }

        $file = trim($file);
        $files[$hash] = html_entity_decode($file);
        $filename = $oc_root.DS.ltrim(str_replace(array('/', '\\'), DS, $file), DS);

        if (is_readable($filename)) {
            $files[$hash] = ltrim(str_replace(array('/', '\\'), DS, $file), DS);
            continue;
        }

        $url_info = parse_url(preg_replace('@^(//\w)@', 'http:$1', $file));

        $host = !empty($url_info['host']) ? $url_info['host'] : $localhost;
        $path = !empty($url_info['path']) ? $url_info['path'] : '';

        $known_hosts = array($localhost);
        if (!empty($cdnHost)) $known_hosts[] = $cdnHost;

        if (!in_array($host, $known_hosts)) {
            if ($autoExclude) {
                $excludes[] = html_entity_decode(basename($file));
            }
        } else {
            $path = preg_replace('@^'.$localpath.'/?@', '/', $path);
            $ext = substr(strrchr($path, '.'), 1);


            if (in_array($ext, array('js', 'css'))) {
                $filename_tmp = $oc_root . DS . preg_replace('@^'.DS.'(\w)@', '$1', str_replace('/', DS, $path));
                if (is_readable($filename_tmp)) {
                    $files[$hash] = ltrim(str_replace('/', DS, $path), DS);
                    continue;
                }
            }

            //Normalize the URL. The code below handles cases like http://localhost/mysite/index.php?route=dir/controller/method, http://localhost/mysite?route=dir/controller/method,//domain.com/resource, /resource/path.css
            //Be very careful if you need to edit this
            $files[$hash] = $webshopUrl . '/' . preg_replace('@' . preg_replace('@^https?:?//@', '', $webshopUrl) . '/?@', '', preg_replace('@^(?:https?\:)/+(\w)@', '$1', $file));
        }
    }
}

function minify($type, $files, $excludes, $excludes_meta = array()) {
    if (!in_array($type, array('css', 'js'))) return $files;

    //extract local fylesystem path
    clean_file_paths($files, $excludes);

    global $registry;
    $oc_root = str_replace('/', DS, dirname(DIR_APPLICATION));
    $filename = NULL;

    $position_excluded_files = 'before';
    switch ($type) {
    case 'css':
        if (getNitroPersistence('Mini.ExcludedCSSPosition') == 'after') {
            $position_excluded_files = 'after';
        }
        break;
    case 'js';
        if (getNitroPersistence('Mini.ExcludedJSPosition') == 'after') {
            $position_excluded_files = 'after';
        }
        break;
    }

    if (!defined('DS')) {
        define('DS', DIRECTORY_SEPARATOR);
    }

    if (!file_exists($oc_root.DS.'assets')) {
        mkdir($oc_root.DS.'assets');
    }

    if (!file_exists($oc_root.DS.'assets'.DS.$type)) {
        mkdir($oc_root.DS.'assets'.DS.$type);
    }

    switch ($type) {
    case 'js':
        include_once NITRO_LIB_FOLDER.'minifier'.DS.'JSShrink.php';
        break;
    case 'css':
        include_once NITRO_LIB_FOLDER.'minifier'.DS.'CSSMin.php';
        break;
    }

    $webshopUrl = getWebshopUrl();
    $localhost = !empty($webshopUrl) ? parse_url('http://' . preg_replace('/^(https?:)?\/\//', '', $webshopUrl), PHP_URL_HOST) : '';
    $localpath = !empty($webshopUrl) ? parse_url('http://' . preg_replace('/^(https?:)?\/\//', '', $webshopUrl), PHP_URL_PATH) : '';
    if ($localpath == '.') {
        $localpath = '/';
    }

    $asset_files = array(
        'before' => array(),
        'middle' => array(),
        'after' => array()
    );

    foreach ($files as $hash=>$file) {
        if (!is_excluded_file($file, $excludes, $excludes_meta)) {
            $filename = $oc_root.DS.trim(str_replace('/', DS, $file), DS);
            $basefilename = basename($file, '.'.$type);

            $content = is_readable($filename) ? file_get_contents($filename) : (is_url($file) ? fetchRemoteContent($file) : '');
            $target = '/assets/'.$type.'/'.'nitro-mini-' . md5($content) . '.'.$type;
            $targetAbsolutePath = $oc_root.DS.trim(str_replace('/', DS, $target), DS);
            $recache = !file_exists($targetAbsolutePath);

            $url_info = parse_url('http://' . preg_replace('/^(https?:)?\/\//', '', $file));
            $host = !empty($url_info['host']) ? $url_info['host'] : '';
            $path = !empty($url_info['path']) ? dirname($url_info['path']) : '';
            if ($path == '.') {
                $path = '/';
            }

            if (is_readable($filename)) {
                if (strpos($filename, 'system/nitro/temp') === false) {
                    $urlToCurrentDir = $webshopUrl.dirname('/'.trim($file, '/'));
                } else {
                    $urlToCurrentDir = $webshopUrl;
                }
            } else {
                if ($host == $localhost || empty($host)) {
                    $path = preg_replace('@^'.$localpath.'/?@', '/', $path);
                    $urlToCurrentDir = $webshopUrl.$path;
                } else {
                    $urlToCurrentDir = dirname($file);
                }
            }

            $urlToCurrentDir = str_replace(DIRECTORY_SEPARATOR, '/', $urlToCurrentDir);//convert Windows style paths, to web style

            if ($recache) {
                touch($targetAbsolutePath);
                $isMinified = is_readable($filename) ? preg_match('/\.min\.' . $type . '$/i', $filename) : preg_match('/\.min\.' . $type . '($|\?)/', $file);

                switch($type) {
                case 'js':
                    try {
                        if (!(NITRO_DONT_MINIFY_MINIFIED_RESOURCES && $isMinified)) {
                            $content = Minifier::minify($content, array('flaggedComments' => false));
                        }
                    } catch (Exception $e) {
                        if (NITRO_DEBUG_MODE) {
                            $log = new Log(date('Y-m-d') . "_nitro_minify_error.txt");
                            $log->write($e->getMessage() . ' in file: ' . $filename);
                        }
                    }
                    break;
                case 'css':
                    $content = preg_replace('/(url\()(?!\s*?[\'\"]?\s*?(?:(?:https?\:\/\/)|(?:data\:)|(?:\/)))(\s*?[\'\"]?\s*?)\/?(?!\/)/', '$1$2'.$urlToCurrentDir.'/', $content);

                    if (!(NITRO_DONT_MINIFY_MINIFIED_RESOURCES && $isMinified)) {
                        $content = Nitro_Minify_CSS_Compressor::process($content);
                    }
                    break;
                }

                file_put_contents($targetAbsolutePath, $content);

            } else {
                touch($targetAbsolutePath);// This fixes the issue where styles were disapearing after a while, because the cron was clearing the minified files, because their mtime was old, although they are still in use. Do not remove this.
            }

            $asset_files['middle'][$hash] = trim($target, '/');
        } else {
            $pos = get_file_explicit_pos($file, $excludes_meta, $position_excluded_files);
            $asset_files[$pos][$hash] = $file;
        }
    }

    return array_merge($asset_files['before'], $asset_files['middle'], $asset_files['after']);
}

function is_excluded_file($path, $excludes, &$excludes_meta = array()) {
    foreach ($excludes as $e) {
        if (strpos($path, $e) !== false) {
            if (!empty($excludes_meta[$e])) {
                $excludes_meta[$path] = $excludes_meta[$e];
            }
            return true;
        }
    }
    return false;
}

function get_file_explicit_pos($path, $excludes_meta, $default = 'before') {
    if (isset($excludes_meta[$path]) && !empty($excludes_meta[$path]['position'])) {
        return $excludes_meta[$path]['position'];
    }

    return $default;
}

function combine($type, $files, $excludes, $excludes_meta) {
    if (!in_array($type, array('css', 'js'))) return $files;

    //extract local fylesystem path
    clean_file_paths($files, $excludes);

    global $registry;
    $oc_root = str_replace('/', DS, dirname(DIR_APPLICATION));
    $filename = NULL;

    $position_excluded_files = 'before';
    switch ($type) {
    case 'css':
        if (getNitroPersistence('Mini.ExcludedCSSPosition') == 'after') {
            $position_excluded_files = 'after';
        }
        break;
        case 'js';
        if (getNitroPersistence('Mini.ExcludedJSPosition') == 'after') {
            $position_excluded_files = 'after';
        }
        break;
    }

    if (!defined('DS')) {
        define('DS', DIRECTORY_SEPARATOR);
    }

    $comboHash = '';
    $excludedFiles = array(
        'before' => array(),
        'after' => array()
    );
    $includedFiles = 0;
    $contents = array();

    foreach ($files as $hash=>$file) {
        if (!is_excluded_file($file, $excludes, $excludes_meta)) {
            $filename = $oc_root.DS.trim(str_replace('/', DS, $file), DS);
            $content = is_readable($filename) ? file_get_contents($filename) : (is_url($file) ? fetchRemoteContent($file) : '');
            $comboHash .= md5($content);
            $contents[$file] = $content;

            $includedFiles++;
        } else {
            $pos = get_file_explicit_pos($file, $excludes_meta, $position_excluded_files);
            $excludedFiles[$pos][$hash] = $file;
        }
    }

    $comboHash = md5($comboHash);
    $target = '/assets/'.$type.'/'.'nitro-combined-' . $comboHash . '.'.$type;
    $targetAbsolutePath = $oc_root.DS.trim(str_replace('/', DS, $target), DS);
    $recache = !file_exists($targetAbsolutePath);

    $webshopUrl = getWebshopUrl();
    $localhost = !empty($webshopUrl) ? parse_url('http://' . preg_replace('/^(https?:)?\/\//', '', $webshopUrl), PHP_URL_HOST) : '';
    $localpath = !empty($webshopUrl) ? parse_url('http://' . preg_replace('/^(https?:)?\/\//', '', $webshopUrl), PHP_URL_PATH) : '';
    if ($localpath == '.') {
        $localpath = '/';
    }

    $combinedContent = '';

    if ($recache) {
        $counter = 0;
        foreach ($files as $hash=>$file) {
            if (!is_excluded_file($file, $excludes, $excludes_meta)) {
                $filename = $oc_root.DS.trim(str_replace('/', DS, $file), DS);

                $content = $contents[$file];
                
                if (!empty($content)) {
                    if ($type == 'js') {
                        if (NITRO_TRY_CATCH_WRAP) {
                            $content = 'try{' . PHP_EOL . $content . PHP_EOL . '}catch(e){console.log(e.message + " in file: ' . $file . '")};';
                        } else if (substr($content, -1) == ')') {
                            $content .= ';';
                        }
                    }

                    if ($type == 'css') {
                        $url_info = parse_url($file);
                        $host = !empty($url_info['host']) ? $url_info['host'] : '';
                        $path = !empty($url_info['path']) ? dirname($url_info['path']) : '';
                        if ($path == '.') {
                            $path = '/';
                        }

                        if (is_readable($filename)) {
                            $urlToCurrentDir = $webshopUrl.dirname('/'.trim($file, '/'));
                        } else {
                            if ($host == $localhost || empty($host)) {
                                $path = preg_replace('@^'.$localpath.'/?@', '/', $path);
                                $urlToCurrentDir = $webshopUrl.$path;
                            } else {
                                $urlToCurrentDir = dirname($file);
                            }
                        }

                        $urlToCurrentDir = str_replace(DIRECTORY_SEPARATOR, '/', $urlToCurrentDir);//convert Windows style paths, to web style
                        
                        $content = preg_replace('/(url\()(?!\s*?[\'\"]?\s*?(?:(?:https?\:\/\/)|(?:data\:)|(?:\/)))(\s*?[\'\"]?\s*?)\/?(?!\/)/', '$1$2'.$urlToCurrentDir.'/', $content);
                    }
                    
                    $combinedContent .= (($counter > 0) ? PHP_EOL : '') . $content;
                    
                    unset($content);
                    $counter++;
                }
            }
        }
        
        if ($type == 'css') {
            /* pull imports to the top and include their content if possible */
            $imports = array();
            preg_match_all('/\@import[^\;]*\;/', $combinedContent, $imports);
            if (!empty($imports)) {
                $imports = array_reverse($imports[0]);
                foreach ($imports as $import) {
                    $importUrl = preg_replace('/[^\'"\(]*(?:[\'"\(\s]?)*(.*?)(?:[\'"\)]).*/', '$1', $import);

                    $url_info = parse_url('http://' . preg_replace('/^(https?:)?\/\//', '', $importUrl));
                    $host = !empty($url_info['host']) ? $url_info['host'] : '';
                    $path = !empty($url_info['path']) ? dirname($url_info['path']) : '';
                    if ($path == '.') {
                        $path = '/';
                    }

                    if (getNitroPersistence('Mini.CSSFetchImport') && (!getNitroPersistence('Mini.AutoExclude') || (getNitroPersistence('Mini.AutoExclude') && $host == $localhost))) {
                        $tmpImportContent = fetchRemoteContent($importUrl);
                    }

                    if (!empty($tmpImportContent)) {
                        $combinedContent = $tmpImportContent.str_replace($import, '', $combinedContent);
                    } else {
                        $combinedContent = $import.str_replace($import, '', $combinedContent);
                    }
                }
            }
        }

        file_put_contents($targetAbsolutePath, $combinedContent);
    } else {
        touch($targetAbsolutePath);
    }

    if ($includedFiles > 0) {
        return array_merge($excludedFiles['before'], array(md5($target) => trim($target, '/')), $excludedFiles['after']);
    } else {
        return array_merge($excludedFiles['before'], $excludedFiles['after']);
    }
}
