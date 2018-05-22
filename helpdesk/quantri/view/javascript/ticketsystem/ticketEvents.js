var events_row, eventAddhtml, button_remove, eventPageUrl, eventTitle;

function addEvent(eventsId) {
  events_row++;
  
  html  = '<tr id="'+ eventsId + events_row + '">'; 
  html += '    <td class="text-left">';
  html += '      <div class="input-group" style="max-width: 200px; display:inline-table"> ';
  html += '      <span class="input-group-addon"><i class="fa fa-link"></i></span>';
  html += '      <select name="'+ eventsId +'[' + events_row + '][type]" class="form-control select-events selectpicker" title="'+ eventTitle +'">';
  html += '         <option></option>';
  html += eventAddhtml;
  html += '       </select>';
  html += '       </div>';
  html += '       <div class="selection-html event-select-html"></div>';
  html += '    </td>';
  html += '    <td class="text-left" style="width:50px;">';
  html += '       <button type="button" onclick="$(this).parents(\'tr\').remove();" data-toggle="tooltip" title="'+button_remove+'" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '</tr>';  
  
  $('#' + eventsId +' tbody').append(html);

  $('[data-toggle=\'tooltip\']').tooltip({container: 'body', html: true});
  $('.selectpicker').selectpicker();
}

$(document).ready(function(){
	$('body').on('change', '.select-events', function(){
		thisthis = this;
    //remove already exits elements - select
    $(this).parents('table').find('.select-events').each(function(){
      if(this.value == thisthis.value)
        $(this).not(thisthis).parents('tr').remove();
    })
		$.ajax({
		  url: eventPageUrl+thisthis.value+'&id='+$(thisthis).parents('tr').attr('id')+'&name='+$(thisthis).parents('table').attr('id'),
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