<script type="text/javascript">
var report_desktop = <?php echo !empty($widget['pagespeed']['desktop']) ? $widget['pagespeed']['desktop'] : 'null'; ?>;
var report_mobile = <?php echo !empty($widget['pagespeed']['mobile']) ? $widget['pagespeed']['mobile'] : 'null'; ?>;
</script>
<table style="width:100%" class="pagespeedMainTable">
<tr>
    <td style="width:50%;vertical-align: top;">
    <div id="g1" class="bigGauge"></div>
    <div id="g2" class="bigGauge"></div>
    </td>
    <td style="width:50%; vertical-align:top;padding-top:5px;">
    	<h3>Steps you need to take</h3>
        <table class="table stepsToTake">
          <tbody>
            <tr class="<?php echo (!empty($nitroData['Nitro']['PageCache']['Enabled']) && $nitroData['Nitro']['PageCache']['Enabled'] == 'yes') ? 'disabled' : ''; ?>">
              <td>1</td>
              <td>Enable Page Caching</td>
              <td style="width: 100px"><a onclick="$('html, body').animate({ scrollTop: 0 }, 200, function() { $('a[href=#pagecache]').trigger('click'); });" class="btn btn-small btn-default">Setup Now</a></td>
            </tr>
            <tr class="<?php echo (!empty($nitroData['Nitro']['BrowserCache']['Enabled']) && $nitroData['Nitro']['BrowserCache']['Enabled'] == 'yes') ? 'disabled' : ''; ?>">
              <td>2</td>
              <td>Leverage Browser Caching</td>
              <td><a onclick="$('html, body').animate({ scrollTop: 0 }, 200, function() { $('a[href=#browsercache]').trigger('click'); });" class="btn btn-small btn-default">Setup Now</a></td>
            </tr>
            <tr class="<?php echo (!empty($nitroData['Nitro']['Compress']['Enabled']) && $nitroData['Nitro']['Compress']['Enabled'] == 'yes') ? 'disabled' : ''; ?>">
              <td>3</td>
              <td>Enable GZIP Compression</td>
              <td><a onclick="$('html, body').animate({ scrollTop: 0 }, 200, function() { $('a[href=#compression]').trigger('click'); });" class="btn btn-small btn-default">Setup Now</a></td>
            </tr>
            <tr class="<?php echo (!empty($nitroData['Nitro']['Mini']['CSS']) && $nitroData['Nitro']['Mini']['CSS'] == 'yes' && $nitroData['Nitro']['Mini']['JS'] == 'yes') ? 'disabled' : ''; ?>">
              <td>4</td>
              <td>Minify CSS and JavaScript</td>
              <td><a onclick="$('html, body').animate({ scrollTop: 0 }, 200, function() { $('a[href=#minification]').trigger('click'); });" class="btn btn-small btn-default">Setup Now</a></td>
            </tr>
            <tr class="<?php echo (!empty($nitroData['Nitro']['Mini']['HTML']) && $nitroData['Nitro']['Mini']['HTML'] == 'yes') ? 'disabled' : ''; ?>">
              <td>5</td>
              <td>Minify HTML</td>
              <td><a onclick="$('html, body').animate({ scrollTop: 0 }, 200, function() { $('a[href=#minification]').trigger('click'); });" class="btn btn-small btn-default">Setup Now</a></td>
            </tr>
          </tbody>
        </table>
    </td>
</tr>
</table>
<div class="text-greatscore hidden"><span class="label label-success">Great Score</span>&nbsp;&nbsp;<a href="http://www.seochat.com/c/a/search-engine-optimization-help/google-page-speed-score-vs-website-loading-time/" target="_blank">Top-ranking websites</a> in Google have an average score of 80.78 and yours is <strong id="greatscore-points"></strong>!</div>


<ul class="nav nav-pills gaugeFilterUL" param="performers">
  <li class="active">
    <a href="javascript:void(0)" param="desktopScore">Desktop</a>
  </li>
  <li>
    <a href="javascript:void(0)" param="mobileScore">Mobile</a>
  </li>
</ul>
<div id="extendedInfo"></div>
<div class="alert alert-success performersSuccess" style="display:none"></div>

<script type="text/javascript">
// This fixes a firefox issue with gauges
if (navigator.appCodeName == 'Mozilla') {
	$('base').remove();
}
var recognizedRules = ["AvoidLandingPageRedirects", "EnableGzipCompression", "LeverageBrowserCaching", "MainResourceServerResponseTime", "MinifyCss", "MinifyHTML", "MinifyJavaScript", "MinimizeRenderBlockingResources", "OptimizeImages", "PrioritizeVisibleContent"];

var notOptimizedImages = [];

var drawReportStats = function() {
    notOptimizedImages = [];
    $("#g1,#g2,#extendedInfo").html("");
    showDesktopReport();
    $('#g2').show();
    loadMainGauges();
    $('#g2').hide();
    loadDesktopReport();
    loadMobileReport();
    addFoldableHandlers();

    if (report_desktop && report_desktop.score && report_desktop.score > 81) {
        $("#greatscore-points").text(report_desktop.score);
        $(".text-greatscore").removeClass("hidden");
    } else {
        $(".text-greatscore").addClass("hidden");
    }

    if (notOptimizedImages.length) {
        $("#optimizeReportedImagesButton").removeClass("hidden");
    } else {
        $("#optimizeReportedImagesButton").addClass("hidden");
    }
}

var loadMainGauges = function() {
	new JustGage({
      id: "g1", 
      value: (report_desktop && report_desktop.score) ? report_desktop.score : 0, 
      min: 0,
      max: 100,
      title: (report_desktop && report_desktop.error) ? report_desktop.error.message : "Your Site Desktop Page Score",
      label: "points",
      levelColors: ["#B20000","#FF9326","#6DD900"],
    });
    
    new JustGage({
      id: "g2", 
      value: (report_mobile && report_mobile.score) ? report_mobile.score : 0,
      min: 0,
      max: 100,
      title: (report_mobile && report_mobile.error) ? report_mobile.error.message : "Your Site Mobile Page Score",
      label: "points",
      levelColors: ["#B20000","#FF9326","#6DD900"],
    });
}

var loadDesktopReport = function() {
    if (!report_desktop) return;

    var ruleResult = null;
    var isRulePassed = false;
    var desktopReportHtml = '<div id="desktopExtendedReport">';
    for(x in report_desktop.formattedResults.ruleResults) {
        ruleResult = report_desktop.formattedResults.ruleResults[x];
        isRulePassed = (parseInt(ruleResult.ruleImpact) == 0);
        for (t=0;t<ruleResult.urlBlocks.length;t++) {
            if (ruleResult.urlBlocks[t].header.args) {
                var formattedResult = ruleResult.urlBlocks[t].header.format;
                for(i=0;i<ruleResult.urlBlocks[t].header.args.length;i++) {
                    if (ruleResult.urlBlocks[t].header.args[i].type != 'HYPERLINK') {
                        formattedResult = formattedResult.replace('$'+(i+1), ruleResult.urlBlocks[t].header.args[i].value);
                    } else {
                        formattedResult = formattedResult.replace('Learn more', '<a target="_blank" href="'+ruleResult.urlBlocks[t].header.args[i].value+'">Learn more</a>');
                    }
                }
                desktopReportHtml += '<li class="'+((isRulePassed) ? 'passedRule' : 'notPassedRule')+'"><span>' + formattedResult + '</span>';
            }

            if (ruleResult.urlBlocks[t].urls) {
                desktopReportHtml += '<ul>';
                for(y=0;y<ruleResult.urlBlocks[t].urls.length;y++) {
                    if (ruleResult.urlBlocks[t].urls[y].result.args) {
                        var formattedResult = ruleResult.urlBlocks[t].urls[y].result.format;
                        for(z=0;z<ruleResult.urlBlocks[t].urls[y].result.args.length;z++) {
                            if (ruleResult.urlBlocks[t].urls[y].result.args[z].type == 'URL') {
                                var url = ruleResult.urlBlocks[t].urls[y].result.args[z].value;

                                if (x == "OptimizeImages" && notOptimizedImages.indexOf(url)) {
                                    notOptimizedImages.push(url);
                                }

                                formattedResult = formattedResult.replace('$'+(z+1), '<a target="_blank" href="'+url+'">'+url+'</a>');
                            } else {
                                formattedResult = formattedResult.replace('$'+(z+1), ruleResult.urlBlocks[t].urls[y].result.args[z].value);
                            }
                        }
                        desktopReportHtml += '<li>' + formattedResult + '</li>';
                    }
                }
                desktopReportHtml += '</ul>';
            }
            desktopReportHtml += '</li>';
        }
    }
    desktopReportHtml += '</div>';
    $('#extendedInfo').append(desktopReportHtml);
    $('#desktopExtendedReport').append('<ul class="passedRules"><li class="passedRulesCounter resultsToggle">{NUM} Passed rules</li></ul><ul class="notPassedRules"><li class="notPassedRulesCounter resultsToggle">{NUM} Rules not passed</li></ul>');
    $('#desktopExtendedReport li.passedRule').appendTo('#desktopExtendedReport ul.passedRules');
    $('#desktopExtendedReport ul.passedRules li.passedRulesCounter').text($('#desktopExtendedReport ul.passedRules li.passedRulesCounter').text().replace('{NUM}', $('#desktopExtendedReport li.passedRule').size()));
    $('#desktopExtendedReport li.notPassedRule').appendTo('#desktopExtendedReport ul.notPassedRules');
    $('#desktopExtendedReport ul.notPassedRules li.notPassedRulesCounter').text($('#desktopExtendedReport ul.notPassedRules li.notPassedRulesCounter').text().replace('{NUM}', $('#desktopExtendedReport li.notPassedRule').size()));
    $('#desktopExtendedReport li ul').hide().parent().addClass('foldable');
}

var loadMobileReport = function() {
    if (!report_mobile) return;

    var ruleResult = null;
    var isRulePassed = false;
    var desktopReportHtml = '<div id="mobileExtendedReport" style="display: none;">';
    for(x in report_mobile.formattedResults.ruleResults) {
        if (recognizedRules.indexOf(x) < 0) continue;
        ruleResult = report_mobile.formattedResults.ruleResults[x];
        isRulePassed = (parseInt(ruleResult.ruleImpact) == 0);
        for (t=0;t<ruleResult.urlBlocks.length;t++) {
            if (ruleResult.urlBlocks[t].header.args) {
                var formattedResult = ruleResult.urlBlocks[t].header.format;
                for(i=0;i<ruleResult.urlBlocks[t].header.args.length;i++) {
                    if (ruleResult.urlBlocks[t].header.args[i].type != 'HYPERLINK') {
                        formattedResult = formattedResult.replace('$'+(i+1), ruleResult.urlBlocks[t].header.args[i].value);
                    } else {
                        formattedResult = formattedResult.replace('Learn more', '<a target="_blank" href="'+ruleResult.urlBlocks[t].header.args[i].value+'">Learn more</a>');
                    }
                }
                desktopReportHtml += '<li class="'+((isRulePassed) ? 'passedRule' : 'notPassedRule')+'"><span>' + formattedResult + '</span>';
            }

            if (ruleResult.urlBlocks[t].urls) {
                desktopReportHtml += '<ul>';
                for(y=0;y<ruleResult.urlBlocks[t].urls.length;y++) {
                    if (ruleResult.urlBlocks[t].urls[y].result.args) {
                        var formattedResult = ruleResult.urlBlocks[t].urls[y].result.format;
                        for(z=0;z<ruleResult.urlBlocks[t].urls[y].result.args.length;z++) {
                            if (ruleResult.urlBlocks[t].urls[y].result.args[z].type == 'URL') {
                                var url = ruleResult.urlBlocks[t].urls[y].result.args[z].value;

                                if (x == "OptimizeImages" && notOptimizedImages.indexOf(url)) {
                                    notOptimizedImages.push(url);
                                }

                                formattedResult = formattedResult.replace('$'+(z+1), '<a target="_blank" href="'+url+'">'+url+'</a>');
                            } else {
                                formattedResult = formattedResult.replace('$'+(z+1), ruleResult.urlBlocks[t].urls[y].result.args[z].value);
                            }
                        }
                        desktopReportHtml += '<li>' + formattedResult + '</li>';
                    }
                }
                desktopReportHtml += '</ul>';
            }
            desktopReportHtml += '</li>';
        }
    }
    desktopReportHtml += '</div>';
    $('#extendedInfo').append(desktopReportHtml);
    $('#mobileExtendedReport').append('<ul class="passedRules"><li class="passedRulesCounter resultsToggle">{NUM} Passed rules</li></ul><ul class="notPassedRules"><li class="notPassedRulesCounter resultsToggle">{NUM} Rules not passed</li></ul>');
    $('#mobileExtendedReport li.passedRule').appendTo('#mobileExtendedReport ul.passedRules');
    $('#mobileExtendedReport ul.passedRules li.passedRulesCounter').text($('#mobileExtendedReport ul.passedRules li.passedRulesCounter').text().replace('{NUM}', $('#mobileExtendedReport li.passedRule').size()));
    $('#mobileExtendedReport li.notPassedRule').appendTo('#mobileExtendedReport ul.notPassedRules');
    $('#mobileExtendedReport ul.notPassedRules li.notPassedRulesCounter').text($('#mobileExtendedReport ul.notPassedRules li.notPassedRulesCounter').text().replace('{NUM}', $('#mobileExtendedReport li.notPassedRule').size()));
    $('#mobileExtendedReport li ul').hide().parent().addClass('foldable');
}

var addFoldableHandlers = function() {
    $('li.foldable').click(function(){
        $(this).find('ul').slideToggle();
    });

    $('li.resultsToggle').click(function(){
        $(this).parent().find('li.passedRule, li.notPassedRule').slideToggle();
    });
}

var showDesktopReport = function() {
    $('#g2').hide();
    $('#g1').show();
    $('#mobileExtendedReport').hide();
    $('#desktopExtendedReport').show();
    $('.gaugeFilterUL li').removeClass('active');
    $('.gaugeFilterUL a[param="desktopScore"]').parent().addClass('active');
}

var showMobileReport = function() {
    $('#g1').hide();
    $('#g2').show();
    $('#desktopExtendedReport').hide();
    $('#mobileExtendedReport').show();
    $('.gaugeFilterUL li').removeClass('active');
    $('.gaugeFilterUL a[param="mobileScore"]').parent().addClass('active');
}

$('.gaugeFilterUL a').click(function() {
    if ($(this).attr('param') == 'desktopScore') {
        showDesktopReport();
    }

    if ($(this).attr('param') == 'mobileScore') {
        showMobileReport();
    }
});

drawReportStats();

$('.stepsToTake tr.disabled .btn').text('Enabled').removeClass('btn-inverse').addClass('disabled');

</script>
