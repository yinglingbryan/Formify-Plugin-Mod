<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="formify-field-input formify-radio <?php   echo $field->fieldClass; ?>">
	<fieldset class="formify-fieldset">
	<div class="formify-legend"><?php   echo $field->label; ?> <?php   echo $field->requiredIndicator; ?></div>
	
	<?php   $i = 0; ?>
	<?php   foreach($field->getOptions() as $o) { ?>
		<label data-formify-ffid="<?php   echo $field->ffID; ?>" class="formify-radio-label" for="formify-field-<?php   echo $field->ffID; ?>-<?php   echo $i; ?>">
			<input class="formify-field formify-radio" type="radio" name="<?php   echo $field->ffID;?>" value="<?php   echo htmlentities($o['value']); ?>" id="formify-field-<?php   echo $field->ffID; ?>-<?php   echo $i; ?>" <?php   if($field->isDefaultValue($o['value'])) { ?>checked="checked"<?php   } ?> />
			<span><i data-formify-ffid="<?php   echo $field->ffID; ?>" class="fa <?php   if($field->isDefaultValue($o['value'])) { ?>fa-check<?php   } ?>"></i></span>
			<?php   echo $o['value']; ?>
		</label>
		<?php   $i++; ?>
	<?php   } ?>	
</div>