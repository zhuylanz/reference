<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
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
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_view; ?></h3>
      </div>
      <div class="panel-body">
    
        <form action="<?php echo $cancel; ?>" method="post" enctype="multipart/form-data" id="form-information">
          <ul class="view-record">
                          <?php if($record) {?>
                           <?php
                             foreach ($record as $label=>$value) {
                           ?>   
                                  <li>
                                    <label><?php echo $label ?></label>
                                    <span>
                                        <?php if (filter_var($value, FILTER_VALIDATE_EMAIL)) { ?>
                                          <a title="Click to reply" href="javascript:void(0);" onclick="emailForm('<?php echo $value; ?>')"><?php echo $value; ?></a>
                                        <?php }  else { ?>
                                          <?php echo $value; ?>
                                        <?php } ?>
                                          
                                     </span>
                                  </li>
                              <?php }?>
                            <?php }?>
                        </ul>
             </form>


             <form style="<?php if ($isError) echo 'display: block;'; ?>" method="post" class="form-horizontal reply-email-form">
             <input type="hidden" name="send_email" value="1" />
              <h3><?php echo $text_send_email; ?></h3>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-name"><?php echo $text_from_name; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="from_name" value="<?php echo $from_name; ?>" placeholder="<?php echo $text_from_name; ?>" id="input-name" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-email"><?php echo $text_from_email; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="from_email" value="<?php echo $from_email; ?>" placeholder="<?php echo $text_from_email; ?>" id="input-email" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-to"><?php echo $text_to_email; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="to_email" id="to_email" value="<?php echo $to_email; ?>" placeholder="<?php echo $text_to_email; ?>" id="input-to" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-subject"><?php echo $entry_subject; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="subject" value="<?php echo $subject; ?>" placeholder="<?php echo $entry_subject; ?>" id="input-subject" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-message"><?php echo $entry_message; ?></label>
                <div class="col-sm-10">
                  <textarea name="message" placeholder="<?php echo $entry_message; ?>" id="input-message" class="form-control summernote"><?php echo $message; ?></textarea>
                </div>
              </div>
              <div class="form-group">
               
                <div class="col-sm-10">
                    <input style="float: right; width: 100px; margin-left: 15px;" type="submit" class="btn btn-success" name="send_email" value="<?php echo $text_send; ?>">
                   <input id="cancel_form" style="float: right; width: 100px; " type="button" class="btn btn-warning" name="send_email" value="<?php echo $text_cancel; ?>">&nbsp;&nbsp;
                 
                </div>
              </div>
        </form>
        
      </div>
    </div>
  </div>
</div>
<link rel="stylesheet" href="view/javascript/xform/xform.css" type="text/css" />
<style type="text/css">
  .reply-email-form {
        background: #ededed;
        padding: 10px;
        display: none;
   }
</style>
  <script type="text/javascript">
    var formStatus = false;
    function emailForm(email) {
        $('#to_email').val(email);
        formStatus = !formStatus;
        if (formStatus) {
          $('.reply-email-form').show();
        } else {
          $('.reply-email-form').hide();
        }
        
    }

    $('#cancel_form').on('click', function(){
        $('.reply-email-form').hide();
        formStatus = false;
    });

  </script>
  <script type="text/javascript">
      $('.summernote').summernote({height: 300});
  </script> 
<?php echo $footer; ?>