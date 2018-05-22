<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<script>
nitro.cachemanager.setToken('<?php echo $_GET['token']; ?>');

if (typeof getURLVar == 'undefined') {
  function getURLVar(key) {
    var value = [];
    var query = String(document.location).split('?');
    if (query[1]) {
      var part = query[1].split('&');
      for (i = 0; i < part.length; i++) {
        var data = part[i].split('=');
        if (data[0] && data[1]) {
          value[data[0]] = data[1];
        }
      }
      if (value[key]) {
        return value[key];
      } else {
        return '';
      }
    }
  } 
}
</script>
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php echo (empty($nitroData['Nitro']['LicensedOn'])) ? base64_decode('ICAgIDxkaXYgY2xhc3M9ImFsZXJ0IGFsZXJ0LWRhbmdlciBmYWRlIGluIj4NCiAgICAgICAgPGJ1dHRvbiB0eXBlPSJidXR0b24iIGNsYXNzPSJjbG9zZSIgZGF0YS1kaXNtaXNzPSJhbGVydCIgYXJpYS1oaWRkZW49InRydWUiPsOXPC9idXR0b24+DQogICAgICAgIDxoND5XYXJuaW5nISBVbmxpY2Vuc2VkIHZlcnNpb24gb2YgdGhlIG1vZHVsZSE8L2g0Pg0KICAgICAgICA8cD5Zb3UgYXJlIHJ1bm5pbmcgYW4gdW5saWNlbnNlZCB2ZXJzaW9uIG9mIHRoaXMgbW9kdWxlISBZb3UgbmVlZCB0byBlbnRlciB5b3VyIGxpY2Vuc2UgY29kZSB0byBlbnN1cmUgcHJvcGVyIGZ1bmN0aW9uaW5nLCBhY2Nlc3MgdG8gc3VwcG9ydCBhbmQgdXBkYXRlcy48L3A+PGRpdiBzdHlsZT0iaGVpZ2h0OjVweDsiPjwvZGl2Pg0KICAgICAgICA8YSBjbGFzcz0iYnRuIGJ0bi1kYW5nZXIiIGhyZWY9ImphdmFzY3JpcHQ6dm9pZCgwKSIgb25jbGljaz0iJCgnYVtocmVmPSNpc2Vuc2Vfc3VwcG9ydF0nKS50cmlnZ2VyKCdjbGljaycpIj5FbnRlciB5b3VyIGxpY2Vuc2UgY29kZTwvYT4NCiAgICA8L2Rpdj4=') : '' ?>
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="icon-exclamation-sign"></i> <?php echo $error_warning; ?></div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="icon-ok-sign"></i> <?php echo $success; ?></div>
    <?php } ?>
    <?php if ($inMaintenanceMode) { ?>
      <div class="alert alert-warning">
        <i class="icon-exclamation-sign"></i> Maintenance mode is enabled for your website. NitroPack will not work while Maintenance mode is enabled.
      </div>
    <?php } ?>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><?php echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">

        <form action="" method="post" id="form">
                <div class="tabbable">
              <div class="tab-navigation">        
                  <ul class="nav nav-tabs mainMenuTabs">
                    <li class="active"><a href="#pane1" data-toggle="tab">Dashboard</a></li>
                    <li><a href="#generalsettings" data-toggle="tab">Settings</a></li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">Cache Systems <b class="caret"></b></a>
                           <ul class="dropdown-menu">
                              <li><a href="#pagecache" data-toggle="tab">Page cache</a></li>
                                <li><a href="#dbcache" data-toggle="tab">Database cache</a></li>                        
                                <li><a href="#occache" data-toggle="tab">System cache</a></li>
                                <li><a href="#browsercache" data-toggle="tab">Browser cache</a></li>
                                <li><a href="#imagecache" data-toggle="tab">Image cache</a></li>                    
                  </ul>
                    
                    </li>
                    <li><a href="#compression" data-toggle="tab">Compression</a></li>
                    <li><a href="#minification" data-toggle="tab">Minification</a></li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">CDN <b class="caret"></b></a>
                       <ul class="dropdown-menu">
                            <li><a href="#cdn" data-toggle="tab">Generic CDN Service</a></li>
                            <li><a href="#cdn-cloudflare" data-toggle="tab">CloudFlare CDN</a></li>
                        </ul>
                    </li>
                    <li><a href="#cron_tab" data-toggle="tab">CRON</a></li>
                    <li><a href="#smushit" data-toggle="tab">Image Optimization</a></li>
                    <li class="dropdown">
                       <a href="#" class="dropdown-toggle" data-toggle="dropdown">Get Support <b class="caret"></b></a>
                       <ul class="dropdown-menu">
                            <li><a href="#isense_support" data-toggle="tab">Get Support and Updates</a></li>
                        <li><a href="#qa" data-toggle="tab">Frequently Asked Questions</a></li>
                            <li class="divider"></li>
                            <li><a href="#support-premium-services" class="premiumServicesMenuItem" data-toggle="tab"><i class="icon-briefcase"></i> &nbsp;&nbsp;Premium Services</a></li>
                        </ul>
                    </li>            
                  </ul>
                  <div class="tab-buttons">
                    <div class="btn-group"> 
                      <a href="javascript:void(0)" class="btn btn-default dropdown-toggle"  data-toggle="dropdown"><i class="icon-trash first-level-spinner"></i> &nbsp;Clear Cache&nbsp; <span class="caret"></span></a> 
                      <ul class="dropdown-menu">
                      <li><a href="javascript:void(0)" onclick="nitro.cachemanager.clearNitroCaches();"><i class="icon-trash"></i> Clear Nitro Cache</a></li>
                      <li class="divider"></li>
                      <li><a href="javascript:void(0)" onclick="nitro.cachemanager.clearPageCache();">Clear Page Cache</a></li>
                      <li><a href="javascript:void(0)" onclick="nitro.cachemanager.clearDBCache();">Clear Database Cache</a></li>
                      <li><a href="javascript:void(0)" onclick="nitro.cachemanager.clearSystemCache();">Clear System Cache</a></li>
                      <li><a href="javascript:void(0)" onclick="nitro.cachemanager.clearImageCache();">Clear Image Cache</a></li>
                      <li><a href="javascript:void(0)" onclick="nitro.cachemanager.clearCSSCache();">Clear CSS Cache</a></li>
                      <li><a href="javascript:void(0)" onclick="nitro.cachemanager.clearJSCache();">Clear JavaScript Cache</a></li>
                      <li><a href="javascript:void(0)" onclick="nitro.cachemanager.clearVqmodCache();">Clear vQmod Cache</a></li>
                      <li class="divider"></li>
                      <li><a href="javascript:void(0)" onclick="nitro.cachemanager.clearAllCaches();"><i class="icon-trash"></i> Clear All Caches</a></li>
                      </ul>
                    </div>
                    <button type="submit" class="btn btn-primary save-changes"><i class="icon-ok"></i> Save changes</button>
                    
                  </div>
                  </div>
                  
                  <div class="tab-content">
                    <div id="pane1" class="tab-pane active googlePageReportWidget">
                <div class="row">
                          <div class="col-md-8">
                                <div class="box-heading">
                                  <h1>Page Report &nbsp;<i class="icon-refresh" id="icon-refresh-pagespeed" title="Re-gather report data" onclick="nitro.pagespeed.refresh(); $(this).addClass('icon-spin')"></i><i class="icon-pagespeed"></i></h1>
                                </div>
                                <div class="box-content">
                                <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/pagespeed_widget.php'); ?>
                                </div>
                            </div>
                          <div class="col-md-4">
                    <div class="box-heading">
                                  <h1><i class="icon-briefcase"></i> Want to speed up your store even more?</h1>
                                </div>
                                <div class="box-content mini-jumbotron">
                      <p>NitroPack does an awesome array of cool techy things that give your store an amazing speed boost, improve SEO and SEM and achieve higher search engine scores. Since every store has an unique set-up, there are many theme-specific and server-specific optimizations that can improve site loading speed even further. Our Premium Services are a proven method to overachieve and redefine what a fast OpenCart website is. All services are hand-coded, by our development team. Please get in touch with us at for a free consultation.</p>
                      <a href="mailto:sales@isenselabs.com?subject=Free Consultation" class="btn btn-default pull-right" target="_blank">
                        <i class="icon-thumbs-up"></i>  Get Free Consultation
                      </a>
                    </div>
                            </div>
                        </div>


                    </div>
                    <div id="generalsettings" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/settings_pane.php'); ?>
                    </div>
              <div id="pagecache" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/pagecache_pane.php'); ?>                        
                    </div>
              <div id="compression" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/compression_pane.php'); ?>                        
                    </div>
              <div id="minification" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/minification_pane.php'); ?>                        
                    </div>
              <div id="browsercache" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/browsercache_pane.php'); ?>                        
                    </div>
              <div id="occache" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/opencartcache_pane.php'); ?>                        
                    </div>
              <div id="imagecache" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/imagecache_pane.php'); ?>                        
                    </div>
              <div id="dbcache" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/dbcache_pane.php'); ?>                        
                    </div>
              <div id="cdn" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/cdn_pane.php'); ?>                        
                    </div>
              <div id="cdn-cloudflare" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/cdn-cloudflare_pane.php'); ?>                        
                    </div>
              <div id="smushit" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/smushit_pane.php'); ?>                        
                    </div>
              <div id="cron_tab" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/cron_pane.php'); ?>                        
                    </div>
              <div id="qa" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/qa_pane.php'); ?>                        
                    </div>
              <div id="isense_support" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/support_pane.php'); ?>                        
                    </div>
                    <div id="support-premium-services" class="tab-pane">
                      <?php require_once(DIR_APPLICATION.'view/template/tool/nitro/premiumservices_pane.php'); ?>                        
                    </div>
                  </div><!-- /.tab-content -->
                </div><!-- /.tabbable -->
                </form>
            <script>
            if (window.localStorage && window.localStorage['currentTab']) {
              $('.mainMenuTabs a[href='+window.localStorage['currentTab']+']').trigger('click');  
            }
            
            if (window.localStorage && window.localStorage['currentSubTab']) {
              $('a[href='+window.localStorage['currentSubTab']+']').trigger('click');  
            }
            
            $('.mainMenuTabs a[data-toggle="tab"]').click(function() {
              if (window.localStorage) {
                window.localStorage['currentTab'] = $(this).attr('href');
              }
            });
            
            $('a[data-toggle="tab"]:not(.mainMenuTabs a[data-toggle="tab"])').click(function() {
              if (window.localStorage) {
                window.localStorage['currentSubTab'] = $(this).attr('href');
              }
            });
          </script>

      </div>
    </div>
  </div>
</div>

<!-- Progress Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="progressModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Clear cache progress</h4>
      </div>
      <div class="modal-body">
        <p>It looks like this is taking longer than usual. Probably there are a lot of cache files. Here is a more detailed view of the progress</p>
        <ul class="progress-list list-unstyled" style="line-height: 26px;">
        </ul>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
<!-- End of Progress Modal -->
<script>
nitro.pagespeed.setToken('<?php echo $_GET['token']; ?>');
nitro.pagespeed.setApiKey($('#pagespeedApiKey').val());
nitro.pagespeed.setSaveUrl('<?php echo $pagespeedSaveUrl; ?>');
nitro.pagespeed.setStoreUrl('<?php echo $pagespeedStoreUrl; ?>');
</script>

<?php echo $footer; ?>
