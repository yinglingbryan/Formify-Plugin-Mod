<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui ccm-dashboard-content-full formify">
	
	<?php  Loader::packageElement('dashboard/form_nav','formify',array('f' => $f)); ?>
	
	<section>
		
		<?php  if(count($f->getActiveIntegrations()) > 0) { ?>
		
		<form method="post" action="<?php  echo View::URL('dashboard/formify/forms/integrations','save'); ?>">
			
			<input type="hidden" name="fID" value="<?php  echo $f->fID; ?>" />
		
			<div>
			
				<ul class="nav nav-tabs formify-tabs-nav" id="formify-integration-tabs-nav">
				<?php  $j = 0; ?>
				<?php  foreach($f->getActiveIntegrations() as $i) {?>
					<li <?php  if($j == 0) { ?>class="active"<?php  } ?>><a href="#formify-integrations-tab-<?php  echo $i->handle; ?>"><?php  echo $i->name; ?></a></li>
					<?php  $j++; ?>
				<?php  } ?>
				</ul>
			</div>
			
			<div id="formify-settings-tabs">
				
				<?php  $j = 0; ?>
				<?php  foreach($f->getActiveIntegrations() as $i) {?>
					<div class="formify-integrations-tab" id="formify-integrations-tab-<?php  echo $i->handle; ?>" <?php  if($j > 0) { ?>style="display:none;"<?php  } ?>>
						
						<?php  foreach($i->getFormConfigKeys() as $fo) { ?>
							<div class="ccm-search-fields-row">
					            <div class="form-group">
					                <?php  echo $form->label($fo['handle'], t($fo['name'])); ?>
					                
						                <?php  if($fo['type'] == 'boolean') { ?>
							                <div class="ccm-search-field-content">
							                	<input type="checkbox" class="ccm-input-checkbox" name="<?php  echo $i->handle; ?>[<?php  echo $fo['handle']; ?>]" value="true" <?php  if($f->getIntegrationConfig($i->handle,$fo['handle']) == 'true') { ?>checked="checked"<?php  } ?> />
							                </div>
						                <?php  } elseif($fo['type'] == 'select') { ?>
											<div class="ccm-search-field-content">
								                <select class="form-control" name="<?php  echo $i->handle; ?>[<?php  echo $fo['handle']; ?>]">
									                <?php  foreach($fo['options'] as $o) { ?>
									                <option value="<?php  echo $o['value']; ?>" <?php  if($o['value'] == $f->getIntegrationConfig($i->handle,$fo['handle'])) { ?>selected="selected"<?php  } ?>><?php  echo $o['label']; ?></option>
									                <?php  } ?>
							                    </select>
											</div>
						                <?php  } else { ?>
						                	<div class="ccm-search-field-content">
						                    	<input type="text" class="form-control" name="<?php  echo $i->handle; ?>[<?php  echo $fo['handle']; ?>]" value="<?php  echo htmlentities($f->getIntegrationConfig($i->handle,$fo['handle'])); ?>"/>
						                	</div>
						                <?php  } ?>
					            </div>
					        </div>
						<?php  } ?>
						
					</div>
					<?php  $j++; ?>
				<?php  } ?>
				
			</div><br /><br />
			
			<input type="submit" class="btn btn-success" value="<?php  echo t('Save Settings') ?>" />
			
		</form>
		
		<?php  } else { ?>
			<h2 style="margin:2.5em 0 0.5em"><?php  echo t('No active integrations.'); ?></h2>
			<p><?php  echo t('You haven\'t activated any integrations. You can do so from the'); ?> <a href="<?php  echo View::url('/dashboard/formify/forms/settings/'); ?>"><?php  echo t('settings page'); ?></a>.</p>
		<?php  } ?>
	
	</section>
	
	
</div>

<?php   Loader::packageElement('dashboard/formify_nav','formify'); ?>