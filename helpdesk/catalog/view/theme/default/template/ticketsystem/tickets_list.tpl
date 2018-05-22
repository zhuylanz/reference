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
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?> <?php echo $ts_column_top ;?>
      <div class="ts-jumbotron" style="padding:0px; border:0">
        <h3 class="text-info"><?php echo $heading_title; ?>
          <button class="btn btn-sm btn-warning pull-right" id="button-clrfilter" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
        </h3>
        <p><?php echo $text_info; ?></p>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" class="form-horizontal" id="form-tickets">
          <hr>
          <div>
            <div id="ticketsResult">
              <div class="ticketsLoader hide text-info"> <i class="fa fa-spin fa-spinner fa-5x"></i> </div>
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr style="    background: #2AA8FE;">
                      <td style="width: 1px; border:0" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                      <td class="text-left"><div style="position:relative;display:inline-block;">
                          <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="status-option" data-toggle="dropdown" aria-expanded="true"><?php echo $text_ticket_status; ?> <i class="fa fa-angle-down" aria-hidden="true"></i></button>
                          <ul class="dropdown-menu filterTickets" role="menu" aria-labelledby="status-option" data-map="filter_t__status">
                            <li role="presentation">
                              <?php foreach ($statuss as $result) { ?>
                              <a role="menuitem" tabindex="-1" href="<?php echo $result['id']; ?>"> <?php echo $result['name']; ?></a>
                              <?php } ?>
                            </li>
                          </ul>
                        </div></td>
                      <td class="text-left"><div style="position:relative;display:inline-block;">
                          <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="priority-option" data-toggle="dropdown" aria-expanded="true"><?php echo $text_ticket_priority; ?> <i class="fa fa-angle-down" aria-hidden="true"></i></button>
                          <ul class="dropdown-menu filterTickets" role="menu" aria-labelledby="priority-option" data-map="filter_t__priority">
                            <li role="presentation">
                              <?php foreach ($priorities as $result) { ?>
                              <a role="menuitem" tabindex="-1" href="<?php echo $result['id']; ?>"> <?php echo $result['name']; ?></a>
                              <?php } ?>
                            </li>
                          </ul>
                        </div></td>
                      <td class="text-left"><div class="text-right" style="position:relative;display:inline-block;">
                          <button class="btn btn-sm btn-warning dropdown-toggle" type="button" id="activity-option" data-toggle="dropdown" aria-expanded="true"> <?php echo $text_sort; ?> <i class="fa fa-angle-down" aria-hidden="true"></i> </button>
                          <ul class="dropdown-menu sorting" role="menu" aria-labelledby="activity-option">
                            <li class="dropdown-header"><?php echo $text_column_name.' - '.$text_column_order; ?></li>
                            <li role="presentation"> <a role="menuitem" tabindex="-1" href="<?php echo $sort_ticket_id; ?>" <?php if ($sort == 't.id') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $text_id; ?></a> </li>
                            <li role="presentation"> <a role="menuitem" tabindex="-1" href="<?php echo $sort_ticket_subject; ?>" <?php if ($sort == 't.subject') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $text_ticket_subject; ?></a> </li>
                            <li role="presentation"> <a role="menuitem" tabindex="-1" href="<?php echo $sort_type; ?>" <?php if ($sort == 't.type') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $entry_type; ?></a> </li>
                            <li role="presentation"> <a role="menuitem" tabindex="-1" href="<?php echo $sort_priority; ?>" <?php if ($sort == 't.priority') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $text_ticket_priority; ?></a> </li>
                            <li role="presentation"> <a role="menuitem" tabindex="-1" href="<?php echo $sort_status; ?>" <?php if ($sort == 't.status') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $text_ticket_status; ?></a> </li>
                            <li role="presentation"> <a role="menuitem" tabindex="-1" href="<?php echo $sort_date_added; ?>" <?php if ($sort == 't.date_added') { ?>class="active <?php echo strtolower($order); ?>"<?php } ?>> <?php echo $entry_date_added; ?> </a> </li>
                          </ul>
                        </div></td>
                      <td class="text-left"><?php if($ts_customer_delete_ticket){ ?>
                        <button type="button" class="btn btn-danger btn-sm selection-required pull-right" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-tickets').submit() : false;"><i class="fa fa-trash-o"></i> <?php echo $button_delete_ticket ;?></button>
                        <?php } ?></td>
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
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<script type="text/javascript">
var updateStatusHtml = '<div class="ticketAlert alert alert-changeClass"><i class="fa fa-exclamation-circle"></i> changeMsg <button type="button" class="close" data-dismiss="alert">&times;</button></div>';

function TicketAlertMesgRemove(){
  setTimeout(function(){
    $('.ticketAlert.alert').remove();
  },4000);
}

$('#button-clrfilter').on('click', function() {
  localStorage.setItem('urlticketFilter', false);
  location = 'index.php?route=ticketsystem/tickets';
});

$('#ticketsResult').on('click', 'ul.pagination a', function(e){
  e.preventDefault();
  $('#ticketsResult').find('ul.pagination li').removeClass('active');
  $(this).parent().addClass('active');
  filterAction();
})

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
          if(key=='filter_t__status' || key=='filter_t__priority')
            $('#'+key + ' a[href='+ multiValueValue +']').addClass('active');
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
}

$('.filterTickets li a').on('click', function(e) {
  e.preventDefault();
  $(this).parent().find('a').removeClass('active');
  $(this).addClass('active');
  filterAction();
});

function filterAction(){
  var url = 'index.php?route=ticketsystem/tickets/tickets';
  urlAttr = '';

  $('ul.filterTickets').each(function(){
    if($(this).find('a.active').length){
      urlAttr += '&'+$(this).attr('data-map')+'='+$(this).find('a.active').attr('href');
    }
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
      },400)
    },
    complete: function(){
      $('.selection-required').attr('disabled', true);
    }
  })
}

<?php if($ts_customer_delete_ticket){ ?>
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
<?php } ?>
//--></script>
<style>
.text-info {
    color: #283d51;
    font-weight: bold;
}
button#button-clrfilter {
    background: #FF422D;
    font-size: 15px;
    padding: 6px 7px;
}
.table-bordered>thead>tr>td, .table-bordered>tbody>tr>td {
	border:0
}
button#status-option, button#priority-option {
    font-size: 14px;
    background: none !important;
    color: #fff !important;
}
button#activity-option {
	font-size: 14px;
    background: #fff !important;
    color: #283d51 !important;
	border-radius:20px;
	padding: 4px 20px;
}
button.btn.btn-default.btn-sm {
    font-size: 14px;
}
.table-responsive {
	overflow:hidden
}
.dropdown-menu a.active {
	color:#fff !important;
}
.dropdown-menu a:hover {
	color:#fff !important;
	background: #229AC8 !important
}
}
</style>
<?php echo $footer; ?>