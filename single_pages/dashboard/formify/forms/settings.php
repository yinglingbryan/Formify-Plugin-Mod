<?php   defined('C5_EXECUTE') or die('Access Denied'); ?>
<?php   $pageSelector = Loader::helper('form/page_selector'); ?>

<div class="ccm-ui ccm-dashboard-content-full formify formify-settings" ng-app="FormifyApp" ng-controller="SettingsController">
		
	<?php   Loader::packageElement('dashboard/form_nav','formify',array('f' => $f)); ?>
	
	<section ng-cloak>
        <div>
	        <ul class="nav nav-tabs formify-tabs-nav" id="formify-settings-tabs-nav">
		        <li class="active"><a href="#formify-settings-tab-submission"><?php   echo t('Submission'); ?></a></li>
		        <li><a href="#formify-settings-tab-ecommerce"><?php   echo t('E-Commerce'); ?></a></li>
		        <li><a href="#formify-settings-tab-errors"><?php   echo t('Errors'); ?></a></li>
		        <li><a href="#formify-settings-tab-permissions"><?php   echo t('Permissions'); ?></a></li>
		        <li><a href="#formify-settings-tab-integrations"><?php   echo t('Integrations'); ?></a></li>
		        <li><a href="#formify-settings-tab-advanced"><?php   echo t('Advanced'); ?></a></li>
	        </ul>
        </div>
		
		<div id="formify-settings-tabs">
			
			<div class="formify-settings-tab" id="formify-settings-tab-submission">
		
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('name', t('Name')); ?>
		                <div class="ccm-search-field-content">
		                    <input type="text" class="form-control" ng-model="form.name" />
		                </div>
		            </div>
		        </div>
		
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('submitLabel', t('Submit Button Label')); ?>
		                <div class="ccm-search-field-content">
		                    <input type="text" class="form-control" ng-model="form.submitLabel" />
		                </div>
		            </div>
		        </div>
		        
		        <div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('afterSubmit', t('After Submit')); ?>
		                <div class="ccm-search-field-content">
			                <select class="form-control" ng-model="form.submitAction">
				                <option value="message"><?php   echo t('Show a message'); ?></option>
				                <option value="cID"><?php   echo t('Redirect to a page in this site'); ?></option>
				                <option value="URL"><?php   echo t('Redirect to a specific URL'); ?></option>
			                </select>
		                </div>
		            </div>
		        </div>
		        
		        <div class="ccm-search-fields-row" ng-show="form.submitAction == 'message'">
		            <div class="form-group">
		                <?php   echo $form->label('message', t('Message')); ?>
		                <div class="ccm-search-field-content">
		                    <textarea class="form-control" ng-model="form.submitActionMessage"></textarea>
		                </div>
		            </div>
		        </div>
		        
		        
		        
		        <div class="ccm-search-fields-row" ng-show="form.submitAction == 'cID'">
		            <div class="form-group">
		                <?php   echo $form->label('cID', t('Page')); ?>
		                <div class="ccm-search-field-content">
			            	<input type="text" class="form-control" ng-model="form.submitActionCollectionName" disabled />
			            	<a href="#" ng-show="!activeTemplate.isFile" ng-click="openSitemap('header')"><?php   echo t('Select Page'); ?></a>
		                </div>
		                <input type="hidden" ng-model="form.submitActionCollectionID" id="formify-cID-hidden" />
		            </div>
		        </div>
				
				<div class="ccm-search-fields-row" ng-show="form.submitAction == 'URL'">
		            <div class="form-group">
		                <?php   echo $form->label('url', t('URL')); ?>
		                <div class="ccm-search-field-content">
		                    <input type="text" class="form-control" ng-model="form.submitActionURL" />
		                </div>
		            </div>
		        </div>
		        
		        <div class="ccm-search-fields-row" ng-show="(form.submitAction == 'cID') || (form.submitAction == 'URL')">
			        <div class="form-group">
				        <?php   echo $form->label('passRecordID', t('Pass Record ID as URL Parameter')); ?>
				        <div class="ccm-search-field-content">
					    	<input type="checkbox" class="ccm-input-checkbox" ng-model="form.submitActionPassRecordID" />
				        </div>
			        </div>
		        </div>
				
				<div class="ccm-search-fields-row" ng-show="(form.submitAction == 'cID') || (form.submitAction == 'URL')">
		            <div class="form-group">
		                <?php   echo $form->label('parameter', t('URL Parameter Name')); ?>
		                <div class="ccm-search-field-content">
		                    <input type="text" class="form-control" ng-model="form.submitActionRecordIDParameter" />
		                </div>
		            </div>
		        </div>

		        <div class="ccm-search-fields-row">
			        <div class="form-group">
				        <?php   echo $form->label('captcha', t('Use CAPTCHA?')); ?>
				        <div class="ccm-search-field-content">
					    	<input type="checkbox" class="ccm-input-checkbox" ng-model="form.captcha" ng-checked="form.captcha == '1'" ng-true-value="'1'" ng-false-value="'0'" />
				        </div>
			        </div>
		        </div>



		        <!-- added by MGHUS to integrate recaptcha //-->
		        <div class="ccm-search-fields-row">
			        <div class="form-group">
				        <?php   echo $form->label('grecaptcha', t('Use Google Invisible reCAPTCHA?')); ?>
				        <div class="ccm-search-field-content">
					    	<input type="checkbox" class="ccm-input-checkbox" ng-model="form.grecaptcha" ng-checked="form.grecaptcha == '1'" ng-true-value="'1'" ng-false-value="'0'" />
				        </div>
			        </div>
		        </div>
				<div class="ccm-search-fields-row" ng-show="form.grecaptcha == 1">
			        <div class="form-group">
		                <?php   echo $form->label('grecaptchaSiteKey', t('Google reCAPTCHA Site Key')); ?>
		                <div class="ccm-search-field-content">
		                	<textarea class="form-control" ng-model="form.grecaptchaSiteKey" disabled="disabled"></textarea>
		                    <?php /* <input type="text" class="form-control" ng-model="form.grecaptchaSiteKey" disabled="disabled" /> */ ?>
		                    <a class="edit-global" style="cursor:pointer;">Edit</a>
		                </div>
		            </div>
		        </div>
		        <div class="ccm-search-fields-row" ng-show="form.grecaptcha == 1">
			        <div class="form-group">
		                <?php   echo $form->label('grecaptchaSecretKey', t('Google reCAPTCHA Secret Key')); ?>
		                <div class="ccm-search-field-content">
		                	<textarea class="form-control" ng-model="form.grecaptchaSecretKey" disabled="disabled"></textarea>
		                    <?php /* <input type="text" class="form-control" ng-model="form.grecaptchaSecretKey" disabled="disabled" /> */ ?>
		                    <a class="edit-global" style="cursor:pointer;">Edit</a>
		                </div>
		            </div>
		        </div>

		    <script type="text/javascript">
			$(document).ready(function() {
			$('.edit-global').each(function() {
				$(this).click(function() {

						// show confirm
						labelText = $(this).closest(".form-group").find("label").first().text();
						result = confirm(labelText+" is a global field. Editing it's value will change the value for all formify forms on this site.\n\nAdditionally, entering an invalid here will cause the Google Invisible reCAPTCHA to stop working on all forms.\n\nAre you sure you want to edit the "+labelText+"?");

						if (result) {
							$(this).closest(".form-group").find("textarea").first().prop('disabled', false); // enable
							$(this).remove(); // remove edit button															
							}

						});

					});
				});			
		    </script>


		        
			</div><!--/.formify-settings-tab-->
			
			<div class="formify-settings-tab" id="formify-settings-tab-ecommerce" style="display:none">
				
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('maxRecords', t('Currency Symbol')); ?>
		                <div class="ccm-search-field-content">
		                    <input type="text" class="form-control" ng-model="form.commerceCurrencySymbol" />
		                </div>
		            </div>
		        </div>
				<!--
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('maxRecords', t('Message before processing e-commerce')); ?>
		                <div class="ccm-search-field-content">
		                    <input type="text" class="form-control" ng-model="form.commerceConfirmationMessage" />
		                </div>
		            </div>
		        </div>
		        -->
				
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('maxRecords', t('Maximum Order Price')); ?>
		                <div class="ccm-search-field-content">
		                    <input type="text" class="form-control" ng-model="form.commerceMaximumOrderPrice" />
		                </div>
		            </div>
		        </div>
				
			</div><!--/.formify-settings-tab-->
			
			<div class="formify-settings-tab" id="formify-settings-tab-errors" style="display:none">
				
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('errorValidation', t('Validation Error Message')); ?>
		                <div class="ccm-search-field-content">
		                    <textarea class="form-control" ng-model="form.errorValidation"></textarea>
		                </div>
		            </div>
		        </div>
				
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('errorValidation', t('Max Submissions Error Message')); ?>
		                <div class="ccm-search-field-content">
		                    <textarea class="form-control" ng-model="form.errorSubmissions"></textarea>
		                </div>
		            </div>
		        </div>
				
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('errorValidation', t('Captcha Error Message')); ?>
		                <div class="ccm-search-field-content">
		                    <textarea class="form-control" ng-model="form.errorCaptcha"></textarea>
		                </div>
		            </div>
		        </div>
				
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('errorValidation', t('E-Commerce Error Message')); ?>
		                <div class="ccm-search-field-content">
		                    <textarea class="form-control" ng-model="form.errorEcommerce"></textarea>
		                </div>
		            </div>
		        </div>
				
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('errorValidation', t('Permission Error Message')); ?>
		                <div class="ccm-search-field-content">
		                    <textarea class="form-control" ng-model="form.errorPermission"></textarea>
		                </div>
		            </div>
		        </div>
		        
			</div><!--/.formify-settings-tab-->
			
			<div class="formify-settings-tab" id="formify-settings-tab-permissions" style="display:none">
				
				<table class="table">
					<tr>
						<th>&nbsp;</th>
						<th><?php   echo t('Add'); ?></th>
						<th><?php   echo t('Edit'); ?></th>
						<th><?php   echo t('Approve'); ?></th>
						<th><?php   echo t('Delete'); ?></th>
					</tr>
					<tr>
						<td><?php   echo t('Record Owner'); ?></td>
						<td></td>
						<td><input type="checkbox" ng-model="form.ownerCanEdit" ng-checked="form.ownerCanEdit == '1'" ng-true-value="'1'" ng-false-value="'0'"  /></td>
						<td></td>
						<td><input type="checkbox" ng-model="form.ownerCanDelete" ng-checked="form.ownerCanDelete == '1'" ng-true-value="'1'" ng-false-value="'0'" /></td>
					</tr>
					<?php   foreach($groups as $g) { ?>
					<tr>
						<td><?php   echo $g->getGroupName(); ?></td>
						<td><input type="checkbox" ng-click="form.togglePermission('add',<?php   echo $g->gID; ?>)" ng-checked="form.checkPermission('add',<?php   echo $g->gID; ?>)" /></td>
						<td><input type="checkbox" ng-click="form.togglePermission('edit',<?php   echo $g->gID; ?>)" ng-checked="form.checkPermission('edit',<?php   echo $g->gID; ?>)" /></td>
						<td><input type="checkbox" ng-click="form.togglePermission('approve',<?php   echo $g->gID; ?>)" ng-checked="form.checkPermission('approve',<?php   echo $g->gID; ?>)" /></td>
						<td><input type="checkbox" ng-click="form.togglePermission('delete',<?php   echo $g->gID; ?>)" ng-checked="form.checkPermission('delete',<?php   echo $g->gID; ?>)" /></td>
					</tr>
					<?php   } ?>
				</table>
		        
			</div><!--/.formify-settings-tab-->
			
			<div class="formify-settings-tab" id="formify-settings-tab-integrations" style="display:none">
				
				<div class="ccm-search-fields-row" ng-repeat="i in form.integrations">
					<h4>{{ i.handle }}</h4>
		            <div class="form-group">
		                <?php   echo $form->label('', t('Enable')); ?>
				        <div class="ccm-search-field-content">
					    	<input type="checkbox" class="ccm-input-checkbox" ng-click="form.toggleIntegration(i.handle)" ng-checked="i.active" ng-true-value="'1'" ng-false-value="'0'" />
				        </div>
			        </div>
		        </div>
				
			</div><!--/.formify-integrations-tab-->
			
			<div class="formify-settings-tab" id="formify-settings-tab-advanced" style="display:none">
				
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('', t('Handle')); ?>
		                <div class="ccm-search-field-content">
		                    <input type="text" class="form-control" ng-model="form.handle" />
		                </div>
		            </div>
		        </div>
				
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('', t('Required Indicator')); ?>
		                <div class="ccm-search-field-content">
		                    <input type="text" class="form-control" ng-model="form.requiredIndicator" />
		                </div>
		            </div>
		        </div>
				
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('', t('Required Color')); ?>
		                <div class="ccm-search-field-content">
		                    <input type="text" class="form-control" ng-model="form.requiredColor" />
		                </div>
		            </div>
		        </div>
				
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('', t('Maximum Number of Records')); ?>
		                <div class="ccm-search-field-content">
		                    <input type="text" class="form-control" ng-model="form.maxSubmissions" />
		                </div>
		            </div>
		        </div>
				
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('', t('Default Record Status')); ?>
		                <div class="ccm-search-field-content">
		                    <select class="form-control" ng-model="form.defaultRecordStatus">
			                    <option value="0"><?php   echo t('Pending'); ?></option>
			                    <option value="1"><?php   echo t('Approved'); ?></option>
			                    <option value="-1"><?php   echo t('Denied'); ?></option>
		                    </select>
		                </div>
		            </div>
		        </div>
				
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('', t('Record lifespan')); ?>
		                <div class="ccm-search-field-content">
		                    <input type="text" class="form-control" ng-model="form.autoExpire" /> <?php   echo t('days'); ?>
		                </div>
		            </div>
		        </div>
				
				<div class="ccm-search-fields-row">
		            <div class="form-group">
		                <?php   echo $form->label('', t('Limit to one record per user')); ?>
		                <div class="ccm-search-field-content">
		                    <input type="checkbox" class="ccm-input-checkbox" ng-model="form.oneRecordPerUser" ng-false-value="0" />
		                </div>
		            </div>
		        </div>
		        
			</div><!--/.formify-settings-tab-->
	        
		</div><!--/#formify-settings-tabs-->
		
		<br /><br />
		
		<button id="" class="btn btn-success" ng-click="form.update()"><i ng-show="!form.working" class="fa fa-check"></i><i ng-show="form.working" class="fa fa-spinner"></i> <?php   echo t('Save Settings') ?></button>
		
	</section>
	
	<div class="clearfix"></div>
	
</div>

<?php   Loader::packageElement('dashboard/formify_nav','formify'); ?>