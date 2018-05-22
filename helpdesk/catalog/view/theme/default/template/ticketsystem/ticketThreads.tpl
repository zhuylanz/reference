<?php $colors = array('green','blue','red'); ?>
<?php if($ticket_pagination_info){ ?>
  <?php $pagiInfo = explode('|',$ticket_pagination_info); ?>
  <div class="div-ticket-pagination">
    <?php if($ticket_pagination_info_disabled){ ?>
      <button class="btn btn-success" type="button" id="ticket-thread-<?php echo $ticket_pagination_info; ?>"><?php echo $text_displayed_all; ?> <i class="fa fa-chevron-down"></i>
    <?php }else{ ?>
      <button class="btn btn-success" type="button" id="ticket-thread-<?php echo $ticket_pagination_info; ?>" onclick="ticketThreadFetch(this);"><?php echo ($pagiInfo[1] - $pagiInfo[0]).' '.$text_more_expand; ?> <i class="fa fa-chevron-down"></i>
    <?php } ?>
    </button>
    <span></span>
  </div>
<?php } ?>
<div class="ticket-messages">
  <div class="ticket-threads">
    <?php if($ticket_threads){ ?>
      <?php foreach($ticket_threads as $threadKey => $thread){ ?>
        <div class="thread-data">
        <?php $thread['customerName'] = str_replace('"', '', $thread['customerName']); ?>
        <?php shuffle($colors); ?>
        <?php if($thread['sender_type']=='customer'){ ?>
            <div class="create-logo pull-left <?php echo $colors[0]; ?>"><?php echo substr($thread['customerName'],0,1); ?></div>
            <div class="message-margin-manage">
              <h4 class="text-primary"><span class="fa">C</span> <?php echo $thread['customerName']; ?></h4>
              <div>
                <span class="fa"><?php echo $text_basic_id; ?></span> <?php echo $thread['id']; ?>&nbsp;&nbsp;&nbsp;
                <?php if($thread['receivers']){ ?>
                  <?php $exp_to = $text_system; ?>
                  <?php echo (isset($thread['receivers']['to']) AND $thread['receivers']['to']) ? $text_to.': '.($exp_to = implode(',',$thread['receivers']['to']) ? $exp_to : $text_system).'&nbsp;&nbsp;&nbsp;' : false; ?>
                  <?php echo (isset($thread['receivers']['cc']) AND $thread['receivers']['cc']) ? $text_cc.': '.implode(',',$thread['receivers']['cc']).'&nbsp;&nbsp;&nbsp;' : false; ?>
                <?php } ?>
              </div>
              <i>
                <?php if($thread['type']=='reply'){ ?>
                  <?php echo sprintf($text_customer_replied, $thread['customerName'], $thread['date_added']); ?>
                <?php } ?>
              </i>
            </div>
            <div class="clearfix"></div>
            <div class="message">
              <div class="ticket-thread-actions hide">
                <?php if($ts_customer_delete_ticketthread){ ?>
                  <button class="btn btn-sm btn-default" id="deletethread-<?php echo $thread['id']; ?>" data-action="deletethread" type="button"><i class="fa fa-trash-o"></i></button>
                <?php } ?>
              </div>
              <?php if($thread['message'] != strip_tags(html_entity_decode($thread['message']))){ ?>
                <?php echo preg_replace('/<img/','<img class="img-responsive"', html_entity_decode($thread['message'], ENT_QUOTES, 'UTF-8')); ?>
              <?php }else{ ?>
                <?php echo nl2br($thread['message']); ?>
              <?php } ?>
              <?php if($thread['attachments']){ ?>
                <br/><br/><br/>
                <?php foreach ($thread['attachments'] as $attachment) { ?>
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
              <?php } ?>
            </div>
        <?php }elseif($thread['sender_type']=='agent'){ ?>
          <?php $agentNamePrint = $thread['agentAliasName'] ? $thread['agentAliasName'] : ($thread['agentName'] ? $thread['agentName'] : $text_system);?>
          <?php $agentNamePrint = str_replace('"', '', $agentNamePrint); ?>
            <?php if($thread['agentImage']) { ?>
              <div class="image-logo pull-left" style="background:url(<?php echo $thread['agentImage']; ?>);"></div>
            <?php }else{ ?>
              <div class="create-logo pull-left <?php echo $colors[0]; ?>"><?php echo substr($agentNamePrint,0,1); ?></div>
            <?php } ?>
            <div class="message-margin-manage">
              <h4 class="text-success"><span class="fa">A</span> <?php echo $agentNamePrint; ?></h4>
              <div>
                <span class="fa"><?php echo $text_basic_id; ?></span> <?php echo $thread['id']; ?>&nbsp;&nbsp;&nbsp;
                <?php if($thread['receivers']){ ?>
                  <?php echo (isset($thread['receivers']['to']) AND $thread['receivers']['to']) ? $text_to.': '.implode(',',$thread['receivers']['to']).'&nbsp;&nbsp;&nbsp;' : false; ?>
                <?php } ?>
              </div>
              <i>
                <?php echo sprintf($text_agent_replied, $agentNamePrint, $thread['date_added']); ?>
              </i>
            </div>
            <div class="clearfix"></div>
            <div class="message">
              <?php if($thread['message'] != strip_tags(html_entity_decode($thread['message']))){ ?>
                <?php echo preg_replace('/<img/','<img class="img-responsive"', html_entity_decode($thread['message'], ENT_QUOTES, 'UTF-8')); ?>
              <?php }else{ ?>
                <?php echo nl2br($thread['message']); ?>
              <?php } ?>
              <?php if($thread['attachments']){ ?>
                <br/><br/><br/>
                <?php foreach ($thread['attachments'] as $attachment) { ?>
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
              <?php } ?>
            </div>
        <?php } ?>
        <div class="seperator"></div>
        </div>
      <?php } ?>
    <?php } ?>
  </div>
</div>
<div class="clearfix"></div>