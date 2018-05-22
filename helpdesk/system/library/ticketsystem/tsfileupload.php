<?php
/**
 * @package		Webkul HelpDesk Mod
 * @copyright	Copyright (C) 2015 Webkul Pvt Ltd. All rights reserved. (webkul.com)
 * 
 * This class validate files 
 */
class TsFileUpload extends TsRegistry{

	/**
	 * fileUploadValidate Validate file
	 * @param  array $file -> It will be like default $_FILES[''] with keys and values not array, use foreach in you calling file
	 * @return string
	 */
	public function fileUploadValidate($file) {

		$this->language->load('ticketsystem/fileerros');

		$error = false;

		if (!empty($file['name']) && is_file($file['tmp_name'])) {

			// Sanitize the filename
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($file['name'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
				$error = $this->language->get('error_filename');
			}

			//validate file size
			if ($file['size'] > (($this->config->get('ts_fileupload_size') ? $this->config->get('ts_fileupload_size') : 200 )*1024)) {
				$error = $this->language->get('error_filesize');
			}

			// Allowed file extension types
			$allowed = array();

			$filetypes = explode(",", $this->config->get('ts_fileupload_ext'));

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array('*', $allowed) AND !in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
				$error = $this->language->get('error_filetype');
			}

			// Allowed file mime types
			$allowed = array();

			$mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

			$filetypes = explode("\n", $mime_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array($file['type'], $allowed)) {
				$error = $this->language->get('error_filetype');
			}

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($file['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				$error = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($file['error'] != UPLOAD_ERR_OK) {
				$error = $this->language->get('error_upload_' . $file['error']);
			}
		} else {
			$error = $this->language->get('error_upload');
		}

		return $error;
	}

}