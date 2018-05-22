<?php if ($tickets) { ?>
  <?php foreach ($tickets as $result) { ?>
  <?php $result['customer'] = str_replace('"', '', $result['customer']); ?>
  <tr>
    <td class="text-center"><?php if (in_array($result['id'], $selected)) { ?>
      <input type="checkbox" name="selected[]" value="<?php echo $result['id']; ?>" checked="checked" />
      <?php } else { ?>
      <input type="checkbox" name="selected[]" value="<?php echo $result['id']; ?>" />
      <?php } ?></td>
    <?php $sla_status_class = ((substr($result['response_time'],0,1)=='-') || (substr($result['response_time'],0,1)=='-') ? ' btn-danger' : ' btn-primary'); ?>
    <td class="text-left">
      <div class="col-sm-3">
        <?php $colors = array('green','blue','red'); ?>
        <?php shuffle($colors); ?>
        <div class="create-logo pull-left <?php echo $colors[0]; ?>" tabindex="0" data-trigger="focus" data-toggle="popover" title="<?php echo $text_customer_details;?>" data-content="<div><i class='fa fa-user'></i> <?php echo $result['customer']; ?><br/> <i class='fa fa-envelope'></i> <?php echo $result['customerEmail']; ?></div>" ><?php echo substr($result['customer'],0,1); ?></div>
        <h4>
        <?php if($result['customer']){ ?>
          <span class="text-info"><?php echo $result['customer'] ;?></span>
        <?php }else{ ?>
          <span class="text-danger"><b><i><?php echo $text_none;?></i></b></span>
        <?php } ?>
        </h4>
        <span class="text-default"><?php echo $result['date_added'] ;?></span><br/>
      </div>
      <div class="col-sm-9">
        <button type="button" class="btn btn-default btn-sm" disabled><b><?php echo $text_basic_id;?> :</b> <?php echo $result['id'] ;?></button>
        <?php if($result['agent']){ ?>
          <button type="button" class="btn btn-default btn-sm" disabled><b><?php echo $text_assign_to_agent;?> :</b> <?php echo $result['agent'] ;?></button>
        <?php }else{ ?>
          <button type="button" class="btn btn-danger btn-sm" disabled><b><?php echo $text_unassigned;?></b></button>
        <?php } ?>
          <button type="button" class="btn btn-default btn-sm" disabled><b><?php echo $text_ticket_priority;?> :</b> <?php echo $result['priority'] ;?></button>
          <button type="button" class="btn btn-default btn-sm" disabled><b><?php echo $text_ticket_status;?> :</b> <?php echo $result['status'] ;?></button>
          <button type="button" class="btn btn-default btn-sm" disabled><b><?php echo $text_ticket_type;?> :</b> <?php echo $result['type'] ;?></button>
          <a class="btn<?php echo $sla_status_class; ?> btn-sm pull-right" href="<?php echo $result['edit']; ?>"><i class="fa fa-pencil"></i></a>
      </div>
      <div class="col-sm-12 text-warning">
        <h4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="fa">Q</span> <?php echo $result['subject']; ?></h4>
        <div class="sla-info hide">
          <?php if($result['response_time']){ ?>
            <button type="button" class="btn btn-sm <?php echo (substr($result['response_time'],0,1)=='+') ? 'btn-success' : 'btn-danger';?>" data-toggle="tooltip" title="<?php echo $text_response_time;?>"><?php echo $result['response_time'] ;?></button>
          <?php } ?>
          <?php if($result['resolve_time']){ ?>
            <button type="button" class="btn btn-sm <?php echo (substr($result['resolve_time'],0,1)=='+') ? 'btn-success' : 'btn-danger';?>" data-toggle="tooltip" title="<?php echo $text_resolve_time;?>"><?php echo $result['resolve_time'] ;?></button>
          <?php } ?>
        </div>
      </div>
    </td>
  </tr>
  <?php } ?>
<?php } else { ?>
  <tr>
    <td class="text-center" colspan="3"><?php echo $text_no_results; ?></td>
  </tr>
<?php } ?>
  <tr>
    <td colspan="3" style="border: 1px solid #fff;padding: 10px 0px 0px 0px;">
      <span class="text-left"><?php echo $pagination; ?></span>
      <span class="text-right pull-right"><?php echo $results; ?></span>
   </td>
  </tr>