<?php
class ModelToolImage extends Model {

                public function cdn_rewrite($host_url, $new_image) {
                    require_once DIR_SYSTEM . 'nitro' . DIRECTORY_SEPARATOR . 'config.php';
                    require_once NITRO_CORE_FOLDER . 'core.php';
                    require_once NITRO_CORE_FOLDER . 'cdn.php';
                    
                    $nitro_result = nitroCDNResolve($new_image, $host_url);

                    return $nitro_result;
                }
            
	public function resize($filename, $width, $height) {
              if (function_exists("getMobilePrefix") && function_exists("getCurrentRoute") && isNitroEnabled() && !isset($_COOKIE["save_image_dimensions"])) {
                $route = getCurrentRoute();

                switch ($route) {
                case "common/home":
                    $page_type = "home";
                    break;
                case "product/category":
                    $page_type = "category";
                    break;
                case "product/product":
                    $page_type = "product";
                    break;
                default:
                    $page_type = "";
                    break;
                }

                if ($page_type) {
                    $device_type = ucfirst(trim(getMobilePrefix(true), "-"));
                    if (!$device_type) {
                        $device_type = "Desktop";
                    }

                    $overrides = getNitroPersistence('DimensionOverride.' . $page_type . '.' . $device_type);
                    if ($overrides) {
                        foreach ($overrides as $override) {
                            if ((int)$override["old"]["width"] == (int)$width && (int)$override["old"]["height"] == (int)$height) {
                                $width = (int)$override["new"]["width"];
                                $height = (int)$override["new"]["height"];
                            }
                        }
                    }
                }
              }
		if (!is_file(DIR_IMAGE . $filename)) {
			return;
		}


                if (isset($_COOKIE["save_image_dimensions"])) {
                    if (empty($GLOBALS["reset_session_dimensions"])) {
                        $GLOBALS["reset_session_dimensions"] = true;
                        $this->session->data["nitro_image_dimensions"] = array();
                    }

                    $dimension_string = $width . "x" . $height;
                    if (!in_array($dimension_string, $this->session->data["nitro_image_dimensions"])) {
                        $this->session->data["nitro_image_dimensions"][] = $dimension_string;
                    }
                }
            
		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$old_image = $filename;
		$new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

		
                $nitro_refresh_file = getQuickImageCacheRefreshFilename();
                $nitro_recache = (getNitroPersistence('Enabled') && getNitroPersistence('ImageCache.OverrideCompression') && is_file(DIR_IMAGE . $new_image) && is_file($nitro_refresh_file)) ? filemtime($nitro_refresh_file) > filectime(DIR_IMAGE . $new_image) : false;
                if (!is_file(DIR_IMAGE . $new_image) || (filectime(DIR_IMAGE . $old_image) > filectime(DIR_IMAGE . $new_image)) || $nitro_recache) {
            
			$path = '';

			$directories = explode('/', dirname(str_replace('../', '', $new_image)));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}

			list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $old_image);


                $isNitroImageOverrideEnabled = getNitroPersistence('Enabled') && getNitroPersistence('ImageCache.OverrideCompression');
            
			
                if ($width_orig != $width || $height_orig != $height || $isNitroImageOverrideEnabled) {
            
				$image = new Image(DIR_IMAGE . $old_image);
				$image->resize($width, $height);
				$image->save(DIR_IMAGE . $new_image);

                require_once DIR_SYSTEM . 'nitro' . DIRECTORY_SEPARATOR . 'config.php';
                require_once NITRO_CORE_FOLDER . 'core.php';
                include NITRO_INCLUDE_FOLDER . 'smush_on_demand.php';
            
			} else {
				copy(DIR_IMAGE . $old_image, DIR_IMAGE . $new_image);

                require_once DIR_SYSTEM . 'nitro' . DIRECTORY_SEPARATOR . 'config.php';
                require_once NITRO_CORE_FOLDER . 'core.php';
                include NITRO_INCLUDE_FOLDER . 'smush_on_demand.php';
            
			}
		}

		if ($this->request->server['HTTPS']) {

                $default_link = $this->config->get('config_ssl').'image/'.$new_image;
                $cdn_link = $this->cdn_rewrite($this->config->get('config_ssl'), 'image/'.$new_image);
                if ($default_link == $cdn_link) {
                    return $this->config->get('config_ssl') . (isset($seoUrlImage) ? $seoUrlImage : 'image/' . $new_image);
                } else {
                    return $cdn_link;
                }
            
			return $this->config->get('config_ssl') . 'image/' . $new_image;
		} else {

                $default_link = $this->config->get('config_url').'image/'.$new_image;
                $cdn_link = $this->cdn_rewrite($this->config->get('config_url'), 'image/'.$new_image);
                if ($default_link == $cdn_link) {
                    return $this->config->get('config_url') . (isset($seoUrlImage) ? $seoUrlImage : 'image/' . $new_image);
                } else {
                    return $cdn_link;
                }
            
			return $this->config->get('config_url') . 'image/' . $new_image;
		}
	}
}
