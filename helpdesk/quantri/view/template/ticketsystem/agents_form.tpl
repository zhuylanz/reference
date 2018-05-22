<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ticketsystem">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-agent" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-agent" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-user"><?php echo $entry_user; ?></label>
            <div class="col-sm-10">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <i class="fa fa-users"></i> <?php echo $text_info_agents; ?>
                </div>
              </div>
            </div>
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>"/>
            <div class="col-sm-2">&nbsp;</div>
            <div class="col-sm-10">
              <input type="text" id="input-user" name="username" value="<?php echo $username; ?>" placeholder="<?php echo $entry_user; ?>" id="input-user" class="form-control" />
              <?php if ($error_user) { ?>
                <div class="text-danger"><?php echo $error_user; ?></div>
              <?php } ?>
            </div>
            <div class="col-sm-2">&nbsp;</div>
            <div class="agent-profile col-sm-10">
              <span class="image pull-left"><img src="<?php echo $image; ?>" class="img-responsive img-thumbnail"></span>
              &nbsp;<span class="name"><?php echo $username; ?></span><br/>
              &nbsp;<span class="email"><?php echo $email; ?></span>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-alias"><?php echo $entry_alias; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name_alias" value="<?php echo $name_alias; ?>" placeholder="<?php echo $entry_alias; ?>" id="input-alias" class="form-control" />
            </div>
          </div> 
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-level"><?php echo $entry_level; ?></label>
            <div class="col-sm-10">
              <select name="level" id="input-level" class="form-control">
                <?php /*foreach($levels as $key=>$value){ ?>
                  <option value="<?php echo $key; ?>" <?php echo $key==$level ? 'selected' : ''; ?>><?php echo ${'text_level_'.$value}; ?></option>
                <?php } */?>
                <?php foreach($levels as $key=>$value){ ?>
                  <option value="<?php echo $value['id']; ?>" <?php echo $value['id']==$level ? 'selected' : ''; ?>><?php echo $value['name']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div> 
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-timezone"><?php echo $entry_timezone; ?></label>
            <div class="col-sm-10">
              <select name="timezone" id="input-timezone" class="form-control">
                <?php foreach($timezones as $key=>$value){ ?>
                  <option value="<?php echo $key; ?>" <?php echo $timezone==$key ? 'selected' : ''; ?>><?php echo $value; ?></option>
                <?php } ?>
              </select>
            </div>
          </div> 
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-signature"><?php echo $entry_signature; ?></label>
            <div class="col-sm-10">
              <textarea name="signature" placeholder="<?php echo $entry_signature; ?>" id="input-signature" class="form-control"><?php echo $signature; ?></textarea>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-agents"><?php echo $entry_groups; ?> <span data-toggle="tooltip" title="<?php echo $text_info_agents_groups; ?>"></span></label>
            <div class="col-sm-10">
              <input type="text" name="groups-search" value="" placeholder="<?php echo $entry_groups; ?>" id="input-groups" class="form-control" />
              <div id="agent-groups" class="well well-sm" style="height: 150px; overflow: auto;">
                <?php foreach ($groups as $group) { ?>
                <div id="agent-groups<?php echo $group['groupid']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $group['groupname']; ?>
                  <input type="hidden" name="groups[]" value="<?php echo $group['groupid']; ?>" />
                </div>
                <?php } ?>
              </div>
            </div>
          </div> 

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_roles; ?></label>
            <div class="col-sm-10">
              <div class="well well-sm" style="height: 150px; overflow: auto;">
                <?php foreach($roles as $value){ ?>
                  <div class="checkbox">
                    <label>
                      <?php if(is_array($role) && in_array($value['id'], array_values($role))) { ?>
                      <input type="checkbox" name="role[<?php echo $value['id']; ?>]" value="<?php echo $value['id']; ?>" checked="checked" />
                      <?php }else{ ?>
                       <input type="checkbox" name="role[<?php echo $value['id']; ?>]" value="<?php echo $value['id']; ?>" />
                      <?php } ?>
                      <?php echo $value['name']; ?>
                    </label>
                  </div>
                <?php } ?>
              </div>
              <?php if ($error_role) { ?>
                <div class="text-danger"><?php echo $error_role; ?></div>
              <?php } ?>
            </div>
          </div> 
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-scope"><?php echo $entry_scope; ?></label>
            <div class="col-sm-10">
              <div class="well well-sm panel-body-div" style="height: 150px; overflow: auto;">
                <?php foreach($scopes as $key => $value){ ?>
                  <div class="radio">
                    <label class="control-label" style="padding-top:0px;">
                      <input type="radio" name="scope" value="<?php echo $key; ?>" <?php echo $scope==$key ? 'checked' : false; ?>/>
                      <?php echo ${'entry_agent_'.$value}; ?>
                      <span data-toggle="tooltip" title="<?php echo ${'text_agent_'.$value.'_info'}; ?>"></span>
                    </label>
                  </div>
                <?php } ?>
              </div>
              <?php if ($error_scope) { ?>
                <div class="text-danger"><?php echo $error_scope; ?></div>
              <?php } ?>
            </div>
          </div> 

        </form>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
$('.panel-body-div a').on('click', function(){
  if($(this).hasClass('select'))
    $(this).parent().find('input[type="checkbox"]').prop('checked',true);
  else
    $(this).parent().find('input[type="checkbox"]').prop('checked',false);
});

$('#input-user').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=ticketsystem/agents/getUsers&token=<?php echo $token; ?>&user=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            value: item['id'],
            label: item['username'],
            email: item['email'],
            image: item['image'],
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('div.agent-profile').prepend('<div class="text-center loader"><i class="fa fa-spinner fa-spin"></i></div>');
    $('input[name=\'user_id\']').val(item['value']);
    $('#input-user').val(item['label']);
    setTimeout(function(){
      $('.agent-profile .loader').remove();
      $('.agent-profile span.name').text(item['label']);
      $('.agent-profile span.email').text(item['email']);
      $('.agent-profile span.image img').attr('src',item['image']);
    },3000)
  }
});

$(document).ready(function(){
  // $('#input-user').trigger('click');
});

// groups
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
    
    $('#agent-groups' + item['value']).remove();
    
    $('#agent-groups').append('<div id="agent-groups' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="groups[]" value="' + item['value'] + '" /></div>');  
  } 
});

$('#agent-groups').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});
//--></script></div>
<?php echo $footer; ?> 