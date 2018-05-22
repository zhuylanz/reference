<!doctype html>
<html ⚡>
<head>
  <meta charset="utf-8">
  <title><?php echo $meta_title; ?></title>
  <link rel="canonical" href="<?php echo $canonical; ?>" >
  <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
  <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
  <style amp-custom>
html {overflow-x:hidden}
body,html{height:auto}
body {
	margin:0;-webkit-text-size-adjust:100%;-moz-text-size-adjust:100%;-ms-text-size-adjust:100%;text-size-adjust:100%
}
body {
    font-family: 'Roboto', Arial;
}
a {
text-decoration:none;	
}
.box1 {
    font-size: 13px;
    line-height: 19px;
    margin-bottom: 15px;
    margin: 8px 0;
}
.amp-logo {
	width: 228px;
    height: 42px;
    margin: 0 auto;
    top: 2px;
    display: block;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: 50% 50%;   
}
.amp-header{
border-bottom: 1px solid #e0e0e0;
    height: 45px;
    line-height: 45px;
    vertical-align: middle;
    margin: 0 10px;
    font-style: italic;
	font-size: 13px;}
.breadcrumb {
	 list-style:none;
	background: none;
	font-size: 13px;
	padding:5px;
	margin-bottom: 5px;
}
.breadcrumb > li{
	display: inline;
}
.breadcrumb > li+li:before {
    content: "/\00a0";
    padding: 0 5px;
    color: #ccc;
}
.price .priceBig{
	margin-top: 0;
	margin-bottom: 15px;
	font-size: 30px;
	font-family: inherit;
    font-weight: 500;
    line-height: 1.1;
    color: inherit;
}
.price .price-old{
	color: #e4003a;
	text-decoration: line-through;
	font-size: 16px;
	font-weight: 300;
	display: block;
	margin-bottom: 5px;
}
.price .tax,
.price .points{
	color: #777;
	font-size: 14px;
	font-weight: 300;
	display: block;
	margin-top: 10px;
}
.amp-product_info {
    display: inline-block;
    text-align: right;
    float: right;
}
.amp_wrapper {
    margin: 0 10px;
}
.amp-page_footer {
    text-align: right;
    height: 30px;
    line-height: 30px;
}
.amp-page_footer_link {
    display: inline-block;
    vertical-align: middle;
    text-decoration: none;
	    font-size: 13px;
}
#button-cart {
	text-decoration:none;
}
.btn-large {
    background: #333745;
    border-color: #21232d;
    padding: 12px 1em;
    font-size: 18px;
    line-height: 1.33;
    border-radius: 4px;
}
.btn {
    text-transform: uppercase;
    -webkit-transition: .2s ease-out;
    -moz-transition: .2s ease-out;
    -o-transition: .2s ease-out;
    -ms-transition: .2s ease-out;
    transition: .2s ease-out;
}
.btn-danger {
    color: #fff;
    text-shadow: 0 -1px 0 rgba(0,0,0,.25);
    background-color: #da4f49;
    border-color: #bd362f #bd362f #802420;
}
.btn {
    padding: 7.5px 12px;
    font-size: 12px;
    text-align: center;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    background-image: none;
    border: 1px solid transparent;
    white-space: nowrap;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    border-radius: 4px;
    color: #fff;
    transition: all .2s;
    border-radius: 4px;
}
.clearfix:after {
  content: "";
  display: table;
  clear: both;
}
.product-item{
	width: 50%;
    float: left;
}
.related-products h2{
	text-transform:uppercase;
 text-align: center;
 }
 polygon{ fill:#EFCE4A;}
  </style>
<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
<script async src="https://cdn.ampproject.org/v0.js"></script>
</head>
<body>
<div itemscope itemtype="http://schema.org/Product" class="amp_wrapper">
<div class="amp-header">
	    	<a href="<?php echo $home; ?>" class="amp-header_link">
				<amp-img src="<?php echo $logo; ?>" width="228" height="42" alt="an image" class="amp-logo -amp-element -amp-layout-fixed -amp-layout-size-defined -amp-layout" id="AMP_1" >
				 </amp-img>
			</a>		 
		</div>
		<ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
		<?php $breadcount = count($breadcrumbs) - 1; ?>
		<?php $i = 0; ?>
		 <?php foreach ($breadcrumbs as $key => $breadcrumb) { ?>
		 <?php $i++; ?>
		 <?php if ($key != $breadcount) { ?>
  <li itemprop="itemListElement" itemscope
      itemtype="http://schema.org/ListItem">
    <a itemprop="item" href="<?php echo $breadcrumb['href']; ?>">
    <span itemprop="name"><?php echo $breadcrumb['text']; ?></span></a>
    <meta itemprop="position" content="<?php echo $i; ?>" />
  </li>
  <?php } else {?>
  <li itemprop="itemListElement" itemscope
      itemtype="http://schema.org/ListItem">
   <span itemprop="name"><?php echo $breadcrumb['text']; ?></span>
    <meta itemprop="position" content="<?php echo $i; ?>" />
  </li>
    <?php } ?>  <?php } ?>
</ol>
  <h1 itemprop="name"><?php echo $heading_title; ?></h1>
   <?php if ($thumb || $images) { ?>
    <amp-img itemprop="image" alt="<?php echo $heading_title; ?>" 
        src="<?php echo $thumb; ?>" width=500 height=500 layout="responsive" ></amp-img> 
  <?php } ?>
  <?php if ($review_status) { ?>
  <span class="stars">
				<?php if ($rating) { ?>
				<span itemprop = "aggregateRating" itemscope itemtype = "http://schema.org/AggregateRating">
					<meta itemprop='reviewCount' content='<?php echo preg_replace("/\D/","",$reviews); ?>' />
					<meta itemprop='worstRating' content='1' />
					<meta itemprop='bestRating' content='5' />
					<meta itemprop='ratingValue' content='<?php echo $rating; ?>' />
				</span>
				<?php } ?>
				<?php for ($i = 1; $i <= 5; $i++) { ?>
				<?php if ($rating < $i) { ?>
 
				<?php } else { ?>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="star-a" x="0px" y="0px" viewBox="0 0 53.867 53.867" width="13px" height="13px" xml:space="preserve">
<polygon points="26.934,1.318 35.256,18.182 53.867,20.887 40.4,34.013 43.579,52.549 26.934,43.798   10.288,52.549 13.467,34.013 0,20.887 18.611,18.182 "/>
</svg>
				<?php } ?>
				<?php } ?>
			</span>
			<?php } ?>
			 <div class="amp-product_info box1">
			 <?php if ($manufacturer) { ?>
		 <br />
			<b><?php echo $text_manufacturer; ?></b> <a href="<?php echo $manufacturers; ?>" class="red-link"><?php echo $manufacturer; ?></a>
		<?php } ?>
		<br />
		<b><?php echo $text_model; ?></b> <?php echo $model; ?>
			 <?php if ($reward) { ?>
		 <br />
			<b><?php echo $text_reward; ?></b> <?php echo $reward; ?>
		<?php } ?>
		 <br />
			 <b><?php echo $text_stock; ?></b> <?php echo $stock; ?>
			 </div>
			<?php if ($price) { ?>
						<div class="price" itemprop = "offers" itemscope itemtype = "http://schema.org/Offer">
							<meta itemprop="priceCurrency" content="<?php echo $currency_code; ?>" />
							<?php if (!$special) { ?>
							<meta itemprop="price" content="<?php echo preg_replace("/\D+/","", $price ); ?>" />
							<div class="priceBig">
								<span ><?php echo $price; ?></span>
								<?php if ($tax) { ?>
								<span class="tax"><?php echo $text_tax; ?> <?php echo $tax; ?></span>
								<?php } ?>
								<?php if ($points) { ?>
								<span class="points"><?php echo $text_points; ?> <strong><?php echo $points; ?></strong></span>
								<?php } ?>
							</div>
							<?php } else { ?>
							<meta itemprop="price" content="<?php echo preg_replace("/[^\d.]/","",rtrim($special, " \t.")); ?>" />
							<div class="priceBig">
								<span class="price-old">&nbsp;<?php echo $price; ?>&nbsp;</span>
								<span><?php echo $special; ?></span>
								<?php if ($tax) { ?>
								<span class="tax"><?php echo $text_tax; ?> <?php echo $tax; ?></span>
								<?php } ?>
								<?php if ($points) { ?>
								<span class="points"><?php echo $text_points; ?> <strong><?php echo $points; ?></strong></span>
								<?php } ?>
							</div>
							<?php } ?>
							<?php if ($discounts) { ?>
							<div class="alert-alt alert-info-alt">
								<?php foreach ($discounts as $discount) { ?>
									<div><strong><?php echo $discount['quantity']; ?></strong><?php echo $text_discount; ?><strong><?php echo $discount['price']; ?></strong></div>
								<?php } ?>
							</div>
							<?php } ?>
						</div>
						<?php } ?>
<a href="<?php echo $canonical; ?>" id="button-cart" class="btn btn-block btn-danger btn-large"><?php echo $button_cart; ?></a>			
<h2><?php echo $tab_description; ?></h2>
<section class="box1"><span itemprop="description">
<?php 
function strip_word_html($text, $allowed_tags = '<a><ul><li><b><i><sup><sub><em><strong><u><br><br/><br /><p><h2><h3><h4><h5><h6>')
{
    mb_regex_encoding('UTF-8');
    //replace MS special characters first
    $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
    $replace = array('\'', '\'', '"', '"', '-');
    $text = preg_replace($search, $replace, $text);
    //make sure _all_ html entities are converted to the plain ascii equivalents - it appears
    //in some MS headers, some html entities are encoded and some aren't
    //$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    //try to strip out any C style comments first, since these, embedded in html comments, seem to
    //prevent strip_tags from removing html comments (MS Word introduced combination)
    if(mb_stripos($text, '/*') !== FALSE){
        $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
    }
    //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
    //'<1' becomes '< 1'(note: somewhat application specific)
    $text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);
    $text = strip_tags($text, $allowed_tags);
    //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
    $text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text);
    //strip out inline css and simplify style tags
    $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
    $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
    $text = preg_replace($search, $replace, $text);
    //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears
    //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains
    //some MS Style Definitions - this last bit gets rid of any leftover comments */
    $num_matches = preg_match_all("/\<!--/u", $text, $matches);
    if($num_matches){
        $text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
    }
    $text = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $text);
return $text;
}
$description= strip_word_html($description);
echo preg_replace('/<\/font[^>]*>/', '', preg_replace('/<font[^>]*>/', '', $description));  ?>
</span>
</section>
<?php if ($attribute_groups) { ?>
<h2><?php echo $tab_attribute; ?></h2>
<section class="box1">
  <table class="table table-bordered">
                <?php foreach ($attribute_groups as $attribute_group) { ?>
                <thead>
                  <tr>
                    <td colspan="2"><strong><?php echo $attribute_group['name']; ?></strong></td>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($attribute_group['attribute'] as $attribute) { ?>
                  <tr>
                    <td><?php echo $attribute['name']; ?></td>
                    <td><?php echo $attribute['text']; ?></td>
                  </tr>
                  <?php } ?>
                </tbody>
                <?php } ?>
              </table>
  </section>
   <?php } ?>
   	<?php if ($products) { ?>
					<div class="related-products"> 
						<h2><?php echo $text_related; ?></h2>
						<div class="panel-body" id="related-products">
							<?php foreach ($products as $product) { ?>
							<div class="product-item">
								<div class="image">
								<a href="<?php echo $product['href']; ?>"><amp-img itemprop="image" alt="<?php echo $product['name']; ?>" 
        src="<?php echo $product['thumb']; ?>" width=500 height=500 layout="responsive" ></amp-img></a>
								<?php if ($product['special']) { ?>
								<?php $new_price = preg_replace("/[^0-9]/", '', $product['special']); ?>
								<?php $old_price = preg_replace("/[^0-9]/", '', $product['price']); ?>
								<?php $total_discount = round(100 - ($new_price / $old_price) * 100); ?>
								<span class="sticker st-sale">-<?php echo $total_discount; ?>%</span>
								<?php } ?>
								</div>
								<div class="caption">
									<div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
									<?php if ($product['price']) { ?>
									<div class="price">
										<?php if (!$product['special']) { ?>
										<?php echo $product['price']; ?>
										<?php } else { ?>
										<span class="price-old">&nbsp;<?php echo $product['price']; ?>&nbsp;</span> <span class="price-new"><?php echo $product['special']; ?></span>
										<?php } ?>
										<?php if ($product['tax']) { ?>
										<br /><span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
										<?php } ?>
									</div>
									<?php } ?>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
					<div class="clearfix"></div> 
					<?php } ?>	
 <div class="amp-page_footer">
				<a href="<?php echo $base; ?>" class="amp-page_footer_link">
          <span><?php echo $name ?></span></a>
<span>© <?php echo date("Y"); ?></span>

			</div>   
</div>
</body>
</html>