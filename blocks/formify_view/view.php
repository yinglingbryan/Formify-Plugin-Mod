<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  global $c; ?>
<?php  if(($template) && ($records)) { ?>

	<?php  if($enableSearch) { ?>
	<form method="post" action="<?php  echo $this->action('search'); ?>">
		<input type="text" name="q" value="<?php  echo htmlentities($query); ?>" />
		<input type="submit" value="Search" />
	</form>
	<hr />
	<?php  } ?>
	
	<?php  $template->render($records); ?>
	
	<?php  if($paginator) { ?>	 
		<div class="pagination" style="text-align:center">
			<div style="float:left"><?php  echo $paginator->getPrevious()?></div>
			<div style="float:right"><?php  echo $paginator->getNext()?></div>
			<?php  echo $paginator->getPages()?>
		</div>		
	<?php  } ?>
	
<?php  } ?>
	