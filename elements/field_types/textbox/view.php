<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="formify-field-label">
	<label><?php  echo $field->label; ?> <?php  echo $field->requiredIndicator; ?></label>
</div>
<div class="formify-field-input formify-textbox <?php  echo $field->fieldClass; ?>">
	<input type="text" id="sem-field-<?php  echo $field->ffID; ?>" name="<?php  echo $field->name; ?>" value="<?php  echo htmlspecialchars($field->defaultValue); ?>" placeholder="<?php  echo htmlspecialchars($field->placeholder); ?>" maxlength="<?php  echo $field->fieldSize; ?>" />
</div>