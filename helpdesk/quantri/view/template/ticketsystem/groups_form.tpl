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
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-roles"><?php echo $text_group_details; ?></label>
            <div class="col-sm-10">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <i class="fa fa-users"></i> <?php echo $text_info_groups; ?>
                </div>
                <div class="panel-body">
                  <ul class="nav nav-tabs" id="language">
                    <?php foreach ($languages as $language) { ?>
                    <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
                    <?php } ?>
                  </ul>

                  <div class="tab-content">
                    <?php foreach ($languages as $language) { ?>
                      <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                        <div class="form-group required">
                          <label class="col-sm-2 control-label" for="input-name<?php echo $language['language_id']; ?>"><?php echo $entry_name; ?></label>
                          <div class="col-sm-10">
                            <input type="text" name="group[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($group[$language['language_id']]) ? $group[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name<?php echo $language['language_id']; ?>" class="form-control" />
                            <?php if (isset($error_name[$language['language_id']])) { ?>
                              <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
                            <?php } ?>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>"><?php echo $entry_description; ?></label>
                          <div class="col-sm-10">
                            <textarea name="group[<?php echo $language['language_id']; ?>][description]" placeholder="<?php echo $entry_description; ?>" id="input-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($group[$language['language_id']]) ? $group[$language['language_id']]['description'] : ''; ?></textarea>
                          </div>
                        </div>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-agents"><?php echo $entry_agents; ?> <span data-toggle="tooltip" title="<?php echo $text_info_groups_agents; ?>"></span></label>
            <div class="col-sm-10">
              <input type="text" name="agents-search" value="" placeholder="<?php echo $entry_agents; ?>" id="input-agents" class="form-control" />
              <div id="group-agents" class="well well-sm" style="height: 150px; overflow: auto;">
                <?php foreach ($agents as $agent) { ?>
                <div id="group-agents<?php echo $agent['id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $agent['name']; ?>
                  <input type="hidden" name="agents[]" value="<?php echo $agent['id']; ?>" />
                </div>
                <?php } ?>
              </div>
            </div>
          </div> 

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-business"><?php echo $entry_business_hours; ?></label>
            <div class="col-sm-10">
              <select name="businesshour_id" class="form-control" id="input-business">
                <?php foreach($businesshours as $businesshour){ ?>
                  <option value="<?php echo $businesshour['id']; ?>" <?php echo $businesshour['id']==$businesshour_id ? 'selected' : ''; ?>><?php echo $businesshour['name']; ?></option>
                <?php } ?>
              </select>
              <?php if ($error_businesshours) { ?>
                <div class="text-danger"><?php echo $error_businesshours; ?></div>
              <?php } ?>
            </div>
          </div> 

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-auto-assign"><?php echo $text_assign_ticket_auto; ?></label>
            <div class="col-sm-10">
              <label class="radio-inline">
                <input type="radio" name="automatic_assign" value="1" <?php echo $automatic_assign ? 'checked' : false; ?>> <?php echo $text_yes; ?>
              </label>
              <label class="radio-inline">
                <input type="radio" name="automatic_assign" value="0" <?php echo !$automatic_assign ? 'checked' : false; ?>> <?php echo $text_no; ?>
              </label>
            </div>
          </div> 

          <div class="form-group hide">
            <label class="col-sm-2 control-label" for="input-inform_time"><?php echo $text_group_policy; ?> <span data-toggle="tooltip" title="<?php echo $text_group_policy_info; ?>"></span></label>
            <div class="col-sm-3">
              <select name="inform_time" class="form-control">
                <?php foreach($informTimes as $key => $informTime){ ?>
                  <option value="<?php echo $key; ?>" <?php echo $key==$inform_time ? 'selected' : false; ?>><?php echo $informTime; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col-sm-3">
              <select name="inform_agent" class="form-control">
                <?php foreach($agentsList as $key => $agentList){ ?>
                  <option value="<?php echo $agentList['id']; ?>" <?php echo $agentList['id']==$inform_agent ? 'selected' : false; ?>><?php echo $agentList['username']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
$('#language a:first').tab('show');

// agents
$('input[name=\'agents-search\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=ticketsystem/agents/autocomplete&token=<?php echo $token; ?>&filter_username=' +  encodeURIComponent(request),
      dataType: 'json',     
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'] + ' - ' + item['email'],
            value: item['id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'agents-search\']').val('');
    
    $('#group-agents' + item['value']).remove();
    
    $('#group-agents').append('<div id="group-agents' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="agents[]" value="' + item['value'] + '" /></div>');  
  } 
});

$('#group-agents').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});

//--></script></div>
<?php echo $footer; ?> 