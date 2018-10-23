<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="sem-field-label">
	<label><?php   echo $field->label; ?> <?php   echo $field->requiredIndicator; ?></label>
</div>
<div class="formify-field-input formify-wysiwyg <?php   echo $field->fieldClass; ?>">
	<textarea id="sem-field-<?php   echo $field->ffID; ?>" name="<?php   echo $field->ffID; ?>"><?php   echo htmlspecialchars($field->defaultValue); ?></textarea>
</div>

<script type="text/javascript">
var CCM_EDITOR_SECURITY_TOKEN = "<?php     echo Loader::helper('validation/token')->generate('editor')?>";
$(document).ready(function() {
	$('#sem-field-<?php   echo $field->ffID; ?>').redactor({
            minHeight: '130',
            'concrete5': {
                filemanager: true,
                sitemap: true,
                lightbox: true
            },
            'plugins': [
                'fontfamily','fontsize','fontcolor','concrete5'
            ]
        });
});
</script>