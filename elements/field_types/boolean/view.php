<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="formify-field-input formify-boolean <?php   echo $field->fieldClass; ?>">
	<fieldset class="formify-fieldset">
		<label class="formify-checkbox-label" for="formify-field-<?php   echo $field->ffID; ?>">
			<input class="formify-field formify-checkbox" type="checkbox" name="<?php   echo $field->ffID;?>" value="true" id="formify-field-<?php   echo $field->ffID; ?>" <?php   if($field->isDefaultValue('true')) { ?>checked="checked"<?php   } ?> />
			<span><i class="fa <?php   if($field->isDefaultValue('true')) { ?>fa-check<?php   } ?>"></i></span>
			<?php   echo $field->label; ?> <?php   echo $field->requiredIndicator; ?>
		</label>
	</fieldset>
</div>