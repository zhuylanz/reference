<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ticketsystem">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-product" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
        - <?php echo $text_info_responses; ?>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
                <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-description"><?php echo $entry_description; ?></label>
            <div class="col-sm-10">
              <textarea name="description" placeholder="<?php echo $entry_description; ?>" id="input-description" class="form-control"><?php echo $description; ?></textarea>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-description"><?php echo $text_actions; ?><span data-toggle="tooltip" title="<?php echo $text_info_actions; ?>"></span></label>
            <div class="col-sm-10">
              <?php echo $ticketAction; ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-valid-for"><?php echo $text_can_use; ?></label>
            <div class="col-sm-10">
              <select name="valid_for[value]" id="input-valid-for" class="form-control">
                <?php if (isset($valid_for['value']) AND $valid_for['value'] == 'me') { ?>
                <option value="me" selected="selected"><?php echo $text_me; ?></option>
                <?php } else { ?>
                <option value="me"><?php echo $text_me; ?></option>
                <?php } ?>
                <?php if (isset($valid_for['value']) AND $valid_for['value'] == 'all') { ?>
                <option value="all" selected="selected"><?php echo $text_for_all; ?></option>
                <?php } else { ?>
                <option value="all"><?php echo $text_for_all; ?></option>
                <?php } ?>
                <?php if (isset($valid_for['value']) AND $valid_for['value'] == 'groups') { ?>
                <option value="groups" selected="selected"><?php echo $text_for_groups; ?></option>
                <?php } else { ?>
                <option value="groups"><?php echo $text_for_groups; ?></option>
                <?php } ?>
              </select>
              <div class="hide" id="for-group">
                <br/>
                <input type="text" name="groups-search" value="" placeholder="<?php echo $entry_groups; ?>" id="input-groups" class="form-control" />
                <div id="response-groups" class="well well-sm" style="height: 150px; overflow: auto;">
                  <?php foreach ($valid_for['groups'] as $group) { ?>
                  <div id="response-groups<?php echo $group['group_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $group['name']; ?>
                    <input type="hidden" name="valid_for[groups][]" value="<?php echo $group['group_id']; ?>" />
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <label class="radio-inline">
                <input type="radio" name="status" value="1" <?php echo $status ? 'checked' : false; ?>> <?php echo $text_enable; ?>
              </label>
              <label class="radio-inline">
                <input type="radio" name="status" value="0" <?php echo !$status ? 'checked' : false; ?>> <?php echo $text_disable; ?>
              </label>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
$('#input-valid-for').on('change', function() {
  if($(this).val()=='groups')
    $('#for-group').removeClass('hide');
  else
    $('#for-group').addClass('hide');
});

// Groups
$('input[name=\'groups-search\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=ticketsystem/groups/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',     
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'groups-search\']').val('');
    
    $('#response-groups' + item['value']).remove();
    
    $('#response-groups').append('<div id="response-groups' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="valid_for[groups][]" value="' + item['value'] + '" /></div>');  
  } 
});

$('#response-groups').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});

$('#input-valid-for').trigger('change');

//--></script></div>
<?php echo $footer; ?> 