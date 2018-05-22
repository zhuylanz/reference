<nav class="navbar navbar-default text-info">
  <div class="container-fluid" style="    background: #283d51; color:#fff; font-weight:bold"> 
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#ts-header-navbar-collapse" aria-expanded="false"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <a class="navbar-brand" href="<?php echo $supportLink; ?>" style="color:#fff; padding-top: 21px;line-height: normal;height: auto;"><?php echo $text_title ;?></a> </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="ts-header-navbar-collapse" >
      <ul class="nav navbar-nav navbar-left">
        <li class="dropdown active"> 
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="
    color: #fff; padding-top: 23px; background:#2aa8fe; padding-bottom: 15px;"><?php echo $text_ticket; ?> <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo $generateTicketLink; ?>"><?php echo $text_generate_ticket; ?></a></li>
            <li><a href="<?php echo $ticketsLink; ?>"><?php echo $text_ticket_status; ?></a></li>
            <li role="separator" class="divider"></li>
            <li><a href="<?php echo $text_link_link; ?>"><?php echo $text_text_link; ?></a></li>
          </ul>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-left">
        <li class="dropdown" > <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="color: #fff; padding-top: 23px;padding-bottom: 15px;"><?php echo $text_category ;?> <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <?php foreach($categories as $category){ ?>
            <li><a data-target="" href="<?php echo $supportLink; ?>#<?php echo preg_replace('/[ \/]/', '' , $category['name']).$category['id']; ?>"><?php echo $category['name']; ?></a></li>
            <?php } ?>
          </ul>
        </li>
      </ul>
      
      <form class="navbar-form navbar-right" role="search" style="    margin-right: -35px;">
        <div class="input-group"> <span class="input-group-btn"> <a type="button" class="btn btn-primary serach-support-a" data-toggle="tooltip" title="<?php echo $text_button_info; ?>" style="color: #283d51;
    background: #fff;"><i class="fa fa-search"></i></a> </span>
          <input type="text" name="search" value="" placeholder="Search" class="form-control" id="search-support" style="font-size:15px;border: none;">
        </div>
      </form>
    </div>
    <!-- /.navbar-collapse --> 
  </div>
  <!-- /.container-fluid --> 
</nav>
<script>
$('#search-support').autocomplete({
    'source': function(request, response) {
      $('.serach-support-a').removeAttr('href');
      $.ajax({
        url: 'index.php?route=ticketsystem/header/informationAutoComplete&id__title=' +  encodeURIComponent(request),
        dataType: 'json',     
        success: function(json) {
          response($.map(json, function(item) {
            return {
              label: item.title,
              value: item.id,
              href: item.href,
            }
          }));
        }
      });
    },
    'select': function(item) {
      $('#search-support').val(item['label']);
      if(item['href'])
        $('.serach-support-a').attr('href',item['href']);
    }
  });
</script>
<style>
input::placeholder {
  color: #283d51 !important;
}
.text-info {
    color: #283d51;
    font-weight: bold;
	padding-top: 20px;
}
.container .ts-jumbotron {
    padding: 0px 30px 20px;
    border: 1px solid #E5E5E5;
    border-radius: 0;
    margin-bottom: 30px;
    color: #283d51;
}
.container .ts-jumbotron a {
	color: #283d51;
    font-weight: bold;
}
#SupportRelatedQuery1 {
	border-bottom:0;
}
.navbar.text-info {
	padding-top:0;
}
</style>
