<div class="row">
  <div class="col-md-7">
    <div class="box-heading"><h1>Generic CDN</h1></div>
    <div class="box-content">
      <table class="form cdnpanetable">
        <tr>
          <td>Generic CDN URL<span class="help">If you are using a custom CDN service like MaxCDN, you can insert the Base URL to which your images, CSS and JavaScript files will get rewritten. Example: <strong>//cdn.mydomain.com/</strong></span></td>
          <td>
            <input class="form-control" type="text" name="Nitro[CDNStandard][GenericURL]" value="<?php echo(!empty($nitroData['Nitro']['CDNStandard']['GenericURL'])) ? $nitroData['Nitro']['CDNStandard']['GenericURL'] : ''?>" />
          </td>
        </tr>
        <tr>
          <td>Ignored Routes<span class="help">Routes (e.g. common/home) to be ignored from the page cache. One route per line.</span></td>
          <td>
            <textarea class="form-control" name="Nitro[CDNStandard][IgnoredRoutes]" style="width:400px; height:180px;" placeholder="One route per line, e.g. information/sitemap"><?php echo (!empty($nitroData['Nitro']['CDNStandard']['IgnoredRoutes'])) ? $nitroData['Nitro']['CDNStandard']['IgnoredRoutes'] : '' ?></textarea>
          </td>
        </tr>
      </table>
    </div>
  </div>
  <div class="col-md-5">
    <div class="box-heading"><h1><i class="icon-info-sign"></i>&nbsp;Benefits of CDN</h1></div>
    <div class="box-content" style="min-height:100px; line-height:20px;">
      <p>CDN (Content Delivery Network) is a web service used for global content delivery. When you integrate with CDN, your content gets copied on all the CDN servers around the world. Therefore, when a visitor tries to access your webstore, content is delivered from the nearest server geographically located to the visitor instead of your hosting server which may be on the other part of the globe. This makes your webstore load faster and improves overall speed.
      </p>
    </div>

    <div class="box-heading"><h1><i class="icon-info-sign"></i>&nbsp;Amazon CloudFront/S3 CDN Service</h1></div>
    <div class="box-content" style="min-height:100px; line-height:20px;">
      <p>If you have an Amazon CloudFront account, you can configure your Amazon CDN by following this tutorial (only the section "Create a CloudFront Distribution"):<br>
        <br>
        <a target="_blank" href="http://docs.aws.amazon.com/gettingstarted/latest/swh/getting-started-create-cfdist.html#create-distribution"><u>http://docs.aws.amazon.com/gettingstarted/latest/swh/getting-started-create-cfdist.html#create-distribution</u></a>
        <br><br>
        <ol>
          <li>Visit the link above to configure the CDN</li>
          <li>Wait for a few hours for Amazon to fetch your website content.</li>
          <li>Configure your NitroPack Generic CDN using the CloudFront Domain Name (which looks like xxxxxxxxxxxx.cloudfront.net).</li>
        </ol>
      </p>
    </div>
  </div>
</div>
