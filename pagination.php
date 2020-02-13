<?php
 
  $count_page = $count_order / $limit;
  if (($count_order % $limit) > 0) {
    $count_page++;
  }
?>
<nav aria-label="Page navigation" class="my-5">
  	<ul class="pagination">
    	
    	<?php
    		$pg = $page - 1;
    		$uri = "/?page={$pg}";
    		$disabled = ($page == 0) ? 'disabled' : '';
    	?>
    	<li class="page-item <?=$disabled;?>">
      		<a  class="page-link" href="<?=$uri;?>" tabindex="-1">Предыдущая</a>
    	</li>
    	<?php $count = $count_page - 1;?>
    	<?php $points = true; ?>
    	<?php for ($i = 0; $i <= $count; $i++): ?>
			<?php 
				$uri = "/?page={$i}";
				if ($i == $page) {
					$active = "active";
				} else {
					$active = '';
				}
			?>

    		<?php if ($page > 0 && $i == 0): ?>
				<li class="page-item <?=$active;?>">
					<?php $uri = "/"; ?>
      				<a class="page-link" href="<?=$uri;?>">1</a>
    			</li>
    		<?php elseif ($i >= ($page - 1)  && $i <= ($page + 2) && $i != $count): ?>
				<li class="page-item <?=$active;?>">
      				<a class="page-link" href="<?=$uri;?>"><?=($i + 1);?></a>
    			</li>
				<?php $points = true; ?>
    		<?php elseif ($i == $count): ?>
				<li class="page-item <?=$active;?>">
      				<a class="page-link" href="<?=$uri;?>"><?=$count + 1;?></a>
    			</li>

    		<?php elseif($points): ?>
				<li class="page-item <?=$active;?>">
      				<a class="page-link" href="#">...</a>
    			</li>
    			<?php $points = false; ?>
    		<?php endif; ?>
    	<?php endfor; ?>
    	<?php 
    		$pg = $page + 1;
    		$uri = "/?page={$pg}";
    		$disabled = ($page == $count) ? 'disabled' : '';
    	?>
    	<li class="page-item <?=$disabled;?>">
      		<a class="page-link" href="<?=$uri;?>">Следущая</a>
    	</li>
  	</ul>
</nav>