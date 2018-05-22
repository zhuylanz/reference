<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-agent').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
                <input type="text" name="filter_firstname" value="<?php echo $filter_firstname; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                <input type="text" name="filter_email" value="<?php echo $filter_email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-login"><?php echo $column_last_login; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_last_login" value="<?php echo $filter_last_login; ?>" placeholder="<?php echo $column_last_login; ?>" id="input-login" class="form-control date" data-date-format="YYYY-MM-DD"/>
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
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-agent">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'u.username') { ?>
                    <a href="<?php echo $sort_username; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_username; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'u.email') { ?>
                    <a href="<?php echo $sort_email; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_email; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_email; ?>"><?php echo $column_email; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'a.last_login') { ?>
                    <a href="<?php echo $sort_lastlogin; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_last_login; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_lastlogin; ?>"><?php echo $column_last_login; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($agents) { ?>
                <?php foreach ($agents as $result) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($result['id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $result['id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $result['id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left">
                    <img src="<?php echo $result['image']; ?>" class="img-responsive img-thumbnail"/>
                    &nbsp;&nbsp;<?php echo $result['name']; ?>
                  </td>
                  <td class="text-left">
                    <?php echo $result['email']; ?>
                  </td>
                  <td class="text-left">
                    <?php echo $result['last_login']; ?>
                  </td>
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
$('#button-clrfilter').on('click', function() {
  location = 'index.php?route=ticketsystem/agents&token=<?php echo $token; ?>';
});

$('#button-filter').on('click', function() {
	var url = 'index.php?route=ticketsystem/agents&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_firstname\']').val();

	if (filter_name) {
		url += '&filter_firstname=' + encodeURIComponent(filter_name);
	}

  var filter_email = $('input[name=\'filter_email\']').val();

  if (filter_email) {
    url += '&filter_email=' + encodeURIComponent(filter_email);
  }

  var filter_last_login = $('input[name=\'filter_last_login\']').val();

  if (filter_last_login) {
    url += '&filter_last_login=' + encodeURIComponent(filter_last_login);
  }

	location = url;
});

$('.date').datetimepicker({
  pickTime: false
});
  //--></script>
</div>
<?php echo $footer; ?>