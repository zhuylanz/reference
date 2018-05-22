<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($error_warning) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
  <?php } ?>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <?php echo $ts_column_top ;?>

      <div class="ts-jumbotron" style="padding:0px 10px 0px 10px;">
        <h1 style="font-weight:bold; padding-top:10px"><?php echo $heading_title; ?></h1>
        <p><?php echo $text_info; ?></p>

        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">  
          <?php if(!$isLogin){ ?>
            <div class="alert alert-info"><i class="fa fa-check-circle"></i> <?php echo $text_login_info; ?></div>
            <div class="form-group required">
              <label class="col-sm-2 control-label" for="input-name"><?php echo $text_name; ?></label>
              <div class="col-sm-10">
                <input type="text" name="name" class="form-control" id="input-name" value="<?php echo $name; ?>"/>
                <?php if($error_name){ ?>
                  <span class="text-danger"><?php echo $error_name ;?></span>
                <?php } ?>
              </div>
            </div>
            <div class="form-group required">
              <label class="col-sm-2 control-label" for="input-email"><?php echo $text_email; ?></label>
              <div class="col-sm-10">
                <input type="email" name="email" class="form-control" id="input-email" value="<?php echo $email; ?>"/>
                <?php if($error_email){ ?>
                  <span class="text-danger"><?php echo $error_email ;?></span>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
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


          <div class="buttons">
            <div class="pull-right">
              <input type="submit" value="Continue" class="btn btn-primary">
            </div>
          </div>






        </form>
      </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<script type="text/javascript">
<?php if($ts_editor){ ?>
$('.summernote').summernote({height: 300});
<?php } ?>
$('#input-tickettype').on('change', function(){
  thisthis = this;
  $('.custom-field').each(function(){
    if(!$(this).attr('data-type') || $(this).attr('data-type')==0 || $(this).attr('data-type')==thisthis.value)
      $(this).removeClass('hide');
    else
      $(this).addClass('hide');
  })
})
$('#input-tickettype').trigger('change');
</script>
<script type="text/javascript"><!--
$('.date').datetimepicker({
  pickTime: false
});

$('.time').datetimepicker({
  pickDate: false
});

$('.datetime').datetimepicker({
  pickDate: true,
  pickTime: true
});
//--></script>
<?php echo $footer; ?>