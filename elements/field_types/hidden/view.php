<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<input type="hidden" id="sem-field-<?php   echo $field->ffID; ?>" name="<?php   echo $field->ffID; ?>" class="<?php   echo $field->fieldClass; ?>" value="<?php   echo htmlspecialchars($field->defaultValue); ?>" />