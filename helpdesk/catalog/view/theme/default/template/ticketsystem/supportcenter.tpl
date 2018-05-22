<?php echo $header; ?>
<div class="contact_title">
<div class="container">
 <h1><?php echo $heading_title; ?></h1>
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
</div>
</div>
<div class="container">
  
  <?php if ($error_warning) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <?php echo $ts_column_top ;?>
      <div class="ts-jumbotron">
      <?php foreach($categories as $category){ ?>
        <div id="<?php echo preg_replace('/[ \/]/', '' , $category['name']).$category['id']; ?>" class="category-div">
          <h3 class="text-info"><?php echo $category['name']; ?></h3>
          <h5><?php echo nl2br($category['description']); ?></h5>
          <ul class="list-unstyled">
            <?php foreach($category['informations'] as $information){ ?>
              <li>
                <a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a>
                <h5>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo substr(strip_tags(html_entity_decode($information['description'])),0,200).' ..'; ?></h5>
              </li>
            <?php } ?>
          </ul>
          <br/>
        </div>
      <?php } ?>
      </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>

<?php echo $footer; ?>