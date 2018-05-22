<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
      </div>
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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-body">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-tickets" class="form-horizontal">
          <div class="ticketFilter col-sm-2">
            <?php echo $filterColumn; ?>
          </div>
          <div class="col-sm-10" style="padding-right: 0px;">
            <div id="ticketsResult">
              <div class="ticketsLoader hide text-info">
                <i class="fa fa-spin fa-spinner fa-5x"></i>
              </div>
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                      <td class="text-left" colspan="2">
                        <?php if(in_array('tickets.update',$ts_roles)){ ?>
                          <div style="position:relative;display:inline-block;">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle selection-required" id="agent-add-option" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-plus"></i> <?php echo $text_assign_agent ;?></button>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="agent-add-option" data-map="input-assign_agent">
                              <li role="presentation">
                                <?php foreach ($agents as $result) { ?>
                                  <a role="menuitem" tabindex="-1" href="<?php echo $result['id']; ?>"> <?php echo $result['username'].' - '.$result['email']; ?></a>
                                <?php } ?>
                              </li>
                            </ul>
                          </div>

                          <div style="position:relative;display:inline-block;">
                              <button class="btn btn-default btn-sm dropdown-toggle selection-required" type="button" id="group-add-option" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-users"></i> <?php echo $text_assign_group; ?></button>
                              <ul class="dropdown-menu" role="menu" aria-labelledby="group-add-option" data-map="input-group">
                                <li role="presentation">
                                  <?php foreach ($groups as $result) { ?>
                                    <a role="menuitem" tabindex="-1" href="<?php echo $result['id']; ?>"> <?php echo $result['name']; ?></a>
                                  <?php } ?>
                                </li>
                              </ul>
                          </div>

                          <div style="position:relative;display:inline-block;">
                              <button class="btn btn-default btn-sm dropdown-toggle selection-required" type="button" id="status-add-option" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-check"></i> <?php echo $text_ticket_status; ?></button>
                              <ul class="dropdown-menu" role="menu" aria-labelledby="status-add-option" data-map="input-status">
                                <li role="presentation">
                                  <?php foreach ($statuss as $result) { ?>
                                    <a role="menuitem" tabindex="-1" href="<?php echo $result['id']; ?>"> <?php echo $result['name']; ?></a>
                                  <?php } ?>
                                </li>
                              </ul>
                          </div>
                          
                          <button type="button" class="btn btn-sm btn-default selection-required" data-toggle="modal" data-target="#myBulkActionModal"><i class="fa fa-futbol-o"></i> <?php echo $button_bulkActions ;?></button>

                          <!-- Modal -->
                          <div class="modal fade" id="myBulkActionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                  <h3 class="modal-title" id="myModalLabel"><?php echo $text_bulkActions ;?></h3>
                                </div>
                                <div class="modal-body">
                                  <form class="form-horizontal">
                                    <div class="form-group">
                                      <label class="col-sm-4 control-label" for="bulk-Status"><?php echo $entry_status; ?></label>
                                      <div class="col-sm-6">
                                        <select name="status" id="bulk-Status" class="form-control selectpicker"  data-live-search="true">
                                          <option value=""></option>
                                          <?php foreach ($statuss as $value) { ?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                          <?php } ?>
                                        </select>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="col-sm-4 control-label" for="bulk-agent"><?php echo $text_assign_agent; ?></label>
                                      <div class="col-sm-6">
                                        <select name="assign_agent" id="bulk-agent" class="form-control selectpicker"  data-live-search="true">
                                          <option value=""></option>
                                          <?php foreach ($agents as $value) { ?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['username'].' - '.$value['email']; ?></option>
                                          <?php } ?>
                                        </select>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="col-sm-4 control-label" for="bulk-group"><?php echo $text_assign_group; ?></label>
                                      <div class="col-sm-6">
                                        <select name="group" id="bulk-group" class="form-control selectpicker"  data-live-search="true">
                                          <option value=""></option>
                                          <?php foreach ($groups as $value) { ?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                          <?php } ?>
                                        </select>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="col-sm-4 control-label" for="bulk-priority"><?php echo $text_ticket_priority; ?></label>
                                      <div class="col-sm-6">
                                        <select name="priority" id="bulk-priority" class="form-control selectpicker"  data-live-search="true">
                                          <option value=""></option>
                                          <?php foreach ($priorities as $value) { ?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                          <?php } ?>
                                        </select>
                                      </div>
                                    </div>
                                  </form>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_close ;?></button>
                                  <button type="button" class="btn btn-primary bulk-actions"><?php echo $button_save  ;?></button>
                                </div>
                              </div>
                            </div>
                          </div>
                        <?php } ?>

                        <?php if(in_array('tickets.merge',$ts_roles)){ ?>
                          <button class="btn btn-sm btn-default selection-required merge-tickets" data-action="merge"  data-toggle="modal" data-target="#myMergeActionModal" title="<?php echo $text_merge_tickets ;?>" type="button"><i class="fa fa-link"></i> <?php echo $text_merge ;?></button>

                          <!-- Modal -->
                          <div class="modal fade" id="myMergeActionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                  <h3 class="modal-title" id="myModalLabel"><?php echo $text_merge_tickets ;?></h3>
                                </div>
                                <div class="modal-body">
                                  <form class="form-horizontal">
                                    <div class="form-group">
                                      <div class="col-sm-12">
                                        <div class="list-group">
                                        </div>
                                        <span class="text-danger model-error hide"><?php echo $error_merge_min_req; ?></span>
                                      </div>
                                    </div>
                                  </form>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_close; ?></button>
                                  <button type="button" class="btn btn-primary merge-actions"><?php echo $button_save; ?></button>
                                </div>
                              </div>
                            </div>
                          </div>
                        <?php } ?>

                        <?php if(in_array('tickets.delete',$ts_roles)){ ?>
                          <button type="button" class="btn btn-danger btn-sm selection-required" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-tickets').submit() : false;"><i class="fa fa-trash-o"></i> <?php echo $button_delete_ticket ;?></button>
                        <?php } ?>

                        <div class="text-right pull-right" style="position:relative;">
                          <button class="btn btn-sm btn-warning dropdown-toggle" type="button" id="activity-option" data-toggle="dropdown" aria-expanded="true">
                            <b><?php echo $text_sort; ?></b>
                          </button>
                          <ul class="dropdown-menu sorting" role="menu" aria-labelledby="activity-option">
                            <li class="dropdown-header"><?php echo $text_column_name.' - '.$text_column_order; ?></li>
                            <li role="presentation">
                              <a role="menuitem" tabindex="-1" href="<?php echo $sort_ticket_id; ?>" <?php if ($sort == 't.id') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $text_id; ?></a>
                            </li>
                            <li role="presentation">
                              <a role="menuitem" tabindex="-1" href="<?php echo $sort_ticket_subject; ?>" <?php if ($sort == 't.subject') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $text_ticket_subject; ?></a>
                            </li>
                            <li role="presentation">
                              <a role="menuitem" tabindex="-1" href="<?php echo $sort_type; ?>" <?php if ($sort == 't.type') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $entry_type; ?></a>
                            </li>
                            <li role="presentation">
                              <a role="menuitem" tabindex="-1" href="<?php echo $sort_priority; ?>" <?php if ($sort == 't.priority') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $text_ticket_priority; ?></a>
                            </li>
                            <li role="presentation">
                              <a role="menuitem" tabindex="-1" href="<?php echo $sort_status; ?>" <?php if ($sort == 't.status') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $text_ticket_status; ?></a>
                            </li>
                            <li role="presentation">
                              <a role="menuitem" tabindex="-1" href="<?php echo $sort_agent; ?>" <?php if ($sort == 't.assign_agent') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $text_agent; ?></a>
                            </li>
                            <li role="presentation">
                              <a role="menuitem" tabindex="-1" href="<?php echo $sort_group; ?>" <?php if ($sort == 't.group') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $text_ticket_group; ?></a>
                            </li>
                            <li role="presentation">
                              <a role="menuitem" tabindex="-1" href="<?php echo $sort_customer; ?>" <?php if ($sort == 't.customer_id') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $text_ticket_requester; ?></a>
                            </li>
                            <li role="presentation">
                              <a role="menuitem" tabindex="-1" href="<?php echo $sort_date_added; ?>" <?php if ($sort == 't.date_added') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $entry_date_added; ?>  </a>
                            </li>
                          </ul>
                        </div>


                      </td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php echo $ticketsResult; ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
var updateStatusHtml = '<div class="ticketAlert alert alert-changeClass"><i class="fa fa-exclamation-circle"></i> changeMsg <button type="button" class="close" data-dismiss="alert">&times;</button></div>';

<?php if(in_array('tickets.merge',$ts_roles)){ ?>
$('button.merge-tickets').on('click', function(){
  var selector = $('input[name*=\'selected\']');
  var html = '';
  selector.each(function(key, value){
    if($(this).is(':checked')){
      html += '<a class="list-group-item" id="'+this.value+'" data-key="'+key+'"><input type="checkbox" name="primary" value="'+this.value+'" class="checkbox-primary"> ## '+this.value+' <button type="button" class="btn btn-sm btn-default pull-right remove" onclick="$(this).parent().remove();" style="margin-top: -5px;"><i class="fa fa-close"></i></button> <button type="button" class="btn btn-sm btn-success pull-right hide msg" style="margin-top: -5px;" disabled><?php echo $text_ticket_primary; ?></button></a>';
    }
  })
  if(html){
    $('#myMergeActionModal .form-group .list-group').html(html);
    $('#myMergeActionModal .form-group .list-group a').eq(0).find('input[type=\'checkbox\']').prop('checked', true);
    $('#myMergeActionModal .form-group .list-group a').eq(0).find('button.remove').addClass('hide');
    $('#myMergeActionModal .form-group .list-group a').eq(0).find('button.msg').removeClass('hide');
  }
})

$('#myMergeActionModal').on('click', '.checkbox-primary', function(){
  $('.checkbox-primary').prop('checked', false);
  $('.checkbox-primary').parent().children('button.remove').removeClass('hide');
  $('.checkbox-primary').parent().children('button.msg').addClass('hide');
  $(this).prop('checked', true);
  $(this).parent().children('button.remove').addClass('hide');
  $(this).parent().children('button.msg').removeClass('hide');
})

$('.merge-actions').on('click', function(){
  mergeHtml = $(this).html();
  $('.model-error').addClass('hide');
  var id = [];
  $('#myMergeActionModal a').each(function(){
    id.push($(this).attr('id'));
  })
  var thisthis = {'primary' : $('#myMergeActionModal input[type=\'checkbox\'][name=\'primary\']:checked').val(), 'id' : id };
  if(id.length > 1)
    ticketsMerge(thisthis);
  else
    $('.model-error').removeClass('hide');
});

function ticketsMerge(thisthis){
  $.ajax({
    url: 'index.php?route=ticketsystem/tickets/merge&token=<?php echo $token; ?>',
    type: 'POST',
    dataType: 'json',
    data: thisthis,
    beforeSend: function(){
      $('.merge-actions').html('<i class="fa fa-spin fa-spinner"></i>')
      $('.ticketAlert').remove();
    },
    success: function(json){
      var html = '';
      if(json['success']){
        html = updateStatusHtml.replace('changeClass','success').replace('changeMsg', json['success']);
      }
      if(json['warning']){
        html = updateStatusHtml.replace('changeClass','danger').replace('changeMsg', json['warning']);
      }
      $('body').append(html);
      TicketAlertMesgRemove();
      filterAction();
    },
    complete: function(){
      $('.merge-actions').html(mergeHtml)
    }
  });
}
<?php } ?>

function TicketAlertMesgRemove(){
  setTimeout(function(){
    $('.ticketAlert.alert').remove();
  },4000);
}

$('.selection-required').attr('disabled', true);

$('#ticketsResult').on('click', 'ul.pagination a', function(e){
  e.preventDefault();
  $('#ticketsResult').find('ul.pagination li').removeClass('active');
  $(this).parent().addClass('active');
  filterAction();
})

$('#ticketsResult').on('click', 'input[type=\'checkbox\']', function(){
  var selector = $('input[name*=\'selected\']');
  var selected = [];
  selector.each(function(key, value){
    if($(this).is(':checked'))
      selected.push($(this).val());
  })
  if(selected.length){
    $('.selection-required').attr('disabled', false);
  }else{
    $('.selection-required').attr('disabled', true);
  }
})

<?php if(in_array('tickets.update',$ts_roles)){ ?>
$('a[role=menuitem]').on('click', function(e){
  e.preventDefault();
  var thisA = this;
  var selector = $('input[name*=\'selected\']');
  var selected = [];
  selector.each(function(key, value){
    if($(this).is(':checked'))
      selected.push($(this).val());
  })
  if(!selected.length)
    return;

  var thisthis = {'id': $(thisA).parents('ul').attr('data-map'), 'value': $(thisA).attr('href'), 'ticketId' : selected};
  ticketUpdate(thisthis);
});

$('button.bulk-actions').on('click', function(){
  var selector = $('input[name*=\'selected\']');
  var selected = [];
  selector.each(function(key, value){
    if($(this).is(':checked'))
      selected.push($(this).val());
  })
  if(!selected.length)
    return;

  var values = {};

  $('#myBulkActionModal select').each(function(){
    values[this.name] = this.value;
  });

  var thisthis = {'id': 'bulk', 'value': values, 'ticketId' : selected};
  ticketUpdate(thisthis);

  
});

function ticketUpdate(thisthis){
  $.ajax({
    url: 'index.php?route=ticketsystem/tickets/update&token=<?php echo $token; ?>',
    type: 'POST',
    dataType: 'json',
    data: {'type':thisthis.id, 'value':thisthis.value ? thisthis.value : false, 'id': thisthis.ticketId ? thisthis.ticketId : false},
    beforeSend: function(){
      $('.ticketAlert').remove();
    },
    success: function(json){
      var html = '';
      if(json['success']){
        html = updateStatusHtml.replace('changeClass','success').replace('changeMsg', json['success']);
      }
      if(json['warning']){
        html = updateStatusHtml.replace('changeClass','danger').replace('changeMsg', json['warning']);
      }
      $('body').append(html);
      TicketAlertMesgRemove();
      filterAction();
    },
    complete: function(){
        $('#myBulkActionModal select').each(function(k, v){          
          $(v).prop("selected", false);
        });
    }
  });
}
<?php } ?>

$('#button-clrfilter').on('click', function() {
  localStorage.setItem('urlticketFilter', false);
  location = 'index.php?route=ticketsystem/tickets&token=<?php echo $token; ?>';
});

$('ul.sorting li a').on('click', function(e){
  e.preventDefault();
  $('ul.sorting li a').removeClass('active').removeClass('asc').removeClass('desc');
  if(this.href.indexOf('asc') > 0){
    $(this).addClass('active').addClass('asc');
  }
  else{
    $(this).addClass('active').addClass('desc');
  }
  filterAction();
   if(this.href.indexOf('asc') > 0){
    $(this).attr('href', $(this).attr('href').replace('asc','desc'));
  }
  else{
    $(this).attr('href', $(this).attr('href').replace('desc','asc'));
  }
})

$(document).ready(function(){
  initilizeInitialStage();
});

$('.filterTicketsText').on('change', function(){
  filterAction();
})

$('.filterTickets').on('change', function() {
  filterAction();
});

$('.filterTicketsCheck').on('click', function() {
  filterAction();
});

function initilizeInitialStage(){
  var hash = window.location.hash.substring(1);

  //var filterArray = hash.split('&');
  var filterArray = hash ? hash.split('&') : (localStorage.getItem('urlticketFilter') ? localStorage.getItem('urlticketFilter').split('&') : []);

  processed_data = {};

  for (i = 0; i < filterArray.length; i++) { 
      m = filterArray[i].split("=");
      processed_data[m[0]] = m[1];
  }

  $.each(processed_data, function(key, value){
    if(value){
      if(value != undefined && value != ''){
        multiValues = value.split(',');
        $.each(multiValues, function(multiKey, multiValueValue){
          if(key!='filter_t__date_added' && key!='sort' && key!='order')
            $('#'+key + ' option[value='+ multiValueValue +']').attr('selected','selected');
          if(key=='filter_t__type' || key=='filter_t__status' || key=='filter_t__priority')
            $('input[name='+key+'][value='+ multiValueValue +']').attr('checked','checked');
          else if(key=='filter_t__date_added' || key=='filter_t__id')
            $('#'+key).val(multiValueValue);
          else if(key=='sort'){
            $('ul.sorting li a').removeClass('active').removeClass('asc').removeClass('desc');
            thisthis = $('ul.sorting li a[href="&sort='+multiValueValue+'&order=desc"]');
            $(thisthis).addClass('active').addClass(processed_data['order']);
            if(processed_data['order']=='asc')
              $(thisthis).attr('href', $(thisthis).attr('href').replace('desc','asc'));
          }
        });
      }
    }
  });
  filterAction();
  // if(processed_data['order']=='asc')
    // $(thisthis).attr('href', $(thisthis).attr('href').replace('asc','desc'));
  // else
    // $(thisthis).attr('href', $(thisthis).attr('href').replace('desc','asc'));
}

function filterAction(){
  var url = 'index.php?route=ticketsystem/tickets/tickets&token=<?php echo $token; ?>';
  urlAttr = '';

  $('.filterTickets').each(function(){
    if(this.id){
      if(this.value){
        var foo = []; 
        urlAttr += '&'+this.id+'=';
        $('#'+this.id + ' option:selected').each(function(i, selected){ 
          // urlAttr += encodeURIComponent(this.value)+'|';
          foo[i] = encodeURIComponent(this.value); 
        })
        urlAttr += foo;
      }
    }
  });

  var checkboxObj = {};
  $('.filterTicketsCheck').each(function(){
    if($(this).is(':checked')){ 
      if(checkboxObj.hasOwnProperty(this.name)){
        checkboxObj[this.name].push(this.value);
      }else{
        checkboxObj[this.name] = [this.value];
      }
    }
  });

  $.each(checkboxObj, function(key, value){
    urlAttr += '&'+key+'='+value;
  });

  $('.filterTicketsText').each(function(){
    if($(this).val())
      urlAttr += '&'+this.id+'='+encodeURIComponent($(this).val());
  });

  if($('ul.sorting li a.active').attr('href'))
    urlAttr += $('ul.sorting li a.active').attr('href');

  if($('#ticketsResult ul.pagination li.active').length){
    if($('#ticketsResult').find('ul.pagination li.active a').attr('href')!=undefined)
      urlAttr += '&page='+$('#ticketsResult').find('ul.pagination li.active a').attr('href');
    else
      urlAttr += '&page='+$('#ticketsResult').find('ul.pagination li.active span').text();
  }
  else if(processed_data.page!=undefined)
    urlAttr += '&page='+processed_data.page;
  else  
    urlAttr += '&page=1';

  window.location.hash = urlAttr;

  url = url + urlAttr;

  localStorage.setItem('urlticketFilter',urlAttr);

  $.ajax({
    url: url,
    dataType: 'html',
    beforeSend: function(){
      $('.watingTicket').remove();
      $('.ticketsLoader').removeClass('hide');
    },
    success: function(html){
      setTimeout(function(){
        $('.watingTicket').remove();
        $('.ticketsLoader').addClass('hide');
        $('#ticketsResult tbody').html(html);
        $('#ticketsResult').find('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
      },400)
    },
    complete: function(){
      $('.selection-required').attr('disabled', true);
    }
  })
}

$('.date').datetimepicker({
  pickTime: false
});

var popOverSettings = {
    placement: 'right',
    container: 'body',
    html: true,
    selector: '.create-logo', //Sepcify the selector here
    content: function () {
        return $('.create-logo').html();
    }
}

$('body').popover(popOverSettings);

$('#ticketsResult').on('mouseover', 'tbody td', function(){
  $(this).find('.sla-info').removeClass('hide');
}).on('mouseleave', 'tbody td', function(){
  $(this).find('.sla-info').addClass('hide');
});
//--></script>
</div>
<?php echo $footer; ?>