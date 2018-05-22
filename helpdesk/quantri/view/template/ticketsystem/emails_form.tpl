<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ticketsystem">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" data-toggle="tooltip" title="<?php echo $button_fetch; ?>" class="btn btn-success" id="fetch-email"><i class="fa fa-envelope"></i></button>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_validate; ?>" class="btn btn-warning" id="validate-email"><i class="fa fa-check-square"></i></button>
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
        - <?php echo $text_info_emails; ?>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" id="input-name" class="form-control"/>
              <?php if ($error_name) { ?>
                <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-description"><?php echo $entry_description; ?></label>
            <div class="col-sm-10">
                <textarea name="description" id="input-description" class="form-control" placeholder="<?php echo $entry_description_info; ?>"><?php echo $description; ?></textarea>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-email"><?php echo $entry_email; ?></label>
            <div class="col-sm-10">
              <input type="text" name="email" value="<?php echo $email; ?>" id="input-email" class="form-control"/>
            </div>
          </div>

          <div class="alert alert-info">
            <?php echo $text_email_piping_settings; ?>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-username"><?php echo $entry_username; ?></label>
            <div class="col-sm-10">
              <input type="text" name="username" value="<?php echo $username; ?>" id="input-username" class="form-control"/>
              <?php if ($error_username) { ?>
                <div class="text-danger"><?php echo $error_username; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-password"><?php echo $entry_password; ?></label>
            <div class="col-sm-10">
              <input type="text" name="password" value="<?php echo $password; ?>" id="input-password" class="form-control"/>
              <?php if ($error_password) { ?>
                <div class="text-danger"><?php echo $error_password; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-hostname"><?php echo $entry_hostname; ?></label>
            <div class="col-sm-10">
              <input type="text" name="hostname" value="<?php echo $hostname; ?>" id="input-hostname" class="form-control"/>
              <?php if ($error_hostname) { ?>
                <div class="text-danger"><?php echo $error_hostname; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-port"><?php echo $entry_port; ?></label>
            <div class="col-sm-10">
              <input type="text" name="port" value="<?php echo $port; ?>" id="input-port" class="form-control"/>
              <?php if ($error_port) { ?>
                <div class="text-danger"><?php echo $error_port; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-mailbox"><?php echo $entry_mailbox; ?></label>
            <div class="col-sm-10">
              <input type="text" name="mailbox" value="<?php echo $mailbox; ?>" id="input-mailbox" class="form-control" placeholder="Inbox"/>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-protocol"><?php echo $entry_protocol; ?> <span data-toggle="tooltip" title="<?php echo $entry_protocol_info; ?>"></span></label>
            <div class="col-sm-10">
              <select name="protocol" id="input-protocol" class="form-control">
                <?php foreach ($mailProtocols as $result) { ?>
                  <option value="<?php echo $result; ?>" <?php echo $protocol==$result ? 'selected' : false ;?>><?php echo $result; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-fetch_time"><?php echo $entry_fetch_time; ?> <span data-toggle="tooltip" title="<?php echo $entry_fetch_time_info; ?>"></span></label>
            <div class="col-sm-10">
              <input type="text" name="fetch_time" value="<?php echo $fetch_time; ?>" id="input-fetch_time" class="form-control"/>
              <?php if ($error_fetch_time) { ?>
                <div class="text-danger"><?php echo $error_fetch_time; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-email_per_fetch"><?php echo $entry_email_per_fetch; ?> <span data-toggle="tooltip" title="<?php echo $entry_email_per_fetch_info; ?>"></span></label>
            <div class="col-sm-10">
              <input type="text" name="email_per_fetch" value="<?php echo $email_per_fetch; ?>" id="input-email_per_fetch" class="form-control"/>
              <?php if ($error_email_per_fetch) { ?>
                <div class="text-danger"><?php echo $error_email_per_fetch; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-actions"><?php echo $entry_email_actions; ?> <span data-toggle="tooltip" title="<?php echo $entry_email_actions_info; ?>"></span></label>
            <div class="col-sm-10">
              <label class="radio-inline">
                <input type="radio" name="actions[action]" value="nothing" id="input-actions" <?php echo (isset($actions['action']) AND $actions['action']=='nothing') ? 'checked' : false; ?>/> <?php echo $entry_nothing; ?>
              </label><br/>
              <label class="radio-inline">
                <input type="radio" name="actions[action]" value="delete" id="input-actions" <?php echo (isset($actions['action']) AND $actions['action']=='delete') ? 'checked' : false; ?>/> <?php echo $entry_delete_email; ?>
              </label><br/>
              <label class="radio-inline">
                <input type="radio" name="actions[action]" value="movetofolder" id="input-actions" <?php echo (isset($actions['action']) AND $actions['action']=='movetofolder') ? 'checked' : false; ?>/> <?php echo $entry_create_folder; ?>
              </label>
              <input type="text" name="actions[folder]" value="<?php echo isset($actions['folder']) ? $actions['folder'] : false; ?>" class="form-control" style="display: inline-block; width: 50%; position: relative; top: 8px;"/>
              <?php if ($error_email_action) { ?>
                <div class="text-danger"><?php echo $error_email_action; ?></div>
              <?php } ?>
              <?php if ($error_email_action_folder) { ?>
                <br/>
                <div class="text-danger"><?php echo $error_email_action_folder; ?></div>
              <?php } ?>
            </div>
          </div>


          <div class="alert alert-info">
            <?php echo $text_default_options_info; ?>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-groups"><?php echo $entry_default_group; ?> </label>
            <div class="col-sm-10">
              <select id="input-groups" name="group" class="form-control">
                <?php foreach ($groups as $result) { ?>
                  <option value="<?php echo $result['id']; ?>" <?php echo $group==$result['id'] ? 'selected' : false ;?>><?php echo $result['name']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-types"><?php echo $entry_default_type; ?> </label>
            <div class="col-sm-10">
              <select id="input-types" name="type" class="form-control">
                <?php foreach ($types as $result) { ?>
                  <option value="<?php echo $result['id']; ?>" <?php echo $type==$result['id'] ? 'selected' : false ;?>><?php echo $result['name']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-priority"><?php echo $entry_default_priority; ?> </label>
            <div class="col-sm-10">
              <select id="input-priority" name="priority" class="form-control">
                <?php foreach ($priorities as $result) { ?>
                  <option value="<?php echo $result['id']; ?>" <?php echo $priority==$result['id'] ? 'selected' : false ;?>><?php echo $result['name']; ?></option>
                <?php } ?>
              </select>
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
var updateStatusHtml = '<div class="alert alert-changeClass"><i class="fa fa-exclamation-circle"></i> changeMsg <button type="button" class="close" data-dismiss="alert">&times;</button></div>';

$('#validate-email').on('click', function(){
  var url = 'index.php?route=ticketsystem/emails/emailValidate&token=<?php echo $token; ?>';
  ajaxCalls(this, url);
});

$('#fetch-email').on('click', function(){
  var url = 'index.php?route=ticketsystem/emails/emailFetch&token=<?php echo $token; ?>';
  ajaxCalls(this, url);
});

function ajaxCalls(thisthis, url){
  updateDisabledProp(thisthis);
  var thisthisOldHtml = $(thisthis).html();
  $.ajax({
    url: url,
    method: 'post',
    dataType: 'json',
    data: {'host':$('input[name=\'hostname\']').val(), 'port':$('input[name=\'port\']').val(), 'username':$('input[name=\'username\']').val(), 'password':$('input[name=\'password\']').val(), 'service':$('select[name=\'protocol\']').val(), 'mailbox': $('input[name=\'mailbox\']').val(), 'fetch_time':$('input[name=\'fetch_time\']').val(), 'email_per_fetch':$('input[name=\'email_per_fetch\']').val(), 'actions':$('input[name=\'actions[action]\']:selected').val(), 'id': '<?php echo isset($id) ? $id : 0; ?>'},
    beforeSend: function(){
      $('#content .container-fluid .alert').remove();
      $(thisthis).html('<i class="fa fa-spin fa-spinner"></i>');
    },
    success: function(json){
      var html = '';
      if(json['success']){
        html = updateStatusHtml.replace('changeClass','success').replace('changeMsg', json['success']);
      }
      if(json['error']){
        html = updateStatusHtml.replace('changeClass','danger').replace('changeMsg', json['error']);
      }
      $('.panel.panel-default').before(html);

      if(json['alertMessage'] != undefined && json['alertMessage']['error']!=undefined){
        html = updateStatusHtml.replace('changeClass','warning').replace('changeMsg', json['alertMessage']['error']);
        $('.panel.panel-default').before(html);
      }

      if(json['alertMessage'] != undefined && json['alertMessage']['ticket']!=undefined){
        html = updateStatusHtml.replace('changeClass','success').replace('changeMsg', json['alertMessage']['ticket']);
        $('.panel.panel-default').before(html);
      }
      
      if(json['alertMessage'] != undefined && json['alertMessage']['thread']!=undefined){
        html = updateStatusHtml.replace('changeClass','success').replace('changeMsg', json['alertMessage']['thread']);
        $('.panel.panel-default').before(html);
      }
    },
    complete: function(){
      updateDisabledProp(thisthis);
      $(thisthis).html(thisthisOldHtml);
    }
  });
}

function updateDisabledProp(thisthis){
  if($(thisthis).prop('disabled')){
    $(thisthis).prop('disabled',false);
  }else{
    $(thisthis).prop('disabled',true);
  }
}
//--></script></div>
<?php echo $footer; ?> 