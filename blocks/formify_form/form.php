<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<div class="ccm-ui">
	<?php   if($isReady) { ?>
		<div class="form-group">
			<label>Form</label>
			<select name="fID" class="form-control">
				<option value=""><?php   echo t('Select a form'); ?></option>
				<?php   if(count($forms) > 0) { ?>
					<?php   foreach($forms as $f) { ?>
						<option value="<?php   echo $f->fID; ?>" <?php   if($f->fID == $fID) { ?>selected="selected"<?php   } ?>><?php   echo $f->name; ?></option>
					<?php   } ?>
				<?php   } ?>
			</select>
		</div>
		
		<div class="form-group">
			<div class="checkbox">
				<label>
					<input type="checkbox" class="ccm-input-checkbox" name="disableDefaultCSS" value="1" <?php  if($disableDefaultCSS == '1') { ?>checked="checked"<?php  } ?> /> Disable default Formify CSS
				</label>
			</div>
		</div>
	
	<?php   } else { ?>
		<div class="alert alert-info"><?php   echo t('Looks like you haven\'t created any forms yet. You can do so'); ?> <a href="<?php   echo DIR_REL; ?>/dashboard/formify"><?php   echo t('from the Dashboard'); ?></a>.</div>
	<?php   } ?>
</div>