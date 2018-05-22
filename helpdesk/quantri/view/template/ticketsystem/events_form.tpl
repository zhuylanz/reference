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
        - <?php echo $text_info_events; ?>
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
            <label class="col-sm-2 control-label" for="input-description"><?php echo $text_events; ?><span data-toggle="tooltip" title="<?php echo $text_info_tevents; ?>"></span></label>
            <div class="col-sm-10">
              <?php echo $ticketEvents; ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-performer"><?php echo $text_events_perform; ?></label>
            <div class="col-sm-10">
              <select name="performer[value]" id="input-performer" class="form-control">
                <?php if ($performer['value'] == 'everyone') { ?>
                <option value="everyone" selected="selected"><?php echo $text_everyone; ?></option>
                <?php } else { ?>
                <option value="everyone"><?php echo $text_everyone; ?></option>
                <?php } ?>
                <?php if ($performer['value'] == 'customer') { ?>
                <option value="customer" selected="selected"><?php echo $entry_customers; ?></option>
                <?php } else { ?>
                <option value="customer"><?php echo $entry_customers; ?></option>
                <?php } ?>
                <?php if ($performer['value'] == 'agents') { ?>
                <option value="agents" selected="selected"><?php echo $entry_agents; ?></option>
                <?php } else { ?>
                <option value="agents"><?php echo $entry_agents; ?></option>
                <?php } ?>
              </select>
              <div class="hide" id="for-agent" style="margin-top:5px;">
                <select name="performer[agents][]" id="input-agent" class="form-control selectpicker" data-live-search="true" multiple title="<?php echo $text_event_select_title; ?>">
                  <option value="all" <?php echo in_array('all', $performer['agents']) ? 'selected' : ''; ?>><?php echo $text_for_all; ?></option> 
                  <?php foreach ($tsAgents as $agent) { ?>
                    <option value="<?php echo $agent['id']; ?>" <?php echo in_array($agent['id'], $performer['agents']) ? 'selected' : ''; ?>><?php echo $agent['username']; ?></option> 
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-description"><?php echo sprintf($text_conditions_one, $heading_title); ?><span data-toggle="tooltip" title="<?php echo sprintf($text_info_conditions_one, $heading_title); ?>"></span></label>
            <div class="col-sm-10">
              <?php echo $ticketConditionsOne; ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-description"><?php echo sprintf($text_conditions_all, $heading_title); ?><span data-toggle="tooltip" title="<?php echo sprintf($text_info_conditions_all, $heading_title); ?>"></span></label>
            <div class="col-sm-10">
              <?php echo $ticketConditionsAll; ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-description"><?php echo $text_actions; ?><span data-toggle="tooltip" title="<?php echo $text_info_actions; ?>"></span></label>
            <div class="col-sm-10">
              <?php echo $ticketAction; ?>
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
$('#input-performer').on('change', function() {
  if($(this).val()=='agents')
    $('#for-agent').removeClass('hide');
  else
    $('#for-agent').addClass('hide');
});

$('#input-performer').trigger('change');
//--></script></div>
<?php echo $footer; ?> 