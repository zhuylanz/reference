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
        - <?php echo $text_info_customers; ?>

      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
                <input type="text" name="name" value="<?php echo $name; ?>" id="input-name" class="form-control"/>
            </div>
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-email"><?php echo $entry_email; ?></label>
            <div class="col-sm-10">
                <input type="text" name="email" value="<?php echo $email; ?>" id="input-email" class="form-control"/>
                <?php if ($error_email) { ?>
                  <div class="text-danger"><?php echo $error_email; ?></div>
                <?php } ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-customer"><?php echo $entry_oc_customer; ?> <span data-toggle="tooltip" title="<?php echo $entry_oc_customer_info;?>"></span></label>
            <div class="col-sm-10">
                <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>"/>
                <input type="text" name="oc_customer" value="<?php echo $oc_customer; ?>" id="input-customer" class="form-control"/>
                <?php if ($error_customer_exists) { ?>
                  <div class="text-danger"><?php echo $error_customer_exists; ?></div>
                <?php } ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-organization"><?php echo $entry_organization; ?></label>
            <div class="col-sm-10">
                <input type="hidden" name="organization_id" value="<?php echo $organization_id; ?>"/>
                <input type="text" name="organization" value="<?php echo $organization; ?>" id="input-organization" class="form-control"/>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
// organizations
$('input[name=\'organization\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=ticketsystem/organizations/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
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
    $('input[name=\'organization\']').val(item['label']);
    $('input[name=\'organization_id\']').val(item['value']);
  } 
});

// customers
$('input[name=\'oc_customer\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=ticketsystem/customers/autocompleteOcCustomers&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',     
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['firstname'] + ' ' + item['lastname'] + ' - ' + item['email'],
            value: item['customer_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'oc_customer\']').val(item['label']);
    $('input[name=\'customer_id\']').val(item['value']);
  } 
});

//--></script></div>
<?php echo $footer; ?> 