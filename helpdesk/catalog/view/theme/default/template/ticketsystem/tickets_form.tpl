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
  <?php if ($ts_update_status_to_delete==$ticket_info['status']) { ?>
  <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $closed_ticket_status; ?></div>
  <?php }elseif($ts_update_status_to_spam==$ticket_info['status']){ ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $spam_ticket_status; ?></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
  <script>localStorage.setItem('tabTicket',false);</script>
  <?php } ?>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <?php $colors = array('green','blue','red'); ?>
    <?php shuffle($colors); ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <?php echo $ts_column_top ;?>
      <div class="ts-jumbotron" style="padding:10px 10px 5px 10px;">
        <h2 class="head-h2"><?php echo $text_form; ?>
        <a type="button" class="btn btn-default btn-sm pull-right" href="<?php echo $cancel; ?>"><i class="fa fa-reply"></i></a>
        </h2>
        <div class="clearfix"></div>
        <div style="overflow: visible;  min-height: 95px;">
          <?php $ticket_info['customerName'] = str_replace('"', '', $ticket_info['customerName']); ?>
          <div class="pull-left ticket-view-head-left">
            <h3 data-toggle="tooltip" title="<?php echo $ticket_info['subject']; ?>"><?php echo $ticket_info['subject']; ?></h3>
            <i class="fa fa-clock-o"></i> <span class="hidden-xs hidden-sm hidden-md"><b><?php echo $text_created; ?>- </span></b> <?php echo $ticket_info['date_added']; ?>&nbsp;&nbsp;&nbsp;
            <br/>
          </div>
          <br/>
          <form class="form-inline text-right">
            <div class="form-group">
              <label for="tsPriority"><?php echo $text_ticket_priority; ?> - </label>
              <button type="button" class="btn btn-primary disabled" ><?php echo $ticket_info['priorityName']; ?></button>
              <span class="hidden-xs hidden-sm hidden-md">&nbsp;&nbsp;&nbsp;</span>
            </div>
            <div class="form-group">
              <label for="tsStatus"><?php echo $text_ticket_status; ?> - </label>
              <button type="button" class="btn <?php echo ($ts_update_status_to_delete==$ticket_info['status'] ? 'btn-warning' : ($ts_update_status_to_spam==$ticket_info['status'] ? 'btn-danger' : 'btn-success' )); ?> disabled" ><?php echo $ticket_info['statusName']; ?></button>
              <span class="hidden-xs hidden-sm hidden-md">&nbsp;&nbsp;&nbsp;</span>
            </div>
            <div class="form-group">
              <label for="tsType"><?php echo $text_ticket_type; ?> - </label>
              <button type="button" class="btn btn-default disabled" ><?php echo $ticket_info['typeName']; ?></button>
            </div>
            <div class="form-group">
              <a class="btn btn-primary" title="<?php echo $text_previous; ?>" data-toggle="tooltip" <?php echo $prevId ? 'href='.$prevId : 'disabled'; ?>><i class="fa fa-chevron-left"></i></a>
              <a class="btn btn-primary" title="<?php echo $text_next; ?>" data-toggle="tooltip" <?php echo $nextId ? 'href='.$nextId : 'disabled'; ?>><i class="fa fa-chevron-right"></i></a>
            </div>
          </form>
        </div>

        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal" id="form-tickets">
        <div class="ticket-right-clm">
          <div class="action-buttons">
            <button type="button" class="btn btn-default btn-sm tabs-hit" href="#ticket-reply"><i class="fa fa-reply"></i> <?php echo $button_reply ;?></button>
            <?php if($ts_customer_update_status){ ?>
              <button type="button" class="btn btn-warning btn-sm update-ticket" id="<?php echo $ts_update_status_to_delete;?>" data-action="status" <?php echo $ts_update_status_to_delete==$ticket_info['status'] ? 'disabled' : false; ?>><i class="fa fa-check-square-o"></i> <?php echo $button_close_ticket ;?></button>
            <?php } ?>
          </div>

          <div class="ticket-messages">
            <div class="ticket-create">
              <?php if($ticket_create){ ?>
                <?php $ticket_create['customerName'] = str_replace('"', '', $ticket_create['customerName']); ?>
                <div class="create-logo pull-left <?php echo $colors[0]; ?>" tabindex="0" data-trigger="focus" data-toggle="popover"  title="<?php echo $text_customer_details;?>" data-content="<div><i class='fa fa-user'></i> <?php echo $ticket_create['customerName']; ?><br/> <i class='fa fa-envelope'></i> <?php echo $ticket_info['customerEmail']; ?></div>" ><?php echo substr($ticket_create['customerName'],0,1); ?></div>
                <div class="message-margin-manage">
                  <h4><i class="fa fa-user"></i> <?php echo $ticket_create['customerName']; ?></h4>
                  <i class="fa fa-clock-o"></i> <span class="hidden-xs hidden-sm hidden-md"><b><?php echo $text_created; ?>- </span></b> <?php echo $ticket_create['date_added']; ?>&nbsp;&nbsp;&nbsp;
                </div>
                <div class="clearfix"></div>
                <div class="message">
                  <h4><b><?php echo $ticket_info['subject']; ?></b></h4>
                  <?php if($ticket_create['message'] != strip_tags(html_entity_decode($ticket_create['message']))){ ?>
                    <?php echo preg_replace('/<img/','<img class="img-responsive"', html_entity_decode($ticket_create['message'], ENT_QUOTES, 'UTF-8')); ?>
                  <?php }else{ ?>
                    <?php echo nl2br($ticket_create['message']); ?>
                  <?php } ?>
                  <br/><br/><br/>
                  <?php foreach ($ticket_create['attachments'] as $attachment) { ?>
                    <?php if($attachment['viewImage']){ ?>
                      <div class="img-thumbnail" data-toggle="tooltip" title="<?php echo $attachment['name'];?>">
                        <img src="<?php echo $attachment['viewImage'];?>" alt="<?php echo $attachment['name'];?>">
                        <a href="<?php echo $attachment['path'];?>" target="_blank" class="image-hover-a hide"><i class="fa fa-download"></i></a>
                      </div>
                    <?php }else{ ?>
                      <div class="img-thumbnail" data-toggle="tooltip" title="<?php echo $attachment['name'];?>">
                        <span class="box"><?php echo strtoupper(pathinfo($attachment['name'])['extension']);?></span>
                        <a href="<?php echo $attachment['path'];?>" target="_blank" class="image-hover-a hide"><i class="fa fa-download"></i></a>
                      </div>
                    <?php } ?>
                  <?php } ?>
                </div>
              <?php } ?>
              <div class="seperator"></div>
            </div>
          </div>
          <?php echo $ticket_threads;?>
          <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ticket" class="form-horizontal">
            <div class="thread-data">
             <div class="create-logo pull-left <?php echo $colors[0]; ?>"><?php echo substr($ticket_create['customerName'],0,1); ?></div>
              <div class="message-margin-manage">
                <h4 class="text-success"><b><span class="fa">A</span> <?php echo $ticket_create['customerName']; ?></b></h4>
                <i><?php echo $text_ask;?></i>
              </div>
              <div class="clearfix"></div>
              <div class="message" id="ticket-reply">
                <input type="hidden" name="reply[receivers][to][]" value="<?php echo $ticket_info['agentEmail']; ?>"/>
                <?php if($ts_customer_add_cc){ ?>
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="<?php echo $text_cc.$text_coma_separated; ?>" name="reply[receivers][cc]">
                  </div>
                <?php } ?>

                <div class="form-group required">
                  <textarea name="reply[message]" class="form-control summernote-textarea"><?php echo (isset($reply['message'])) ? $reply['message'] : ''; ?></textarea>
                  <span class="text-danger"><?php echo isset($error_reply_message) ? $error_reply_message : false; ?></span>
                  <br/>
                  <label class="btn btn-default" for="upload-reply-file" data-toggle="tooltip" title="<?php echo $text_fileupload_info; ?>"><?php echo $text_fileupload; ?></label>
                  <button type="submit" class="btn btn-primary pull-right" name="reply[submit]"><i class="fa fa-reply"></i> <?php echo $button_reply ;?></button>
                  <input type="file" name="reply[file][]" class="hide" id="upload-reply-file" multiple>
                  <br/><span class="text-danger"><?php echo isset($error_reply_file) ? $error_reply_file : false; ?></span>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<script type="text/javascript">
var notesId, tagsId, updateMessage, oldUpdateMessage;
var updateStatusHtml = '<div class="ticketAlert alert alert-changeClass"><i class="fa fa-exclamation-circle"></i> changeMsg <button type="button" class="close" data-dismiss="alert">&times;</button></div>';
var logoText = $('.panel-heading .create-logo').text();

$('body').on('click', 'span.image-hover-a', function(){
  $(this).parent().remove();
});

<?php if($ts_customer_update_status){ ?>
$('.update-ticket').on('click', function(){
  updateDisabledProp(this);
  var thisthis = {'id':this.id, 'event': $(this).attr('data-action')};
  if(!confirm('<?php echo $text_confirm;?>')){
    updateDisabledProp(this);
    return false;
  }
  ticketThreadUpdate(thisthis);
})
<?php } ?>

<?php if($ts_editor){ ?>
$('.summernote-textarea').summernote({
  onChange: function(shtml, thisTextarea) {
    updateMessage = {'event' : $(thisTextarea.context).attr('name'), 'shtml' : shtml};
  },
  height: 200
});
<?php } ?>

<?php if($ts_customer_delete_ticketthread || $ts_customer_update_status){ ?>
function ticketThreadUpdate(thisthis){
  $.ajax({
    url: 'index.php?route=ticketsystem/tickets/threadActions',
    type: 'POST',
    dataType: 'json',
    data: {'threadId':(thisthis.id!=undefined ? thisthis.id : false ), 'html':(thisthis.shtml!=undefined ? thisthis.shtml : false ), 'event':thisthis.event, 'id': '<?php echo $ticketId; ?>'},
    beforeSend: function(){
      $('.ticketAlert').remove();
      if(thisthis.event=='deletethread' || thisthis.event=='split'){
        $('.panel-heading .create-logo').html('<i class="fa fa-spin fa-spinner"></i>');
      }
    },
    success: function(json){
      var html = '';
      if(json['success']){
        html = updateStatusHtml.replace('changeClass','success').replace('changeMsg', json['success']);
      }
      if(json['ticket_id']){
        html = updateStatusHtml.replace('changeClass','success').replace('changeMsg', json['success']);
      }
      if(json['warning']){
        html = updateStatusHtml.replace('changeClass','danger').replace('changeMsg', json['warning']);
      }
      if(thisthis.event=='deletethread'){
        var paginationId = $('body').find('.div-ticket-pagination button').attr('id');
        var hFunSplit = paginationId.split('-');
        if(hFunSplit[2]!=undefined){
          var hPipeSplit = hFunSplit[2].split('|');
          if(hPipeSplit[0]!=undefined){
            var newPageUpdate = parseInt(hPipeSplit[0])-1;
            var newPaginationId = hFunSplit[0]+'-'+hFunSplit[1]+'-'+newPageUpdate+'|'+hPipeSplit[1]+'|'+hPipeSplit[2];
            $('body').find('.div-ticket-pagination button').attr('id', newPaginationId);
          }
        }
        var threadData = $('.ticket-right-clm').find('button[id='+thisthis.id+']').parents('.thread-data');
        threadData.fadeOut('slow', function(){
          setTimeout(function(){
            threadData.remove();
          },2000);
        });
      }

      if(thisthis.shtml!=undefined)
        setTimeInterDeActive();

      $('body').append(html);
      TicketAlertMesgRemove();
    },
    complete: function(){
      if(thisthis.event=='deletethread' || thisthis.event=='split'){
        setTimeout(function(){
          $('.panel-heading .create-logo').text(logoText);
        },500);
      }else if(thisthis.event=='getViewers'){
        setTimeout(function(){
          $('#ticket-viewers').html('<i class="fa fa-eye"></i>');
        },500);
      }
    }
  });
}
<?php } ?>

<?php if($ts_customer_delete_ticketthread){ ?>
$('.ticket-right-clm').on('click', '.thread-data .message .ticket-thread-actions button', function(){
  var thisthis = {'id':this.id, 'event': $(this).attr('data-action')};
  if(thisthis.event=='deletethread'){
    if(!confirm('<?php echo $text_confirm;?>'))
      return false;
  }
  ticketThreadUpdate(thisthis);
});

$('.ticket-right-clm').on('mouseover', '.thread-data .message', function(){
  $(this).find('.ticket-thread-actions').removeClass('hide');
}).on('mouseleave', '.thread-data .message', function(){
  $(this).find('.ticket-thread-actions').addClass('hide');
})
<?php } ?>

$('body').on('mouseover', '.img-thumbnail', function(){
  $(this).find('.image-hover-a').removeClass('hide');
}).on('mouseleave', '.img-thumbnail', function(){
  $(this).find('.image-hover-a').addClass('hide');
});

$('.tabs-hit').on('click', function(){
  var divId = $(this).attr('href');
  $('a[href='+divId+']').trigger('click');
  $('html,body').animate({
        scrollTop: $(divId).offset().top
        },'slow');
});

function ticketThreadFetch(thisthis){
  updateDisabledProp(thisthis);
  $.ajax({
    url: 'index.php?route=ticketsystem/tickets/getTicketThreads',
    dataType: 'html',
    data: {'limit':thisthis.id, 'id': '<?php echo $ticketId; ?>'},
    beforeSend: function(){
      $(thisthis).html('<i class="fa fa-spin fa-spinner"></i>');
    },
    success: function(html){
      if(html){
        setTimeout(function(){
          $('.div-ticket-pagination').after(html);
        },500);
      }
    },
    complete: function(){
      setTimeout(function(){
        $(thisthis).parent().remove();
      },500);
    }
  });
}

$('.create-logo').popover({
  html : true,
});

function TicketAlertMesgRemove(){
  setTimeout(function(){
    $('.ticketAlert.alert').remove();
  },4000);
}

function updateDisabledProp(thisthis){
  if($(thisthis).prop('disabled')){
    $(thisthis).prop('disabled',false);
  }else{
    $(thisthis).prop('disabled',true);
  }
}
//--></script>
<?php echo $footer; ?>