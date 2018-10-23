<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="formify-field-input formify-checkboxes <?php   echo $field->fieldClass; ?>">
	<fieldset class="formify-fieldset">
	<div class="formify-legend"><?php   echo $field->label; ?> <?php   echo $field->requiredIndicator; ?></div>
	
	<?php   $i = 0; ?>
	<?php   foreach($field->getOptions() as $o) { ?>
		<label class="formify-checkbox-label" for="formify-field-<?php   echo $field->ffID; ?>-<?php   echo $i; ?>">
			<input class="formify-field formify-checkbox" type="checkbox" name="<?php   echo $field->ffID;?>[]" value="<?php   echo htmlentities($o['value']); ?>" id="formify-field-<?php   echo $field->ffID; ?>-<?php   echo $i; ?>" <?php   if($field->isDefaultValue($o['value'])) { ?>checked="checked"<?php   } ?> />
			<span><i class="fa <?php   if($field->isDefaultValue($o['value'])) { ?>fa-check<?php   } ?>"></i></span>
			<?php   echo $o['value']; ?>
		</label>
		<?php   $i++; ?>
	<?php   } ?>	
</div>