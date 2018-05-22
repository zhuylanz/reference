<?php if ($error_blank_event) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_blank_event; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
<?php } ?>
<table id="<?php echo $eventsId; ?>" class="table table-striped table-bordered table-hover" style="display: table;">
    <thead>
      <tr>
        <td colspan="2">
        <?php echo ${'text_info_events2'}; ?>
        </td>
      </tr>
    </thead>
    <tbody>
      <?php $key = 0; ?>
      <?php if($events){ ?>
        <?php foreach ($events as $key => $event) { ?>
            <tr id="<?php echo $eventsId.$key; ?>">  
              <td class="text-left">
                <div class="input-group ticketsystem-input-group">      
                  <span class="input-group-addon">
                    <i class="fa fa-link"></i>
                  </span>
                  <select name="<?php echo $eventsId; ?>[<?php echo $key; ?>][type]" class="form-control select-events selectpicker" title="<?php echo $text_event_title ;?>">
                    <option></option>  
                    <?php foreach ($ticketEvents as $value) { ?>
                      <option value="<?php echo $value; ?>" <?php echo $event['type']==$value ? 'selected' : false;?>><?php echo ${'text_event_'.$value}; ?></option>
                    <?php } ?>
                  </select>
                </div>  
                <div class="selection-html event-select-html">
                  <?php echo isset($event['html']) ? $event['html'] : false; ?>
                </div>
              </td>
              <td class="text-left" style="width:50px;">
                <button onclick="$('#<?php echo $eventsId.$key; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
              </td>
            </tr>
        <?php } ?>
      <?php }else{ ?>
        <tr id="<?php echo $eventsId.$key; ?>">  
          <td class="text-left">
            <div class="input-group ticketsystem-input-group">      
              <span class="input-group-addon">
                <i class="fa fa-link"></i>
              </span>
              <select name="<?php echo $eventsId; ?>[<?php echo $key; ?>][type]" class="form-control select-events selectpicker" title="<?php echo $text_event_title ;?>">  
                <option></option>  
                <?php foreach ($ticketEvents as $value) { ?>
                    <option value="<?php echo $value; ?>"><?php echo ${'text_event_'.$value}; ?></option>
                <?php } ?>
              </select>
            </div>  
            <div class="selection-html event-select-html"></div>
          </td>
          <td class="text-left" style="width:50px;">
            <button onclick="$('#<?php echo $eventsId.$key; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
          </td>
        </tr>
      <?php } ?>

    </tbody>
  <tfoot>
    <tr>
      <td colspan="2" class="text-right"><button type="button" onclick="addEvent('<?php echo $eventsId; ?>');" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
    </tr>
  </tfoot>
</table>
<script type="text/javascript">
// $('.selectpicker').selectpicker();
$('.prev-red').prev().css('border-color','#f56b6b');
$('.date').datetimepicker({
  pickTime: false
});

eventPageUrl = 'index.php?route=ticketsystem/ticketevents/autocomplete&token=<?php echo $token; ?>&filter_value='
events_row = <?php echo $key; ?>;
button_remove = '<?php echo $button_remove; ?>';
eventTitle = '<?php echo $text_event_title ;?>';
eventAddhtml = '';

<?php foreach ($ticketEvents as $value) { ?>
	 eventAddhtml += '<option value="<?php echo $value; ?>"><?php echo ${'text_event_'.$value}; ?></option>';
<?php } ?>
</script>
