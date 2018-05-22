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
    <div class="modal fade ticketsystem-modal" id="Ticketsystem-Modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo $text_placeholder_title; ?></h4>
          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <div class="row">
                <div class="alert alert-info"><?php echo $text_placeholder_info; ?></div>
                <?php foreach($ticketPlaceHolder as $key => $placeHolder){ ?>
                  <button type="button" class="btn btn-default button-margin placeholders" value="{{ticket.<?php echo $placeHolder; ?>}}"><?php echo ${'text_'.$placeHolder}; ?></button>
                <?php } ?>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal"><?php echo $button_close; ?></button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading" style="overflow: auto;">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
        - <?php echo $text_info_emailtemplates; ?>
        <button class="btn btn-success pull-right" data-toggle="modal" data-target="#Ticketsystem-Modal" type="button"><?php echo $text_placeholder; ?></button>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $text_subject; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" id="input-name" class="form-control add-placeholders"/>
              <?php if ($error_name) { ?>
                <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-description"><?php echo $entry_description; ?></label>
            <div class="col-sm-10">
              <textarea name="message" id="input-message" class="form-control"><?php echo $message; ?></textarea>
              <?php if ($error_message) { ?>
                <div class="text-danger"><?php echo $error_message; ?></div>
              <?php } ?>
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
var cursorPos, oldContent, selection, cursorPosition;

$('#Ticketsystem-Modal').modal({
  keyboard: false,
  backdrop: false,
  show: false,
  handleUpdate: true
})

$('#Ticketsystem-Modal').on('shown.bs.modal', function (e) {
  $('body').removeClass('modal-open');
});

$('body').on('click', '.add-placeholders', function(){
  selection = this;
  oldContent = selection.value;
  cursorPosition = $(this).prop("selectionStart");
});

$('body').on('click', '.note-editable', function(){
  cursorPosition = false;

  selection = document.getSelection();
  cursorPos = selection.anchorOffset;
  oldContent = selection.anchorNode.nodeValue;
});

$('.placeholders').on('click', function(){
  var toInsert = this.value;

  if(cursorPosition){
    var newContent = oldContent.substring(0, cursorPosition) + toInsert + oldContent.substring(cursorPosition);

    selection.value = newContent;
    cursorPosition = cursorPosition + toInsert.length;

  }else if(!cursorPosition && cursorPos){
    if(oldContent)
      var newContent = oldContent.substring(0, cursorPos) + toInsert + oldContent.substring(cursorPos);
    else
      var newContent = toInsert;

    selection.anchorNode.nodeValue = newContent;
    cursorPos = cursorPos + toInsert.length;
  }
  oldContent = newContent;
});

$('#input-message').summernote({height: 200});
//--></script></div>
<?php echo $footer; ?> 