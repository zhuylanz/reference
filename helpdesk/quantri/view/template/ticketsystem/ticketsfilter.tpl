    <h3><?php echo $text_tickets_filter; ?>
    <button class="btn btn-sm btn-warning pull-right" id="button-clrfilter" type="button"><?php echo $button_clrfilter; ?></button>
    </h3>
    <div class="form-group">
      <label class="col-sm-12 control-label" for="filter-id"><?php echo $text_id; ?></label>
      <div class="col-sm-12">
          <input type="text" name="id" id="filter_t__id" class="form-control filterTicketsText">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-12 control-label" for="filter_t__customer_id"><?php echo $text_ticket_requester; ?></label>
      <div class="col-sm-12">
          <select name="customers" id="filter_t__customer_id" class="form-control selectpicker filterTickets" data-live-search="true" multiple title="<?php echo $text_select; ?>">
            <?php foreach($customers as $result){ ?>
              <option value="<?php echo $result['id'];?>" ><?php echo $result['name'].' - '.$result['email'];?></option>
            <?php } ?>
          </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-12 control-label" for="filter_t__assign_agent"><?php echo $entry_agents; ?></label>
      <div class="col-sm-12">
          <select name="agents" id="filter_t__assign_agent" class="form-control selectpicker filterTickets" data-live-search="true" multiple title="<?php echo $text_select; ?>">
            <?php foreach($agents as $result){ ?>
              <option value="<?php echo $result['id'];?>" ><?php echo $result['username'].' - '.$result['email'];?></option>
            <?php } ?>
          </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-12 control-label" for="filter_t__group"><?php echo $entry_groups; ?></label>
      <div class="col-sm-12">
          <select name="groups" id="filter_t__group" class="form-control selectpicker filterTickets" data-live-search="true" multiple title="<?php echo $text_select; ?>">
            <?php foreach($groups as $result){ ?>
              <option value="<?php echo $result['id'];?>" ><?php echo $result['name'];?></option>
            <?php } ?>
          </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-12 control-label" for="filter_t__status"><?php echo $text_ticket_status; ?></label>
      <div class="col-sm-12">
          <!-- <select name="status" id="filter_t__status" class="form-control selectpicker filterTickets" data-live-search="true" multiple title="<?php echo $text_select; ?>">
            <?php foreach($statuss as $result){ ?>
              <option value="<?php echo $result['id'];?>" ><?php echo $result['name'];?></option>
            <?php } ?>
          </select> -->
          <?php foreach($statuss as $result){ ?>
            <input type="checkbox" class="filterTicketsCheck" name="filter_t__status" id="<?php echo $result['name'].$result['id'];?>" value="<?php echo $result['id'];?>">
            <label class="control-label" for="<?php echo $result['name'].$result['id'];?>"><?php echo $result['name'];?></label>
            <br/>
          <?php } ?>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-12 control-label" for="filter_t__type"><?php echo $text_ticket_type; ?></label>
      <div class="col-sm-12">
          <!-- <select name="types" id="filter_t__type" class="form-control selectpicker filterTickets" data-live-search="true" multiple title="<?php echo $text_select; ?>">
            <?php foreach($types as $result){ ?>
              <option value="<?php echo $result['id'];?>" ><?php echo $result['name'];?></option>
            <?php } ?>
          </select> -->
          <?php foreach($types as $result){ ?>
            <input type="checkbox" class="filterTicketsCheck" name="filter_t__type" id="<?php echo $result['name'].$result['id'];?>" value="<?php echo $result['id'];?>">
            <label class="control-label" for="<?php echo $result['name'].$result['id'];?>"><?php echo $result['name'];?></label>
            <br/>
          <?php } ?>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-12 control-label" for="filter_t__priority"><?php echo $text_ticket_priority; ?></label>
      <div class="col-sm-12">
          <!-- <select name="priority" id="filter_t__priority" class="form-control selectpicker filterTickets" data-live-search="true" multiple title="<?php echo $text_select; ?>">
            <?php foreach($priorities as $result){ ?>
              <option value="<?php echo $result['id'];?>" ><?php echo $result['name'];?></option>
            <?php } ?>
          </select> -->
          <?php foreach($priorities as $result){ ?>
            <input type="checkbox" class="filterTicketsCheck" name="filter_t__priority" id="<?php echo $result['name'].$result['id'];?>" value="<?php echo $result['id'];?>">
            <label class="control-label" for="<?php echo $result['name'].$result['id'];?>"><?php echo $result['name'];?></label>
            <br/>
          <?php } ?>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-12 control-label" for="filter_ttt__id"><?php echo $entry_tags; ?></label>
      <div class="col-sm-12">
          <select name="tags" id="filter_ttt__tag_id" class="form-control selectpicker filterTickets" data-live-search="true" multiple title="<?php echo $text_select; ?>">
            <?php foreach($tags as $result){ ?>
              <option value="<?php echo $result['id'];?>" ><?php echo $result['name'];?></option>
            <?php } ?>
          </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-12 control-label" for="filter_t__provider"><?php echo $text_source; ?></label>
      <div class="col-sm-12">
          <!-- <select name="source" id="filter_t__provider" class="form-control selectpicker filterTickets" data-live-search="true" multiple title="<?php echo $text_select; ?>">
            <?php foreach($source as $result){ ?>
              <option value="<?php echo $result;?>" ><?php echo ${'text_'.$result};?></option>
            <?php } ?>
          </select> -->
          <?php foreach($source as $result){ ?>
            <input type="checkbox" class="filterTicketsCheck" name="filter_t__provider" id="<?php echo 'source-'.$result; ?>" value="<?php echo $result; ?>">
            <label class="control-label" for="<?php echo 'source-'.$result;?>"><?php echo ${'text_'.$result} ;?></label>
            <br/>
          <?php } ?>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-12 control-label" for="filter_t__date_added"><?php echo $text_ticket_created; ?></label>
      <div class="col-sm-12">
        <div class="input-group date">
          <input type="text" name="date" id="filter_t__date_added" class="form-control filterTicketsText date" data-date-format="YYYY-MM-DD">
          <span class="input-group-btn">
            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
          </span>
        </div>
      </div>
    </div>