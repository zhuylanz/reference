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
        - <?php echo $text_info_organizations; ?>

      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" id="input-name" class="form-control"/>
              <?php if ($error_name) { ?>
                <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-description"><?php echo $entry_description; ?></label>
            <div class="col-sm-10">
                <textarea name="description" id="input-description" class="form-control" placeholder="<?php echo $entry_description_info; ?>"><?php echo $description; ?></textarea>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-domain"><?php echo $entry_domain; ?></label>
            <div class="col-sm-10">
                <input type="text" name="domain" value="<?php echo $domain; ?>" id="input-domain" class="form-control"/>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-notes"><?php echo $entry_notes; ?></label>
            <div class="col-sm-10">
                <textarea name="note" id="input-notes" class="form-control" placeholder="<?php echo $entry_notes_info; ?>"><?php echo $note; ?></textarea>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-customers"><?php echo $entry_customers; ?> <span data-toggle="tooltip" title="<?php echo $text_info_organization_customers; ?>"></span></label>
            <div class="col-sm-10">
              <input type="text" name="customers-search" value="" placeholder="<?php echo $entry_customers; ?>" id="input-customers" class="form-control" />
              <div id="organization-customers" class="well well-sm" style="height: 150px; overflow: auto;">
                <?php foreach ($customers as $customer) { ?>
                <div id="organization-customers<?php echo $customer['customer_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $customer['name']; ?>
                  <input type="hidden" name="customers[]" value="<?php echo $customer['customer_id']; ?>" />
                </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-customer_role"><?php echo $entry_customer_role; ?> <span data-toggle="tooltip" title="<?php echo $entry_customer_role_info; ?>"></span></label>
            <div class="col-sm-10">
              <label class="radio-inline">
                <input type="radio" name="customer_role" value="1" <?php echo $customer_role ? 'checked' : false; ?>> <?php echo $text_enable; ?>
              </label>
              <label class="radio-inline">
                <input type="radio" name="customer_role" value="0" <?php echo !$customer_role ? 'checked' : false; ?>> <?php echo $text_disable; ?>
              </label>
            </div>
          </div> 
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-customers"><?php echo $entry_groups; ?> <span data-toggle="tooltip" title="<?php echo $text_info_organization_groups; ?>"></span></label>
            <div class="col-sm-10">
              <input type="text" name="groups-search" value="" placeholder="<?php echo $entry_groups; ?>" id="input-groups" class="form-control" />
              <div id="organization-groups" class="well well-sm" style="height: 150px; overflow: auto;">
                <?php foreach ($groups as $group) { ?>
                <div id="organization-groups<?php echo $group['group_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $group['name']; ?>
                  <input type="hidden" name="groups[]" value="<?php echo $group['group_id']; ?>" />
                </div>
                <?php } ?>
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
// customers
$('input[name=\'customers-search\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=ticketsystem/customers/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request)+'&filter_organization=<?php echo $id ? $id : "true"; ?>',
      dataType: 'json',     
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'] + ' - ' + item['email'],
            value: item['id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'customers-search\']').val('');
    
    $('#organization-customers' + item['value']).remove();
    
    $('#organization-customers').append('<div id="organization-customers' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="customers[]" value="' + item['value'] + '" /></div>');  
  } 
});

$('#organization-customers').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});

// Groups
$('input[name=\'groups-search\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=ticketsystem/groups/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',     
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'groups-search\']').val('');
    
    $('#organization-groups' + item['value']).remove();
    
    $('#organization-groups').append('<div id="organization-groups' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="groups[]" value="' + item['value'] + '" /></div>');  
  } 
});

$('#organization-groups').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});

//--></script></div>
<?php echo $footer; ?> 