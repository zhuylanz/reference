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

<?php if ($error_responses) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_responses; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
<?php } ?>
<table id="actions" class="table table-striped table-bordered table-hover" style="display: table;">
    <thead>
      <tr>
        <td colspan="2">
        <?php echo $text_info_action2; ?>
        <button class="btn btn-success pull-right" data-toggle="modal" data-target="#Ticketsystem-Modal" type="button"><?php echo $text_placeholder; ?></button>
        </td>
      </tr>
    </thead>
    <tbody>
      <?php $key = 0; ?>
      <?php if($actions){ ?>
        <?php foreach ($actions as $key => $action) { ?>
          <tr id="actions<?php echo $key; ?>">  
            <td class="text-left">
              <div class="input-group ticketsystem-input-group">      
                <span class="input-group-addon">
                  <i class="fa fa-check-circle-o"></i>
                </span>
                <select name="actions[<?php echo $key; ?>][type]" class="form-control select-actions">    
                  <option></option>
                  <?php foreach ($ticketActions as $value) { ?>
                    <option value="<?php echo $value; ?>" <?php echo $action['type']==$value ? 'selected' : false;?>><?php echo ${'text_responses_'.$value}; ?></option>
                  <?php } ?>
                </select>
              </div>  
              <div class="selection-html">
                <?php echo isset($action['html']) ? $action['html'] : false; ?>
              </div>
            </td>
            <td class="text-left" style="width:50px;">
              <button onclick="$('#actions<?php echo $key; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
            </td>
          </tr>
        <?php } ?>
      <?php }else{ ?>
        <tr id="actions<?php echo $key; ?>">  
          <td class="text-left">
            <div class="input-group ticketsystem-input-group">      
              <span class="input-group-addon">
                <i class="fa fa-check-circle-o"></i>
              </span>
              <select name="actions[<?php echo $key; ?>][type]" class="form-control select-actions">    
                <option></option>
                <?php foreach ($ticketActions as $value) { ?>
                  <option value="<?php echo $value; ?>"><?php echo ${'text_responses_'.$value}; ?></option>
                <?php } ?>
              </select>
            </div>  
            <div class="selection-html"></div>
          </td>
          <td class="text-left" style="width:50px;">
            <button onclick="$('#actions<?php echo $key; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
          </td>
        </tr>
      <?php } ?>

    </tbody>
  <tfoot>
    <tr>
      <td colspan="2" class="text-right"><button type="button" onclick="addAction();" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
    </tr>
  </tfoot>
</table>
<script type="text/javascript">
var cursorPos, oldContent, selection, cursorPosition;

$('#Ticketsystem-Modal').modal({
  keyboard: false,
  backdrop: false,
  show: false,
  handleUpdate: true
})

$('#Ticketsystem-Modal').on('shown.bs.modal', function (e) {
  $('body').removeClass('modal-open');
})

$('body').on('click', '.placeholders-enabled', function(){
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

$('.selectpicker').selectpicker();
$('.prev-red').prev().css('border-color','#f56b6b');
$('.action-summernote').summernote({height: 200});

pageUrl = 'index.php?route=ticketsystem/ticketactions/autocomplete&token=<?php echo $token; ?>&filter_value='
actions_row = <?php echo $key; ?>;
button_remove = '<?php echo $button_remove; ?>';
addhtml = '';
<?php foreach ($ticketActions as $value) { ?>
	addhtml += '<option value="<?php echo $value; ?>"><?php echo ${'text_responses_'.$value}; ?></option>';
<?php } ?>
</script>
