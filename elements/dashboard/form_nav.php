<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  $c = Page::getCurrentPage(); ?>

<script type="text/javascript">
	var fID = <?php  echo $f->fID; ?>;
</script>

<nav class="forms">
		<h3><?php  echo $f->name; ?></h3>
		<hr />
		<ul>
			<li class="settings">
				<a href="<?php  echo View::url('/dashboard/formify/forms/settings/')?>" <?php  if($c->getCollectionHandle() == 'settings') { ?>class="active"<?php  } ?>>
					<i class="fa fa-cog"></i>
					<span><?php  echo t('Settings'); ?></span>
				</a>
			</li>
			<li class="fields">
				<a href="<?php  echo View::url('/dashboard/formify/forms/fields/')?>" <?php  if($c->getCollectionHandle() == 'fields') { ?>class="active"<?php  } ?>>
					<i class="fa fa-list-alt"></i>
					<span><?php  echo t('Fields'); ?></span>
				</a>
			</li>
			<li class="records">
				<a href="<?php  echo View::url('/dashboard/formify/forms/records/')?>" <?php  if($c->getCollectionHandle() == 'records') { ?>class="active"<?php  } ?>>
					<i class="fa fa-inbox"></i>
					<span><?php  echo t('Records'); ?></span>
				</a>
			</li>
			<li class="notifications">
				<a href="<?php  echo View::url('/dashboard/formify/forms/notifications/')?>" <?php  if($c->getCollectionHandle() == 'notifications') { ?>class="active"<?php  } ?>>
					<i class="fa fa-paper-plane"></i>
					<span><?php  echo t('Notifications'); ?></span>
				</a>
			</li>
			<li class="export">
				<a href="<?php  echo View::url('/dashboard/formify/forms/export/')?>" <?php  if($c->getCollectionHandle() == 'export') { ?>class="active"<?php  } ?>>
					<i class="fa fa-arrow-circle-down"></i>
					<span><?php  echo t('Export'); ?></span>
				</a>
			</li>
			<li class="integrations">
				<a href="<?php  echo View::url('/dashboard/formify/forms/integrations/')?>" <?php  if($c->getCollectionHandle() == 'integrations') { ?>class="active"<?php  } ?>>
					<i class="fa fa-puzzle-piece"></i>
					<span><?php  echo t('Integrations'); ?></span>
				</a>
			</li>
			<!--
			<li class="attributes">
				<a href="<?php  echo View::url('/dashboard/formify/forms/attributes/')?>" <?php  if($c->getCollectionHandle() == 'attributes') { ?>class="active"<?php  } ?>>
					<i class="fa fa-tags"></i>
					<span><?php  echo t('Attributes'); ?></span>
				</a>
			</li>
			-->
		</ul>
	</nav>