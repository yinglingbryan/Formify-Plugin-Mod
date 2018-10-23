<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  
	if(is_numeric($field->defaultValue)) {
		$m = date('n',$field->defaultValue);
		$d = date('j',$field->defaultValue);
		$y = date('Y',$field->defaultValue);
	}
	
	if(($field->minYear != '') && ($field->maxYear != '')) {
		$minYear = $field->minYear;
		$maxYear = $field->maxYear;
	} else {
		$minYear = date('Y') - 80;
		$maxYear = date('Y') + 10;	
	}
	
	switch($field->dateFormat) {
		case 'F j, Y':
			$dateFormat = 'MM d, yy';
			break;
		case 'j F, Y':
			$dateFormat = 'd MM, yy';
			break;
		case 'n/j/y':
			$dateFormat = 'm/d/y';
			break;
		case 'j/n/y':
			$dateFormat = 'd/m/y';
			break;
		case 'Y/n/j':
			$dateFormat = 'yy/m/d';
			break;
		case 'n-j-y':
			$dateFormat = 'm-d-y';
			break;
		case 'j-n-y':
			$dateFormat = 'd-m-y';
			break;
		case 'Y-n-j':
			$dateFormat = 'yy-m-d';
			break;
	}
?>

<div class="formify-field-label">
	<label><?php  echo $field->label; ?> <?php  echo $field->requiredIndicator; ?></label>
</div>

<div class="formify-field-input formify-date <?php  echo $field->fieldClass; ?>">
	
	<?php  if($field->dateInterface == 'popup') { ?>
	
		<input type="text" id="sem-field-<?php  echo $field->ffID; ?>" hasDatepicker name="<?php  echo $field->ffID; ?>" value="<?php  if($field->defaultValue != '') { echo htmlspecialchars(date($field->dateFormat,$field->defaultValue)); } ?>" placeholder="<?php  echo htmlspecialchars($field->placeholder); ?>" maxlength="<?php  echo $field->fieldSize; ?>" />
		
		<script type="text/javascript">
		$(function () {
			$("#sem-field-<?php  echo $field->ffID; ?>").datepicker({
				dateFormat: '<?php  echo $dateFormat; ?>',
				changeYear: true,
				showAnim: 'fadeIn'
			});
		});</script>
	
	<?php  } else { ?>
	
		<select name="<?php  echo $field->ffID; ?>[]">
			<option value=""></option>
			<option value="1" <?php  echo $ft->renderSelected($m,'1'); ?>><?php  echo t('January'); ?></option>
			<option value="2" <?php  echo $ft->renderSelected($m,'2'); ?>><?php  echo t('February'); ?></option>
			<option value="3" <?php  echo $ft->renderSelected($m,'3'); ?>><?php  echo t('March'); ?></option>
			<option value="4" <?php  echo $ft->renderSelected($m,'4'); ?>><?php  echo t('April'); ?></option>
			<option value="5" <?php  echo $ft->renderSelected($m,'5'); ?>><?php  echo t('May'); ?></option>
			<option value="6" <?php  echo $ft->renderSelected($m,'6'); ?>><?php  echo t('June'); ?></option>
			<option value="7" <?php  echo $ft->renderSelected($m,'7'); ?>><?php  echo t('July'); ?></option>
			<option value="8" <?php  echo $ft->renderSelected($m,'8'); ?>><?php  echo t('August'); ?></option>
			<option value="9" <?php  echo $ft->renderSelected($m,'9'); ?>><?php  echo t('September'); ?></option>
			<option value="10" <?php  echo $ft->renderSelected($m,'10'); ?>><?php  echo t('October'); ?></option>
			<option value="11" <?php  echo $ft->renderSelected($m,'11'); ?>><?php  echo t('November'); ?></option>
			<option value="12" <?php  echo $ft->renderSelected($m,'12'); ?>><?php  echo t('December'); ?></option>
		</select>
		
		<select name="<?php  echo $field->ffID; ?>[]">
			<option value=""></option>
			<?php  for($i=1;$i<=31;$i++) { ?>
				<option value="<?php  echo $i; ?>" <?php  echo $ft->renderSelected($d,$i); ?>><?php  echo $i; ?></option>
			<?php  } ?>
		</select>
		
		<select name="<?php  echo $field->ffID; ?>[]">
			<option value=""></option>
			<?php  for($i=$minYear;$i<=$maxYear;$i++) {?>
				<option value="<?php  echo $i; ?>" <?php  echo $ft->renderSelected($y,$i); ?>><?php  echo $i; ?></option>
			<?php  } ?>
		</select>
	
	<?php  } ?>
	
</div>