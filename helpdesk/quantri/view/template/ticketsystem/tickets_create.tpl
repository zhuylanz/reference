<?php echo $header; ?><?php echo $column_left; ?>

<div id="content" class="ticketsystem">
<style type="text/css">
  #ts_mail_list, #ts_manual_entry{
    display: none;
  }
</style>
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-ticket-create" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ticket-create" class="form-horizontal">

        <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_login_select; ?></label>
            <div class="col-sm-10">
             <select class="form-control" name="login_option">
                <option value=""><?php echo $select_login_option; ?></option>
                <option value="list" <?php if(isset($login_option) && $login_option == 'list'){ echo 'selected'; } ?>  > <?php echo $text_auto_select; ?></option>
                <option value="manual" <?php if(isset($login_option) && $login_option == 'manual'){ echo 'selected'; } ?> > <?php echo $text_manual_select; ?></option>
              </select>
             <?php if($error_login_option){ ?>
                <span class="text-danger"><?php echo $error_login_option;?></span>
              <?php } ?>
            </div>
        </div>

        <div class="form-group" id="ts_mail_list">
        <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_select_mailid; ?></label>
            <div class="col-sm-10">
             <select class="form-control" name="ts_customer_mailid">
                <option value=""><?php echo $select_mailid; ?></option>
                <?php foreach ($total_customers as $key => $mail) { ?>
                    <option value="<?php echo $mail['email']; ?>" ><?php echo $mail['email']; ?></option>
                <?php } ?>
              </select>
               <?php if($error_ts_customer_mailid){ ?>
                <span class="text-danger"><?php echo $error_ts_customer_mailid;?></span>
              <?php } ?>            
            </div>
        </div>

        <div id="ts_manual_entry" class="form-group required">
          <div class="col-sm-12" style="margin-bottom:10px">
          <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_entry_name; ?></label>
              <div class="col-sm-10">
              <input type="text" name="ts_customer_manual_name" class="form-control" value="<?php if(isset($ts_customer_manual_name) && $ts_customer_manual_name){ echo $ts_customer_manual_name; }else{ echo ''; } ?>"  />
                <?php if($error_ts_customer_manual_name){ ?>
                  <span class="text-danger"><?php echo $error_ts_customer_manual_name;?></span>
                <?php } ?>
              </div>
          </div>
          <div class="col-sm-12" style="margin-bottom:10px">
          <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_entry_mailid; ?></label>
              <div class="col-sm-10">
              <input type="text" name="ts_customer_manual" class="form-control" value="<?php if(isset($ts_customer_manual) && $ts_customer_manual){ echo $ts_customer_manual; }else{ echo ''; } ?>"  />
                <?php if($error_ts_customer_manual){ ?>
                  <span class="text-danger"><?php echo $error_ts_customer_manual;?></span>
                <?php } ?>
                
              </div>
          </div>
        </div>
        
          <?php foreach($ts_fields as $field){ ?>
            <?php switch ($field) { 
                  case 'subject': ?>
                <div class="form-group<?php echo (is_array($ts_required_fields) AND in_array($field, $ts_required_fields)) ? ' required' : false; ?>">
                  <label class="col-sm-2 control-label" for="input-<?php echo $field; ?>"><?php echo ${'text_'.$field}; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="<?php echo $field; ?>" class="form-control" id="input-<?php echo $field; ?>" value="<?php echo $$field; ?>"/>
                    <?php if(${'error_'.$field}){ ?>
                      <span class="text-danger"><?php echo ${'error_'.$field} ;?></span>
                    <?php } ?>
                  </div>
                </div>
            <?php   break; ?>
            <?php case 'message': ?>
                <div class="form-group<?php echo (is_array($ts_required_fields) AND in_array($field, $ts_required_fields)) ? ' required' : false; ?>">
                  <label class="col-sm-2 control-label" for="input-<?php echo $field; ?>"><?php echo ${'text_'.$field}; ?></label>
                  <div class="col-sm-10">
                    <textarea name="<?php echo $field; ?>" class="form-control summernote" id="input-<?php echo $field; ?>"><?php echo $$field; ?></textarea> 
                    <?php if(${'error_'.$field}){ ?>
                      <span class="text-danger"><?php echo ${'error_'.$field} ;?></span>
                    <?php } ?>
                  </div>
                </div>
            <?php   break; ?>
            <?php case 'group': ?>
                <div class="form-group<?php echo (is_array($ts_required_fields) AND in_array($field, $ts_required_fields)) ? ' required' : false; ?>">
                  <label class="col-sm-2 control-label" for="input-<?php echo $field; ?>"><?php echo ${'text_'.$field}; ?></label>
                  <div class="col-sm-10">
                    <select name="<?php echo $field; ?>" class="form-control" id="input-<?php echo $field; ?>"> 
                      <option></option>
                      <?php foreach($groups as $result){ ?>
                        <option value="<?php echo $result['id'];?>" <?php echo $$field==$result['id'] ? 'selected' : false; ?>><?php echo $result['name']; ?></option>
                      <?php } ?>
                    </select>
                    <?php if(${'error_'.$field}){ ?>
                      <span class="text-danger"><?php echo ${'error_'.$field} ;?></span>
                    <?php } ?>
                  </div>
                </div>
            <?php   break; ?>
            <?php case 'agent': ?>
                <div class="form-group<?php echo (is_array($ts_required_fields) AND in_array($field, $ts_required_fields)) ? ' required' : false; ?>">
                  <label class="col-sm-2 control-label" for="input-<?php echo $field; ?>"><?php echo ${'text_'.$field}; ?></label>
                  <div class="col-sm-10">
                    <select name="<?php echo $field; ?>" class="form-control" id="input-<?php echo $field; ?>"> 
                      <option></option>
                      <?php foreach($agents as $result){ ?>
                        <option value="<?php echo $result['id'];?>" <?php echo $$field==$result['id'] ? 'selected' : false; ?>><?php echo $result['name']; ?></option>
                      <?php } ?>
                    </select>
                    <?php if(${'error_'.$field}){ ?>
                      <span class="text-danger"><?php echo ${'error_'.$field} ;?></span>
                    <?php } ?>
                  </div>
                </div>
            <?php   break; ?>
            <?php case 'status': ?>
                <div class="form-group<?php echo (is_array($ts_required_fields) AND in_array($field, $ts_required_fields)) ? ' required' : false; ?>">
                  <label class="col-sm-2 control-label" for="input-<?php echo $field; ?>"><?php echo ${'text_'.$field}; ?></label>
                  <div class="col-sm-10">
                    <select name="<?php echo $field; ?>" class="form-control" id="input-<?php echo $field; ?>"> 
                      <option></option>
                      <?php foreach($statuss as $result){ ?>
                        <option value="<?php echo $result['id'];?>" <?php echo $$field==$result['id'] ? 'selected' : false; ?>><?php echo $result['name']; ?></option>
                      <?php } ?>
                    </select>
                    <?php if(${'error_'.$field}){ ?>
                      <span class="text-danger"><?php echo ${'error_'.$field} ;?></span>
                    <?php } ?>
                  </div>
                </div>
            <?php   break; ?>
            <?php case 'priority': ?>
                <div class="form-group<?php echo (is_array($ts_required_fields) AND in_array($field, $ts_required_fields)) ? ' required' : false; ?>">
                  <label class="col-sm-2 control-label" for="input-<?php echo $field; ?>"><?php echo ${'text_'.$field}; ?></label>
                  <div class="col-sm-10">
                    <select name="<?php echo $field; ?>" class="form-control" id="input-<?php echo $field; ?>"> 
                      <option></option>
                      <?php foreach($priorities as $result){ ?>
                        <option value="<?php echo $result['id'];?>" <?php echo $$field==$result['id'] ? 'selected' : false; ?>><?php echo $result['name']; ?></option>
                      <?php } ?>
                    </select>
                    <?php if(${'error_'.$field}){ ?>
                      <span class="text-danger"><?php echo ${'error_'.$field} ;?></span>
                    <?php } ?>
                  </div>
                </div>
            <?php   break; ?>
            <?php case 'tickettype': ?>
                <div class="form-group<?php echo (is_array($ts_required_fields) AND in_array($field, $ts_required_fields)) ? ' required' : false; ?>">
                  <label class="col-sm-2 control-label" for="input-<?php echo $field; ?>"><?php echo ${'text_'.$field}; ?></label>
                  <div class="col-sm-10">
                    <select name="<?php echo $field; ?>" class="form-control" id="input-<?php echo $field; ?>"> 
                      <option></option>
                      <?php foreach($types as $result){ ?>
                        <option value="<?php echo $result['id'];?>" <?php echo $$field==$result['id'] ? 'selected' : false; ?>><?php echo $result['name']; ?></option>
                      <?php } ?>
                    </select>
                    <?php if(${'error_'.$field}){ ?>
                      <span class="text-danger"><?php echo ${'error_'.$field} ;?></span>
                    <?php } ?>
                  </div>
                </div>
            <?php   break; ?>
            <?php case 'fileupload': ?>
                <div class="form-group<?php echo (is_array($ts_required_fields) AND in_array($field, $ts_required_fields)) ? ' required' : false; ?>">
                  <label class="col-sm-2 control-label" for="input-<?php echo $field; ?>"><?php echo ${'text_'.$field}; ?></label>
                  <div class="col-sm-10">
                    <label type="button" for="input-<?php echo $field; ?>" class="btn btn-primary"><i class="fa fa-upload"></i> <?php echo $button_upload; ?></label>
                    <input type="file" name="<?php echo $field; ?>" id="input-<?php echo $field; ?>" class="hide"/>
                    <?php if(${'error_'.$field}){ ?>
                      <br/>
                      <span class="text-danger"><?php echo ${'error_'.$field} ;?></span>
                    <?php } ?>
                  </div>
                </div>
            <?php   break; ?>
            <?php  default:
                break; ?>
            <?php } ?>
          <?php } ?>

          
          <?php foreach ($custom_fields as $custom_field) { ?>
          <?php if ($custom_field['location'] == 'tickets') { ?>
          <?php if ($custom_field['type'] == 'select') { ?>
          <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
            <label class="col-sm-2 control-label" for="input-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
            <div class="col-sm-10">
              <select name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" id="input-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control">
                <option value=""><?php echo $text_select; ?></option>
                <?php foreach ($custom_field['custom_field_value'] as $custom_field_value) { ?>
                <?php if (isset($tickets[$custom_field['custom_field_id']]) && $custom_field_value['custom_field_value_id'] == $tickets[$custom_field['custom_field_id']]) { ?>
                <option value="<?php echo $custom_field_value['custom_field_value_id']; ?>" selected="selected"><?php echo $custom_field_value['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $custom_field_value['custom_field_value_id']; ?>"><?php echo $custom_field_value['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
              <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
              <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
          <?php if ($custom_field['type'] == 'radio') { ?>
          <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
            <label class="col-sm-2 control-label"><?php echo $custom_field['name']; ?></label>
            <div class="col-sm-10">
              <div>
                <?php foreach ($custom_field['custom_field_value'] as $custom_field_value) { ?>
                <div class="radio">
                  <?php if (isset($tickets[$custom_field['custom_field_id']]) && $custom_field_value['custom_field_value_id'] == $tickets[$custom_field['custom_field_id']]) { ?>
                  <label>
                    <input type="radio" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo $custom_field_value['custom_field_value_id']; ?>" checked="checked" />
                    <?php echo $custom_field_value['name']; ?></label>
                  <?php } else { ?>
                  <label>
                    <input type="radio" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo $custom_field_value['custom_field_value_id']; ?>" />
                    <?php echo $custom_field_value['name']; ?></label>
                  <?php } ?>
                </div>
                <?php } ?>
              </div>
              <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
              <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
          <?php if ($custom_field['type'] == 'checkbox') { ?>
          <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
            <label class="col-sm-2 control-label"><?php echo $custom_field['name']; ?></label>
            <div class="col-sm-10">
              <div>
                <?php foreach ($custom_field['custom_field_value'] as $custom_field_value) { ?>
                <div class="checkbox">
                  <?php if (isset($tickets[$custom_field['custom_field_id']]) && in_array($custom_field_value['custom_field_value_id'], $tickets[$custom_field['custom_field_id']])) { ?>
                  <label>
                    <input type="checkbox" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>][]" value="<?php echo $custom_field_value['custom_field_value_id']; ?>" checked="checked" />
                    <?php echo $custom_field_value['name']; ?></label>
                  <?php } else { ?>
                  <label>
                    <input type="checkbox" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>][]" value="<?php echo $custom_field_value['custom_field_value_id']; ?>" />
                    <?php echo $custom_field_value['name']; ?></label>
                  <?php } ?>
                </div>
                <?php } ?>
              </div>
              <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
              <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
          <?php if ($custom_field['type'] == 'text') { ?>
          <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
            <label class="col-sm-2 control-label" for="input-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
            <div class="col-sm-10">
              <input type="text" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo (isset($tickets[$custom_field['custom_field_id']]) ? $tickets[$custom_field['custom_field_id']] : $custom_field['value']); ?>" placeholder="<?php echo $custom_field['name']; ?>" id="input-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control" />
              <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
              <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
          <?php if ($custom_field['type'] == 'textarea') { ?>
          <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
            <label class="col-sm-2 control-label" for="input-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
            <div class="col-sm-10">
              <textarea name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" rows="5" placeholder="<?php echo $custom_field['name']; ?>" id="input-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control"><?php echo (isset($tickets[$custom_field['custom_field_id']]) ? $tickets[$custom_field['custom_field_id']] : $custom_field['value']); ?></textarea>
              <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
              <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
          <?php if ($custom_field['type'] == 'file') { ?>
          <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
            <label class="col-sm-2 control-label"><?php echo $custom_field['name']; ?></label>
            <div class="col-sm-10">
              <label type="button" for="button-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="btn btn-default"><i class="fa fa-upload"></i> <?php echo $button_upload; ?></label>
              <input type="file" name="custom_field<?php echo $custom_field['custom_field_id']; ?>" id="button-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="hide"/>
              <?php if (isset(${'error_custom_field'.$custom_field['custom_field_id']})) { ?>
              <div class="text-danger"><?php echo ${'error_custom_field'.$custom_field['custom_field_id']}; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
          <?php if ($custom_field['type'] == 'date') { ?>
          <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
            <label class="col-sm-2 control-label" for="input-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
            <div class="col-sm-10">
              <div class="input-group date">
                <input type="text" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo (isset($tickets[$custom_field['custom_field_id']]) ? $tickets[$custom_field['custom_field_id']] : $custom_field['value']); ?>" placeholder="<?php echo $custom_field['name']; ?>" data-date-format="YYYY-MM-DD" id="input-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control" />
                <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </span></div>
              <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
              <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
          <?php if ($custom_field['type'] == 'time') { ?>
          <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
            <label class="col-sm-2 control-label" for="input-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
            <div class="col-sm-10">
              <div class="input-group time">
                <input type="text" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo (isset($tickets[$custom_field['custom_field_id']]) ? $tickets[$custom_field['custom_field_id']] : $custom_field['value']); ?>" placeholder="<?php echo $custom_field['name']; ?>" data-date-format="HH:mm" id="input-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control" />
                <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </span></div>
              <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
              <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
          <?php if ($custom_field['type'] == 'datetime') { ?>
          <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
            <label class="col-sm-2 control-label" for="input-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
            <div class="col-sm-10">
              <div class="input-group datetime">
                <input type="text" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo (isset($tickets[$custom_field['custom_field_id']]) ? $tickets[$custom_field['custom_field_id']] : $custom_field['value']); ?>" placeholder="<?php echo $custom_field['name']; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control" />
                <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </span></div>
              <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
              <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
          <?php } ?>
          <?php } ?>


        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
<?php if($ts_editor){ ?>
$('.summernote').summernote({height: 300});
<?php } ?>


  $('select[name=\'login_option\']').on("change", function(){
    $('#ts_mail_list').fadeOut();
    $('#ts_manual_entry').fadeOut();
    if(($(this).val()).length > 0)  {
      if($(this).val() == 'list'){
        $('#ts_mail_list').fadeIn('slow');
      }else{
        $('#ts_manual_entry').fadeIn('slow');
      }
    }
  })

  $(document).ready(function(){
  <?php if(isset($login_option) && $login_option){ ?>
    var get_priceOption = $('select[name=\'login_option\']').val();
    if(get_priceOption == 'list'){
      $('#ts_mail_list').fadeIn();
    }else{
      $('#ts_manual_entry').fadeIn();
    }
  <?php } ?>  
})

</script>
<?php echo $footer; ?> 