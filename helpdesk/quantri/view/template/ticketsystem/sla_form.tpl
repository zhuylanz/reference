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
        - <?php echo $text_info_sla; ?>
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
            <label class="col-sm-2 control-label" for="input-sort"><?php echo $text_sla_priority; ?><span data-toggle="tooltip" title="<?php echo $text_info_sla_priority; ?>"></span></label>
            <div class="col-sm-10">
              <?php if ($error_priority_blank) { ?>
                <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_priority_blank; ?>
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
              <?php } ?>
                <div class="table-responsive">
                <table id="priority" class="table table-striped table-bordered table-hover" style="display: table;">
                    <thead>
                      <tr>
                        <td class="text-center"><?php echo $text_priority; ?></td>
                        <td class="text-center">
                          <?php echo $text_sla_respond_within; ?>
                          <span data-toggle="tooltip" title="<?php echo $text_sla_respond_withini_info; ?>"></span>
                        </td>
                        <td class="text-center">
                          <?php echo $text_sla_resolve_within; ?>
                          <span data-toggle="tooltip" title="<?php echo $text_sla_resolve_withini_info; ?>"></span>
                        </td>
                        <td class="text-center">
                          <?php echo $text_sla_working_hours; ?>
                          <span data-toggle="tooltip" title="<?php echo $text_sla_working_hours_info; ?>"></span>
                        </td>
                        <td class="text-center"><?php echo $entry_status; ?></td>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if($tsPriority){ ?>
                        <?php foreach ($tsPriority as $key => $tspri) { ?>
                          <tr>  
                            <td class="text-center"><?php echo $tspri['name']; ?></td>
                            <td class="text-center">
                              <div class="input-group ticketsystem-input-group">      
                                <input name="priority[<?php echo $tspri['id']; ?>][respond][time]" type="number" class="form-control number" min="0" value="<?php echo isset($priority[$tspri['id']]['respond']['time']) ? $priority[$tspri['id']]['respond']['time'] : ''; ?>"/>    
                                <div class="input-group-btn">
                                  <input name="priority[<?php echo $tspri['id']; ?>][respond][type]" type="hidden" value="<?php echo isset($priority[$tspri['id']]['respond']['type']) ? $hrType = $priority[$tspri['id']]['respond']['type'] : $hrType = 'minute'; ?>"/>    
                                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $text_min;?> <span class="caret"></span></button>
                                  <ul class="dropdown-menu">
                                    <li><a value="minute" class="time-menu <?php echo $hrType=='minute' ? 'active' : ($hrType=='webkul' ? 'active' : ''); ?>"><?php echo $text_min;?></a></li>
                                    <li><a value="hours" class="time-menu <?php echo $hrType=='hours' ? 'active' : ''; ?>"><?php echo $text_hours;?></a></li>
                                    <li><a value="days" class="time-menu <?php echo $hrType=='days' ? 'active' : ''; ?>"><?php echo $text_days;?></a></li>
                                    <li><a value="months" class="time-menu <?php echo $hrType=='months' ? 'active' : ''; ?>"><?php echo $text_month;?></a></li>
                                  </ul>
                                </div>
                              </div> 
                              <?php if(isset($error_priority[$tspri['id']]['error_respond_time'])){ ?>
                                <br/>
                                <span class="text-danger"><?php echo $error_priority[$tspri['id']]['error_respond_time'] ;?></span>
                              <?php } ?>
                            </td>
                            <td class="text-center">
                              <div class="input-group ticketsystem-input-group">      
                                <input name="priority[<?php echo $tspri['id']; ?>][resolve][time]" type="number" class="form-control number" min="0" value="<?php echo isset($priority[$tspri['id']]['resolve']['time']) ? $priority[$tspri['id']]['resolve']['time'] : ''; ?>"/>    
                                <div class="input-group-btn">
                                  <input name="priority[<?php echo $tspri['id']; ?>][resolve][type]" type="hidden" value="<?php echo isset($priority[$tspri['id']]['resolve']['type']) ? $hrType = $priority[$tspri['id']]['resolve']['type'] : $hrType = 'minute'; ?>"/>    
                                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $text_min;?> <span class="caret"></span></button>
                                  <ul class="dropdown-menu">
                                    <li><a value="minute" class="time-menu <?php echo $hrType=='minute' ? 'active' : ($hrType=='webkul' ? 'active' : ''); ?>"><?php echo $text_min;?></a></li>
                                    <li><a value="hours" class="time-menu <?php echo $hrType=='hours' ? 'active' : ''; ?>"><?php echo $text_hours;?></a></li>
                                    <li><a value="days" class="time-menu <?php echo $hrType=='days' ? 'active' : ''; ?>"><?php echo $text_days;?></a></li>
                                    <li><a value="months" class="time-menu <?php echo $hrType=='months' ? 'active' : ''; ?>"><?php echo $text_month;?></a></li>
                                  </ul>
                                </div>
                              </div> 
                              <?php if(isset($error_priority[$tspri['id']]['error_resolve_time'])){ ?>
                                <br/>
                                <span class="text-danger"><?php echo $error_priority[$tspri['id']]['error_resolve_time'] ;?></span>
                              <?php } ?>
                            </td>
                            <td class="text-center">
                              <select name="priority[<?php echo $tspri['id']; ?>][hours_type]" class="form-control">    
                                <?php if(isset($priority[$tspri['id']]['hours_type']) AND $priority[$tspri['id']]['hours_type']=='0'){ ?>
                                  <option value="0" selected><?php echo $text_calendar; ?></option>
                                <?php }else{ ?>
                                  <option value="0"><?php echo $text_calendar; ?></option>
                                <?php } ?>
                                <?php if(isset($priority[$tspri['id']]['hours_type']) AND $priority[$tspri['id']]['hours_type']=='1'){ ?>
                                  <option value="1" selected><?php echo $entry_business_hours; ?></option>
                                <?php }else{ ?>
                                  <option value="1"><?php echo $entry_business_hours; ?></option>
                                <?php } ?>
                              </select>
                            </td>
                            <td class="text-center">
                              <select name="priority[<?php echo $tspri['id']; ?>][status]" class="form-control">  
                                <?php if(isset($priority[$tspri['id']]['status']) AND $priority[$tspri['id']]['status']=='0'){ ?>
                                  <option value="0" selected><?php echo $text_disabled; ?></option>
                                <?php }else{ ?>
                                  <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } ?>  
                                <?php if(isset($priority[$tspri['id']]['status']) AND $priority[$tspri['id']]['status']=='1'){ ?>
                                  <option value="1" selected><?php echo $text_enabled; ?></option>
                                <?php }else{ ?>
                                  <option value="1"><?php echo $text_enabled; ?></option>
                                <?php } ?>
                              </select>
                            </td>
                          </tr>
                        <?php } ?>
                      <?php }else{ ?>
                        <tr>  
                          <td class="text-center text-danger" colspan="5">
                            <?php echo $error_blank_priority; ?>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                </table>
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
            <label class="col-sm-2 control-label" for="input-sort"><?php echo $text_sort_order; ?><span data-toggle="tooltip" title="<?php echo $text_info_sort_order; ?>"></span></label>
            <div class="col-sm-10">
              <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $text_sort_order; ?>" id="input-sort" class="form-control" />
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
$('.time-menu').on('click', function(e){
  e.preventDefault();
  $(this).parents('ul').find('.time-menu').removeClass('active');
  $(this).parents('.input-group-btn').find('button').html($(this).text()+' <span class="caret"></span>');
  $(this).addClass('active');
  $(this).parents('.input-group-btn').find('input[type="hidden"]').val($(this).attr('value'));
});

$('a.active').trigger('click');

$('.number').on('change', function(){
  thisthis = this;
  thisvalue = this.value;
  typeValue = $(this).next().find('a.active').attr('value');
  html = '';
  if(typeValue=='minute' && thisvalue >= 60){
    html = '<div class="ticketsystem-message alert alert-info"><?php echo $text_sla_number_message; ?>  <button type="button" class="close" data-dismiss="alert">&times;</button></div>';
  }else if(typeValue=='days' && thisvalue >= 24){
    html = '<div class="ticketsystem-message alert alert-info"><?php echo $text_sla_number_message; ?>  <button type="button" class="close" data-dismiss="alert">&times;</button></div>';
  }else if(typeValue=='months' && thisvalue >= 12){
    html = '<div class="ticketsystem-message alert alert-danger"><?php echo $text_sla_number_message_late; ?>  <button type="button" class="close" data-dismiss="alert">&times;</button></div>';
  }
  $('.ticketsystem-input-group').find('.ticketsystem-message').remove();
  $(this).parent().prepend(html);
  $messageDiv = $(thisthis).parents('.ticketsystem-input-group').find('.ticketsystem-message');
  setTimeout(function(){
    $messageDiv.fadeOut(200, function(){
      $messageDiv.remove();
    });
  },4000);
});
//--></script></div>
<?php echo $footer; ?> 