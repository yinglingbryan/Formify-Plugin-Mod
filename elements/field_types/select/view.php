<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="formify-field-label">
	<label><?php   echo $field->label; ?> <?php   echo $field->requiredIndicator; ?></label>
</div>
<div class="formify-field-input formify-select <?php   echo $field->fieldClass; ?>">
	<select name="<?php   echo $field->ffID; ?>">
	<?php   if($field->firstOptionBlank) { ?><option value=""></option><?php   } ?>
	<?php   foreach($field->getOptions() as $o) { ?>
		<option value="<?php   echo htmlentities($o['value']); ?>" <?php   if($field->defaultValue == $o['value']) { ?>selected="selected"<?php   } ?>><?php   echo htmlentities($o['label']); ?></option>
	<?php   } ?>
	</select>
</div>