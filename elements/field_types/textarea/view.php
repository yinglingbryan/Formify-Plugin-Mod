<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="formify-field-label">
	<label><?php   echo $field->label; ?> <?php   echo $field->requiredIndicator; ?></label>
</div>
<div class="formify-field-input formify-textarea <?php   echo $field->fieldClass; ?>">
	<textarea id="sem-field-<?php   echo $field->ffID; ?>" name="<?php   echo $field->ffID; ?>"><?php   echo htmlspecialchars($field->defaultValue); ?></textarea>
</div>