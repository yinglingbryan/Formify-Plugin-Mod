<?php  defined('C5_EXECUTE') or die(_("Access Denied.")) ?>

<?php  if(is_object($f)) { ?>
<?php 

/* GET AND CHECK THE SITE KEY AND SECRET KEY */
$grecaptcha = $f->grecaptcha;
$grecaptcha_site_key = trim($f->grecaptchaSiteKey);
$grecaptcha_secret_key = trim($f->grecaptchaSecretKey);

if ($grecaptcha == 1) {
$jswarn = array();
if (strlen($grecaptcha_site_key) != 40) $jswarn[] = "Warning: reCAPTCHA Site key invalid (should be 40 characters long)";
if (strlen($grecaptcha_secret_key) != 40) $jswarn[] = "Warning: reCAPTCHA Secret key invalid (should be 40 characters long)";
if (count($jswarn) > 0) { 
  $grecaptcha = 0;
  $jswarn[] = "Form will be submitted with no reCAPTCHA";
  $jswarning = implode("\\n",$jswarn);
?>
<script type="text/javascript">
    $(window).load(function() {
      console.log("----------------------------------------------------------------------");
      console.log("<?php echo $jswarning; ?>");
      console.log("----------------------------------------------------------------------");
    });
</script>
<?php } } ?>

<form class="formify-form <?php  if($disableDefaultCSS != '1') { ?>with-style<?php  } ?>" id="formify-form-<?php  echo $f->fID; ?>-<?php  echo intval($bID); ?>" data-bid="<?php  echo intval($bID); ?>" data-fid="<?php  echo $f->fID; ?>" data-rid="<?php  echo intval($rID); ?>" data-context="<?php  echo $context; ?>" enctype="multipart/form-data" method="post" action="<?php  echo DIR_REL; ?>/index.php/formify/go/<?php  echo $f->fID; ?>">
	
	<input type="hidden" name="rID" value="<?php  echo $rID; ?>" />
	<input type="hidden" name="token" value="<?php  echo $token; ?>" />
	<input type="hidden" name="source" value="<?php  echo htmlentities(URL::to(Page::getCurrentPage())); ?>" />
	<input type="hidden" name="referrer" value="<?php  echo htmlentities($_SERVER['HTTP_REFERER']); ?>" />
  <input type="hidden" name="rcresponse" value="" />
  <input type="hidden" name="grecaptcha" value="<?php echo $grecaptcha; ?>" />
  <input type="hidden" name="grecaptchaSiteKey" value="<?php echo $grecaptcha_site_key; ?>" />

	<?php  if(count($f->getSections()) > 0) { ?>
  	<?php  foreach($f->getSections() as $s) { ?>
  		<div class="formify-section" data-formify-section-index="<?php  echo $s->index; ?>">
  			
  			<?php  if(count($s->getFields()) > 0) { ?>
    			<?php  foreach($s->getFields() as $ff) { ?>
    				<div
    					class="formify-field-container <?php  echo $ff->containerClass; ?>"
    					id="formify-field-container-<?php  echo $ff->ffID; ?>"
    					data-ffid="<?php  echo $ff->ffID; ?>"
    					data-field-type="<?php  echo $ff->getType()->handle; ?>"
    					data-rule-count="<?php  echo count($ff->getRules()); ?>"
    					data-unmet-rule-count="<?php  echo count($ff->getRules()); ?>"
    					data-rule-action="<?php  echo $ff->ruleAction; ?>"
    					data-rule-requirement="<?php  echo $ff->ruleRequirement; ?>"
    				>
    					<?php  $ff->render(); ?>
    				</div>
    			<?php  } ?>
    		<?php  } ?>
  			
  			<?php  if($s->index == count($f->getSections())) { //Last section ?>
  			
  				<?php  foreach($f->getActiveIntegrations() as $i) { ?>
  					
  					<?php  foreach($i->getFields() as $ff) { ?>
  						<div class="formify-field-container">
  							<?php  $ff->render(); ?>
  						</div>
  					<?php  } ?>
  					
  				<?php  } ?>
  			
  				<?php  if($captcha) { ?>
  				<div class="formify-field-container">
  					<div class="formify-field-input captcha">
  						<?php  
  						$captchaLabel = $captcha->label();
  						if (!empty($captchaLabel)) {
  							?>
  							<label class="control-label"><?php  echo $captchaLabel; ?></label>
  							<?php  
  						}
  						?>
  						<div><?php  $captcha->display(); ?></div>
  						<div><?php  $captcha->showInput(); ?></div>
  					</div>
  				</div>
  				<?php  } ?>
  			<?php  } ?>
  			
  			<div class="formify-field-container">
  				<div class="formify-field-input">
  					<?php  if($s->index != 1) { ?>
  					<button class="formify-nav-button" data-formify-section-index="<?php  echo $s->index; ?>" data-formify-section-index-target="<?php  echo $s->index - 1; ?>"><?php  echo t('Previous'); ?></button>
  					<?php  } ?>
  					<?php  if($s->index < count($f->getSections())) { ?>
  					<button class="formify-nav-button" data-formify-section-index="<?php  echo $s->index; ?>" data-formify-section-index-target="<?php  echo $s->index + 1; ?>"><?php  echo t('Next'); ?> <i style="display:none" class="fa fa-spinner fa-spin"></i></button>
  					<?php  } ?>
  					<?php  if($s->index == count($f->getSections())) { ?>
  					<input type="submit" value="<?php  echo htmlentities($f->submitLabel); ?>" />
  					<?php  } ?>
  				</div>
  			</div>
  		
  		</div>
  	<?php  } ?>
  <?php  } ?>
	
</form>

<?php  if($_GET['nojs'] != 1) { ?>
<script type="text/javascript">	
$(document).ready(function() {
	
	<?php  foreach($f->getRules() as $r) { ?>
	$('#formify-field-container-<?php  echo $r['comparisonFieldID']; ?>').rulify(<?php  echo json_encode($r); ?>);
	<?php  } ?>
	
	$('#formify-form-<?php  echo $f->fID; ?>-<?php  echo intval($bID); ?>').formify();
});
</script>
<?php  } ?>

<?php  } ?>