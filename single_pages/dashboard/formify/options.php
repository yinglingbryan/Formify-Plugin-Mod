<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui ccm-dashboard-content-full formify" ng-app="FormifyApp">

	<section id="formify-options" class="full" ng-controller="OptionsController" ng-cloak>
		<nav class="formify-options-nav">
			<ul>
				<li>
					<div class="percent-70">
						<input class="form-control" type="text" ng-model="newOptionGroupName" placeholder="Option Group Name"></select>
					</div>
					<div class="percent-30">
						<button style="margin-left:1em" class="btn btn-success" type="button" ng-click="add(newOptionGroupName);"><?php   echo t('Add')?></button>
					</div>
					<div class="clearfix"></div>
				</li>
			</ul>
			
			
			<ul>
				<li ng-repeat="og in optionGroups" ng-click="detail(og)" ng-class="og.ogID == activeOptionGroup.ogID ? 'on' : 'off'">
					<span class="name">{{ og.name }}</span>
					<span class="count">{{ og.optionsValues.length }} <?php   echo t('options'); ?></span>
				</li>
			</ul>
	
		</nav>
		
		<article ng-class="activeOptionGroup ? 'on' : 'off'" class="formify-option-group-detail">
			<div class="formify-option-group-detail-content">
				<nav>
					<ul class="formify-option-detail-delete">
						<li><i class="formify-option-detail-delete fa fa-times" ng-click="activeOptionGroup.delete()"></i></li>
					</ul>
					<div class="percent-50">
						<input type="text" class="form-control" ng-model="activeOptionGroup.name" />
					</div>
					<div class="clearfix"></div>
				</nav>
				
				<hr />
				
				<div>
					
					<textarea wrap="off" style="overflow:auto;height:15em" class="form-control" ng-model="activeOptionGroup.optionsValues" ng-list="&#10;" ng-trim="false"></textarea>
					
					<hr />
					
					<p>
						<button class="btn btn-success" type="button" ng-click="save(activeOptionGroup);"><?php   echo t('Save Options')?></button>
					</p>
					<div class="clearfix"></div>
				</div>
			</div>
		</article>
				
	</section>
	
</div>

<?php   Loader::packageElement('dashboard/formify_nav','formify'); ?>