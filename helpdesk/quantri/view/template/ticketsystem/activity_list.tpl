<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-product').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
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
        <div class="well">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-activity"><?php echo $entry_activity; ?></label>
                <input type="text" name="filter_a__activity" value="<?php echo $filter_a__activity; ?>" placeholder="<?php echo $entry_activity; ?>" id="input-activity" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-type"><?php echo $entry_type; ?></label>
                <select name="filter_a__type" class="form-control" id="input-type">
                  <option></option>
                  <?php foreach ($tsType as $value) { ?>
                    <option value="<?php echo $value; ?>" <?php echo $value==$filter_a__type ? 'selected' : false; ?>><?php echo ${'entry_'.$value}; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-user"><?php echo $entry_agents; ?></label>
                <input type="text" name="filter_u__firstname" value="<?php echo $filter_u__firstname; ?>" placeholder="<?php echo $entry_agents; ?>" id="input-user" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-level"><?php echo $entry_level; ?></label>
                <select name="filter_a__level" class="form-control" id="input-level">
                  <option></option>
                  <?php foreach ($tsPriority as $key => $value) { ?>
                    <option value="<?php echo $value; ?>" <?php echo $value==$filter_a__level ? 'selected' : false; ?>><?php echo ${'text_'.$value}; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date_added"><?php echo $entry_date_added; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_a__date_added" value="<?php echo $filter_a__date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" id="input-date_added" data-date-format="YYYY-MM-DD" class="form-control" />
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
              <br/>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
              <span class="pull-right">&nbsp;</span>
              <button type="button" id="button-clrfilter" class="btn btn-warning pull-right"><i class="fa fa-eraser"></i> <?php echo $button_clrfilter; ?></button>
            </div>
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
          <div class="">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-right" style="position:relative;">
                  <button class="btn btn-default dropdown-toggle" type="button" id="activity-option" data-toggle="dropdown" aria-expanded="true">
                    <b><a class="<?php echo strtolower($order); ?>"></a></b>
                  </button>
                  <ul class="dropdown-menu" role="menu" aria-labelledby="activity-option">
                    <li role="presentation">
                      <a role="menuitem" tabindex="-1" href="<?php echo $sort_activity; ?>"><?php if ($sort == 'a.id') { ?><i class="fa fa-check-circle"></i><?php } ?> <?php echo $entry_activity; ?></a>
                    </li>
                    <li role="presentation">
                      <a role="menuitem" tabindex="-1" href="<?php echo $sort_type; ?>"><?php if ($sort == 'a.type') { ?><i class="fa fa-check-circle"></i><?php } ?> <?php echo $entry_type; ?></a>
                    </li>
                    <li role="presentation">
                      <a role="menuitem" tabindex="-1" href="<?php echo $sort_user; ?>"><?php if ($sort == 'a.performer') { ?><i class="fa fa-check-circle"></i><?php } ?> <?php echo $entry_user; ?></a>
                    </li>
                    <li role="presentation">
                      <a role="menuitem" tabindex="-1" href="<?php echo $sort_level; ?>"><?php if ($sort == 'a.level') { ?><i class="fa fa-check-circle"></i><?php } ?> <?php echo $entry_level; ?></a>
                    </li>
                    <li role="presentation">
                      <a role="menuitem" tabindex="-1" href="<?php echo $sort_date_added; ?>"><?php echo $entry_date_added; ?> <?php if ($sort == 'a.date_added') { ?><i class="fa fa-check-circle"></i><?php } ?> </a>
                    </li>
                  </ul>
                  </td>
                </tr>
              </thead>
              <tbody>
                <?php if ($activity) { ?>
                <?php $colors = array('green','blue','red'); ?>
                <?php foreach ($activity as $result) { ?>
                <?php shuffle($colors); ?>
                <tr>
                  <td class="text-center"><?php if (in_array($result['id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $result['id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $result['id']; ?>" />
                    <?php } ?></td>
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
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
$('.create-logo').popover({
  html : true,
});

$('.date').datetimepicker({
  pickTime: false
});
$('#button-clrfilter').on('click', function() {
  location = 'index.php?route=ticketsystem/activity&token=<?php echo $token; ?>';
});

$('#button-filter').on('click', function() {
	var url = 'index.php?route=ticketsystem/activity&token=<?php echo $token; ?>';

	var filter_a__activity = $('input[name=\'filter_a__activity\']').val();

	if (filter_a__activity) {
		url += '&filter_a__activity=' + encodeURIComponent(filter_a__activity);
	}

  var filter_u__firstname = $('input[name=\'filter_u__firstname\']').val();

  if (filter_u__firstname) {
    url += '&filter_u__firstname=' + encodeURIComponent(filter_u__firstname);
  }

  var filter_a__date_added = $('input[name=\'filter_a__date_added\']').val();

  if (filter_a__date_added) {
    url += '&filter_a__date_added=' + encodeURIComponent(filter_a__date_added);
  }

  var filter_a__level = $('select[name=\'filter_a__level\']').val();

  if (filter_a__level) {
    url += '&filter_a__level=' + encodeURIComponent(filter_a__level);
  }

  var filter_a__type = $('select[name=\'filter_a__type\']').val();

  if (filter_a__type) {
    url += '&filter_a__type=' + encodeURIComponent(filter_a__type);
  }

	location = url;
});
//--></script>
</div>
<?php echo $footer; ?>