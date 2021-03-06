var actions_row, addhtml, button_remove, pageUrl, addHtmlObj, lastClicked;

function addAction() {
  actions_row++;
  
  html  = '<tr id="actions' + actions_row + '">'; 
  html += '    <td class="text-left">';
  html += '      <div class="input-group" style="max-width: 200px; display:inline-table"> ';
  html += '      <span class="input-group-addon"><i class="fa fa-check-circle-o"></i></span>';
  html += '      <select name="actions[' + actions_row + '][type]" class="form-control select-actions">';
  html += '         <option></option>';
  html += addhtml;
  html += '       </select>';
  html += '       </div>';
  html += '       <div class="selection-html"></div>';
  html += '    </td>';
  html += '    <td class="text-left" style="width:50px;">';
  // html += '       <button type="button" onclick="$(\'#actions' + actions_row + '\').remove();" data-toggle="tooltip" title="'+button_remove+'" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '       <button type="button" onclick="$(this).parents(\'tr\').remove();" data-toggle="tooltip" title="'+button_remove+'" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '</tr>';  
  
  $('#actions tbody').append(html);

  $('[data-toggle=\'tooltip\']').tooltip({container: 'body', html: true});
}

$(document).ready(function(){
	$('body').on('change', '.select-actions', function(){
		thisthis = this;
    //remove already exits elements - select
    $('.select-actions').each(function(){
      if(this.value == thisthis.value)
        $(this).not(thisthis).parents('tr').remove();
    })
		$.ajax({
		  url: pageUrl+thisthis.value+'&id='+$(thisthis).parents('tr').attr('id'),
		  dataType: 'html',     
		  success: function(html) {
		    $(thisthis).parent().next('.selection-html').html(html);
        $('.selectpicker').selectpicker();
        $('.action-summernote').summernote({height: 200});
		  }
		});
	})
})