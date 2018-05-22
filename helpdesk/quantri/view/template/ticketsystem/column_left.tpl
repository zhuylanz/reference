<column id="ts-column-left" class="col-sm-2 hidden-xs">
  <div class="list-group">
  	<?php foreach($column_left as $link){ ?>
    	<a href="<?php echo $link['href']; ?>" class="list-group-item<?php echo $link['class']; ?>"><?php echo $link['text']; ?></a>
    <?php } ?>
  </div>
</column>