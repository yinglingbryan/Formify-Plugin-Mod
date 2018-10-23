<?php  defined('C5_EXECUTE') or die('Access Denied'); ?>
<div class="ccm-ui ccm-dashboard-content-full formify" ng-app="FormifyApp">
	
	<?php  Loader::packageElement('dashboard/form_nav','formify',array('f' => $f)); ?>

	<section id="formify-fields" ng-controller="FieldsController" ng-cloak>
		<table class="ccm-search-results-table">
			<thead>
				<tr>
					<th class="false"><span ng-click="add()"><i class="fa fa-plus-circle" ng-show="!working"></i><i class="fa fa-spinner" ng-show="working"></i></span></th>
					<th class="false"><span><?php  echo t('Name'); ?></span></th>
					<th class="false"><span><?php  echo t('Type'); ?></span></th>
					<th class="false" colspan="7"></th>
				</tr>
			</thead>
			<tbody class="ui-sortable">
				<tr ng-repeat="ff in fields" class="ft-{{ff.type}}">
					<td><i ng-click="add(ff)" class="formify-field-add fa fa-plus-circle"  ng-class="{ 'rotating': ff.isLoading }"></i></td>
					<td>
						<input type="text" class="formify-field-label" ng-model="ff.label" ng-change="ff.notSaved = true" ng-blur="ff.update()" ng-enter="ff.update()" />
						<i class="formify-field-save-label-check fa fa-check-circle" ng-class="ff.notSaved ? 'on' : 'off'" ng-click="ff.notSaved = false"></i>
					</td>
					<td>
						<select class="form-control" ng-options="ft.handle as ft.name for ft in fieldTypes" ng-model="ff.type" ng-change="ff.update()"></select>
					</td>
					<td><i ng-click="edit(ff)" class="formify-field-edit fa fa-pencil"></i></td>
					<td>
						<i ng-class="(ff.isPrimary == '1') ? 'on' : 'off'" ng-click="ff.toggle('isPrimary')" class="formify-field-primary fa fa-heart" tooltip="<?php  echo t('Is Primary'); ?>"></i>
					</td>
					<td>
						<i ng-class="(ff.isRequired == '1') ? 'on' : 'off'" ng-click="ff.toggle('isRequired')" class="formify-field-required fa fa-asterisk" tooltip="<?php  echo t('Is Required'); ?>"></i>
					</td>
					<td>
						<i ng-class="(ff.isIndexable == '1') ? 'on' : 'off'" ng-click="ff.toggle('isIndexable')" class="formify-field-indexable fa fa-search" tooltip="<?php  echo t('Is Searchable'); ?>"></i>
					</td>
					<td>
						<i ng-class="(ff.includeInEmail == '1') ? 'on' : 'off'" ng-click="ff.toggle('includeInEmail')" class="formify-field-emailable fa fa-paper-plane" tooltip="<?php  echo t('Include in notifications'); ?>"></i>
					</td>
					<td><i ng-click="ff.delete()" class="formify-field-delete fa fa-times"></i></td>
					<td><i class="formify-field-sort fa fa-bars"></i></td>
				</tr>
			</tbody>
		</table>
		
		<div id="formify-field-settings" style="display:none;">
			<div class="ccm-ui">
				 <div>
			        <ul class="nav nav-tabs" id="formify-field-settings-tabs-nav">
				        <li class="active"><a href="#formify-field-settings-tab-general"><?php  echo t('General'); ?></a></li>
				        <li ng-show="activeFieldType.hasOptions"><a href="#formify-field-settings-tab-options"><?php  echo t('Options'); ?></a></li>
				        <li ng-show="activeFieldType.properties.length > 0"><a href="#formify-field-settings-tab-type">{{activeFieldType.name}} <?php  echo t('Properties'); ?></a></li>
			            <li><a href="#formify-field-settings-tab-rules"><?php  echo t('Visibility Rules'); ?></a></li>
			            <li><a href="#formify-field-settings-tab-integrations"><?php  echo t('Integrations'); ?></a></li>
			        </ul>
		        </div>
		        <br /><br />
		        <div class="formify-field-settings-tabs">
			        <div class="formify-field-settings-tab" id="formify-field-settings-tab-general">
			            <div class="form-group">
			                <?php  echo $form->label('submitLabel', t('Placeholder'))?>
			                <input name="placeholder" type="text" class="form-control" ng-model="activeField.placeholder" />
			            </div> 
						<div class="form-group">
			                <?php  echo $form->label('submitLabel', t('Default Value'))?>
			                <select name="defaultValueSource" class="form-control" ng-options="source.handle as source.name for source in defaultValueSources" ng-model="activeField.defaultValueSource"></select>
			            </div>
			            <div class="form-group" ng-show="activeField.defaultValueSource == 'static'">
				            <?php  echo $form->label('submitLabel', t('Specify Default Value'))?>
				             <input name="defaultValue" type="text" class="form-control" ng-model="activeField.defaultValue" />
			            </div>
			            <div class="form-group" ng-show="activeField.defaultValueSource == 'url'">
				            <?php  echo $form->label('submitLabel', t('URL Parameter'))?>
				             <input name="urlParameter" type="text" class="form-control" ng-model="activeField.urlParameter" />
			            </div>
			            <div class="form-group">
				            <?php  echo $form->label('submitLabel', t('Container Class'))?>
				             <input name="containerClass" type="text" class="form-control" ng-model="activeField.containerClass" />
			            </div>
			            <div class="form-group">
				            <?php  echo $form->label('submitLabel', t('Field Class'))?>
				             <input name="fieldClass" type="text" class="form-control" ng-model="activeField.fieldClass" />
			            </div>
						<div class="form-group">
			                <?php  echo $form->label('submitLabel', t('Handle'))?>
			                <input name="handle" type="text" class="form-control" ng-model="activeField.handle" />
			            </div>
			        </div>
			        
			        <div class="formify-field-settings-tab" id="formify-field-settings-tab-options" style="display:none">
				        
				        <div class="form-group" ng-show="activeFieldType.hasOptions">
			                <?php  echo $form->label('submitLabel', t('Options Source'))?>
			                <select name="optionsSource" class="form-control" ng-options="source.handle as source.name for source in optionsSources" ng-model="activeField.optionsSource"></select>
			            </div>
			            
			            <div class="form-group" ng-show="activeField.optionsSource == 'static'">
				            <?php  echo $form->label('submitLabel', t('Options'))?>
				            <textarea style="white-space:nowrap;overflow:auto;height:8em" class="form-control" name="options" ng-model="activeField.optionsValues" ng-list="&#10;" ng-trim="false"></textarea>
			            </div>
			            
			            <div class="form-group" ng-show="activeField.optionsSource == 'optionGroup'">
				            <?php  echo $form->label('submitLabel', t('Option Group'))?>
				            <select name="ogID" class="form-control" ng-options="og.ogID as og.name for og in optionGroups" ng-model="activeField.ogID"></select>
			            </div>
			            
			            <div class="form-group" ng-show="activeField.optionsSource == 'formRecords'">
				            <?php  echo $form->label('submitLabel', t('Source Form'))?>
				            <select name="ogFormID" class="form-control" ng-options="f.fID as f.name for f in forms" ng-model="activeField.ogFormID" ng-change="setSourceForm(activeField.ogFormID)"></select>
			            </div>
			            
			            <div class="form-group" ng-show="activeField.optionsSource == 'formRecords'">
				            <?php  echo $form->label('submitLabel', t('Source Field'))?>
				            <select name="ogFieldID" class="form-control" ng-options="sf.ffID as sf.label for sf in sourceForm.fields" ng-model="activeField.ogFieldID"></select>
			            </div>
			        </div>
			        
			        <div class="formify-field-settings-tab" id="formify-field-settings-tab-type" style="display:none">
				        
				        <div class="form-group" ng-show="activeFieldType.hasProperty('firstOptionBlank')">
				            <?php  echo $form->label('submitLabel', t('First Option is Blank'))?>
				            <div><input name="firstOptionBlank" type="checkbox" ng-model="activeField.firstOptionBlank" ng-checked="activeField.firstOptionBlank == '1'" ng-true-value="'1'" ng-false-value="'0'" /></div>
			            </div>
			        
			            <div class="form-group" ng-show="activeFieldType.hasProperty('size')">
				            <?php  echo $form->label('submitLabel', t('Field Size (Characters)'))?>
				            <input name="fieldSize" type="text" class="form-control" ng-model="activeField.fieldSize" />
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('regex')">
				            <?php  echo $form->label('submitLabel', t('Regular Expression'))?>
				            <input name="regex" type="text" class="form-control" ng-model="activeField.regex" />
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('maxLength')">
				            <?php  echo $form->label('submitLabel', t('Maximum Length (Characters)'))?>
				            <input name="maxLength" type="text" class="form-control" ng-model="activeField.maxLength" />
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('wysiwygFormat')">
				            <?php  echo $form->label('submitLabel', t('WYSIWYG Editor Format'))?>
				            <select name="toolbar" class="form-control" ng-options="o.value as o.label for o in wysiwygFormatOptions" ng-model="activeField.wysiwygFormat"></select>
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('toolbar')">
				            <?php  echo $form->label('submitLabel', t('Show Concrete5 Toolbar'))?>
				            <div><input type="checkbox" ng-model="activeField.toolbar" ng-checked="activeField.toolbar == '1'" ng-true-value="'1'" ng-false-value="'0'"></div>
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('price')">
				            <?php  echo $form->label('submitLabel', t('Price'))?>
				            <input name="price" type="text" class="form-control" ng-model="activeField.price" />
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('qtyStart')">
				            <?php  echo $form->label('submitLabel', t('Start Quantity'))?>
				            <input name="qtyStart" type="text" class="form-control" ng-model="activeField.qtyStart" />
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('qtyIncrement')">
				            <?php  echo $form->label('submitLabel', t('Increment'))?>
				            <input name="qtyIncrement" type="text" class="form-control" ng-model="activeField.qtyIncrement" />
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('qtyEnd')">
				            <?php  echo $form->label('submitLabel', t('End Quantity'))?>
				            <input name="qtyEnd" type="text" class="form-control" ng-model="activeField.qtyEnd" />
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('commerceName')">
				            <?php  echo $form->label('submitLabel', t('Commerce Name'))?>
				            <select name="commerceName" class="form-control" ng-options=""></select>
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('dateInterface')">
				            <?php  echo $form->label('submitLabel', t('Date Interface'))?>
				            <select name="dateInterface" class="form-control" ng-options="o.value as o.label for o in dateInterfaceOptions" ng-model="activeField.dateInterface"></select>
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('dateFormat') && activeField.dateInterface == 'popup'">
				            <?php  echo $form->label('submitLabel', t('Date Format'))?>
				            <select name="dateInterface" class="form-control" ng-options="o.value as o.label for o in dateFormatOptions" ng-model="activeField.dateFormat"></select>
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('minYear') && activeField.dateInterface == 'dropdown'">
				            <?php  echo $form->label('submitLabel', t('Minimum Year'))?>
				            <input name="minYear" type="text" class="form-control" ng-model="activeField.minYear" />
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('maxYear') && activeField.dateInterface == 'dropdown'">
				            <?php  echo $form->label('submitLabel', t('Maximum Year'))?>
				            <input name="maxYear" type="text" class="form-control" ng-model="activeField.maxYear" />
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('timeInterval')">
				            <?php  echo $form->label('submitLabel', t('Time Interval (Minutes)'))?>
				            <input name="timeInterval" type="text" class="form-control" ng-model="activeField.timeInterval" />
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('fsID')">
				            <?php  echo $form->label('submitLabel', t('File Set'))?>
				            <select name="fsID" class="form-control" ng-options="fs.fsID as fs.name for fs in fileSets" ng-model="activeField.fsID"></select>
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('nlToBr')">
				            <?php  echo $form->label('submitLabel', t('Convert New Lines to HTML Line Breaks'))?>
				            <div><input name="nlToBr" type="checkbox" ng-model="activeField.nlToBr" ng-checked="activeField.nlToBr == '1'" ng-true-value="'1'" ng-false-value="'0'" /></div>
			            </div>
			            
			            <div class="form-group" ng-show="activeFieldType.hasProperty('validateSection')">
				            <?php  echo $form->label('submitLabel', t('Validate Before Proceeding'))?>
				           <div><input name="validateSection" type="checkbox" ng-model="activeField.validateSection" ng-checked="activeField.validateSection == '1'" ng-true-value="'1'" ng-false-value="'0'" /></div>
			            </div>
			        
			        </div>
			        
			        <div class="formify-field-settings-tab" id="formify-field-settings-tab-rules" style="display:none">
				        
				        <p>
					        <label>
					        	<input type="checkbox" ng-model="activeField.enableRules" ng-checked="activeField.enableRules == '1'" ng-true-value="'1'" ng-false-value="'0'" />
								<?php  echo t('Enable visibility rules'); ?>
					        </label>
				        </p>
				        
				        <hr />
				        
				        <div ng-show="activeField.enableRules == '1'">
				        
					        <p>
						        <span class="form-inline">
							        <select class="form-control" ng-model="activeField.ruleAction">
								        <option value="hide">Hide</option>
								        <option value="show">Show</option>
							        </select>
						        </span>
						        <?php  echo t('this field if'); ?>
						        <span class="form-inline">
							        <select class="form-control" ng-model="activeField.ruleRequirement">
								        <option value="any">any</option>
								        <option value="all">all</option>
							        </select>
						        </span>
						        <?php  echo t('of the following are true.'); ?>
						    </p>
					        
					        <table class="table table-striped">
						        <tr>
							        <td>Field</td>
							        <td>Comparison</td>
							        <td>Value</td>
							        <td></td>
						        </tr>
						        <tr ng-repeat="r in activeField.rules">
							        <td>
								        <select class="form-control" ng-model="r.comparisonField" ng-options="cField as cField.label for cField in fields"></select>
								    </td>
							        <td>
								        <select class="form-control" ng-model="r.comparison">
									        <option value="=">is equal to</option>
									        <option value="!=">is not equal to</option>
									        <option value="~">contains</option>
									        <option value="!~">does not contain</option>
								        </select>
							        </td>
							        <td valign="center">
								        <input class="form-control" ng-model="r.value" ng-show="(!r.comparisonField.optionsValues.length) && (r.comparisonField.type != 'boolean')" type="text" value="" />
								        
								        <select class="form-control" ng-model="r.value" ng-show="r.comparisonField.optionsValues.length > 0" ng-options="o as o for o in r.comparisonField.optionsValues"></select>
								       
								        <select class="form-control" ng-model="r.value" ng-show="r.comparisonField.type == 'boolean'">
									        <option value="true">true</option>
									        <option value="false">false</option>
								        </select>
							        </td>
							        <td style="vertical-align:middle">
								        <a ng-click="activeField.deleteRule(r)">
									        <i style="color:#ccc" class="fa fa-times"></i>
								        </a>
								    </td>
						        </tr>
						        <tr>
							        <td colspan="4"><a ng-click="activeField.addRule()">Add Rule</a></td>
						        </tr>
					        </table>
				        
				        </div>
				        
			        </div>
			        
			        <div class="formify-field-settings-tab" id="formify-field-settings-tab-integrations" style="display:none">
				        
				        <div ng-if="i.activeField.integrations.length < 1">
					        <p>No integration options available.</p>
				        </div>
				        
				        <div ng-repeat="i in activeField.integrations">
					        
					        <div ng-if="i.fieldConfigKeys.length > 0">
					        
					            <strong>{{ i.name }}</strong>
					            
					            <br /><br />
					            
					            <div class="form-group" ng-repeat="key in i.fieldConfigKeys">
						           {{ key.name }}
						           
						           <div ng-if="key.options.length >= 1">
							           <select class="form-control" ng-model="activeField.integrationConfig[i.handle][key.handle]"  ng-options="o.value as o.label for o in key.options"></select>
						           </div>
						           
						           <div ng-if="key.options.length < 1">
							           <div><input class="form-control" type="text" ng-model="activeField.integrationConfig[i.handle][key.handle]" /></div>
						           </div>
					            </div>
					            
					            <hr />
					            
					        </div>
				            
				        </div>
				        
			        </div>
			        
			        
		        </div><!--/.formify-field-settings-tabs-->
		        
		        <div class="">
			        
		        </div>
		        
		        <div class="ccm-panel-detail-form-actions dialog-buttons">
					<button class="pull-left btn btn-default" type="button" data-dialog-action="cancel" data-panel-detail-action="cancel"><?php  echo t('Cancel')?></button>
					<button class="pull-right btn btn-success" type="button" ng-click="save(activeField);"><?php  echo t('Save Changes')?></button>
				</div>
			</div><!--/.ccm-ui-->
        </div><!--/#formify-field-settings-->
	</section>					
</div>

<?php  Loader::packageElement('dashboard/formify_nav','formify'); ?>

<script>
	
	var formifyWysiwygFormatOptions = [
		{
			value:'basic',
			label:'<?php  echo t('Basic'); ?>'
		},{
			value:'simple',
			label:'<?php  echo t('Simple'); ?>'
		},{
			value:'advanced',
			label:'<?php  echo t('Advanced'); ?>'
		},{
			value:'office',
			label:'<?php  echo t('Office'); ?>'
		}
	];
	
	var formifyToolbarOptions = [
		{
			value: 'popup',
			label: '<?php  echo t('Calendar Popup'); ?>'
		},{
			value: 'mdy',
			label: '<?php  echo t('M-D-Y Dropdowns'); ?>'
		},{
			value: 'dmy',
			label: '<?php  echo t('D-M-Y Dropdowns'); ?>'
		}
	];
	
	var formifyDateInterfaceOptions = [
		{
			value: 'dropdown',
			label: '<?php  echo t('Dropdowns'); ?>'
		},{
			value: 'popup',
			label: '<?php  echo t('Calendar Popup'); ?>'
		}
	];
	
	var formifyDateFormatOptions = [
		{
			value: 'F j, Y',
			label: '<?php  echo date('F j, Y'); ?>'
		},{
			value: 'j F, Y',
			label: '<?php  echo date('j F, Y'); ?>'
		},{
			value: 'n/j/y',
			label: '<?php  echo date('n/j/y'); ?>'
		},{
			value: 'j/n/y',
			label: '<?php  echo date('j/n/y'); ?>'
		},{
			value: 'Y/n/j',
			label: '<?php  echo date('Y/n/j'); ?>'
		},{
			value: 'n-j-y',
			label: '<?php  echo date('n-j-y'); ?>'
		},{
			value: 'j-n-y',
			label: '<?php  echo date('j-n-y'); ?>'
		},{
			value: 'Y-n-j',
			label: '<?php  echo date('Y-n-j'); ?>'
		}
	];
	
	var formifyFileSets = [
		{
			fsID: '0',
			name: '<?php  echo t('None'); ?>'
		}
		<?php  foreach($fileSets as $fs) { ?>
		,{
			fsID: '<?php  echo $fs->getFileSetID(); ?>',
			name: '<?php  echo addslashes($fs->getFileSetName()); ?>'
		}
		<?php  } ?>
	];
	
	var formifyDefaultValueSources = [
		{
			name: '<?php  echo t('None'); ?>',
			handle: ''
		},{
			name: '<?php  echo t('Specify Manually'); ?>',
			handle: 'static'
		},{
			name: '<?php  echo t('URL Parameter'); ?>',
			handle: 'url'
		},{
			name: '<?php  echo t('Username'); ?>',
			handle: 'username'
		},{
			name: '<?php  echo t('User Email'); ?>',
			handle: 'email'
		},{
			name: '<?php  echo t('User ID'); ?>',
			handle: 'uID'
		}
		<?php  foreach($userAttributes as $ak) { ?>
		,{
			name: '<?php  echo addslashes($ak->getAttributeKeyName()); ?>',
			handle: '<?php  echo $ak->getAttributeKeyHandle(); ?>'
		}
		<?php  } ?>
	];
	
	var formifyOptionsSources = [
		{
			name: '<?php  echo t('Specify Manually'); ?>',
			handle: 'static',
		},{
			name: '<?php  echo t('Option Group'); ?>',
			handle: 'optionGroup'
		},{
			name: '<?php  echo t('Form Records'); ?>',
			handle: 'formRecords'
		}
	];
	
	
</script>