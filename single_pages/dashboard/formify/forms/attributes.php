<?php   defined('C5_EXECUTE') or die('Access Denied'); ?>
<div class="ccm-ui ccm-dashboard-content-full formify" ng-app="FormifyApp">
	
	<?php   Loader::packageElement('dashboard/form_nav','formify',array('f' => $f)); ?>
	
</div>

<?php   Loader::packageElement('dashboard/formify_nav','formify'); ?>

<h2 style="margin:2.5em 0 0.5em"><?php   echo t('Hold your horses!'); ?></h2>
<p><?php   echo t('Attributes aren\'t quite ready yet, but they will be soon. Be sure to keep Formify up-to-date so you\'ll always have the newest features as soon as they are available.'); ?></p>