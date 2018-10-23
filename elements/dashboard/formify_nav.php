<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-dashboard-header-buttons">
	<a href="<?php   echo View::url('/dashboard/formify')?>" class="btn btn-success"><?php   echo t("Forms")?></a>
	<a href="<?php   echo View::url('/dashboard/formify/templates')?>" class="btn btn-primary"><?php   echo t("Templates")?></a>
	<a href="<?php   echo View::url('/dashboard/formify/import')?>" class="btn btn-primary"><?php   echo t("Import")?></a>
	<!--<a href="<?php   echo View::url('/dashboard/formify/attributes')?>" class="btn btn-primary"><?php   echo t("Attributes")?></a>-->
	<a href="<?php   echo View::url('/dashboard/formify/options')?>" class="btn btn-primary"><?php   echo t("Options")?></a>
</div>