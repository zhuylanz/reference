<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-ts" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ts" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-activity" data-toggle="tab"><?php echo $tab_activity; ?></a></li>
            <li><a href="#tab-support" data-toggle="tab"><?php echo $tab_support; ?></a></li>
            <li><a href="#tab-ticket" data-toggle="tab"><?php echo $tab_ticket; ?></a></li>
          </ul>

          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="ts_status" id="input-status" class="form-control">
                    <?php if ($ts_status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-date"><?php echo $entry_date_format; ?> <span data-toggle="tooltip" title="<?php echo $entry_info_date_format; ?>"></span></label>
                <div class="col-sm-10">
                  <input type="text" name="ts_date_format" value="<?php echo $ts_date_format;?>" class="form-control" placeholder="like D M Y - you can create more combinations" id="input-date"/> 
                </div>
              </div>
            </div>

            <div class="tab-pane" id="tab-activity">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-activity_limit"><?php echo $text_activity_limit; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="ts_activity_limit" value="<?php echo $ts_activity_limit;?>" class="form-control" placeholder="10" id="input-activity_limit"/> 
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-agents"><?php echo $entry_generate_activities; ?> <span data-toggle="tooltip" title="<?php echo $text_info_generate_activities; ?>"></span></label>
                <div class="col-sm-10">
                  <div id="group-agents" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($controllers as $controller) { ?>
                      <label class="control-label">
                        <input type="checkbox" name="ts_register_activity[]" value="<?php echo $controller;?>" class="parent-checkbox" <?php if(is_array($ts_register_activity) AND in_array($controller, $ts_register_activity)){ ?> checked<?php } ?>/> 
                        <?php echo ${'heading_'.$controller}; ?>
                      </label>
                    </br>
                    <?php } ?>
                  </div>
                  <a onclick="$(this).parent().find('input[type=\'checkbox\']').prop('checked',true)"><?php echo $text_select_all ;?></a>&nbsp;
                  <a onclick="$(this).parent().find('input[type=\'checkbox\']').prop('checked',false)"><?php echo $text_deselect_all ;?></a>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-priority"><?php echo $entry_activity_priority; ?> <span data-toggle="tooltip" title="<?php echo $text_info_activity_priority; ?>"></span></label>
                <div class="col-sm-10">
                  <label class="control-label col-sm-2" for="input-delete"><?php echo $entry_on_delete; ?></label>
                  <div class="col-sm-2">
                    <select name="ts_action_level_delete" id="input-delete" class="form-control">
                      <option></option>
                      <?php foreach($tsPriority as $value){ ?>
                        <option value="<?php echo $value; ?>" <?php echo $ts_action_level_delete == $value ? 'selected' : false ;?> ><?php echo ${'text_'.$value}; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <label class="control-label col-sm-2" for="input-add"><?php echo $entry_on_add; ?></label>
                  <div class="col-sm-2">
                    <select name="ts_action_level_add" id="input-add" class="form-control">
                      <option></option>
                      <?php foreach($tsPriority as $value){ ?>
                        <option value="<?php echo $value; ?>" <?php echo $ts_action_level_add == $value ? 'selected' : false ;?> ><?php echo ${'text_'.$value}; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <label class="control-label col-sm-2" for="input-edit"><?php echo $entry_on_edit; ?></label>
                  <div class="col-sm-2">
                    <select name="ts_action_level_edit" id="input-edit" class="form-control">
                      <option></option>
                      <?php foreach($tsPriority as $value){ ?>
                        <option value="<?php echo $value; ?>" <?php echo $ts_action_level_edit == $value ? 'selected' : false ;?> ><?php echo ${'text_'.$value}; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane" id="tab-support">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-header"><?php echo $entry_show_header; ?> <span data-toggle="tooltip" title="<?php echo $entry_show_header_info ;?>"></span></label>
                <div class="col-sm-10">
                  <select name="ts_header[]" id="input-header" class="form-control" multiple>
                    <option></option>
                    <?php foreach($tsHeader as $header){ ?>
                      <option value="<?php echo $header; ?>" <?php echo (is_array($ts_header) AND in_array($header, $ts_header)) ? "selected" : false; ?>><?php echo ${'text_'.$header}; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-cateory-limit"><?php echo $entry_information_limit; ?> <span data-toggle="tooltip" title="<?php echo $entry_information_limit_info; ?>"></span></label>
                <div class="col-sm-10">
                  <input type="text" name="ts_information_limit" value="<?php echo $ts_information_limit;?>" class="form-control" id="input-cateory-limit"/> 
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-info-order"><?php echo $entry_information_order; ?></label>
                <div class="col-sm-10">
                  <select name="ts_information_order" id="input-info-order" class="form-control">
                    <?php if ($ts_information_order=='desc') { ?>
                    <option value="desc" selected="selected"><?php echo $text_newest; ?></option>
                    <option value="asc"><?php echo $text_oldest; ?></option>
                    <?php } else { ?>
                    <option value="desc"><?php echo $text_newest; ?></option>
                    <option value="asc" selected="selected"><?php echo $text_oldest; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="tab-pane" id="tab-ticket">

              <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-ticket-general" data-toggle="tab"><?php echo $tab_ticket_status; ?></a></li>
                <li><a href="#tab-ticket-default" data-toggle="tab"><?php echo $tab_ticket_default; ?></a></li>
                <li><a href="#tab-ticket-admin-default" data-toggle="tab"><?php echo $tab_ticket_admin_default; ?></a></li>
                <li><a href="#tab-ticket-fields" data-toggle="tab"><?php echo $tab_ticket_fields; ?></a></li>
              </ul>

              <div class="tab-content">
                <div class="tab-pane active" id="tab-ticket-general">
                  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $text_ticket_status_info; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-ticket-open"><?php echo $text_ticket_open; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_ticket_status[open]" id="input-ticket-open" class="form-control">
                        <?php if ($tsStatus) { ?>
                          <?php foreach ($tsStatus as $status) { ?>
                            <?php if($status['status']){ ?>
                              <option value="<?php echo $status['id']; ?>" <?php echo (isset($ts_ticket_status['open']) AND $ts_ticket_status['open']==$status['id']) ? 'selected' : false; ?>><?php echo $status['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-ticket-new"><?php echo $text_ticket_new; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_ticket_status[new]" id="input-ticket-new" class="form-control">
                        <?php if ($tsStatus) { ?>
                          <?php foreach ($tsStatus as $status) { ?>
                            <?php if($status['status']){ ?>
                              <option value="<?php echo $status['id']; ?>" <?php echo (isset($ts_ticket_status['new']) AND $ts_ticket_status['new']==$status['id']) ? 'selected' : false; ?>><?php echo $status['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-ticket-pending"><?php echo $text_ticket_pending; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_ticket_status[pending]" id="input-ticket-pending" class="form-control">
                        <?php if ($tsStatus) { ?>
                          <?php foreach ($tsStatus as $status) { ?>
                            <?php if($status['status']){ ?>
                              <option value="<?php echo $status['id']; ?>" <?php echo (isset($ts_ticket_status['pending']) AND $ts_ticket_status['pending']==$status['id']) ? 'selected' : false; ?>><?php echo $status['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-ticket-solved"><?php echo $text_ticket_solved; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_ticket_status[solved]" id="input-ticket-solved" class="form-control">
                        <?php if ($tsStatus) { ?>
                          <?php foreach ($tsStatus as $status) { ?>
                            <?php if($status['status']){ ?>
                              <option value="<?php echo $status['id']; ?>" <?php echo (isset($ts_ticket_status['solved']) AND $ts_ticket_status['solved']==$status['id']) ? 'selected' : false; ?>><?php echo $status['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-ticket-closed"><?php echo $text_ticket_closed; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_ticket_status[closed]" id="input-ticket-closed" class="form-control">
                        <?php if ($tsStatus) { ?>
                          <?php foreach ($tsStatus as $status) { ?>
                            <?php if($status['status']){ ?>
                              <option value="<?php echo $status['id']; ?>" <?php echo (isset($ts_ticket_status['closed']) AND $ts_ticket_status['closed']==$status['id']) ? 'selected' : false; ?>><?php echo $status['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-ticket-spam"><?php echo $text_ticket_spam; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_ticket_status[spam]" id="input-ticket-spam" class="form-control">
                        <?php if ($tsStatus) { ?>
                          <?php foreach ($tsStatus as $status) { ?>
                            <?php if($status['status']){ ?>
                              <option value="<?php echo $status['id']; ?>" <?php echo (isset($ts_ticket_status['spam']) AND $ts_ticket_status['spam']==$status['id']) ? 'selected' : false; ?>><?php echo $status['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="tab-pane" id="tab-ticket-default">
                  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $text_ticket_default_info; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-default-status"><?php echo $text_default_status; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_ticket_default[status]" id="input-default-status" class="form-control">
                        <?php if ($tsStatus) { ?>
                          <?php foreach ($tsStatus as $status) { ?>
                            <?php if($status['status']){ ?>
                              <option value="<?php echo $status['id']; ?>" <?php echo (isset($ts_ticket_default['status']) AND $ts_ticket_default['status']==$status['id']) ? 'selected' : false; ?>><?php echo $status['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-default-type"><?php echo $text_default_type; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_ticket_default[type]" id="input-default-type" class="form-control">
                        <?php if ($tsTypes) { ?>
                          <?php foreach ($tsTypes as $type) { ?>
                            <?php if($type['status']){ ?>
                              <option value="<?php echo $type['id']; ?>" <?php echo (isset($ts_ticket_default['type']) AND $ts_ticket_default['type']==$type['id']) ? 'selected' : false; ?>><?php echo $type['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-default-dept"><?php echo $text_default_dept; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_ticket_default[group]" id="input-default-dept" class="form-control">
                        <?php if ($tsGroups) { ?>
                          <?php foreach ($tsGroups as $group) { ?>
                            <option value="<?php echo $group['id']; ?>" <?php echo (isset($ts_ticket_default['group']) AND $ts_ticket_default['group']==$group['id']) ? 'selected' : false; ?>><?php echo $group['name']; ?></option>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-default-priority"><?php echo $text_default_priority; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_ticket_default[priority]" id="input-default-priority" class="form-control">
                        <?php if ($tsPriorities) { ?>
                          <?php foreach ($tsPriorities as $priority) { ?>
                            <?php if($priority['status']){ ?>
                              <option value="<?php echo $priority['id']; ?>" <?php echo (isset($ts_ticket_default['priority']) AND $ts_ticket_default['priority']==$priority['id']) ? 'selected' : false; ?>><?php echo $priority['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="tab-pane" id="tab-ticket-fields">
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-login"><?php echo $entry_customer_login; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_login" id="input-login" class="form-control">
                        <?php if ($ts_login) { ?>
                        <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                        <option value="0"><?php echo $text_no; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $text_yes; ?></option>
                        <option value="0" selected="selected"><?php echo $text_no; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-fields"><?php echo $entry_ticket_fields; ?> <span data-toggle="tooltip" title="<?php echo $entry_ticket_fields_info ;?>"></span></label>
                    <div class="col-sm-10">
                      <select name="ts_fields[]" id="input-fields" class="form-control" multiple style="height:150px;">
                        <option></option>
                        <?php foreach($tsFields as $field){ ?>
                          <option value="<?php echo $field; ?>" <?php echo (is_array($ts_fields) AND in_array($field, $ts_fields)) ? "selected" : false; ?>><?php echo ${'text_'.$field}; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-fields"><?php echo $entry_ticket_required_fields; ?> <span data-toggle="tooltip" title="<?php echo $entry_ticket_required_fields_info ;?>"></span></label>
                    <div class="col-sm-10">
                      <select name="ts_required_fields[]" id="input-required-fields" class="form-control" multiple style="height:150px;">
                        <option></option>
                        <?php foreach($tsFields as $field){ ?>
                          <option value="<?php echo $field; ?>" <?php echo (is_array($ts_required_fields) AND in_array($field, $ts_required_fields)) ? "selected" : false; ?>><?php echo ${'text_'.$field}; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-file-no"><?php echo $entry_fileupload_no; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="ts_fileupload_no" value="<?php echo $ts_fileupload_no;?>" class="form-control" placeholder="5" id="input-file-no"/> 
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-file-size"><?php echo $entry_fileupload_size; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="ts_fileupload_size" value="<?php echo $ts_fileupload_size;?>" class="form-control" placeholder="2000" id="input-file-size"/> 
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-file-ext"><?php echo $entry_fileupload_ext; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="ts_fileupload_ext" value="<?php echo $ts_fileupload_ext;?>" class="form-control" placeholder="jpg, pdf or * for all" id="input-file-ext"/> 
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-editor"><?php echo $entry_editor_allow; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_editor" id="input-editor" class="form-control">
                        <?php if ($ts_editor) { ?>
                        <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                        <option value="0"><?php echo $text_no; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $text_yes; ?></option>
                        <option value="0" selected="selected"><?php echo $text_no; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-delete_ticket"><?php echo $entry_customer_delete_ticket; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_customer_delete_ticket" id="input-delete_ticket" class="form-control">
                        <?php if ($ts_customer_delete_ticket) { ?>
                        <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                        <option value="0"><?php echo $text_no; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $text_yes; ?></option>
                        <option value="0" selected="selected"><?php echo $text_no; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-login"><?php echo $entry_customer_delete_ticketthreads; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_customer_delete_ticketthread" id="input-delete_ticketthread" class="form-control">
                        <?php if ($ts_customer_delete_ticketthread) { ?>
                        <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                        <option value="0"><?php echo $text_no; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $text_yes; ?></option>
                        <option value="0" selected="selected"><?php echo $text_no; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-update_status"><?php echo $entry_customer_update_status; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_customer_update_status" id="input-update_status" class="form-control">
                        <?php if ($ts_customer_update_status) { ?>
                        <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                        <option value="0"><?php echo $text_no; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $text_yes; ?></option>
                        <option value="0" selected="selected"><?php echo $text_no; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-add_cc"><?php echo $entry_customer_add_cc; ?></label>
                    <div class="col-sm-10">
                      <select name="ts_customer_add_cc" id="input-add_cc" class="form-control">
                        <?php if ($ts_customer_add_cc) { ?>
                        <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                        <option value="0"><?php echo $text_no; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $text_yes; ?></option>
                        <option value="0" selected="selected"><?php echo $text_no; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="tab-pane" id="tab-ticket-admin-default">
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-ticket_view_limit"><?php echo $text_ticket_view_limit; ?> <span data-toggle="tooltip" title="<?php echo $text_ticket_view_limit_info; ?>"></span></label>
                    <div class="col-sm-10">
                      <input type="text" name="ts_ticket_view_limit" value="<?php echo $ts_ticket_view_limit;?>" class="form-control" placeholder="3" id="input-ticket_view_limit"/> 
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-ticket_save_draft_time"><?php echo $text_ticket_save_draft_time; ?> <span data-toggle="tooltip" title="<?php echo $text_ticket_save_draft_time_info; ?>"></span></label>
                    <div class="col-sm-10">
                      <input type="text" name="ts_save_draft_time" value="<?php echo $ts_save_draft_time;?>" class="form-control" placeholder="20000" id="input-ticket_save_draft_time"/> 
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-ticket_view_time"><?php echo $text_ticket_view_time; ?> <span data-toggle="tooltip" title="<?php echo $text_ticket_save_draft_time_info; ?>"></span></label>
                    <div class="col-sm-10">
                      <input type="text" name="ts_ticket_view_time" value="<?php echo $ts_ticket_view_time;?>" class="form-control" placeholder="50000" id="input-ticket_view_time"/> 
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-ticket_view_expire_time_info"><?php echo $text_ticket_view_expire_time; ?> <span data-toggle="tooltip" title="<?php echo $text_ticket_view_expire_time_info; ?>"></span></label>
                    <div class="col-sm-10">
                      <input type="text" name="ts_ticket_view_expire_time" value="<?php echo $ts_ticket_view_expire_time;?>" class="form-control" placeholder="10:00:00" id="input-ticket_save_draft_time"/> 
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-apply-action-note"><?php echo $text_add_internal_note_for_actions; ?> <span data-toggle="tooltip" title="<?php echo $text_add_internal_note_for_actions_info; ?>"></span></label>
                    <div class="col-sm-10">
                      <select name="ts_add_internal_after_applying_actions" id="input-apply-action-note" class="form-control">
                        <?php if ($ts_add_internal_after_applying_actions) { ?>
                        <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                        <option value="0"><?php echo $text_no; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $text_yes; ?></option>
                        <option value="0" selected="selected"><?php echo $text_no; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>


                </div>



              </div>
            </div>


          </div>

        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>