<div class="box-heading">
  <h1>General Settings</h1>
</div>
<div class="box-content">
    <table class="form settings">
      <tr>
        <td>Extension Status<span class="help">Used to Enable/Disable NitroPack</span></td>
        <td>
        <select name="Nitro[Enabled]" class="form-control NitroEnabled">
            <option value="yes" <?php echo( (!empty($nitroData['Nitro']['Enabled']) && $nitroData['Nitro']['Enabled'] == 'yes')) ? 'selected=selected' : ''?>>Enabled</option>
            <option value="no" <?php echo ( (empty($nitroData['Nitro']['Enabled']) || $nitroData['Nitro']['Enabled'] != 'yes')) ? 'selected=selected' : ''?>>Disabled</option>
        </select>
        </td>
      </tr>
      <tr>
        <td>Recommended settings<span class="help">This will apply the most popular NitroPack configuration.</span></td>
        <td><a class="btn btn-default" id="btn-recommended-settings"><i class="fa fa-magic"></i>&nbsp;&nbsp;Click to apply the recommended settings</a></td>
      </tr>
      <tr>
        <td>Google PageSpeed API Key<span class="help"><strong>Important: </strong>We have inserted a default code to get you up and running. Please obtain your own Server API Key from <a href="https://code.google.com/apis/console" target="_blank"><u>Google API Console</u></a></span></td>
        <td>
          <input class="form-control" type="text" name="Nitro[GooglePageSpeedApiKey]" id="pagespeedApiKey" value="<?php echo !empty($nitroData['Nitro']['GooglePageSpeedApiKey']) ? $nitroData['Nitro']['GooglePageSpeedApiKey'] : 'AIzaSyCxptR6CbHYrHkFfsO_XN3nkf6FjoQp2Mg'; ?>" />
        </td>
      </tr>
      <tr>
        <td>System information<span class="help">Displays useful system information.</span></td>
        <td>
          <a class="btn btn-default system-info-refresh" data-toggle="modal" href="#infoModal"><i class="icon-search"></i> Click to view system information</a>
          
            <div id="infoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 id="myModalLabel">System information</h3>
                      </div>
                      <div class="modal-body">
                        <table>
                        	<tr>
                            	<td colspan="2"><h4>System test</h4></td>
                            </tr>
                        	<tr>
                            	<td>PHP version:</td>
                                <td id="system_info_php_version"></td>
                            </tr>
                            <tr>
                            	<td>PHP User:</td>
                                <td id="system_info_php_user"></td>
                            </tr>
                            <tr>
                            	<td>Web Server:</td>
                                <td id="system_info_web_server"></td>
                            </tr>
                            <tr>
                            	<td>FTP functions:</td>
                                <td id="system_info_ftp_functions"></td>
                            </tr>
                            <tr>
                            	<td>OpenSSL extension:</td>
                                <td id="system_info_openssl"></td>
                            </tr>
                            <tr>
                            	<td>cURL extension:</td>
                                <td id="system_info_curl"></td>
                            </tr>
                            <tr>
                            	<td>Memcache extension:</td>
                                <td id="system_info_memcache"></td>
                            </tr>
                            <tr>
                            	<td>exec() function:</td>
                                <td id="system_info_exec"></td>
                            </tr>
                            <tr>
                            	<td>zlib:</td>
                                <td id="system_info_zlib"></td>
                            </tr>
                            <tr>
                            	<td>Safe mode:</td>
                                <td id="system_info_safe_mode"></td>
                            </tr>
                        	<tr>
                            	<td colspan="2"><h4>File system test</h4></td>
                            </tr>
                            <tr>
                                <td>assets/css/</td>
                                <td id="system_info_path_assets_css"></td>
                            </tr>
                            <tr>
                                <td>assets/js/</td>
                                <td id="system_info_path_assets_js"></td>
                            </tr>
                        	<tr>
                            	<td>system/nitro/cache/</td>
                                <td id="system_info_path_system_nitro_cache"></td>
                            </tr>
                            <tr>
                            	<td>system/nitro/data/</td>
                                <td id="system_info_path_system_nitro_data"></td>
                            </tr>
                            <tr>
                            	<td>system/nitro/data/googlepagespeed-desktop.tpl</td>
                                <td id="system_info_path_system_nitro_data_googlepagespeed-desktop"></td>
                            </tr>
                            <tr>
                            	<td>system/nitro/data/googlepagespeed-mobile.tpl</td>
                                <td id="system_info_path_system_nitro_data_googlepagespeed-mobile"></td>
                            </tr>
                            <tr>
                            	<td>system/nitro/data/persistence.tpl</td>
                                <td id="system_info_path_system_nitro_data_persistence"></td>
                            </tr>
                            <tr>
                                <td>system/nitro/temp/</td>
                                <td id="system_info_path_system_nitro_temp"></td>
                            </tr>
                        </table>
                      </div>
                      <div class="modal-footer">
                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                        <button class="btn btn-primary system-info-refresh"><i class="icon-white icon-refresh"></i> Refresh</button>
                      </div>
                    </div>
                </div>
            </div>
        </td>
      </tr>
      <tr>
        <td>Google PageScore Debug<span class="help">Displays the raw PageScore response.</span></td>
        <td>
          <a class="btn btn-default google-raw-refresh" data-toggle="modal" data-target="#infoGoogleRaw"><i class="icon-search"></i> Click to view Google PageScore RAW result</a>
            <div id="infoGoogleRaw" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 id="myModalLabel2">Google PageScore RAW result</h3>
                  </div>
                  <div class="modal-body">
                    <textarea class="form-control" id="infoGoogleRawText"></textarea>
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                  </div>
                </div>
              </div>
            </div>
        </td>
      </tr>
       <tr>
        <td style="vertical-align:top;">Disable NitroPack for specific pages<span class="help">List the URLs (part or whole) of the pages, separated by newline. Wildcard * is available.</span></td>
        <td>
          <textarea class="form-control" placeholder="e.g. ?route=product/product, each file on a new line" style="width:400px; height:180px;" name="Nitro[DisabledURLs]"><?php echo !empty($nitroData['Nitro']['DisabledURLs']) ? $nitroData['Nitro']['DisabledURLs'] : ''; ?></textarea>
        </td>
      </tr>
      <tr>
        <td>Serve jQuery from Google?<span class="help">If you enable this option, jQuery will be served from Google's hosted library. This will save one HTTP request to your server and may help deliver jQuery faster.</span></td>
        <td>
        <select class="form-control" name="Nitro[GoogleJQuery]">
        	<option value="no" <?php echo ( (!empty($nitroData['Nitro']['GoogleJQuery']) && $nitroData['Nitro']['GoogleJQuery'] == 'no')) ? 'selected=selected' : ''?>>Disabled</option>
            <option value="yes" <?php echo( (!empty($nitroData['Nitro']['GoogleJQuery']) && $nitroData['Nitro']['GoogleJQuery'] == 'yes')) ? 'selected=selected' : ''?>>Enabled</option>
        </select>
        </td>
      </tr>
    </table>
</div>
<script type="text/javascript">

var refreshSystemInformation = function() {
	$.ajax({
		url: 'index.php?route=tool/nitro/serverinfo&token=' + getURLVar('token'),
		type: 'get',
		dataType: 'json',
		beforeSend: function() {
			$('.system-info-refresh').attr('disabled', 'disabled');
		},
		success: function(data) {
			for (var i in data) {
				$('#system_info_' + i).html(data[i]);
			}
		},
		complete: function() {
			$('.system-info-refresh').removeAttr('disabled');
		}
	});
}

$('.system-info-refresh').click(refreshSystemInformation);

var googleRawRefresh = function() {
	$.ajax({
		url: 'index.php?route=tool/nitro/googlerawrefresh&token=' + getURLVar('token'),
		type: 'get',
		beforeSend: function() {
			$('.google-raw-refresh').attr('disabled', 'disabled');
		},
		success: function(data) {
			$('#infoGoogleRawText').html(data);
		},
		complete: function() {
			$('.google-raw-refresh').removeAttr('disabled');
		}
	});
}

$('.google-raw-refresh').click(googleRawRefresh);

$('#btn-recommended-settings').on('click', function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    if (confirm("This will change your current NitroPack settings to pre-defined values. Are you sure you want to proceed?")) {
        $.ajax({
            url: 'index.php?route=tool/nitro/apply_recommended_settings&token=' + getURLVar('token'),
            complete: function() {
                location.reload();
            }
        });
    }
});
</script>
