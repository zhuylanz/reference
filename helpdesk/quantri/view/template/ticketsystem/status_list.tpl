<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
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
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
                <select name="filter_status" id="input-status" class="form-control">
                  <option></option>
                  <option value="1" <?php echo $filter_status=='1' ? 'selected': false; ?>><?php echo $text_enabled; ?></option>
                  <option value="0" <?php echo $filter_status=='0' ? 'selected': false; ?>><?php echo $text_disabled; ?></option>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date_updated"><?php echo $column_date_updated; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_updated" value="<?php echo $filter_date_updated; ?>" placeholder="<?php echo $column_date_updated; ?>" id="input-date_updated" class="form-control" data-date-format="YYYY-MM-DD"/>
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
              <span class="pull-right">&nbsp;</span>
              <button type="button" id="button-clrfilter" class="btn btn-warning pull-right"><i class="fa fa-eraser"></i> <?php echo $button_clrfilter; ?></button>
            </div>
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'tsd.name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-center"><?php if ($sort == 'ts.status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $entry_status; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'ts.date_updated') { ?>
                    <a href="<?php echo $sort_date_updated; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_updated; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_updated; ?>"><?php echo $column_date_updated; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($status) { ?>
                <?php foreach ($status as $result) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($result['id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $result['id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $result['id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left">
                    <h4><b><?php echo $result['name']; ?></b></h4>
                    <?php echo nl2br($result['description']); ?>
                  </td>
                  <td class="text-center">
                    <button class="btn <?php echo $result['status'] ? 'btn-success' : 'btn-warning'; ?>" disabled><?php echo $result['status'] ? $text_enabled : $text_disabled ;?></button>
                  </td>
                  <td class="text-right"><?php echo $result['date_updated']; ?></td>
                  <td class="text-right"><a href="<?php echo $result['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
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
$('.date').datetimepicker({
  pickTime: false
});
$('#button-clrfilter').on('click', function() {
  location = 'index.php?route=ticketsystem/status&token=<?php echo $token; ?>';
});

$('#button-filter').on('click', function() {
	var url = 'index.php?route=ticketsystem/status&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

  var filter_date_updated = $('input[name=\'filter_date_updated\']').val();

  if (filter_date_updated) {
    url += '&filter_date_updated=' + encodeURIComponent(filter_date_updated);
  }

  var filter_status = $('select[name=\'filter_status\']').val();

  if (filter_status) {
    url += '&filter_status=' + encodeURIComponent(filter_status);
  }

	location = url;
});
//--></script>
</div>
<?php echo $footer; ?>