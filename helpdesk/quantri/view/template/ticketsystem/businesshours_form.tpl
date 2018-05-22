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
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>            
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-description"><?php echo $entry_description; ?></label>
            <div class="col-sm-10">
              <textarea name="description" placeholder="<?php echo $entry_description; ?>" id="input-description" class="form-control"><?php echo $description; ?></textarea>
              <?php if ($error_description) { ?>
              <div class="text-danger"><?php echo $error_description; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-timezone"><?php echo $entry_timezone; ?></label>
            <div class="col-sm-10">
              <select name="timezone" id="input-timezone" class="form-control">
                <?php foreach($timezones as $key=>$value){ ?>
                  <option value="<?php echo $key; ?>" <?php echo $timezone==$key ? 'selected' : ''; ?>><?php echo $value; ?></option>
                <?php } ?>
              </select>
            </div>
          </div> 

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-business">
              <?php echo $entry_business_hours; ?> & <?php echo $entry_business_holidays; ?>
              <br/>
              <br/>
              <ul class="nav nav-pills nav-stacked" id="businesshourstab">
                <li class="active"><a href="#tab-businesshour" data-toggle="tab"><?php echo $entry_business_hours; ?></a></li>
                <li><a href="#tab-businessholidays" data-toggle="tab"><?php echo $entry_business_holidays; ?></a></li>
              </ul>
            </label>
            <div class="col-sm-10">
              <div class="tab-content">
                <div class="tab-pane active" id="tab-businesshour">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <i class="fa fa-clock-o"></i> <?php echo $text_info_businesshours; ?>
                    </div>
                    <div class="table-responsive business-table">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <td></td>
                            <?php foreach ($weekDays as $key => $days) { ?>
                              <td class="text-center"><?php echo ${'text_'.$days} ;?></td>
                            <?php } ?>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td class="business-table-td">
                              <div class="timings">
                                <?php foreach ($timings as $key => $time) { ?>
                                  <span class="time"> <?php echo $key%2 ? '' : $time ;?> <span class="border"></span></span>
                                <?php } ?>
                              </div>
                            </td>
                            <?php $noOfSlots = 0; ?>

                            <?php foreach ($weekDays as $key => $weekDay) { ?>
                              <td>
                                <div class="business-days<?php echo isset($daysTimings[$weekDay]) ? '' : ' closed'; ?>" id="<?php echo $weekDay; ?>" >
                                  <?php foreach ($daysTimings as $nameOfDays => $day) { ?>
                                    <?php if($nameOfDays==$weekDay){ ?>
                                      <?php foreach ($day as $noOfSlots => $slots) { ?>
                                      <div class="resize-drag" id="resize-<?php echo $weekDay.$noOfSlots; ?>" style="height:<?php echo $daysSizes[$nameOfDays][$noOfSlots]; ?>px;transform: translate(0px,<?php echo $daysPositions[$nameOfDays][$noOfSlots]; ?>px);" data-y="<?php echo $daysPositions[$nameOfDays][$noOfSlots]; ?>">
                                        <span class="business-days-close"><i class="fa fa-times"></i></span>
                                        <input type="hidden" name="businesshours[days][<?php echo $weekDay; ?>][]" id="days-<?php echo $weekDay.$noOfSlots; ?>" value="<?php echo $slots; ?>">
                                        <input type="hidden" name="businesshours[position][<?php echo $weekDay; ?>][]" id="dayspostion-<?php echo $weekDay.$noOfSlots; ?>" value="<?php echo $daysPositions[$nameOfDays][$noOfSlots]; ?>">
                                        <input type="hidden" name="businesshours[size][<?php echo $weekDay; ?>][]" id="dayssize-<?php echo $weekDay.$noOfSlots; ?>" value="<?php echo $daysSizes[$nameOfDays][$noOfSlots]; ?>">
                                        <span class="handle top"><i class="fa fa-caret-up"></i></span>
                                        <span class="slot"><?php echo $slots; ?></span>
                                        <span class="handle bottom"><i class="fa fa-caret-down"></i></span>
                                      </div>
                                      <?php } ?>
                                    <?php } ?>
                                  <?php } ?>
                                  <!-- <div class="resize-drag" id="resize-<?php echo $weekDay.$key; ?>">
                                    <span class="business-days-close"><i class="fa fa-times"></i></span>
                                    <input type="hidden" name="businesshours[days][<?php echo $weekDay; ?>][]" id="days-<?php echo $weekDay.$key; ?>" value="">
                                    <input type="hidden" name="businesshours[position][<?php echo $weekDay; ?>][]" id="dayspostion-<?php echo $weekDay.$key; ?>" value="">
                                    <input type="hidden" name="businesshours[size][<?php echo $weekDay; ?>][]" id="dayssize-<?php echo $weekDay.$key; ?>" value="">
                                    <span class="handle top"><i class="fa fa-caret-up"></i></span>
                                    <span class="slot">00:00 - 00:30</span>
                                    <span class="handle bottom"><i class="fa fa-caret-down"></i></span>
                                  </div> -->

                                </div>
                              </td>
                            <?php } ?>
                          </tr>
                        </tbody>
                      </table>
                      <?php if ($error_businesshours) { ?>
                        <br/>
                        <div class="text-danger"><?php echo $error_businesshours; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                </div>

                <div class="tab-pane " id="tab-businessholidays">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <i class="fa fa-beer"></i> <?php echo $text_info_businessholidays; ?>
                    </div>
                    <div class="table-responsive business-table">
                      <table class="table">
                        <thead>
                          <tr>
                            <td colspan="2"><a class="btn btn-primary pull-right add-holiday"><?php echo $button_addholiday ;?></a></td>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $noOfholidays = 0; ?>

                          <?php if($holiday AND is_array($holiday)){ ?>
                            <?php $holiday = array_values($holiday); ?>
                            <?php foreach ($holiday as $noOfholidays => $value) { ?>
                              <tr class="trClass">
                                <td>
                                  <input type="text" name="holiday[<?php echo $noOfholidays ;?>][name]" value="<?php echo $value['name'] ;?>" placeholder="<?php echo $entry_business_holidays;?>" class="form-control"/>
                                </td>
                                <td>
                                  <div class="input-group date pull-right">
                                    <input type="text" name="holiday[<?php echo $noOfholidays ;?>][from_date]" value="<?php echo $value['from_date'];?>" class="form-control" data-date-format="YYYY-MM-DD"/>
                                    <span class="input-group-btn">
                                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                  </div>
                                  <div class="input-group date pull-right">
                                    <input type="text" name="holiday[<?php echo $noOfholidays ;?>][to_date]" value="<?php echo $value['to_date'];?>" class="form-control" data-date-format="YYYY-MM-DD"/>
                                    <span class="input-group-btn">
                                      <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                  </div>
                                </td>
                              </tr>
                              <tr>
                                <td colspan="2">
                                <?php if ($error_holiday AND isset($error_holiday[$noOfholidays])) { ?>
                                  <div class="text-danger pull-left"><?php echo $error_holiday[$noOfholidays]; ?></div>
                                <?php } ?>
                                <a class="btn btn-danger pull-right" onclick="$(this).parents('tr').prev().remove();$(this).parents('tr').remove();"><?php echo $button_remove ;?></a></td>
                              </tr>
                            <?php } ?>
                          <?php } ?>
                          
                        </tbody>
                      </table>
                      <?php if ($error_businesshours) { ?>
                        <br/>
                        <div class="text-danger"><?php echo $error_businesshours; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--

$('.date').datetimepicker({
  pickTime: false,
  minDate: moment()
});

noOfholidays = '<?php echo $noOfholidays ;?>';
holidatHtml = '<tr class="trClass"><td><input type="text" name="holiday[noOfholidays][name]" placeholder="<?php echo $entry_business_holidays.' '.$entry_name;?>" value="" class="form-control"/></td> <td class="tdClass"><div class="input-group date pull-right"><input type="text" name="holiday[noOfholidays][from_date]" value="" placeholder="<?php echo $entry_date_from;?>" class="form-control" data-date-format="YYYY-MM-DD"/><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div><div class="input-group date pull-right"><input type="text" name="holiday[noOfholidays][to_date]" placeholder="<?php echo $entry_date_to;?>" value="" class="form-control" data-date-format="YYYY-MM-DD"/><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td></tr><tr><td colspan="3"><a class="btn btn-danger pull-right" onclick="$(this).parents(\'tr\').prev().remove();$(this).parents(\'tr\').remove();"><?php echo $button_remove ;?></a></td> </tr>';

$('.add-holiday').on('click', function(event){
  noOfholidays++;
  $('#tab-businessholidays tbody').append(holidatHtml.replace(/noOfholidays/g,noOfholidays));
  $('.date').datetimepicker({
    pickTime: false
  });
});

var addSlot = true;
noOfSlots = '<?php echo $noOfSlots ;?>';

slotHtml = '<div class="resize-drag" id="resize-days-id-noOfSlots"> <span class="business-days-close"><i class="fa fa-times"></i></span> <input type="hidden" name="businesshours[days][days-id][]" id="days-days-id-noOfSlots"> <input type="hidden" name="businesshours[position][days-id][]" id="dayspostion-days-id-noOfSlots"><input type="hidden" name="businesshours[size][days-id][]" id="dayssize-days-id-noOfSlots" value=""> <span class="handle top"><i class="fa fa-caret-up"></i></span> <span class="slot">00:00 - 00:30</span> <span class="handle bottom"><i class="fa fa-caret-down"></i></span> </div>';

$('td').on('click', '.business-days', function(event){
  if(addSlot){
    noOfSlots++;
    $(this).removeClass('closed').prepend(slotHtml.replace(/days-id/g, $(this).attr('id')).replace(/-noOfSlots/g, noOfSlots));

    addSlot = true;
    return;

    console.log(event.clientY);
    element = $('.business-days')[0];
    var e = document.getElementsByClassName('.business-days');
    var offset = {x:0,y:0};
    e = e[0];
    while (e)
    {
        offset.x += e.offsetLeft;
        offset.y += e.offsetTop;
        e = e.offsetParent;
    }

    if (document.documentElement && (document.documentElement.scrollTop || document.documentElement.scrollLeft))
    {
        offset.x -= document.documentElement.scrollLeft;
        offset.y -= document.documentElement.scrollTop;
    }
    else if (document.body && (document.body.scrollTop || document.body.scrollLeft))
    {
        offset.x -= document.body.scrollLeft;
        offset.y -= document.body.scrollTop;
    }
    else if (window.pageXOffset || window.pageYOffset)
    {
        offset.x -= window.pageXOffset;
        offset.y -= window.pageYOffset;
    }

    alert(offset.x + '\n' + offset.y);

    console.log(element.offsetTop - element.scrollTop + element.clientTop);
    var parentPosition = getPosition(event.currentTarget);
    // console.log(parentPosition);

    var x = 0;
    var y = event.clientY;
    // var y = event.clientY + parentPosition.y;

    // x = 0;
    // y = event.screenY;

    // target.style.webkitTransform = target.style.transform =
    //     'translate(' + x + 'px,' + y + 'px)';

    // target.setAttribute('data-x', x);
    // target.setAttribute('data-y', y);

    $(this).find('#resize-'+$(this).attr('id')+noOfSlots).css({'webkitTransform':'translate(' + x + 'px,' + y + 'px)', 'transform':'translate(' + x + 'px,' + y + 'px)'}).attr({'data-x':x,'data-y': y});

  }
  addSlot = true;

  // console.log(addSlot);

})
 
function getPosition(element) {
    var xPosition = 0;
    var yPosition = 0;
      
    while (element) {
        xPosition += (element.offsetLeft - element.scrollLeft + element.clientLeft);
        yPosition += (element.offsetTop - element.scrollTop + element.clientTop);
        element = element.offsetParent;
    }
    return { x: xPosition, y: yPosition };
}


$(document).ready(function(){
  blanceHeight = $('.timings').css('height');
  $('.business-days').css('height',blanceHeight);
  $('.business-table-td').css('height',blanceHeight);

  $('.business-days').on('click', '.business-days-close', function(){
    // $(this).parents('.business-days').addClass('closed');
    $(this).parents('.resize-drag').remove();
  });
})
//--></script>
<script type="text/javascript" src="view/javascript/ticketsystem/interact-1.2.4.js"></script>
<script type="text/javascript">
gap = 25;
base = 50;

interact('.resize-drag')
  .dropzone({
    // only accept elements matching this CSS selector
    accept: '.business-days',
  })
  .draggable({
    snap: {
      targets: [
        interact.createSnapGrid({ x: 1, y: gap })
      ],
      // range: 40
    },
    inertia: true,
    restrict: {
      restriction: "parent",
      endOnly: true,
      elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
    },

    onmove: dragMoveListener,
    onend: function (event) {
      var textEl = event.target.querySelector('p');

      textEl && (textEl.textContent =
        'moved a distance of '
        + (Math.sqrt(event.dx * event.dx +
                     event.dy * event.dy)|0) + 'px');
    }
  })
  .resizable({
    accept: '.business-days',
    snap: {
      targets: [
        interact.createSnapGrid({ x: 1, y: gap })
      ],
    },
    edges: { left: false, right: false, bottom: true, top: true },
    restrict: {
      restriction: 'parent',
      // endOnly: true,
      // elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
    },
  })
  .on('resizemove', function (event) {
    var target = event.target,
        x = (parseFloat(target.getAttribute('data-x')) || 0),
        y = (parseFloat(target.getAttribute('data-y')) || 0);

    // update the element's style
    target.style.width  = event.rect.width + 'px';
    target.style.height = event.rect.height + 'px';

    // translate when resizing from top or left edges
    x += event.deltaRect.left;
    y += event.deltaRect.top;

    target.style.webkitTransform = target.style.transform =
        'translate(' + x + 'px,' + y + 'px)';

    target.setAttribute('data-x', x);
    target.setAttribute('data-y', y);

    setTimeText(y,event.rect.height,target);
});

function dragMoveListener (event) {
  addSlot = false;

  var target = event.target,
  // keep the dragged position in the data-x/data-y attributes
  x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx,
  y = (parseInt(target.getAttribute('data-y')) || 0) + event.dy;

  // translate the element
  target.style.webkitTransform =
  target.style.transform =
    'translate(' + x + 'px, ' + y + 'px)';

  // update the posiion attributes
  target.setAttribute('data-x', x);
  target.setAttribute('data-y', y);

  setTimeText(y, event.target.clientHeight+2 ,target);
}

function setTimeText(y,height,target){
    y = Math.ceil(y);
    height = Math.ceil(height);
    startTime = y/base;
    time = (height+y)/base;
    thisSlotHtml = target.innerHTML;

    target.innerHTML = thisSlotHtml  .replace(/<span class="slot">.+<\/span>/g, '<span class="slot">'+changeIntoTimeFormat(startTime) + ' - ' + changeIntoTimeFormat(time)+'</span>');

     document.getElementById('days-'+target.id.replace('resize-', '')).value = changeIntoTimeFormat(startTime) + ' - ' + changeIntoTimeFormat(time);
     document.getElementById('dayspostion-'+target.id.replace('resize-', '')).value = y;
     document.getElementById('dayssize-'+target.id.replace('resize-', '')).value = height;
}

function changeIntoTimeFormat(time){
  timeInteger = parseInt(time,10);
  time = time.toFixed(2).replace(/\.0[1-9]+/,'.00').replace(/\.[1-9]+/,'.3');
  if(time.match(/24\./, time))
    time = time.replace(/24\./,'00');
  return parseFloat(time).toFixed(2).replace(timeInteger, (timeInteger >= 10 ? (timeInteger == 24 ? 00 : timeInteger) : '0'+timeInteger)).replace('.',':');
}

</script>
</div>
<?php echo $footer; ?> 