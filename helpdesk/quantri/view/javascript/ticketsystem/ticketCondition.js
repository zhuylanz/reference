var conditions_row, conditionAddhtml, button_remove, conditionPageUrl, conditionTitle;

function addCondition(conditionsId) {
  conditions_row++;
  
  html  = '<tr id="'+ conditionsId + conditions_row + '">'; 
  html += '    <td class="text-left">';
  html += '      <div class="input-group" style="max-width: 200px; display:inline-table"> ';
  html += '      <span class="input-group-addon"><i class="fa fa-bell"></i></span>';
  html += '      <select name="'+ conditionsId +'[' + conditions_row + '][type]" class="form-control select-conditions selectpicker" title="'+ conditionTitle +'">';
  html += '         <option></option>';
  html += conditionAddhtml;
  html += '       </select>';
  html += '       </div>';
  html += '       <div class="selection-html condition-select-html"></div>';
  html += '    </td>';
  html += '    <td class="text-left" style="width:50px;">';
  html += '       <button type="button" onclick="$(this).parents(\'tr\').remove();" data-toggle="tooltip" title="'+button_remove+'" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '</tr>';  
  
  $('#' + conditionsId +' tbody').append(html);

  $('[data-toggle=\'tooltip\']').tooltip({container: 'body', html: true});
  $('.selectpicker').selectpicker();
}

$(document).ready(function(){
	$('body').on('change', '.select-conditions', function(){
		thisthis = this;
    //remove already exits elements - select
    $(this).parents('table').find('.select-conditions').each(function(){
      if(this.value == thisthis.value)
        $(this).not(thisthis).parents('tr').remove();
    })
		$.ajax({
		  url: conditionPageUrl+thisthis.value+'&id='+$(thisthis).parents('tr').attr('id')+'&name='+$(thisthis).parents('table').attr('id'),
		  dataType: 'html',     
		  success: function(html) {
		    $(thisthis).parent().next('.selection-html').html(html);
        $('.selectpicker').selectpicker();
        $('.date').datetimepicker({
          pickTime: false
        });
		  }
		});
	})
})