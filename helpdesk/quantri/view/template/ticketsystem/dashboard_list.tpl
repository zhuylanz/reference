<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
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
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <?php echo $text_list_dashboard; ?>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-sm-12">
            <div style="margin: 0px -14px;">
              <?php foreach ($tile as $status) { ?>
                <div class="col-sm-2">
                  <div class="tile">
                    <div class="tile-heading"><?php echo $status['name']; ?> <span class="pull-right">
                      <?php if ($status['total'] > 0) { ?>
                      <i class="fa fa-caret-up"></i>
                      <?php } elseif ($status['total'] < 0) { ?>
                      <i class="fa fa-caret-down"></i>
                      <?php } ?></span>
                    </div>
                    <div class="tile-body"><i class="fa fa-eye"></i>
                      <h2 class="pull-right"><?php echo $status['total']; ?></h2>
                    </div>
                    <div class="tile-footer"><a href="<?php echo $status['link']; ?>"><?php echo $text_view; ?></a></div>
                  </div>
                </div>
              <?php } ?>
            </div>
            <table class="table table-bordered table-hover">
              <tbody>
                <?php if ($activity) { ?>
                <?php $colors = array('green','blue','red'); ?>
                <?php foreach ($activity as $result) { ?>
                <?php shuffle($colors); ?>
                <tr>
                  <td class="text-left" colspan="5">
                    <h4><b><?php echo ucfirst($result['performertype']); ?></br><?php echo ucwords($result['type']); ?></b></h4>
                    <?php in_array(strtolower(substr($result['username'],0,1)), range('a','g')) ? 'blue' : (in_array(strtolower(substr($result['username'],0,1)), range('h','o')) ? 'green' : 'red');?>
                    <?php echo $result['username'] ? '' : '-off'; ?>
                    <div class="create-logo pull-left <?php echo $colors[0]; ?>" tabindex="0" data-trigger="focus" data-toggle="popover"  title="<?php echo $text_perfromer_details;?>" data-content="<div><a href='<?php echo $result['performerLink']; ?>'><i class='fa fa-user'></i> <?php echo $result['username']; ?></a><br/> <i class='fa fa-envelope'></i> <?php echo $result['email']; ?></div>" ><?php echo substr($result['username'],0,1); ?></div>
                    <div class="pull-left">
                      <?php echo $result['level']; ?><br/>
                      <?php echo $result['activity']; ?>
                    </div>
                  </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            <div class="row">
              <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
              <div class="col-sm-6 text-right"><?php echo $results; ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
$('.create-logo').popover({
  html : true,
});
//--></script>
</div>
<?php echo $footer; ?>