<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid ticketsView">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <script>localStorage.setItem('tabTicket',false);</script>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading" style="overflow: visible;  min-height: 95px;">
        <?php $colors = array('green','blue','red'); ?>
        <?php shuffle($colors); ?>
        <?php $ticket_info['customerName'] = str_replace('"', '', $ticket_info['customerName']); ?>
        <div class="create-logo pull-left <?php echo $colors[0]; ?>" tabindex="0" data-trigger="focus" data-toggle="popover"  title="<?php echo $text_customer_details;?>" data-content="<div><a href='<?php echo $ticket_info['customerLink']; ?>'><i class='fa fa-user'></i> <?php echo $ticket_info['customerName']; ?></a><br/> <i class='fa fa-envelope'></i> <?php echo $ticket_info['customerEmail']; ?></div>" ><?php echo substr($ticket_info['customerName'],0,1); ?></div>
        <div class="pull-left ticket-view-head-left">
          <h2 data-toggle="tooltip" title="<?php echo $ticket_info['subject']; ?>"><?php echo substr($ticket_info['subject'],0,22).' ..'; ?></h2>
          <i class="fa fa-clock-o"></i> <span class="hidden-xs hidden-sm hidden-md"><b><?php echo $text_created; ?>- </span></b> <?php echo $ticket_info['date_added']; ?>&nbsp;&nbsp;&nbsp;
          <br/>
          <i class="fa fa-user"></i> <span class="hidden-xs hidden-sm hidden-md"><b><?php echo $text_customer; ?>- </span></b> <?php echo $ticket_info['customerName']; ?> <i class="fa fa-chevron-left"></i><a href="mail:<?php echo $ticket_info['customerEmail']; ?>" target="_blank"><?php echo $ticket_info['customerEmail']; ?></a><i class="fa fa-chevron-right"></i>
        </div>
        <div class="hidden-lg clearfix"></div>
        <form class="form-inline text-right">
          <div class="form-group">
            <label for="tsPriority"><?php echo $text_ticket_priority; ?> - </label>
            <?php if(in_array('tickets.update',$ts_roles)){ ?>
              <select id="tsPriority" class="form-control update-ticket">
                <option></option>
                <?php foreach ($ts_priorities as $priority) { ?>
                  <option value="<?php echo $priority['id']; ?>" <?php echo ($ticket_info['priority']==$priority['id']) ? 'selected' : false ;?>><?php echo $priority['name']; ?></option>
                <?php } ?>
              </select>
            <?php }else{ ?>
            <button type="button" class="btn btn-default" ><?php echo $ticket_info['priorityName']; ?></button>
            <?php } ?>
            <span class="hidden-xs hidden-sm hidden-md">&nbsp;&nbsp;&nbsp;</span>
          </div>
          <div class="form-group">
            <label for="tsStatus"><?php echo $text_ticket_status; ?> - </label>
            <?php if(in_array('tickets.update',$ts_roles)){ ?>
              <select id="tsStatus" class="form-control update-ticket">
                <option></option>
                <?php foreach ($ts_statuss as $status) { ?>
                  <option value="<?php echo $status['id']; ?>" <?php echo ($ticket_info['status']==$status['id']) ? 'selected' : false ;?>><?php echo $status['name']; ?></option>
                <?php } ?>
              </select>
            <?php }else{ ?>
            <button type="button" class="btn btn-default" ><?php echo $ticket_info['statusName']; ?></button>
            <?php } ?>
            <span class="hidden-xs hidden-sm hidden-md">&nbsp;&nbsp;&nbsp;</span>
          </div>
          <div class="form-group">
            <label for="tsType"><?php echo $text_ticket_type; ?> - </label>
            <?php if(in_array('tickets.update',$ts_roles)){ ?>
              <select id="tsType" class="form-control update-ticket">
                <option></option>
                <?php foreach ($ts_types as $type) { ?>
                  <option value="<?php echo $type['id']; ?>" <?php echo ($ticket_info['type']==$type['id']) ? 'selected' : false ;?>><?php echo $type['name']; ?></option>
                <?php } ?>
            </select>
            <?php }else{ ?>
            <button type="button" class="btn btn-default" ><?php echo $ticket_info['typeName']; ?></button>
            <?php } ?>
          </div>
          <div class="form-group">
            <a class="btn btn-primary" title="<?php echo $text_previous; ?>" data-toggle="tooltip" <?php echo $prevId ? 'href='.$prevId : 'disabled'; ?>><i class="fa fa-chevron-left"></i></a>
            <a class="btn btn-primary" title="<?php echo $text_next; ?>" data-toggle="tooltip" <?php echo $nextId ? 'href='.$nextId : 'disabled'; ?>><i class="fa fa-chevron-right"></i></a>
          </div>
          <div class="form-group">
            <?php if($response_time){ ?>
              <button type="button" class="btn btn-sm <?php echo (substr($response_time,0,1)=='+') ? 'btn-success' : 'btn-danger';?>" data-toggle="tooltip" title="<?php echo $text_response_time;?>"  style="margin-top:5px;"><?php echo $response_time ;?></button>
            <?php } ?>
            <?php if($resolve_time){ ?>
              <button type="button" class="btn btn-sm <?php echo (substr($resolve_time,0,1)=='+') ? 'btn-success' : 'btn-danger';?>" data-toggle="tooltip" title="<?php echo $text_resolve_time;?>"  style="margin-top:5px;"><?php echo $resolve_time ;?></button>
            <?php } ?>
          </div>
        </form>
      </div>
      <div class="panel-body">
        <div class="ticketFilter col-sm-2  ticket-left-clm">
          <form class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-12 control-label" for="input-assign_agent"><?php echo $text_assign_agent; ?></label>
              <div class="col-sm-12">
                <?php if(in_array('tickets.assign',$ts_roles)){ ?>
                  <select class="form-control update-ticket" id="input-assign_agent">
                    <option></option>
                    <?php foreach ($ts_agents as $agent) { ?>
                      <option value="<?php echo $agent['id']; ?>" <?php echo $agent['id']==$ticket_info['assign_agent'] ? 'selected' : false; ?>><?php echo $agent['name_alias'] ? $agent['name_alias'] : $agent['username']; ?></option>
                    <?php } ?>
                  </select>
                <?php }else{ ?>
                  <button type="button" class="btn bnt-sm btn-default" ><?php echo $ticket_info['agentAliasName'] ? $ticket_info['agentAliasName'] : $ticket_info['agentName']; ?> &lt;<?php echo $ticket_info['agentEmail']; ?>></button>
                <?php } ?>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-12 control-label" for="input-agent"><?php echo $text_assign_group; ?></label>
              <div class="col-sm-12">
                <?php if(in_array('tickets.update',$ts_roles)){ ?>
                  <select class="form-control update-ticket" id="input-group">
                    <option></option>
                    <?php foreach ($ts_groups as $group) { ?>
                      <option value="<?php echo $group['id']; ?>" <?php echo $group['id']==$ticket_info['group'] ? 'selected' : false; ?>><?php echo $group['name']; ?></option>
                    <?php } ?>
                  </select>
                <?php }else{ ?>
                  <button type="button" class="btn bnt-sm btn-default" ><?php echo $ticket_info['groupName']; ?></button>
                <?php } ?>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-12 control-label" for="input-tags"><?php echo $text_tags; ?></label>
              <div class="col-sm-12">
                <select class="form-control selectpicker tags-select" name="tags-select[]" id="input-tags" multiple data-live-search="true">
                  <option></option>
                  <?php foreach ($ts_tags as $tags) { ?>
                    <option value="<?php echo $tags['id']; ?>" <?php echo in_array($tags['id'], $ticket_info['tags']) ? 'selected' : false; ?>><?php echo $tags['name']; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="form-group notes-entry">
              <label class="col-sm-12 control-label" for="input-notes"><?php echo $text_ticketnotes; ?></label>
              <div class="col-sm-12">
                <input type="text" id="input-notes" class="form-control add-notes"/>
              </div>
              <?php foreach ($ticket_notes as $note) { ?>
              <div class="col-sm-12 note-div">
                <input type="checkbox" id="note-<?php echo $note['id']; ?>" class="note-checkbox" value="1"  <?php echo $note['completed'] ? 'checked' : false; ?>> <label for="note-<?php echo $note['id']; ?>"><?php echo $note['completed'] ? '<s>' : false; ?><?php echo $note['note']; ?><?php echo $note['completed'] ? '</s>' : false; ?></label>
                <span class="pull-right hide withDeleteIcon cursor-pointer" id="delete-note-<?php echo $note['id']; ?>"><i class="fa fa-trash-o"></i></span>
              </div>
              <?php } ?>
            </div>
          </form>
          
          <form class="form-horizontal" method="post" action="<?php echo $action_custom_fields; ?>" enctype="multipart/form-data">
            <div id="ticket-custom-fields">
              <br/>
              <label><?php echo $text_custom_fields; ?></label>
              <?php foreach ($custom_fields as $custom_field) { ?>
              <?php if ($custom_field['location'] == 'tickets') { ?>
              <?php if ($custom_field['type'] == 'select') { ?>
              <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
                <label class="col-sm-12 control-label" for="input-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
                <div class="col-sm-12">
                  <select name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" id="input-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control">
                    <option value=""><?php echo $text_select; ?></option>
                    <?php foreach ($custom_field['custom_field_value'] as $custom_field_value) { ?>
                    <?php if (isset($tickets[$custom_field['custom_field_id']]) && $custom_field_value['custom_field_value_id'] == $tickets[$custom_field['custom_field_id']]) { ?>
                    <option value="<?php echo $custom_field_value['custom_field_value_id']; ?>" selected="selected"><?php echo $custom_field_value['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $custom_field_value['custom_field_value_id']; ?>"><?php echo $custom_field_value['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
                  <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <?php if ($custom_field['type'] == 'radio') { ?>
              <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
                <label class="col-sm-12 control-label"><?php echo $custom_field['name']; ?></label>
                <div class="col-sm-12">
                  <div>
                    <?php foreach ($custom_field['custom_field_value'] as $custom_field_value) { ?>
                    <div class="radio">
                      <?php if (isset($tickets[$custom_field['custom_field_id']]) && $custom_field_value['custom_field_value_id'] == $tickets[$custom_field['custom_field_id']]) { ?>
                      <label>
                        <input type="radio" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo $custom_field_value['custom_field_value_id']; ?>" checked="checked" />
                        <?php echo $custom_field_value['name']; ?></label>
                      <?php } else { ?>
                      <label>
                        <input type="radio" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo $custom_field_value['custom_field_value_id']; ?>" />
                        <?php echo $custom_field_value['name']; ?></label>
                      <?php } ?>
                    </div>
                    <?php } ?>
                  </div>
                  <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
                  <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <?php if ($custom_field['type'] == 'checkbox') { ?>
              <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
                <label class="col-sm-12 control-label"><?php echo $custom_field['name']; ?></label>
                <div class="col-sm-12">
                  <div>
                    <?php foreach ($custom_field['custom_field_value'] as $custom_field_value) { ?>
                    <div class="checkbox">
                      <?php if (isset($tickets[$custom_field['custom_field_id']]) && in_array($custom_field_value['custom_field_value_id'], $tickets[$custom_field['custom_field_id']])) { ?>
                      <label>
                        <input type="checkbox" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>][]" value="<?php echo $custom_field_value['custom_field_value_id']; ?>" checked="checked" />
                        <?php echo $custom_field_value['name']; ?></label>
                      <?php } else { ?>
                      <label>
                        <input type="checkbox" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>][]" value="<?php echo $custom_field_value['custom_field_value_id']; ?>" />
                        <?php echo $custom_field_value['name']; ?></label>
                      <?php } ?>
                    </div>
                    <?php } ?>
                  </div>
                  <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
                  <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <?php if ($custom_field['type'] == 'text') { ?>
              <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
                <label class="col-sm-12 control-label" for="input-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
                <div class="col-sm-12">
                  <input type="text" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo (isset($tickets[$custom_field['custom_field_id']]) ? $tickets[$custom_field['custom_field_id']] : $custom_field['value']); ?>" placeholder="<?php echo $custom_field['name']; ?>" id="input-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control" />
                  <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
                  <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <?php if ($custom_field['type'] == 'textarea') { ?>
              <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
                <label class="col-sm-12 control-label" for="input-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
                <div class="col-sm-12">
                  <textarea name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" rows="5" placeholder="<?php echo $custom_field['name']; ?>" id="input-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control"><?php echo (isset($tickets[$custom_field['custom_field_id']]) ? $tickets[$custom_field['custom_field_id']] : $custom_field['value']); ?></textarea>
                  <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
                  <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <?php if ($custom_field['type'] == 'file') { ?>
              <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
                <label class="col-sm-12 control-label"><?php echo $custom_field['name']; ?></label>
                <div class="col-sm-12">
                  <label type="button" for="button-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="btn btn-default"><i class="fa fa-upload"></i> <?php echo $button_upload; ?></label>
                  <input type="file" name="custom_field<?php echo $custom_field['custom_field_id']; ?>" id="button-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="hide"/>
                  <?php if (isset(${'error_custom_field'.$custom_field['custom_field_id']})) { ?>
                  <div class="text-danger"><?php echo ${'error_custom_field'.$custom_field['custom_field_id']}; ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <?php if ($custom_field['type'] == 'date') { ?>
              <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
                <label class="col-sm-12 control-label" for="input-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
                <div class="col-sm-12">
                  <div class="input-group date">
                    <input type="text" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo (isset($tickets[$custom_field['custom_field_id']]) ? $tickets[$custom_field['custom_field_id']] : $custom_field['value']); ?>" placeholder="<?php echo $custom_field['name']; ?>" data-date-format="YYYY-MM-DD" id="input-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control" />
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span></div>
                  <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
                  <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <?php if ($custom_field['type'] == 'time') { ?>
              <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
                <label class="col-sm-12 control-label" for="input-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
                <div class="col-sm-12">
                  <div class="input-group time">
                    <input type="text" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo (isset($tickets[$custom_field['custom_field_id']]) ? $tickets[$custom_field['custom_field_id']] : $custom_field['value']); ?>" placeholder="<?php echo $custom_field['name']; ?>" data-date-format="HH:mm" id="input-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control" />
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span></div>
                  <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
                  <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <?php if ($custom_field['type'] == 'datetime') { ?>
              <div id="custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-group custom-field<?php echo $custom_field['tstype'] ? ' hide' : false ;?><?php echo $custom_field['required'] ? ' required' : false ;?>" data-sort="<?php echo $custom_field['sort_order']; ?>" data-type="<?php echo $custom_field['tstype'];?>">
                <label class="col-sm-12 control-label" for="input-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
                <div class="col-sm-12">
                  <div class="input-group datetime">
                    <input type="text" name="custom_field[<?php echo $custom_field['location']; ?>][<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo (isset($tickets[$custom_field['custom_field_id']]) ? $tickets[$custom_field['custom_field_id']] : $custom_field['value']); ?>" placeholder="<?php echo $custom_field['name']; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-custom-field<?php echo $custom_field['custom_field_id']; ?>" class="form-control" />
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span></div>
                  <?php if (isset($error_custom_field[$custom_field['custom_field_id']])) { ?>
                  <div class="text-danger"><?php echo $error_custom_field[$custom_field['custom_field_id']]; ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <?php } ?>
              <?php } ?>
            </div>
            <div class="buttons">
              <button class="btn btn-primary pull-right" type="submit"><i class="fa fa-save"></i></button>
            </div>
          </form>





        </div>
        <div class="col-sm-10 ticket-right-clm">
          <div class="action-buttons">
            <button type="button" class="btn btn-primary btn-sm" id="ticket-viewers" data-html="true"><i class="fa fa-eye"></i></button>

            <button type="button" class="btn btn-default btn-sm tabs-hit" href="#ticket-reply"><i class="fa fa-reply"></i> <?php echo $button_reply ;?></button>
            <button type="button" class="btn btn-default btn-sm tabs-hit" href="#ticket-forward"><i class="fa fa-share"></i> <?php echo $button_forward ;?></button>
            <button type="button" class="btn btn-default btn-sm tabs-hit" href="#ticket-note"><i class="fa fa-pencil-square-o"></i> <?php echo $button_add_note ;?></button>
            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Ticketsystem-response-Modal"><i class="fa fa-check"></i> <?php echo $button_responses ;?></button>
            <?php if(in_array('tickets.merge',$ts_roles)){ ?>
              <!-- <button class="btn btn-sm btn-default" data-action="merge" data-toggle="tooltip" title="<?php echo $text_merge_tickets ;?>"><i class="fa fa-link"></i> <?php echo $text_merge ;?></button> -->
            <?php } ?>
            <button type="button" class="btn btn-warning btn-sm" <?php echo $ts_update_status_to_spam ? 'onclick="$(\'#tsStatus\').val('.$ts_update_status_to_spam.').trigger(\'change\'); "' : false; ?>><i class="fa fa-ban"></i> <?php echo $text_responses_mark_spam ;?></button>
            <?php if(in_array('tickets.delete',$ts_roles)){ ?>
              <button type="button" class="btn btn-danger btn-sm" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#delete-ticket-form').submit() : false;"><i class="fa fa-trash-o"></i> <?php echo $button_delete_ticket ;?></button>
              <form class="hide" method="post" id="delete-ticket-form" action="<?php echo $delete_form; ?>">
                <input type="hidden" name="selected[]" value="<?php echo $ticketId; ?>">
              </form>
            <?php } ?>
            <!-- <button type="button" class="btn btn-default btn-sm"><i class="fa fa-floppy-o"></i> <?php echo $text_print ;?></button> -->

            <div class="modal fade ticketsystem-response-modal" id="Ticketsystem-response-Modal">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php echo $text_heading_responses; ?></h4>
                  </div>
                  <div class="modal-body">
                        <div class="alert alert-info"><?php echo $text_responses_apply_info; ?></div>
                    <div class="container-fluid">
                      <div class="row">
                        <?php foreach($ticket_responses as $key => $ticketResponse){ ?>
                          <h4><?php echo $ticketResponse['name'] ;?>
                          <button type="button" class="btn btn-info btn-sm pull-right apply-responses" value="<?php echo $ticketResponse['id']; ?>"><?php echo $button_apply; ?></button>
                          </h4>
                          <h5><?php echo $ticketResponse['description'] ;?>
                          </h5>
                          <br/>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_close; ?></button>
                  </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->


            <button type="button" class="btn btn-default btn-sm hidden-xs hidden-lg hidden-md hidden-sm" data-toggle="tooltip" title="<?php echo $button_reply ;?>"><i class="fa fa-reply"></i></button>
            <button type="button" class="btn btn-default btn-sm hidden-xs hidden-lg hidden-md hidden-sm" data-toggle="tooltip" title="<?php echo $button_forward ;?>"><i class="fa fa-share"></i></button>
            <button type="button" class="btn btn-default btn-sm hidden-xs hidden-lg hidden-md hidden-sm" data-toggle="tooltip" title="<?php echo $button_add_note ;?>"><i class="fa fa-pencil-square-o"></i></button>
            <button type="button" class="btn btn-default btn-sm hidden-xs hidden-lg hidden-md hidden-sm" data-toggle="tooltip" title="<?php echo $button_responses ;?>"><i class="fa fa-check"></i></button>
            <button type="button" class="btn btn-default btn-sm hidden-xs hidden-lg hidden-md hidden-sm" data-toggle="tooltip" title="<?php echo $button_delete_ticket ;?>"><i class="fa fa-trash-o"></i></button>
          </div>

          <div class="ticket-messages">
            <div class="ticket-create">
              <?php if($ticket_create){ ?>
                <?php $ticket_create['customerName'] = str_replace('"', '', $ticket_create['customerName']); ?>
                <div class="create-logo pull-left <?php echo $colors[0]; ?>" tabindex="0" data-trigger="focus" data-toggle="popover"  title="<?php echo $text_customer_details;?>" data-content="<div><a href='<?php echo $ticket_info['customerLink']; ?>'><i class='fa fa-user'></i> <?php echo $ticket_create['customerName']; ?></a><br/> <i class='fa fa-envelope'></i> <?php echo $ticket_info['customerEmail']; ?></div>" ><?php echo substr($ticket_create['customerName'],0,1); ?></div>
                <div class="message-margin-manage">
                  <h4><i class="fa fa-user"></i> <?php echo $ticket_create['customerName']; ?></h4>
                  <i class="fa fa-clock-o"></i> <span class="hidden-xs hidden-sm hidden-md"><b><?php echo $text_created; ?>- </span></b> <?php echo $ticket_create['date_added']; ?>&nbsp;&nbsp;&nbsp;
                </div>
                <div class="clearfix"></div>
                <div class="message">
                  <h4><b><?php echo $ticket_info['subject']; ?></b></h4>
                  <?php if($ticket_create['message'] != strip_tags(html_entity_decode($ticket_create['message']))){ ?>
                    <?php echo preg_replace('/<img/','<img class="img-responsive"', html_entity_decode($ticket_create['message'], ENT_QUOTES, 'UTF-8')); ?>
                  <?php }else{ ?>
                    <?php echo nl2br($ticket_create['message']); ?>
                  <?php } ?>
                  <br/><br/><br/>
                  <?php foreach ($ticket_create['attachments'] as $attachment) { ?>
                    <?php if($attachment['viewImage']){ ?>
                      <div class="img-thumbnail" data-toggle="tooltip" title="<?php echo $attachment['name'];?>">
                        <img src="<?php echo $attachment['viewImage'];?>" alt="<?php echo $attachment['name'];?>">
                        <a href="<?php echo $attachment['path'];?>" target="_blank" class="image-hover-a hide"><i class="fa fa-download"></i></a>
                      </div>
                    <?php }else{ ?>
                      <div class="img-thumbnail" data-toggle="tooltip" title="<?php echo $attachment['name'];?>">
                        <span class="box"><?php echo strtoupper(pathinfo($attachment['name'])['extension']);?></span>
                        <a href="<?php echo $attachment['path'];?>" target="_blank" class="image-hover-a hide"><i class="fa fa-download"></i></a>
                      </div>
                    <?php } ?>
                  <?php } ?>
                </div>
              <?php } ?>
              <div class="seperator"></div>
            </div>
          </div>
          <?php echo $ticket_threads;?>
          <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ticket" class="form-horizontal">
            <?php if($current_agent['image']) { ?>
              <div class="image-logo pull-left" style="background:url(<?php echo $current_agent['image']; ?>);"></div>
            <?php }else{ ?>
              <div class="create-logo pull-left <?php echo $colors[0]; ?>"><?php echo substr($current_agent['name'],0,1); ?></div>
            <?php } ?>
            <div class="message-margin-manage">
              <h4 class="text-success"><b><span class="fa">A</span> <?php echo $current_agent['name']; ?></b></h4>
              <div class="draft-saved text-success">
                 <i><?php echo $text_draft_saved;?></i>
              </div>
              <div class="draft-saving hide text-warning">
                <i class="fa fa-spinner fa-spin"></i> <i><?php echo $text_draft_saving;?></i>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="message">
              <ul class="nav nav-tabs" id="replies">
                <?php if(in_array('tickets.reply',$ts_roles)){ ?>
                  <li><a href="#ticket-reply" data-toggle="tab"><?php echo $button_reply; ?></a></li>
                <?php } ?>
                <?php if(in_array('tickets.forword',$ts_roles)){ ?>
                  <li><a href="#ticket-forward" data-toggle="tab"><?php echo $button_forward; ?></a></li>
                <?php } ?>
                <?php if(in_array('tickets.internal',$ts_roles)){ ?>
                  <li><a href="#ticket-note" data-toggle="tab"><?php echo $button_add_note; ?></a></li>
                <?php } ?>
              </ul>

              <div class="tab-content">
                <?php if(in_array('tickets.reply',$ts_roles)){ ?>
                  <div class="tab-pane" id="ticket-reply">
                    <input type="hidden" name="reply[receivers][to][]" value="<?php echo $ticket_info['customerEmail']; ?>"/>
                    <?php if(in_array('tickets.addcc',$ts_roles)){ ?>
                      <div class="form-group">
                        <select class="form-control add-email selectpicker" multiple data-live-search="true" title="<?php echo $text_cc; ?>" name="reply[receivers][cc][]">
                          <?php foreach ($ts_agents as $agent) { ?>
                            <option value="<?php echo $agent['email']; ?>" <?php echo (isset($reply['receivers']['cc']) AND in_array($agent['email'], $reply['receivers']['cc'])) ? 'selected' : false; ?>><?php echo $agent['name_alias'] ? $agent['name_alias'] : $agent['username']; ?> - <?php echo $agent['email']; ?></option>
                          <?php } ?>
                          <?php if(isset($reply['receivers']['cc']) AND ($diff = array_diff($reply['receivers']['cc'], TsService::fetchOnlyValues($ts_agents)))){ ?>
                            <?php foreach($diff as $emailOption){ ?>
                              <option value="<?php echo $emailOption; ?>" selected><?php echo $emailOption; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <select class="form-control add-email selectpicker" multiple data-live-search="true" title="<?php echo $text_bcc; ?>" name="reply[receivers][bcc][]">
                          <?php foreach ($ts_agents as $agent) { ?>
                            <option value="<?php echo $agent['email']; ?>" <?php echo (isset($reply['receivers']['bcc']) AND in_array($agent['email'], $reply['receivers']['bcc'])) ? 'selected' : false; ?>><?php echo $agent['name_alias'] ? $agent['name_alias'] : $agent['username']; ?> - <?php echo $agent['email']; ?></option>
                          <?php } ?>
                          <?php if(isset($reply['receivers']['bcc']) AND ($diff = array_diff($reply['receivers']['bcc'], TsService::fetchOnlyValues($ts_agents)))){ ?>
                            <?php foreach($diff as $emailOption){ ?>
                              <option value="<?php echo $emailOption; ?>" selected><?php echo $emailOption; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    <?php } ?>

                    <div class="form-group required">
                      <textarea name="reply[message]" class="form-control summernote-textarea"> <?php echo (isset($reply['message'])) ? $reply['message'] : ((isset($ticket_drafts['reply']) ? $ticket_drafts['reply'] : '<br/><br/><br/><br/><br/><br/><br/><br/>'.nl2br($current_agent['signature']))); ?></textarea>
                      <br/>
                      <label class="btn btn-default" for="upload-reply-file" data-toggle="tooltip" title="<?php echo $text_fileupload_info; ?>"><?php echo $text_fileupload; ?></label>
                      <button type="submit" class="btn btn-primary pull-right" name="reply[submit]"><i class="fa fa-reply"></i> <?php echo $button_reply ;?></button>
                      <input type="file" name="reply[file][]" class="hide" id="upload-reply-file" multiple>
                      <br/><span class="text-danger"><?php echo isset($error_reply_file) ? $error_reply_file : false; ?></span>
                    </div>
                  </div>
                <?php } ?>

                <?php if(in_array('tickets.forword',$ts_roles)){ ?>
                  <div class="tab-pane" id="ticket-forward">
                    <div class="form-group required">
                      <select class="form-control add-email selectpicker" multiple data-live-search="true" title="<?php echo $text_to; ?>" name="forward[receivers][to][]">
                        <?php foreach ($ts_agents as $agent) { ?>
                          <option value="<?php echo $agent['email']; ?>" <?php echo (isset($forward['receivers']['to']) AND in_array($agent['email'], $forward['receivers']['to'])) ? 'selected' : false; ?>><?php echo $agent['name_alias'] ? $agent['name_alias'] : $agent['username']; ?> - <?php echo $agent['email']; ?></option>
                        <?php } ?>
                        <?php if(isset($forward['receivers']['to']) AND ($diff = array_diff($forward['receivers']['to'], TsService::fetchOnlyValues($ts_agents)))){ ?>
                          <?php foreach($diff as $emailOption){ ?>
                            <option value="<?php echo $emailOption; ?>" selected><?php echo $emailOption; ?></option>
                          <?php } ?>
                        <?php } ?>
                      </select>
                      <br/><span class="text-danger"><?php echo isset($error_forward_to) ? $error_forward_to : false; ?></span>
                    </div>

                    <?php if(in_array('tickets.addcc',$ts_roles)){ ?>
                      <div class="form-group">
                        <select class="form-control add-email selectpicker" multiple data-live-search="true" title="<?php echo $text_cc; ?>" name="forward[receivers][cc][]">
                          <?php foreach ($ts_agents as $agent) { ?>
                            <option value="<?php echo $agent['email']; ?>" <?php echo (isset($forward['receivers']['cc']) AND in_array($agent['email'], $forward['receivers']['cc'])) ? 'selected' : false; ?>><?php echo $agent['name_alias'] ? $agent['name_alias'] : $agent['username']; ?> - <?php echo $agent['email']; ?></option>
                          <?php } ?>
                          <?php if(isset($forward['receivers']['cc']) AND ($diff = array_diff($forward['receivers']['cc'], TsService::fetchOnlyValues($ts_agents)))){ ?>
                            <?php foreach($diff as $emailOption){ ?>
                              <option value="<?php echo $emailOption; ?>" selected><?php echo $emailOption; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <select class="form-control add-email selectpicker" multiple data-live-search="true" title="<?php echo $text_bcc; ?>" name="forward[receivers][bcc][]">
                          <?php foreach ($ts_agents as $agent) { ?>
                            <option value="<?php echo $agent['email']; ?>" <?php echo (isset($forward['receivers']['bcc']) AND in_array($agent['email'], $forward['receivers']['bcc'])) ? 'selected' : false; ?>><?php echo $agent['name_alias'] ? $agent['name_alias'] : $agent['username']; ?> - <?php echo $agent['email']; ?></option>
                          <?php } ?>
                          <?php if(isset($forward['receivers']['bcc']) AND ($diff = array_diff($forward['receivers']['bcc'], TsService::fetchOnlyValues($ts_agents)))){ ?>
                            <?php foreach($diff as $emailOption){ ?>
                              <option value="<?php echo $emailOption; ?>" selected><?php echo $emailOption; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    <?php } ?>

                    <div class="form-group required">
                      <textarea name="forward[message]" class="form-control summernote-textarea">
                        <?php if(isset($forward['message']) AND $forward['message']) { ?>
                          <?php echo $forward['message']; ?>
                        <?php }elseif(isset($ticket_drafts['forward'])){ ?>
                            <?php echo $ticket_drafts['forward']; ?>
                        <?php }else{ ?>
                            <h4><b><?php echo $ticket_info['subject']; ?></b></h4>
                            <span class="text-info"><?php echo $ticket_create['customerName']; ?> [<?php echo $ticket_info['customerEmail']; ?>] <?php echo $text_wrote; ?></span><br/>
                            <?php echo $ticket_create['message']; ?>
                        <?php } ?>
                      </textarea>
                      <br/>
                      <?php foreach ($ticket_create['attachments'] as $attachment) { ?>
                          <input type="hidden" name="forward[attachment][]" value="<?php echo $attachment['id'];?>">
                          <?php if($attachment['viewImage']){ ?>
                            <div class="img-thumbnail" data-toggle="tooltip" title="<?php echo $attachment['name'];?>">
                              <img src="<?php echo $attachment['viewImage'];?>" alt="<?php echo $attachment['name'];?>">
                              <span class="image-hover-a hide"><i class="fa fa-times-circle-o"></i></span>
                            </div>
                          <?php }else{ ?>
                            <div class="img-thumbnail" data-toggle="tooltip" title="<?php echo $attachment['name'];?>">
                              <span class="box"><?php echo strtoupper(pathinfo($attachment['name'])['extension']);?></span>
                              <span class="image-hover-a hide"><i class="fa fa-times-circle-o"></i></span>
                            </div>
                          <?php } ?>
                      <?php } ?>
                      <br/><br/>
                      <button type="submit" class="btn btn-primary pull-right" name="forward[submit]"><i class="fa fa-share"></i> <?php echo $button_forward;?></button>
                      <label class="btn btn-default" for="upload-forward-file" data-toggle="tooltip" title="<?php echo $text_fileupload_info; ?>"><?php echo $text_fileupload; ?></label>
                      <input type="file" name="forward[file][]" class="hide" id="upload-forward-file" multiple>
                      <br/><span class="text-danger"><?php echo isset($error_forward_file) ? $error_forward_file : false; ?></span>
                    </div>
                  </div>
                <?php } ?>

                <?php if(in_array('tickets.internal',$ts_roles)){ ?>
                  <div class="tab-pane" id="ticket-note">
                    <div class="form-group required">
                      <textarea name="internal[message]" class="form-control summernote-textarea"> <?php echo (isset($internal['message'])) ? $internal['message'] : (isset($ticket_drafts['internal']) ? $ticket_drafts['internal'] : false); ?></textarea>
                      <br/>
                      <button type="submit" class="btn btn-primary pull-right" name="internal[submit]"><i class="fa fa-pencil-square-o"></i> <?php echo $button_add_note;?></button>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
var notesId, tagsId, updateMessage, oldUpdateMessage;
var updateStatusHtml = '<div class="ticketAlert alert alert-changeClass"><i class="fa fa-exclamation-circle"></i> changeMsg <button type="button" class="close" data-dismiss="alert">&times;</button></div>';
var logoText = $('.panel-heading .create-logo').text();

$('.apply-responses').on('click', function(){
  var thisthis = this;
  var thisthisOldHtml = $(thisthis).html();
  $.ajax({
    url: 'index.php?route=ticketsystem/tickets/applyResponses&token=<?php echo $token; ?>',
    dataType: 'json',
    data: {'action_id':$(thisthis).val(), 'id': '<?php echo $ticketId; ?>'},
    beforeSend: function(){
      $('#Ticketsystem-response-Modal .modal-body').find('.alert-warning, .alert-success').remove();
      $(thisthis).html('<i class="fa fa-spin fa-spinner"></i>');
    },
    success: function(json){
      var html = '';
      if(json['success']){
        html = updateStatusHtml.replace('changeClass','success').replace('changeMsg', json['success']);
      }
      if(json['message']){
        $('#Ticketsystem-response-Modal .modal-body').prepend(updateStatusHtml.replace('changeClass','success').replace('changeMsg', json['message']));
        $('#Ticketsystem-response-Modal .modal-body').find('.alert').removeClass('ticketAlert');
      }
      if(json['warning']){
        html = updateStatusHtml.replace('changeClass','danger').replace('changeMsg', json['warning']);
      }
      $('body').append(html);
      TicketAlertMesgRemove();
    },
    complete: function(){
      $(thisthis).html(thisthisOldHtml);
    }
  });
})

$('#tsType').on('change', function(){
  var thisthis = this;
  $('.custom-field').each(function(){
    if(!$(this).attr('data-type') || $(this).attr('data-type')==0 || $(this).attr('data-type')==thisthis.value)
      $(this).removeClass('hide');
    else
      $(this).addClass('hide');
  })
})
$('#tsType').trigger('change');

$('body').on('click', 'span.image-hover-a', function(){
  $(this).parent().remove();
});

$('.ticket-right-clm').on('click', '.thread-data .message .ticket-thread-actions button', function(){
  var thisthis = {'id':this.id, 'event': $(this).attr('data-action')};
  if(thisthis.event=='deletethread'){
    if(!confirm('<?php echo $text_confirm;?>'))
      return false;
  }
  ticketThreadUpdate(thisthis);
});

$('.summernote-textarea').summernote({
  onChange: function(shtml, thisTextarea) {
    updateMessage = {'event' : $(thisTextarea.context).attr('name'), 'shtml' : shtml};
  },
  height: 200
});

setInterval(function(){
  if(updateMessage){
    if(oldUpdateMessage==undefined){
      setTimeInterActive();
    }
    else{
      if(oldUpdateMessage != updateMessage){
        setTimeInterActive();
      }
    }
  }
}, <?php echo $ts_save_draft_time; ?>);

$("#ticket-viewers").on('click', function(){
  ticketThreadUpdate({'event' : 'getViewers'});
})

setInterval(function(){
  ticketThreadUpdate({'event' : 'getViewers'});
}, <?php echo $ts_ticket_view_time; ?>);

function setTimeInterActive(){
  $('.draft-saved').addClass('hide');
  $('.draft-saving').removeClass('hide');
  oldUpdateMessage = updateMessage;
  ticketThreadUpdate(updateMessage);
}

function setTimeInterDeActive(){
  $('.draft-saved').removeClass('hide');
  $('.draft-saving').addClass('hide');
}

function ticketThreadUpdate(thisthis){
  $.ajax({
    url: 'index.php?route=ticketsystem/tickets/threadActions&token=<?php echo $token; ?>',
    type: 'POST',
    dataType: 'json',
    data: {'threadId':(thisthis.id!=undefined ? thisthis.id : false ), 'html':(thisthis.shtml!=undefined ? thisthis.shtml : false ), 'event':thisthis.event, 'id': '<?php echo $ticketId; ?>'},
    beforeSend: function(){
      $('.ticketAlert').remove();
      if(thisthis.event=='deletethread' || thisthis.event=='split'){
        $('.panel-heading .create-logo').html('<i class="fa fa-spin fa-spinner"></i>');
      }else if(thisthis.event=='getViewers'){
        $('#ticket-viewers').html('<i class="fa fa-spin fa-spinner"></i>');
      }
    },
    success: function(json){
      var html = '';
      if(json['success']){
        html = updateStatusHtml.replace('changeClass','success').replace('changeMsg', json['success']);
      }
      if(json['ticket_id']){
        html = updateStatusHtml.replace('changeClass','success').replace('changeMsg', json['success']);
      }
      if(json['warning']){
        html = updateStatusHtml.replace('changeClass','danger').replace('changeMsg', json['warning']);
      }
      if(thisthis.event=='deletethread' || thisthis.event=='split'){
        var paginationId = $('body').find('.div-ticket-pagination button').attr('id');
        var hFunSplit = paginationId.split('-');
        if(hFunSplit[2]!=undefined){
          var hPipeSplit = hFunSplit[2].split('|');
          if(hPipeSplit[0]!=undefined){
            var newPageUpdate = parseInt(hPipeSplit[0])-1;
            var newPaginationId = hFunSplit[0]+'-'+hFunSplit[1]+'-'+newPageUpdate+'|'+hPipeSplit[1]+'|'+hPipeSplit[2];
            $('body').find('.div-ticket-pagination button').attr('id', newPaginationId);
          }
        }
        var threadData = $('.ticket-right-clm').find('button[id='+thisthis.id+']').parents('.thread-data');
        threadData.fadeOut('slow', function(){
          setTimeout(function(){
            threadData.remove();
          },2000);
        });
      }
      if(thisthis.event=='getViewers'){
        if(json['viewers']!=undefined){
          if(json.viewers.no){
            $('#ticket-viewers').attr('data-original-title','<h5><?php echo $text_viewer; ?> - '+json.viewers.no+'</h5> '+json.viewers.viewers).tooltip('show').addClass('btn-danger').removeClass('btn-primary');
          }else{
            $('#ticket-viewers').tooltip('destroy').addClass('btn-primary').removeClass('btn-danger');
          }
        }
      }

      if(thisthis.shtml!=undefined)
        setTimeInterDeActive();

      $('body').append(html);
      TicketAlertMesgRemove();
    },
    complete: function(){
      if(thisthis.event=='deletethread' || thisthis.event=='split'){
        setTimeout(function(){
          $('.panel-heading .create-logo').text(logoText);
        },500);
      }else if(thisthis.event=='getViewers'){
        setTimeout(function(){
          $('#ticket-viewers').html('<i class="fa fa-eye"></i>');
        },500);
      }
    }
  });
}

$('.ticket-right-clm').on('mouseover', '.thread-data .message', function(){
  $(this).find('.ticket-thread-actions').removeClass('hide');
}).on('mouseleave', '.thread-data .message', function(){
  $(this).find('.ticket-thread-actions').addClass('hide');
})

$('body').on('mouseover', '.img-thumbnail', function(){
  $(this).find('.image-hover-a').removeClass('hide');
}).on('mouseleave', '.img-thumbnail', function(){
  $(this).find('.image-hover-a').addClass('hide');
});

$('.tabs-hit').on('click', function(){
  var divId = $(this).attr('href');
  $('a[href='+divId+']').trigger('click');
  $('html,body').animate({
        scrollTop: $(divId).offset().top
        },'slow');
});

$('.message').on('keyup', '.bs-searchbox input', function(e){
  var thisthis = this;
  if(e.keyCode == 13 && this.value){
    var selectOpt = $('select.add-email option').filter(function () { return $(this).html() == thisthis.value; }).val();
    if(selectOpt != undefined){
    }else{
      addEmailCallBack(thisthis);
    }
  }
})

function addEmailCallBack(thisthis){
  var html =  "<option value="+thisthis.value+" selected>" +thisthis.value+ "</option>";
  $(thisthis).parents('.form-group').find('select.add-email').append(html).selectpicker('refresh');
}

$('.ticket-left-clm').on('keyup', '.bs-searchbox input', function(e){
  var thisthis = this;
  if(e.keyCode == 13 && this.value){
    var selectOpt = $('select.tags-select option').filter(function () { return $(this).html() == thisthis.value; }).val();
    if(selectOpt != undefined){
    }else{
      this.id = 'tag-add'
      ticketUpdate(this);
    }
  }
})

$('.notes-entry').on('mouseover','.note-div', function(){
  $(this).find('span.withDeleteIcon').removeClass('hide');
}).on('mouseleave','.note-div', function(){
  $(this).find('span.withDeleteIcon').addClass('hide');
});

$('.notes-entry').on('click', '.note-checkbox', function(){
  var thisthis = this;
  if($(this).is(':checked')){
    $(this).next().html('<s>'+$(this).next().text()+'</s>');
  }else{
    $(this).next().text($(this).next().text());
    thisthis.value = 0;
  }
  ticketUpdate(thisthis);
  thisthis.value = 1;
});

$('.notes-entry').on('click', 'span.withDeleteIcon', function(){
  ticketUpdate(this);
  $(this).parents('.note-div').remove();
});

$('.update-ticket').on('change', function(){
  ticketUpdate(this);
})

$('.tags-select').on('change', function(){
  var thisthis = {'id':this.id};
  thisthis.value = [];
  $('#'+this.id + ' option:selected').each(function(i, selected){ 
    thisthis.value[i] = encodeURIComponent(this.value); 
  })
  ticketUpdate(thisthis);
})

$('#input-notes').on('keyup', function(e){
  if(e.keyCode==13 && this.value){
    ticketUpdate(this);
  }
});

function notesCallBack(thisthis){
    var html = '';
    html =  '<div class="col-sm-12 note-div">';
    html +=   '<input type="checkbox" id="note-'+notesId+'" class="note-checkbox" value="1"> <label for="note-'+notesId+'">'+ thisthis.value +'</label>';
    html +=   '<span class="pull-right hide withDeleteIcon cursor-pointer" id="delete-note-'+notesId+'"><i class="fa fa-trash-o"></i></span>';
    html += '</div>';
    $('.notes-entry').append(html);
    $(thisthis).val('');
}

function tagsCallBack(thisthis){
    var html = '';
    html =  "<option value="+tagsId+" selected>" +thisthis.value+ "</option>";
    $('select.tags-select').append(html);
    $('select.tags-select').selectpicker('refresh');
}

function ticketThreadFetch(thisthis){
  updateDisabledProp(thisthis);
  $.ajax({
    url: 'index.php?route=ticketsystem/tickets/getTicketThreads&token=<?php echo $token; ?>',
    dataType: 'html',
    data: {'limit':thisthis.id, 'id': '<?php echo $ticketId; ?>'},
    beforeSend: function(){
      $(thisthis).html('<i class="fa fa-spin fa-spinner"></i>');
    },
    success: function(html){
      if(html){
        setTimeout(function(){
          $('.div-ticket-pagination').after(html);
        },500);
      }
    },
    complete: function(){
      setTimeout(function(){
        $(thisthis).parent().remove();
      },500);
    }
  });
}

function ticketUpdate(thisthis){
  $.ajax({
    url: 'index.php?route=ticketsystem/tickets/update&token=<?php echo $token; ?>',
    type: 'POST',
    dataType: 'json',
    data: {'type':thisthis.id, 'value':thisthis.value ? thisthis.value : false, 'id': '<?php echo $ticketId; ?>'},
    beforeSend: function(){
      $('.ticketAlert').remove();
      $('.panel-heading .create-logo').html('<i class="fa fa-spin fa-spinner"></i>');
    },
    success: function(json){
      var html = '';
      if(json['success']){
        html = updateStatusHtml.replace('changeClass','success').replace('changeMsg', json['success']);
      }
      if(json['warning']){
        html = updateStatusHtml.replace('changeClass','danger').replace('changeMsg', json['warning']);
      }
      if(json['notesId']){
        notesId = json['notesId'];
        notesCallBack(thisthis);
      }
      if(json['tagsId']){
        tagsId = json['tagsId'];
        tagsCallBack(thisthis);
      }
      $('body').append(html);
      TicketAlertMesgRemove();
    },
    complete: function(){
      setTimeout(function(){
        $('.panel-heading .create-logo').text(logoText);
      },500);
    }
  });
}

$('.create-logo').popover({
  html : true,
});

$('#replies a').on('click', function(){
  localStorage.setItem('tabTicket', $(this).attr('href'));
});

$('.selectpicker').selectpicker({noneResultsText : '<?php echo $text_add_to_enter; ?>'});

$('#replies a:first').tab('show');

$('#ticket-viewers').trigger('click');

if (localStorage.getItem('tabTicket')) {
  $('button[href='+localStorage.getItem('tabTicket')+']').trigger('click');
  localStorage.setItem('tabTicket',false);
}

function TicketAlertMesgRemove(){
  setTimeout(function(){
    $('.ticketAlert.alert').remove();
  },4000);
}

function updateDisabledProp(thisthis){
  if($(thisthis).prop('disabled')){
    $(thisthis).prop('disabled',false);
  }else{
    $(thisthis).prop('disabled',true);
  }
}
//--></script>
<script type="text/javascript"><!--
$('.date').datetimepicker({
  pickTime: false
});

$('.time').datetimepicker({
  pickDate: false
});

$('.datetime').datetimepicker({
  pickDate: true,
  pickTime: true
});
//--></script></div>
<?php echo $footer; ?> 