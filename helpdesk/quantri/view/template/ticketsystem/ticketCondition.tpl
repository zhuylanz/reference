<?php if ($error_condition) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_condition; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
<?php } ?>
<table id="<?php echo $conditionsId; ?>" class="table table-striped table-bordered table-hover" style="display: table;">
    <thead>
      <tr>
        <td colspan="2">
        <?php echo ${'text_info_'.$conditionsId.'2'}; ?>
        </td>
      </tr>
    </thead>
    <tbody>
      <?php $key = 0; ?>
      <?php if($conditions){ ?>
        <?php foreach ($conditions as $key => $condition) { ?>
            <tr id="<?php echo $conditionsId.$key; ?>">  
              <td class="text-left">
                <div class="input-group ticketsystem-input-group">      
                  <span class="input-group-addon">
                    <i class="fa fa-bell"></i>
                  </span>
                  <select name="<?php echo $conditionsId; ?>[<?php echo $key; ?>][type]" class="form-control select-conditions selectpicker" title="<?php echo $text_condition_title ;?>">
                    <option></option>  
                    <?php foreach ($ticketConditions as $name => $ticketCondition) { ?>
                      <optgroup label="<?php echo ${'text_condition_name_'.$name}; ?>">
                      <?php foreach ($ticketCondition as $value) { ?>
                        <option value="<?php echo $value; ?>" <?php echo $condition['type']==$value ? 'selected' : false;?>><?php echo ${'text_condition_'.$value}; ?></option>
                      <?php } ?>
                      </optgroup>
                    <?php } ?>
                  </select>
                </div>  
                <div class="selection-html condition-select-html">
                  <?php echo isset($condition['html']) ? $condition['html'] : false; ?>
                </div>
              </td>
              <td class="text-left" style="width:50px;">
                <button onclick="$('#<?php echo $conditionsId.$key; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
              </td>
            </tr>
        <?php } ?>
      <?php }else{ ?>
        <tr id="<?php echo $conditionsId.$key; ?>">  
          <td class="text-left">
            <div class="input-group ticketsystem-input-group">      
              <span class="input-group-addon">
                <i class="fa fa-bell"></i>
              </span>
              <select name="<?php echo $conditionsId; ?>[<?php echo $key; ?>][type]" class="form-control select-conditions selectpicker" title="<?php echo $text_condition_title ;?>">  
                <option></option>  
                <?php foreach ($ticketConditions as $name => $ticketCondition) { ?>
                  <optgroup label="<?php echo ${'text_condition_name_'.$name}; ?>">
                  <?php foreach ($ticketCondition as $value) { ?>
                    <option value="<?php echo $value; ?>"><?php echo ${'text_condition_'.$value}; ?></option>
                  <?php } ?>
                  </optgroup>
                <?php } ?>
              </select>
            </div>  
            <div class="selection-html condition-select-html"></div>
          </td>
          <td class="text-left" style="width:50px;">
            <button onclick="$('#<?php echo $conditionsId.$key; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger" type="button"><i class="fa fa-minus-circle"></i></button>
          </td>
        </tr>
      <?php } ?>

    </tbody>
  <tfoot>
    <tr>
      <td colspan="2" class="text-right"><button type="button" onclick="addCondition('<?php echo $conditionsId; ?>');" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
    </tr>
  </tfoot>
</table>
<script type="text/javascript">
$('.selectpicker').selectpicker();
$('.prev-red').prev().css('border-color','#f56b6b');
$('.date').datetimepicker({
  pickTime: false
});

conditionPageUrl = 'index.php?route=ticketsystem/ticketconditions/autocomplete&token=<?php echo $token; ?>&filter_value='
conditions_row = <?php echo $key; ?>;
button_remove = '<?php echo $button_remove; ?>';
conditionTitle = '<?php echo $text_condition_title ;?>';
conditionAddhtml = '';

<?php foreach ($ticketConditions as $name => $ticketCondition) { ?>
   conditionAddhtml += '<optgroup label="<?php echo ${'text_condition_name_'.$name}; ?>">';
  <?php foreach ($ticketCondition as $value) { ?>
	 conditionAddhtml += '<option value="<?php echo $value; ?>"><?php echo ${'text_condition_'.$value}; ?></option>';
  <?php } ?>
   conditionAddhtml += '</optgroup>';
<?php } ?>
</script>
