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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <ul class="list-group">
          <?php foreach ($reporting as $name => $text) { ?>
            <?php if(is_array($text)){ ?>
              <h4><?php echo $$name; ?></h4>
              <?php foreach ($text as $realName => $realText) { ?>
                <a class="list-group-item" href="<?php echo $reporting_view.$realName;?>"><?php echo $realText; ?></a>
              <?php } ?>
              <br/>
            <?php }else{ ?>
              <a class="list-group-item"><?php echo $text; ?></a>
            <?php } ?>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
$('.date').datetimepicker({
  pickTime: false
});
//--></script>
</div>
<?php echo $footer; ?>