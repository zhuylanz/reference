<?php if ($tickets) { ?>
<?php foreach ($tickets as $result) { ?>
<?php $result['customer'] = str_replace('"', '', $result['customer']); ?>
<tr class="td">
  <td class="text-center"><?php if (in_array($result['id'], $selected)) { ?>
    <input type="checkbox" name="selected[]" value="<?php echo $result['id']; ?>" checked="checked" />
    <?php } else { ?>
    <input type="checkbox" name="selected[]" value="<?php echo $result['id']; ?>" />
    <?php } ?></td>
  <td class="text-left"><button type="button" class="btn <?php echo ($ts_update_status_to_delete==$result['statusId'] ? 'btn-warning' : ($ts_update_status_to_spam==$result['statusId'] ? 'btn-danger' : 'btn-default' )); ?> btn-sm" disabled><!--<b><?php echo $text_ticket_status;?> :</b> --><?php echo $result['status'] ;?></button></td>
  <td class="text-left"><button type="button" class="btn btn-default btn-sm" disabled><!--<b><?php echo $text_ticket_priority;?> :</b> --><?php echo $result['priority'] ;?></button></td>
  <td class="text-left"><div class="col-sm-3">
      <h4>
        <?php if($result['customer']){ ?>
        <span class="text-info"><?php echo $result['customer'] ;?></span>
        <?php }else{ ?>
        <span class="text-danger"><b><i><?php echo $text_none;?></i></b></span>
        <?php } ?>
      </h4>
      <span class="text-default"><?php echo $result['date_added'] ;?></span><br/>
    </div>
    <div class="col-sm-12">
      <button type="button" class="btn btn-default btn-sm" disabled><b><?php echo $text_basic_id;?> :</b> <?php echo $result['id'] ;?></button>
      <?php if($result['agent']){ ?>
      <button type="button" class="btn btn-default btn-sm" disabled><b><?php echo $text_assign_to_agent;?> :</b> <?php echo $result['agent'] ;?></button>
      <?php }else{ ?>
      <button type="button" class="btn btn-danger btn-sm" disabled><b><?php echo $text_unassigned;?></b></button>
      <?php } ?>
      <button type="button" class="btn btn-default btn-sm" disabled><b><?php echo $text_ticket_type;?> :</b> <?php echo $result['type'] ;?></button>
    </div>
    <div class="col-sm-12 text-warning">
      <h4><span class="fa">Q</span> <?php echo $result['subject']; ?></h4>
    </div></td>
  <td class="text-center">
  <a class="btn btn-primary btn-sm pull-right" href="<?php echo $result['edit']; ?>"><i class="fa fa-pencil"></i></a>
  </td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
  <td class="text-center" colspan="3"><?php echo $text_no_results; ?></td>
</tr>
<?php } ?>
<tr>
  <td colspan="3" style="border: 1px solid #fff;padding: 10px 0px 0px 0px;"><span class="text-left"><?php echo $pagination; ?></span> <span class="text-right pull-right"><?php echo $results; ?></span></td>
</tr>
<style>
  .td td.text-center {
    border-bottom: 1px solid #ddd;
}
.td td.text-left {
    border-bottom: 1px solid #ddd;
}
.td .btn-default:hover {
	color:#000
}
  </style>
