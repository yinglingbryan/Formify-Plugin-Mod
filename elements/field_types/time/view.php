<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  
if($field->defaultValue != '') {
	$timeParts = explode(' ',$field->defaultValue);
	$time = explode(':',trim($timeParts[0]));
	$hour = trim($time[0]);
	$minute = trim($time[1]);
	$ampm = trim($timeParts[1]);
}
?>

<div class="formify-field-label">
	<label><?php  echo $field->label; ?> <?php  echo $field->requiredIndicator; ?></label>
</div>

<div class="formify-field-input formify-time <?php  echo $field->fieldClass; ?>">
	
	<select name="<?php  echo $field->ffID; ?>[]">
		<option value=""></option>
		<?php  for($i=1;$i<=12;$i++) { ?>
			<option
				value="<?php  echo $i; ?>"
				<?php  if($hour == $i) { ?>
				selected="selected"
				<?php  } ?>
			><?php  echo $i; ?>
			</option>
		<?php  } ?>
	</select> :
	
	<select name="<?php  echo $field->ffID; ?>[]">
		<option value=""></option>
		<?php  for($i=0;$i<=59;$i++) { ?>
			<?php  if($i < 10) {
				$val = '0' . $i;	
			} else {
				$val = $i;
			} ?>
			<option value="<?php  echo $val; ?>"
				<?php  if($minute == $val) { ?>
					selected="selected"
				<?php  } ?>
			><?php  echo $val; ?>
			</option>
		<?php  } ?>
	</select>
	<select name="<?php  echo $field->ffID; ?>[]">
		<option value=""></option>
		<option value="AM"
		<?php  if($ampm == 'AM') { ?>
			selected="selected"
		<?php  } ?>
		>AM</option>
		<option value="PM"
		<?php  if($ampm == 'PM') { ?>
			selected="selected"
		<?php  } ?>
		>PM</option>
	</select>
	
</div>