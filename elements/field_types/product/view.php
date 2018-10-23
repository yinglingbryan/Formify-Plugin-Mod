<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  $fi = $field->getFormInfo(); ?>
<?php  $symbol = $fi['commerceCurrencySymbol']; ?>

<div class="formify-field-label">
	<label><?php  echo $field->label; ?> <?php  echo $field->requiredIndicator; ?></label>
</div>
<div class="formify-field-input formify-product <?php  echo $field->fieldClass; ?>">
	<?php  if($field->price == 0) { ?>
		<?php  echo $symbol; ?><input type="text" class="formify-field" name="<?php  echo $field->ffID; ?>" value="<?php  echo $field->defaultValue; ?>" />
	<?php  } elseif (($field->qtyStart == $field->qtyEnd) && ($field->qtyStart > 0)) { //Display automatically checked box: User must purchase the product ?>
		<fieldset>
			<label>
				<span><i class="fa fa-check"></i></span>
				<?php  echo $symbol; ?><?php  echo number_format($field->price,2); ?>
			</label>
		</fieldset>
		<input type="hidden" class="formify-field formify-hidden" id="formify-field-<?php  echo $field->ffID; ?>" name="<?php  echo $field->ffID; ?>" value="<?php  echo $field->qtyEnd; ?>" />
	<?php  } elseif (($field->qtyEnd == 1) && ($field->qtyStart == 0)) { //Display regular checkbox: User has an option to purchase only 1 of the product ?>
		<fieldset>
			<label class="formify-checkbox-label">
				<input class="formify-field formify-checkbox" type="checkbox" name="<?php   echo $field->ffID;?>" value="<?php  echo $field->qtyEnd; ?>" id="formify-field-<?php   echo $field->ffID; ?>" />
				<span><i class="fa"></i></span>
				<?php  echo $symbol; ?><?php  echo number_format($field->price,2); ?>
			</label>
		</fieldset>
	<?php  } elseif (($field->qtyEnd - $field->qtyStart > 1) && ($field->qtyIncrement > 0)) { //Display dropdown: User selects how many to purchase ?>
		<select class="formify-field formify-select" id="formify-field-<?php  echo $field->ffID; ?>" name="<?php  echo $field->ffID; ?>">';
			<?php  for($i = $field->qtyStart;$i<=$field->qtyEnd;$i+=$field->qtyIncrement) { ?>
				<option value="<?php  echo $i; ?>"><?php  echo $i; ?></option>'
			<?php  } ?>
		</select> x <?php  echo $symbol; ?><?php  echo number_format($field->price,2); ?>
	<?php  } else { //Allow for donations ?>
		<?php  echo $symbol; ?> <input type="text" class="formify-field" name="<?php  echo $field->ffID; ?>" value="<?php  echo $field->defaultValue; ?>" />
	<?php  } ?>
</div>