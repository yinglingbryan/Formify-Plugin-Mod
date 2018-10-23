<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui ccm-dashboard-content-full formify" ng-app="FormifyApp">
	
	<section id="formify-templates" class="full" ng-controller="TemplatesController" ng-cloak>
		<nav class="formify-templates-nav">
			<ul>
				<li>
					<div class="percent-70">
						<input class="form-control" type="text" ng-model="newTemplateName" placeholder="Template Name"></select>
					</div>
					<div class="percent-30">
						<button style="margin-left:1em" class="btn btn-success" type="button" ng-click="add(newTemplateName);"><?php   echo t('Add')?></button>
					</div>
					<div class="clearfix"></div>
				</li>
			</ul>
			
			
			<ul>
				<li ng-repeat="t in templates" ng-click="detail(t)" ng-class="t.tID == activeTemplate.tID ? 'on' : 'off'">
					<span class="name">{{ t.name }}</span>
					<span class="count">{{ t.formName }}</span>
				</li>
			</ul>
	
		</nav>
		
		<article ng-class="activeTemplate ? 'on' : 'off'" class="formify-template-detail">
			
			<div class="formify-template-detail-content">
				
				<nav ng-show="!activeTemplate.isFile">
					<ul class="formify-template-detail-delete">
						<li><i class="formify-template-detail-delete fa fa-times" ng-click="activeTemplate.delete()"></i></li>
					</ul>
					<div class="clearfix"></div>
				</nav>
			
				<div class="formify-template-detail-setting formify-template-detail-name">
					<label><?php   echo t('Template Name'); ?></label>
					<input class="form-control" ng-model="activeTemplate.name" ng-disabled="activeTemplate.isFile" />
					<div class="clearfix"></div>
				</div>
			
				<div class="formify-template-detail-setting formify-template-form-name" ng-show="!activeTemplate.isFile">
					<label><?php   echo t('Form'); ?></label>
					<select class="form-control" ng-options="f.fID as f.name for f in forms" ng-model="activeTemplate.fID"></select>
					<div class="clearfix"></div>
				</div>
				
				<div class="clearfix"></div>
				
				<div>
			        <ul class="nav nav-tabs" id="formify-templates-tabs-nav">
				        <li class="active"><a href="#formify-templates-tab-header"><?php   echo t('Header'); ?></a></li>
				        <li><a href="#formify-templates-tab-content"><?php   echo t('Content'); ?></a></li>
				        <li><a href="#formify-templates-tab-footer"><?php   echo t('Footer'); ?></a></li>
				        <li><a href="#formify-templates-tab-empty"><?php   echo t('Empty'); ?></a></li>
			        </ul>
					<br /><br />
		        </div>
		        
		        <div class="formify-templates-tab" id="formify-templates-tab-header">
			        <a class="btn btn-default" href="#" ng-show="!activeTemplate.isFile" ng-click="openSitemap('header')"><?php   echo t('Insert Path to Page'); ?></a>
			        <a class="btn btn-default" href="#" ng-show="!activeTemplate.isFile" ng-click="openFileManager('header')"><?php   echo t('Insert Path to File'); ?></a>
			        <h4><?php   echo t('Displays once at the top of the template.'); ?></h4>
			        <textarea wrap="off" ng-model="activeTemplate.header" ng-disabled="activeTemplate.isFile"></textarea>
		        </div>
		        
		        <div class="formify-templates-tab" id="formify-templates-tab-content" style="display:none">
			        <select style="display:inline-block;width:auto" class="form-control" ng-show="!activeTemplate.isFile" ng-options="placeholder.handle as placeholder.label for placeholder in activeTemplate.placeholders" ng-model="activeTemplate.activePlaceholder"></select>
			        <a class="btn btn-primary" href="#" ng-show="!activeTemplate.isFile" ng-click="activeTemplate.appendPlaceholder('content')"><?php   echo t('Insert'); ?></a>
			        <a class="btn btn-default" href="#" ng-show="!activeTemplate.isFile" ng-click="openSitemap('content')"><?php   echo t('Insert Path to Page'); ?></a>
			        <a class="btn btn-default" href="#" ng-show="!activeTemplate.isFile" ng-click="openFileManager('content')"><?php   echo t('Insert Path to File'); ?></a>
			        <h4><?php   echo t('Displays for each item in the record set.'); ?></h4>
			        <textarea wrap="off" ng-model="activeTemplate.content" ng-disabled="activeTemplate.isFile"></textarea>
		        </div>
		        
		        <div class="formify-templates-tab" id="formify-templates-tab-footer" style="display:none">
			        <a class="btn btn-default" href="#" ng-show="!activeTemplate.isFile" ng-click="openSitemap('footer')"><?php   echo t('Insert Path to Page'); ?></a>
			        <a class="btn btn-default" href="#" ng-show="!activeTemplate.isFile" ng-click="openFileManager('footer')"><?php   echo t('Insert Path to File'); ?></a>
			        <h4><?php   echo t('Displays once at the bottom of the template.'); ?></h4>
			        <textarea wrap="off" ng-model="activeTemplate.footer" ng-disabled="activeTemplate.isFile"></textarea>
		        </div>
		        
		        <div class="formify-templates-tab" id="formify-templates-tab-empty" style="display:none">
			        <a class="btn btn-default" href="#" ng-show="!activeTemplate.isFile" ng-click="openSitemap('empty')"><?php   echo t('Insert Path to Page'); ?></a>
			        <a class="btn btn-default" href="#" ng-show="!activeTemplate.isFile" ng-click="openFileManager('empty')"><?php   echo t('Insert Path to File'); ?></a>
			        <h4><?php   echo t('Displays when no records are found.'); ?></h4>
			    	<textarea wrap="off" ng-model="activeTemplate.empty" ng-disabled="activeTemplate.isFile"></textarea>
		        </div>
		        
		        <hr />
					
				<p ng-show="!activeTemplate.isFile">
					<button class="pull-right btn btn-success" type="button" ng-click="save(activeTemplate);"><?php   echo t('Save')?></button>
				</p>
				<div class="clearfix"></div>
		        
			</div>
			
		</article>
	
</div>

<?php   Loader::packageElement('dashboard/formify_nav','formify'); ?>