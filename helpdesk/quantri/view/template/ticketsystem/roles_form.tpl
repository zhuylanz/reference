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
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>            
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-description"><?php echo $entry_description; ?></label>
            <div class="col-sm-10">
              <textarea name="description" placeholder="<?php echo $entry_description; ?>" id="input-description" class="form-control"><?php echo $description; ?></textarea>
              <?php if ($error_description) { ?>
              <div class="text-danger"><?php echo $error_description; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-roles"><?php echo $entry_roles; ?></label>
            <div class="col-sm-10">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <i class="fa fa-gavel"></i> <?php echo $text_info_roles; ?>
                </div>
                <div class="panel-body">
                  <?php $role ? $role = unserialize($role) : $role = array(); ?>
                  <?php $roles_key = array_keys($role); ?>
                  <?php foreach($roles as $key => $roleResult){ ?>
                    <?php if(is_array($roleResult)) { ?>
                      <div class="panel-body-div">
                        <h4>
                          <label class="control-label" for="<?php echo $key;?>">
                            <input type="checkbox" name="roles[<?php echo $key;?>][]" value="<?php echo $key;?>" id="<?php echo $key;?>" style="margin: 2px 5px 0px -2px;" class="parent-checkbox <?php if(in_array($key, $roles_key)){ ?>active" checked<?php }else echo '"' ; ?> <?php if($key=='default'){ ?>checked disabled<?php } ?>/> 
                            <b><?php echo ${'text_roles_'.$key}; ?></b> 
                            <span data-toggle="tooltip" title="<?php echo ${'text_roles_'.$key.'_info'}; ?>"></span>
                          </label>
                        </h4>
                        <?php foreach($roleResult as $value){ ?>
                          <?php if($key=='default'){ ?>
                            <input type="checkbox" name="roles[<?php echo $key;?>][]" value="<?php echo $key.'.'.$value ;?>" id="<?php echo $key.'.'.$value ;?>" checked disabled/>
                          <?php }else{ ?>
                            <input type="checkbox" name="roles[<?php echo $key;?>][]" value="<?php echo $key.'.'.$value ;?>" id="<?php echo $key.'.'.$value ;?>" <?php echo (isset($role[$key]) AND in_array($key.'.'.$value, $role[$key])) ? 'checked' : false; ?>  class="child-checkbox" disabled/>
                          <?php } ?>
                          <label for="<?php echo $key.'.'.$value ;?>"><?php echo ${'text_roles_'.$key.'_'.$value}; ?></label>
                          <br/>
                        <?php } ?>
                        <?php if($key!='default'){ ?>
                          <a class="select"><?php echo $text_select_all;?></a>&nbsp;&nbsp;<a class="deselect"><?php echo $text_deselect_all;?></a>
                        <?php } ?>
                      </div>
                    <?php } ?>
                  <?php } ?>
                  <?php if ($error_roles) { ?>
                    <div class="text-danger"><?php echo $error_roles; ?></div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
$(document).ready(function(){
  $('.parent-checkbox.active').each(function(){
    $(this).parents('.panel-body-div').find('input[type="checkbox"]').attr('disabled',false);
  });
})
$('.parent-checkbox').on('change', function(){
  if($(this).is(':checked')){
    $(this).parents('.panel-body-div').find('input[type="checkbox"]').attr('disabled',false);
  }else{
    $(this).parents('.panel-body-div').find('input[type="checkbox"]').not(this).attr('disabled',true);
  }
});

$('.panel-body-div a').on('click', function(){
  if($(this).hasClass('select')){
    $(this).parent().find('input[type="checkbox"]').prop('checked',true);
    $(this).parents('.panel-body-div').find('input[type="checkbox"]').attr('disabled',false);
  }else{
    $(this).parent().find('input[type="checkbox"]').prop('checked',false);
    $(this).parents('.panel-body-div').find('input[type="checkbox"]').not('.parent-checkbox').attr('disabled',true);
  }
})
//--></script></div>
<?php echo $footer; ?> 