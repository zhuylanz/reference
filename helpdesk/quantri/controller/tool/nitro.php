<?php 
class ControllerToolNitro extends Controller { 
    private $error = array();
    private $session_closed = false;
    private $start_time;
    private $smush_progress_message_limit = 10;

    public function __construct($registry) {
        parent::__construct($registry);

        require_once NITRO_FOLDER . 'core/http_response_code.php';
        require_once(NITRO_FOLDER . 'core/core.php');

        $this->start_time = time();
    }

    public function index() {
        $this->language->load('tool/nitro');

        $this->load->model('tool/nitro');

        $this->load->model('tool/nitro_upgrade');

        $this->model_tool_nitro_upgrade->run_upgrade();

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addScript('view/javascript/nitro/justgage/resources/js/raphael.2.1.0.min.js');
        $this->document->addScript('view/javascript/nitro/justgage/resources/js/justgage.1.0.1.js');
        $this->document->addScript('view/javascript/nitro/nitro.js');
        $this->document->addScript('view/javascript/nitro/nitro.cachemanager.js');
        $this->document->addScript('view/javascript/nitro/nitro.precache.js');
        $this->document->addScript('view/javascript/nitro/nitro.smusher.js');
        $this->document->addScript('view/javascript/nitro/nitro.pagespeed.js');
        $this->document->addScript('view/javascript/nitro/nitro.preminify.js');
        $this->document->addScript('view/javascript/nitro/nitro.css_extract.js');
        $this->document->addStyle('view/stylesheet/nitro.css');
        $this->document->addStyle('view/javascript/nitro/font-awesome/css/font-awesome.min.css');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->user->hasPermission('modify', 'tool/nitro') == false) {
            $this->session->data['error'] = $this->language->get('text_nopermission');
            $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));             
        }

        $persistence = $this->model_tool_nitro->getPersistence();

        $data['widget']['pagespeed'] = null;
        $data['pagespeedStoreUrl'] = (defined('HTTP_SERVER') && HTTP_SERVER != '') ? dirname(HTTP_SERVER) . '/' : false;
        $data['pagespeedSaveUrl'] = preg_replace("/^https?\:\/\//", "//", html_entity_decode($this->url->link("tool/nitro/savePagespeedResults", "token=" . $this->session->data["token"])));
        $data['pagespeedApiKey'] = !empty($persistence['Nitro']['GooglePageSpeedApiKey']) ? $persistence['Nitro']['GooglePageSpeedApiKey'] : '';
        $data['widget']['pagespeed'] = $this->model_tool_nitro->getGooglePageSpeedReport(null, array('mobile', 'desktop'));

        $this->performGetHandlers();

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->user->hasPermission('modify', 'tool/nitro')) {
            $this->session->data['success'] = '';

            if (empty($this->request->post['Nitro']['Enabled']) || $this->request->post['Nitro']['Enabled'] == 'no') {
                unset($this->request->post['NitroTemp']);
            } else {
                //Check if we are turning NitroPack on after it has been disabled. In such case we want to enable all NitroPack modules
                if (empty($persistence['Nitro']['Enabled']) || $persistence['Nitro']['Enabled'] == 'no') {
                    $this->request->post['NitroTemp']['ActiveModule'] = array(
                        'pagecache' => 'on',
                        'cdn_generic' => 'on',
                        'db_cache' => 'on',
                        'image_cache' => 'on',
                        'jquery' => 'on',
                        'minifier' => 'on',
                        'product_count_fix' => 'on',
                        'system_cache' => 'on',
                        'pagecache_widget' => 'on'
                    );
                }
            }

            $this->model_tool_nitro->setNitroPackModules($this->request->post);

            $this->model_tool_nitro->tryChmod();

            if (!empty($this->request->post['OaXRyb1BhY2sgLSBDb21'])) {
                $this->request->post['Nitro']['LicensedOn'] = $this->request->post['OaXRyb1BhY2sgLSBDb21'];
            }

            if (!empty($this->request->post['cHRpbWl6YXRpb24ef4fe'])) {
                $this->request->post['Nitro']['License'] = json_decode(base64_decode($this->request->post['cHRpbWl6YXRpb24ef4fe']),true);
            }

            $jpeg_compression_changed = getNitroPersistence('ImageCache.JPEGCompression') != $this->request->post['Nitro']['ImageCache']['JPEGCompression'];
            $nitro_toggled_on = !getNitroPersistence('Enabled') && $this->request->post['Nitro']['Enabled'] == 'yes';

            if ($this->model_tool_nitro->setPersistence($this->request->post)) {
                $this->session->data['success'] .= $this->language->get('text_success');

                $this->model_tool_nitro->applyNitroCacheHTCompressionRules();
                $this->model_tool_nitro->applyNitroCacheHTRules();
                $this->model_tool_nitro->applyNitroCacheHTCookieRules();
                $this->model_tool_nitro->applyNitroCacheHTCdnRules();

                $this->clearpagecache();

                if (getNitroPersistence('ImageCache.OverrideCompression') && ($nitro_toggled_on || (getNitroPersistence('Enabled') && $jpeg_compression_changed)) ) {
                    $this->clearimagecache();
                }

                $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
            }
        }

        if(
            empty($this->session->data['error']) && 
            (
                (
                    file_exists(NITRO_FOLDER . 'data/googlepagespeed-desktop.tpl') && 
                    !is_writable(NITRO_FOLDER . 'data/googlepagespeed-desktop.tpl')
                ) || (
                    file_exists(NITRO_FOLDER . 'data/googlepagespeed-mobile.tpl') && 
                    !is_writable(NITRO_FOLDER . 'data/googlepagespeed-mobile.tpl')
                )
            )
        ) {
            $this->session->data['error'] = 'Your PHP user does not have permissions to write to files in <strong>system/nitro/data/</strong> Please enable write permissions for thie folder.';    
            $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['nitroData'] = $persistence;
        $data['activeModules'] = $this->model_tool_nitro->getActiveNitroModules();

        $cannotUseMinify = (bool)(phpversion() < '5.3');

        $data['cannotUseMinify'] = $cannotUseMinify;

        if ($cannotUseMinify) {
            $persistence_temp = $persistence;
            $persistence_temp['Nitro']['Mini']['JS'] = 'no';
            $this->model_tool_nitro->setPersistence($persistence_temp);
            $data['nitroData'] = $persistence_temp;
        }

        $data['inMaintenanceMode'] = inMaintenanceMode();

        $data['cron_error'] = $this->model_tool_nitro->configureCron(!empty($persistence['Nitro']['CRON']) ? $persistence['Nitro']['CRON'] : array());

        $data['cron_command'] = $this->model_tool_nitro->cron_command(!empty($persistence['Nitro']['CRON']) ? $persistence['Nitro']['CRON'] : array(), false);

        $data['heading_title'] = $this->language->get('heading_title');

        $data['cron_token_url'] = substr(HTTP_SERVER, 0, strripos(HTTP_SERVER, '/', -2) + 1) . 'index.php?route=tool/nitro/cron&cron_token={CRON_TOKEN}';

        $data['admin_email'] = $this->config->get('config_email');

        $data['has_base_css_cache'] = is_dir(NITRO_BASE_CSS_FOLDER) ? count(scandir(NITRO_BASE_CSS_FOLDER)) > 2 : false;

        $data["image_dimensions_url"] = html_entity_decode($this->url->link("tool/nitro/get_image_dimensions", "token=" . $this->session->data["token"], 'SSL'));

        $data["server_time"] = date("Y-m-d H:i:s");

        if (isset($this->session->data['error'])) {
            $data['error_warning'] = $this->session->data['error'];

            unset($this->session->data['error']);
        } elseif (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = is_array($this->session->data['success']) ? '<strong>Success</strong><br/><br/>' . implode('<br/>', $this->session->data['success']) : $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tool/nitro.tpl', $data));
    }

    private function quickClearPageCache() {
        truncateNitroProductCache();

        $filename = getQuickCacheRefreshFilename();
        return touch($filename);
    }

    private function quickClearImageCache() {
        $filename = getQuickImageCacheRefreshFilename();
        return touch($filename);
    }

    public function performGetHandlers() {
        $this->language->load('tool/nitro');
        if ($this->request->server['REQUEST_METHOD'] == 'GET' && isset($this->request->get['nitroaction'])) {
            $this->load->model('tool/nitro');
            switch($this->request->get['nitroaction']) {
            case 'refreshgps': 
                if ($this->user->hasPermission('modify', 'tool/nitro') == false) {
                    $this->session->data['error'] = $this->language->get('text_nopermission');
                    $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));             
                }
                $this->session->data['success'] = $this->model_tool_nitro->refreshGooglePageSpeedReport();              
                $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
                break;
            }
        }
    }

    public function savePagespeedResults() {
        if (!$this->request->server['REQUEST_METHOD'] == 'POST' || !$this->user->hasPermission('modify', 'tool/nitro')) {
            $this->session->data['error'] = $this->language->get('text_nopermission');
            $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));             
        }

        $data = $this->request->post['data'];
        $strategy = $this->request->post['strategy'];

        $this->load->model("tool/nitro");
        $this->model_tool_nitro->loadCore();
        setGooglePageSpeedReport(json_encode($data), $strategy);
    }

    public function getactivemodules() {
        $this->load->model('tool/nitro');
        if ($this->user->hasPermission('modify', 'tool/nitro') != false) {
            $modules = $this->model_tool_nitro->getActiveNitroModules();
            if ($modules) {
                echo json_encode($modules);
            }
        }
        exit;
    }

    public function googlerawrefresh() {
        $this->load->model('tool/nitro');

        if ($this->user->hasPermission('modify', 'tool/nitro') != false) {
            $data = $this->model_tool_nitro->getGoogleRawData();
            echo "======== Desktop ========\n";
            echo $data['desktop']."\n";
            echo "======== Mobile ========\n";
            echo $data['mobile']."\n";
            exit;
        } else {
            echo 'You do not have permissions to view this data.';
        }
    }

    public function serverinfo() {
        $this->load->model('tool/nitro');

        echo json_encode($this->model_tool_nitro->getServerInfo($this->user->hasPermission('modify', 'tool/nitro')));
    }

    public function performvalidation() {
        $this->load->model('tool/nitro');
        define('MID', 12658);

        $lcode = (!empty($_POST['l'])) ? $_POST['l'] : '';
        $hostname = (!empty($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '' ;
        $hostname = (strstr($hostname,'http://') === false) ? 'http://'.$hostname: $hostname;
        $context = stream_context_create(array('http' => array('header' => 'Referer: '.$hostname)));
        $license = json_decode(file_get_contents('http://isenselabs.com/licenses/checklicense/'.base64_encode($lcode), false, $context),true);
        // check error 
        if (!empty($license['error'])) {
            echo '<div class="alert alert-danger">'.$license['error'].'</div>';
            return false;
        }
        // check product match
        if ($license['productId'] != MID) {
            echo '<div class="alert alert-danger">Incorrect code - you cannot use license code from another product!</div>';
            return false;           
        }
        // check expire date
        if (strtotime($license['licenseExpireDate']) < time()) {
            echo '<div class="alert alert-danger">Your license has expired on '.$license['licenseExpireDate'].'</div>';
            return false;           
        }

        //checkdomains 
        $domainPresent = false;
        foreach ($license['licenseDomainsUsed'] as $domain) {
            if (strstr($hostname,$domain) !== false) {
                $domainPresent = true;  
            }
        }
        if ($domainPresent == false) {
            echo '<div class="alert alert-danger">Unable to activate license for domain '.$domain.' - Please add your domain to your product license.</div>';
            return false;           
        }

        //success, activate the license
        $nitro = $this->model_tool_nitro->getPersistence(null,true);
        $nitro['Nitro']['LicensedOn'] = time();
        $nitro['Nitro']['License'] = $license;
        $this->model_tool_nitro->setPersistence($nitro,true);
        echo '<div class="alert alert-success">Licensing successful for domain '.$domain.' - please wait... </div><script> setTimeout(function() { document.location = document.location; } , 1000);  </script>';
    }

    private function session_success_to_array() {
        if (isset($this->session->data['success']) && is_array($this->session->data['success'])) return;

        $prev_val = !empty($this->session->data['success']) ? $this->session->data['success'] : '';
        $this->session->data['success'] = array();

        if ($prev_val) $this->session->data['success'][] = $prev_val;
    }

    public function clearimagecache() {
        $this->load->model('tool/nitro');
        if ($this->quickClearImageCache()) {
            if (empty($this->session->data['success'])) {
                $this->session->data['success'] = '';   
            }
            $this->session->data['success'] .= 'The Image Cache has been cleared successfully!';

            $this->clearpagecache();
            $this->clearjournalcache();
        }
    }

    public function clearjournalcache() {
        $this->load->model('tool/nitro');
        $this->model_tool_nitro->clearJournalCache();
    }

    public function clearimagecacheajax() {
        $json = array(
            'done' => false
        );

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->lockNitro();
        if ($this->model_tool_nitro->clearImageCache(true) == true) {
            $this->clearpagecache();
            $this->model_tool_nitro->clearJournalCache();
            $this->model_tool_nitro->unlockNitro();
            $this->session_success_to_array();
            $this->session->data['success'][] = 'The Image Cache has been cleared successfully!';
            $json['done'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    public function clearsystemcache() {
        $this->load->model('tool/nitro');

        if ($this->model_tool_nitro->clearSystemCache() == true) {
            $this->session->data['success'] = 'The OpenCart System Cache has been cleared successfully!';
        }
        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function clearsystemcacheajax() {
        $json = array(
            'done' => false
        );

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->lockNitro();
        if ($this->model_tool_nitro->clearSystemCache(true) == true) {
            $this->model_tool_nitro->unlockNitro();
            $this->session_success_to_array();
            $this->session->data['success'][] = 'The OpenCart System Cache has been cleared successfully!';
            $json['done'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    public function clearpagecache() {
        $this->load->model('tool/nitro');
        // if ($this->model_tool_nitro->clearPageCache() == true && $this->model_tool_nitro->clearHeadersCache() == true) {
        if ($this->quickClearPageCache()) {
            if (empty($this->session->data['success'])) {
                $this->session->data['success'] = '';   
            }

            if (is_array($this->session->data['success'])) {
                $this->session->data['success'][] = 'The Nitro PageCache has been cleared successfully!';
            } else {
                $this->session->data['success'] .= 'The Nitro PageCache has been cleared successfully!';
            }
        }
    }

    public function clearpagecacheajax() {
        $json = array(
            'done' => false
        );

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->lockNitro();
        if ($this->model_tool_nitro->clearPageCache(true) == true && $this->model_tool_nitro->clearHeadersCache(true) == true) {
            $this->model_tool_nitro->unlockNitro();
            $this->session_success_to_array();
            $this->session->data['success'][] = 'The Nitro PageCache has been cleared successfully!';
            $json['done'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    public function cleardbcache() {
        $this->load->model('tool/nitro');
        if ($this->model_tool_nitro->clearDBCache() == true) {
            $this->session->data['success'] = 'The Nitro DB Cache has been cleared successfully!';
        }
        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function cleardbcacheajax() {
        $json = array(
            'done' => false
        );

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->lockNitro();
        if ($this->model_tool_nitro->clearDBCache(true) == true) {
            $this->model_tool_nitro->unlockNitro();
            $this->session_success_to_array();
            $this->session->data['success'][] = 'The Nitro DB Cache has been cleared successfully!';
            $json['done'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    public function clearjscache() {
        $this->load->model('tool/nitro');
        if ($this->model_tool_nitro->clearJSCache() == true && $this->model_tool_nitro->clearTempJSCache() == true) {
            $this->session->data['success'] = 'The Nitro JS Cache has been cleared successfully!';
        }
        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function clearjscacheajax() {
        $json = array(
            'done' => false
        );

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->lockNitro();
        if ($this->model_tool_nitro->clearJSCache(true) == true && $this->model_tool_nitro->clearTempJSCache(true) == true) {
            $this->model_tool_nitro->unlockNitro();
            $this->session_success_to_array();
            $this->session->data['success'][] = 'The Nitro JS Cache has been cleared successfully!';
            $json['done'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    public function clearcsscache() {
        $this->load->model('tool/nitro');
        if ($this->model_tool_nitro->clearCSSCache() == true && $this->model_tool_nitro->clearTempCSSCache() == true) {
            $this->session->data['success'] = 'The Nitro CSS Cache has been cleared successfully!';
        }
        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function clearcsscacheajax() {
        $json = array(
            'done' => false
        );

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->lockNitro();
        if ($this->model_tool_nitro->clearCSSCache(true) == true && $this->model_tool_nitro->clearTempCSSCache(true) == true) {
            $this->model_tool_nitro->unlockNitro();
            $this->session_success_to_array();
            $this->session->data['success'][] = 'The Nitro CSS Cache has been cleared successfully!';
            $json['done'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    public function clearvqmodcache() {
        $this->load->model('tool/nitro');
        if ($this->model_tool_nitro->clearVqmodCache() == true) {
            $this->session->data['success'] = 'The vQmod Cache has been cleared successfully!';
        }
        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function clearvqmodcacheajax() {
        $json = array(
            'done' => false
        );

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->lockNitro();
        if ($this->model_tool_nitro->clearVqmodCache(true) == true) {
            $this->model_tool_nitro->unlockNitro();
            $this->session_success_to_array();
            $this->session->data['success'][] = 'The vQmod Cache has been cleared successfully!';
            $json['done'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    public function clearnitrocaches() {
        $this->load->model('tool/nitro');

        $result = 
            $this->model_tool_nitro->clearPageCache() &&
            $this->model_tool_nitro->clearHeadersCache() &&
            $this->model_tool_nitro->clearDBCache() &&
            $this->model_tool_nitro->clearCSSCache() &&
            $this->model_tool_nitro->clearTempCSSCache() &&
            $this->model_tool_nitro->clearJSCache() &&
            $this->model_tool_nitro->clearTempJSCache();

        if ($result) {
            $this->session->data['success'] = 'All NitroPack-generated caches have been cleared successfully!';
        }
        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function clearallcaches() {
        $this->load->model('tool/nitro');

        $result = 
            $this->model_tool_nitro->clearPageCache() &&
            $this->model_tool_nitro->clearHeadersCache() &&
            $this->model_tool_nitro->clearDBCache() &&
            $this->model_tool_nitro->clearImageCache() &&
            $this->model_tool_nitro->clearCSSCache() &&
            $this->model_tool_nitro->clearJSCache() &&
            $this->model_tool_nitro->clearTempJSCache() &&
            $this->model_tool_nitro->clearTempCSSCache() &&
            $this->model_tool_nitro->clearSystemCache() &&
            $this->model_tool_nitro->clearVqmodCache();

        if ($result) {
            $this->session->data['success'] = 'All caches have been cleared successfully!';
        }

        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    private function smush_can_proceed($progress) {
        $lock_filename = NITRO_DATA_FOLDER . 'smush.lock';
        if (file_exists($lock_filename)) return false;

        if (time() - $this->start_time > 20) return false;

        return !$progress->abortCalled();
    }

    public function smush_read_dir($dir, $fh, &$total_images) {
        $dir = rtrim($dir, DIRECTORY_SEPARATOR);
        if (!file_exists($dir)) throw new RuntimeException("<b>Error:</b> Directory/file " . $dir . " could not be found");

        if (is_dir($dir)) {
            $dh = opendir($dir);
            while ( false !== ($entry = readdir($dh)) ) {
                if (in_array($entry, array('.', '..'))) continue;

                $full_path = $dir . DIRECTORY_SEPARATOR . $entry;
                if (is_dir($full_path)) {
                    $this->smush_read_dir($full_path, $fh, $total_images);
                } else {
                    $this->addSmushItemImage($fh, $total_images, $full_path);
                }
            }
            fflush($fh);
            closedir($dh);
        } else {
            $this->addSmushItemImage($fh, $total_images, $dir);
        }
    }

    private function addSmushItemImage($fh, &$total_images, $item) {
        if (preg_match('/\.(jpe?g|png|gif)$/i', $item)) {
            fwrite($fh, $item."\n");
            $total_images++;
        }
    }

    public function smush_init() {
        session_write_close();
        loadNitroLib('iprogress');
        $progress = new iProgress('smush_web', $this->smush_progress_message_limit);
        $list_filename = NITRO_DATA_FOLDER . 'smush_files.flist';
        $lock_filename = NITRO_DATA_FOLDER . 'smush.lock';
        $total_images = 0;

        $fh = fopen($list_filename, 'w');
        try {
            if (file_exists($lock_filename)) {
                throw new RuntimeException('<b>Error:</b> A smush process is already in progress');
            }
            touch($lock_filename);
            $progress->clear();

            $target_dir = DIR_IMAGE . 'cache';
            $array_parsed = false;

            if (!empty($this->request->post['targetDir'])) {
                $dir = $this->request->post['targetDir'];

                $test_array = json_decode(html_entity_decode($dir), true);

                if (!empty($test_array) && is_array($test_array)) {
                    $array_parsed = true;

                    foreach ($test_array as $json_item) {
                        $json_item = dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.trim(str_replace('/', DIRECTORY_SEPARATOR, urldecode($json_item)), DIRECTORY_SEPARATOR);
                        $this->addSmushItemImage($fh, $total_images, $json_item);
                    }
                } 

                if (!$array_parsed) {
                    $target_dir = dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.trim(str_replace('/', DIRECTORY_SEPARATOR, urldecode($dir)), DIRECTORY_SEPARATOR);
                }
            }

            if (!$array_parsed) {
                $this->smush_read_dir($target_dir, $fh, $total_images);
            }
        } catch (Exception $e) {
            if (file_exists($lock_filename)) unlink($lock_filename);
            echo json_encode(array(
                'status' => 'fail',
                'errors' => array($e->getMessage())
            ));
            exit;
        }
        fclose($fh);
        if (file_exists($lock_filename)) unlink($lock_filename);

        $progress->setMax($total_images);
        $progress->setData('last_pointer_position', 0);
        $progress->setData('b_saved', 0);
        $progress->setData('already_smushed', 0);
        $progress->setData('is_process_active', false);
        echo json_encode(array(
            'status' => 'success'
        ));
        exit;
    }

    private function smush_get_next_image($progress, $fh) {
        $pos = $progress->getData('last_pointer_position');
        fseek($fh, $pos);

        if (feof($fh)) return false;
        do {
            $result = trim(fgets($fh));
        } while (empty($result) && !feof($fh));

        $progress->setData('last_pointer_position', ftell($fh));

        if (empty($result)) return false;
        return $result;
    }

    private function smush($resume = false, $method = 'local', $quality) {
        session_write_close();
        loadNitroLib('iprogress');
        loadNitroLib('NitroSmush/NitroSmush');

        $method = in_array($method, array('local', 'remote')) ? $method : 'local';
        $progress = new iProgress('smush_web', $this->smush_progress_message_limit);
        $smusher = new NitroSmush();
        $smusher->setTempDir(NITRO_FOLDER . 'temp');
        $list_filename = NITRO_DATA_FOLDER . 'smush_files.flist';
        $fh = fopen($list_filename, 'r');
        if ($resume) {
            fseek($fh, $progress->getData('last_pointer_position'), SEEK_SET);
            $progress->resume();
        }

        $data_sizes = array(
            'KiloBytes' => 1024,
            'MegaBytes' => 1024*1024
        );

        $progress->setData('is_process_active', true);

        if (NITRO_DEBUG_MODE) {
            $smush_error_log = new Log(date('Y-m-d') . '_nitrosmush_error.txt');
        }

        $progress->setData('last_smush_timestamp', time());
        while ($this->smush_can_proceed($progress) && false !== ($filename = $this->smush_get_next_image($progress, $fh))) {
            $progress->addMsg($filename . " -> Optimizing <i class=\"icon-spinner icon-spin\"></i>");
            // $isPng = preg_match("/.*?(\d+)x(\d+)\.png$/i", $filename, $matches);
            // $isSmallPng = $isPng ? ((int)$matches[1] <= 1000 && (int)$matches[2] <= 1000) : true;

            //if (!$isPng || $isSmallPng) {
                set_time_limit(30);
                try {
                    switch ($method) {
                    case 'local':
                        $res = $smusher->smush($filename, false, false, $quality);
                        break;
                    case 'remote':
                        $res = $smusher->smush($filename, false, true, $quality);
                        break;
                    }

                    if ($res['smushed']) {
                        $savings = $progress->getData('b_saved');
                        $savings += $res['savings_b'];
                        $progress->setData('b_saved', $savings);
                        $saved_size = $res['savings_b'] . ' bytes';
                        foreach ($data_sizes as $k=>$v) {
                            if ($res['savings_b'] > $v) {
                                $saved_size = number_format($res['savings_b'] / $v, 2) . " " . $k;
                            }
                        }
                        $progress->replaceMsg($filename . " -> Saved " . $saved_size . " (<b>" . $res['savings_percent'] . "%</b>)");
                    } else if (empty($res['errors'])) {
                        $already_smushed = $progress->getData('already_smushed');
                        $already_smushed++;
                        $progress->setData('already_smushed', $already_smushed);
                        $progress->replaceMsg($filename . " is already optimized");
                    } else {
                        foreach ($res['errors'] as $err) {
                            $progress->replaceMsg("<b>ERROR</b> [$filename] => $err");
                            if (NITRO_DEBUG_MODE) {
                                $smush_error_log->write($filename . " | " . $err);
                            }
                        }
                    }
                } catch (Exception $e) {
                    $progress->replaceMsg("<b>Error</b> [$filename] => " . $e->getMessage());
                    if (NITRO_DEBUG_MODE) {
                        $smush_error_log->write($filename . " | " . $e->getMessage());
                    }
                }
            // } else {
            //     $progress->replaceMsg("Skipping " . $filename . " because it is a big png");
            // }

            $progress->iterateWith(1);
            $progress->setData('last_smush_timestamp', time());
        }
        $progress->setData('is_process_active', false);

        return $progress->getProgress() == $progress->getMax();
    }

    public function smush_start() {
        $method = !empty($this->request->get['method']) ? $this->request->get['method'] : getNitroPersistence('Smush.Method');
        $quality = !empty($this->request->get['quality']) ? (int)$this->request->get['quality'] : (int)getNitroPersistence('Smush.Quality');
        $isReady = $this->smush(false, $method, $quality);

        echo (int)$isReady; exit;
    }

    public function smush_resume() {
        $method = !empty($this->request->get['method']) ? $this->request->get['method'] : getNitroPersistence('Smush.Method');
        $quality = !empty($this->request->get['quality']) ? (int)$this->request->get['quality'] : (int)getNitroPersistence('Smush.Quality');
        $isReady = $this->smush(true, $method, $quality);

        echo (int)$isReady; exit;
    }

    public function smush_pause() {
        session_write_close();
        loadNitroLib('iprogress');
        $progress = new iProgress('smush_web', $this->smush_progress_message_limit);
        $progress->abort();
        $progress->setData('is_process_active', false);
        exit;
    }

    public function smush_clear_buffer() {
        session_write_close();
        loadNitroLib('iprogress');
        $progress = new iProgress('smush_web', $this->smush_progress_message_limit);
        echo json_encode(array('messages' => $progress->clearMessages())); exit;
    }

    public function smush_get_progress() {
        session_write_close();
        loadNitroLib('iprogress');
        $progress = new iProgress('smush_web', $this->smush_progress_message_limit);
        $smush_progress = array(
            'processed_images_count' => $progress->getProgress(),
            'already_smushed_images_count' => $progress->getData('already_smushed'),
            'total_images' => $progress->getMax(),
            'b_saved' => $progress->getData('b_saved'),
            'last_smush_timestamp' => $progress->getData('last_smush_timestamp'),
            'is_process_active' => $progress->getData('is_process_active'),
            'messages' => $progress->getMessages()
        );
        echo json_encode($smush_progress);
        exit;
    }

    public function gecacheimagescountnow() {
        echo $this->getCacheImagesCount();
    }


    public function getCacheImagesCount($path = '') {
        $this->load->model('tool/nitro');
        $this->model_tool_nitro->loadCore();

        if (empty($path)) $path = DIR_IMAGE.'cache';
        $size = 0;
        $ignore = array('.','..','cgi-bin','.DS_Store','index.html');
        $files = scandir($path);
        foreach($files as $t) {
            if(in_array($t, $ignore)) continue;
            if (is_dir(rtrim($path, '/') . '/' . $t)) {
                $size += $this->getCacheImagesCount(rtrim($path, '/') . '/' . $t);
            } else {
                $size++;
            }   
        }

        return $size;

    }

    public function get_preminify_stack() {
        header('Content-Type: application/json');

        $this->load->model('tool/nitro');
        $this->model_tool_nitro->loadCore();

        require_once NITRO_LIB_FOLDER . 'NitroFiles.php';

        $nf = new NitroFiles(array(
            'root' => DIR_CATALOG,
            'ext' => array('css', 'js')
        ));

        $files = $nf->find();
        if (!empty($files)) {
            $files_linear = array();
            foreach ($files as $file) {
                $files_linear[] = $file['full_path'];
            }
            echo json_encode($files_linear);
        } else {
            echo json_encode(array());
        }

        exit;
    }

    public function get_image_dimensions() { 
        if (empty($this->request->get["page_type"])) {
            http_response_code(500);
            exit;
        }

        session_write_close();

        $page_type = $this->request->get["page_type"];

        $this->load->model("catalog/product");
        $this->load->model("catalog/category");

        $pages = array();
        $user_agents = array(
            "Mobile" => "Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1",
            "Tablet" => "Mozilla/5.0 (iPad; CPU OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1",
            "Desktop" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A"
        );

        $results_retrieve_url = preg_replace('/^https?:/', '', HTTPS_CATALOG . "index.php?route=tool/nitro/get_image_dimensions&nopagecache=");

        switch ($page_type) {
        case "home":
            foreach ($user_agents as $strategy => $user_agent) {
                $pages[] = array(
                    "type" => "home",
                    "name" => "Home Page " . $strategy,
                    "strategy" => $strategy,
                    "user_agent" => $user_agent,
                    "url" => preg_replace('/^https?:/', '', HTTPS_CATALOG . "?save_image_dimensions=1")
                );
            }
            break;
        case "product":
            $products = $this->model_catalog_product->getProducts(array("start" => 0, "limit" => 1));
            if ($products) {
                foreach ($user_agents as $strategy => $user_agent) {
                    $pages[] = array(
                        "type" => "product",
                        "name" => "Product Page " . $strategy,
                        "strategy" => $strategy,
                        "user_agent" => $user_agent,
                        "url" => preg_replace('/^https?:/', '', HTTPS_CATALOG . "index.php?save_image_dimensions=1&route=product/product&product_id=" . $products[0]["product_id"])
                    );
                }
            }
            break;
        case "category":
            $category_result = $this->db->query("SELECT c.category_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c2s.category_id=c.category_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.category_id = c.category_id) LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id) WHERE c2s.store_id='0' AND c.status = '1' AND p2c.product_id IS NOT NULL AND p.status='1' AND p.date_available <= NOW() LIMIT 0,1")->row;

            if (!empty($category_result['category_id'])) {
                foreach ($user_agents as $strategy => $user_agent) {
                    $pages[] = array(
                        "type" => "category",
                        "name" => "Category Page " . $strategy,
                        "strategy" => $strategy,
                        "user_agent" => $user_agent,
                        "url" => preg_replace('/^https?:/', '', HTTPS_CATALOG . "index.php?save_image_dimensions=1&route=product/category&path=" . $category_result['category_id'])
                    );
                }
            }
            break;
        }

        require_once NITRO_LIB_FOLDER . "browser.php";

        $dimensions = array();
        foreach ($pages as $page) {
            try {
                $browser = new NitroBrowser('http:' . $page["url"]);
                $browser->timeout = 20;
                $browser->setHeader("User-Agent", $page["user_agent"]);
                $browser->setCookie("nonitro", "1");
                $browser->setCookie("save_image_dimensions", "1");
                $browser->fetch();

                if ($browser->getBody() == '') {
                    $browser->setUrl('https:' . $page["url"]);
                    $browser->fetch();
                }


                $browser->setUrl('http:' . $results_retrieve_url . microtime(true));
                $browser->removeCookie("save_image_dimensions");
                $browser->fetch();

                if ($browser->getBody() == '') {
                    $browser->setUrl('https:' . $results_retrieve_url . microtime(true));
                    $browser->fetch();
                }

                $results = json_decode(str_replace("\xEF\xBB\xBF", '' ,$browser->getBody()), true);//Remove BOM character
                if (is_array($results)) {
                    $dimensions[$page["strategy"]] = $results;
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo $e->getMessage();
                exit;
            }
        }

        header('Content-Type: application/json');
        echo json_encode($dimensions);
        exit;
    }

    public function get_css_extract_stack() {
        header('Content-Type: application/json');

        $this->load->model("catalog/product");
        $this->load->model("catalog/category");

        $pages = array();
        $user_agents = array(
            "Mobile" => "Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1",
            "Tablet" => "Mozilla/5.0 (iPad; CPU OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1",
            "Desktop" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A"
        );

        foreach ($user_agents as $strategy => $user_agent) {
            $pages[] = array(
                "type" => "home",
                "name" => "Home Page " . $strategy,
                "strategy" => $strategy,
                "user_agent" => $user_agent,
                "url" => HTTPS_CATALOG
            );
        }

        $products = $this->model_catalog_product->getProducts(array("start" => 0, "limit" => 1));
        if ($products) {
            foreach ($user_agents as $strategy => $user_agent) {
                $pages[] = array(
                    "type" => "product",
                    "name" => "Product Page " . $strategy,
                    "strategy" => $strategy,
                    "user_agent" => $user_agent,
                    "url" => HTTPS_CATALOG . "index.php?route=product/product&product_id=" . $products[0]["product_id"]
                );
            }
        }

        $categories = $this->model_catalog_product->getProductCategories($products[0]["product_id"]);
        if ($categories) {
            foreach ($user_agents as $strategy => $user_agent) {
                $pages[] = array(
                    "type" => "category",
                    "name" => "Category Page " . $strategy,
                    "strategy" => $strategy,
                    "user_agent" => $user_agent,
                    "url" => HTTPS_CATALOG . "index.php?route=product/category&path=" . $categories[0]
                );
            }
        }

        echo json_encode($pages);
        exit;
    }

    public function css_extract() {
        if (!empty($this->request->post['page'])) {
            $page = $this->request->post['page'];

            $this->load->model('tool/nitro');
            $this->model_tool_nitro->loadCore();

            $remoteURL = "http://nitrosmush.com/abovethefold.php?url=" . base64_encode(html_entity_decode($page["url"])) . "&ua=" . base64_encode(html_entity_decode($page["user_agent"]));

            try {
                $resp = fetchRemoteContent($remoteURL, 120);

                if ($resp) {
                    $resp = json_decode($resp, true);

                    if ($resp["status"] == "success") {
                        if (!is_dir(NITRO_BASE_CSS_FOLDER) && !@mkdir(NITRO_BASE_CSS_FOLDER, 0755, true)) {
                            return;
                        }

                        switch ($page["strategy"]) {
                            case "Mobile":
                                $mobile_prefix = "mobile-";
                                break;
                            case "Tablet":
                                $mobile_prefix = "tablet-";
                                break;
                            case "Desktop":
                                $mobile_prefix = "";
                                break;
                        }

                        $resp['css'] = preg_replace('/^.*?url\s*\(.*$/im', '', $resp['css']);

                        switch ($page["type"]) {
                            case "home":
                                file_put_contents(NITRO_BASE_CSS_FOLDER . $mobile_prefix . "default.css", $resp["css"]);
                                break;
                            case "product":
                                file_put_contents(NITRO_BASE_CSS_FOLDER . $mobile_prefix . "product.css", $resp["css"]);
                                break;
                            case "category":
                                file_put_contents(NITRO_BASE_CSS_FOLDER . $mobile_prefix . "category.css", $resp["css"]);
                                break;
                        }
                    } else {
                        throw new Exception($resp["return_value"] . ": " . $resp["msg"]);
                    }
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo $e->getMessage();
                exit;
            }
        }
        exit;
    }

    public function clear_extracted_css_cache() {
        if (is_dir(NITRO_BASE_CSS_FOLDER)) {
            $files = scandir(NITRO_BASE_CSS_FOLDER);

            foreach ($files as $file) {
                if (!in_array($file, array(".", ".."))) {
                    $path = NITRO_BASE_CSS_FOLDER . $file;
                    if (is_file($path)) {
                        if (!@unlink($path)) {
                            http_response_code(500);
                            exit;
                        }
                    }
                }
            }
        }
    }

    public function minify_file() {
        $this->load->model('tool/nitro');
        $this->model_tool_nitro->loadCore();

        if (!empty($this->request->post['file'])) {
            require_once NITRO_CORE_FOLDER . 'minify_functions.php';

            $file = $this->request->post['file'];
            if (file_exists($file)) {
                $ext = preg_replace('/.*?\.(\w+)$/', '$1', $file);
                $this->config->set('config_url', HTTP_CATALOG);
                $this->config->set('config_ssl', HTTPS_CATALOG);
                minify($ext, array(
                    md5($file) => $file
                ), array());
            }
        }
        exit;
    }

    public function apply_recommended_settings() {
        $this->load->model('tool/nitro');
        $this->model_tool_nitro->loadCore();

        if (applyNitroRecommendedSettings()) {
            $this->clearimagecache();//This is so the JPEG quality override can take effect
            $this->model_tool_nitro->applyNitroCacheHTCompressionRules();
            $this->model_tool_nitro->applyNitroCacheHTRules();
            $this->model_tool_nitro->applyNitroCacheHTCookieRules();
            $this->model_tool_nitro->applyNitroCacheHTCdnRules();

            $this->session->data['success'] = 'Success: Recommended settings have been applied!';
        } else {
            $this->session->data['error'] = 'Error: Recommended settings could not be applied! There is probably a permission issue. Make sure that the PHP user has write permissions to the ' . NITRO_DATA_FOLDER . ' directory';
        }

        $this->response->redirect($this->url->link('tool/nitro', 'token=' . $this->session->data['token'], 'SSL'));
    }

    private function isSessionClosed() {
        return $this->session_closed;
    }

    private function closeSession() {
        if (session_id() && !$this->session_closed) session_write_close();
        $this->session_closed = true;
    }

    private function openSession() {
        if ($this->session_closed) session_start();
        $this->session_closed = false;
        return session_id();
    }
}
