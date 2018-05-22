<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <td><?php echo $text_reporting_agent_name; ?></td>
                <td><?php echo $text_reporting_total_ticket; ?></td>
                <td><?php echo $text_reporting_resolved_ticket; ?></td>
                <td><?php echo $text_reporting_first_response_time; ?></td>
                <td><?php echo $text_reporting_avg_response_time; ?></td>
              </tr>
            </thead>
            <tbody>
            <?php if($results){ ?>
              <?php foreach($results as $result){ ?>
                <tr>
                  <td><?php echo $result['agentAliasName'] ? $result['agentAliasName'] : $result['agentName']; ?></td>
                  <td><?php echo $result['total_tickets']; ?></td>
                  <td><?php echo $result['resolved_tickets']; ?></td>
                  <td><?php echo ($result['first_response_time'] ? (int)$result['first_response_time'] : 0).$text_reporting_seconds; ?></td>
                  <td><?php echo ($result['avg_response_time'] ? (int)$result['avg_response_time'] : 0).$text_reporting_seconds; ?></td>
                </tr>
              <?php } ?>
            <?php }else{ ?>
              <tr><td colspan="5" class="text-center"><?php echo $text_no_results ;?></td></tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>