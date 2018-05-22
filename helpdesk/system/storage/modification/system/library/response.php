<?php
class Response {
	private $headers = array();
	private $level = 0;
	private $output;

	public function addHeader($header) {
		$this->headers[] = $header;
	}

	public function redirect($url, $status = 302) {
		header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url), true, $status);
		exit();
	}

	public function setCompression($level) {
		$this->level = $level;

                if (isNitroEnabled() && getNitroPersistence('PageCache.Enabled')) { $this->level = 0; }
            
	}

	public function setOutput($output) {
		$this->output = $output;
	}

	public function getOutput() {
		return $this->output;
	}

	private function compress($data, $level = 0) {
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)) {
			$encoding = 'gzip';
		}

		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)) {
			$encoding = 'x-gzip';
		}

		if (!isset($encoding) || ($level < -1 || $level > 9)) {
			return $data;
		}

		if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
			return $data;
		}

		if (headers_sent()) {
			return $data;
		}

		if (connection_status()) {
			return $data;
		}

		$this->addHeader('Content-Encoding: ' . $encoding);

		return gzencode($data, (int)$level);
	}

	public function output() {
		if ($this->output) {
			if ($this->level) {
				$output = $this->compress($this->output, $this->level);
			} else {
				$output = $this->output;
			}

			if (!headers_sent()) {
				foreach ($this->headers as $header) {
					header($header, true);
				}
			}


                if (!headers_sent() && in_array(strtolower(PHP_SAPI), array("apachehandler", "apache2handler"))) {
                    header("Connection: close");
                    header("Content-Length: " . strlen($output));
                }
            
			echo $output;

                $GLOBALS["nitro_final_output"] = ob_get_contents();
                $GLOBALS["nitro_headers_list"] = headers_list();

                if (in_array(strtolower(PHP_SAPI), array("apachehandler", "apache2handler"))) {
                    if (ob_get_level() > 0) ob_end_flush();
                    flush();
                } else if (function_exists("fastcgi_finish_request")) {
                    fastcgi_finish_request();
                }

                require_once DIR_SYSTEM . 'nitro' . DIRECTORY_SEPARATOR . 'config.php';
                require_once NITRO_CORE_FOLDER . 'core.php';
                require_once NITRO_INCLUDE_FOLDER . 'pagecache_bottom.php';
            
		}
	}
}