<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div ng-app="FormifyApp" class="formify" ng-cloak>
	
	<div ng-controller="FormsController">
		
		<div class="formify-form-new form-inline">
			<form>
				<input class="form-control" type="text" placeholder="<?php  echo t('New Form Name'); ?>" ng-model="newForm.name" ng-class="{error:newForm.hasError}" />
				<input class="form-control" type="email" placeholder="<?php  echo t('Notification Email'); ?>" ng-model="newForm.email" />
				<button class="btn btn-success" ng-click="add()"><?php  echo t('Create New Form'); ?> <i class="fa fa-spinner" ng-show="add.working"></i><i class="fa fa-plus-circle" ng-show="!add.working"></i></button>
			</form>
		</div>
		
		<div class="formify-form-mode">
			<a class="btn btn-default"><i ng-class="magic ? 'on' : 'off'" ng-click="toggleMagic()" class="fa fa-magic formify-magic launch-tooltip" title="<?php  echo t('Toggle Magic Mode'); ?>"></i></a>
			<a class="btn btn-default"><i ng-click="editSettings()" class="fa fa-gear formify-settings launch-tooltip" title="<?php  echo t('Default Settings'); ?>"></i></a>
			<div class="btn-group view-mode">
				<button type="button" class="btn btn-default" id="view-mode-grid"><i class="fa fa-th-large"></i></button>
				<button type="button" class="btn btn-default" id="view-mode-table"><i class="fa fa-bars"></i></button>
			</div>
		</div>
		
		<div class="formify-form-filter">
			<input class="form-control" id="formify-form-filter-input" type="text" placeholder="<?php  echo t('Search Forms'); ?>" />
		</div>
		
		<div class="clearfix"></div>
		
		<hr />
		
		<div ng-show="itemsLoading > 0">
			<div class="formify-intro">
				<img src="<?php  echo DIR_REL; ?>/packages/formify/logo.png" class="formify-spinner" style="width:100px" />
				<h3><?php  echo t('Loading forms...'); ?></h3>
			</div>
		</div>
		
		<div ng-show="itemsLoading == '0'">
		
			<div class="formify-intro" ng-show="!forms.length" ng-animate="animate">
				<img src="<?php  echo DIR_REL; ?>/packages/formify/logo.png" style="width:100px" />
				<h3><?php  echo t('Welcome to Formify.'); ?></h3>
				<p><?php  echo t('Create a form by entering a name into the box above.'); ?></p>
			</div>
			
			<table id="formify-forms-table-view" class="ccm-search-results-table on" style="width:100%" ng-cloak ng-show="forms.length > 0">
				<thead>
					<tr>
						<th class="false" colspan="2"><span><?php  echo t('Forms'); ?></span></th>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="f in forms">
						<td>{{f.name}}</td>
						<td>
							<div class="btn-group" style="float:right">
								<a class="btn btn-default settings" href="<?php  echo View::url('/dashboard/formify/forms/settings')?>/{{ f.fID }}">
									<i class="fa fa-cog"></i>
									Settings
								</a>
								<a class="btn btn-default fields" href="<?php  echo View::url('/dashboard/formify/forms/fields')?>/{{ f.fID }}">
									<i class="fa fa-list-alt"></i>
									Fields
								</a>
								<a class="btn btn-default records" href="<?php  echo View::url('/dashboard/formify/forms/records')?>/{{ f.fID }}">
									<i class="fa fa-inbox"></i>
									Records
								</a>
								<a class="btn btn-default notifications" href="<?php  echo View::url('/dashboard/formify/forms/notifications')?>/{{ f.fID }}">
									<i class="fa fa-paper-plane"></i>
									Notifications
								</a>
								<a class="btn btn-default integrations" href="<?php  echo View::url('/dashboard/formify/forms/integrations')?>/{{ f.fID }}">
									<i class="fa fa-puzzle-piece"></i>
									Integrations
								</a>
								<a class="btn btn-default export" href="<?php  echo View::url('/dashboard/formify/forms/export')?>/{{ f.fID }}">
									<i class="fa fa-arrow-circle-down"></i>
									Export
								</a>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			
			<div id="formify-forms-grid-view" ng-show="forms.length > 0">
				
				<div>
  				    <ul class="nav nav-tabs formify-tabs-nav" id="formify-groups-tabs-nav">
  			        <li class="active"><a href="#" ng-click="activeGroupID = '0'" ui-on-drop="addFormToGroup($event,$data,'0')"><?php  echo t('All forms'); ?></a></li>
				        <li ng-repeat="g in groups">
				        	<a ng-show="!g.edit" ng-dblclick="g.edit = true" ng-click="g.filterBy()" ui-on-drop="addFormToGroup($event,$data,g.gID)" href="#">{{ g.name }}</a>
				        	<a href="#" ng-show="g.edit">
					        	<input type="text" ng-model="g.name" ng-enter="g.update()" ng-blur="g.update()" />
					        	<i class="fa fa-times" ng-click="g.delete()"></i>
					        </a>
                </li>
				        <li ng-show="newGroup.ready">
				        	<a href="#">
					        	<input type="text" ng-model="newGroup.name" ng-enter="addGroup()" />
					        	<i class="fa fa-times" ng-click="newGroup.ready = false"></i>
					        </a>
					    </li>
				        <li><a href="#" ng-click="newGroup.ready = true"><i class="fa fa-plus"></i></a></li>
			        </ul>
		        </div>
				
				<div class="formify-form-box" ng-repeat="f in forms | filter:{ gID:activeGroupID }:true" ui-draggable="true" drag="f" ng-cloak>
					<i class="fa fa-times formify-form-delete" ng-click="f.delete()"></i>
					<div class="formify-form-title">
						<i ng-class="f.magic ? 'on' : 'off'" class="fa fa-magic formify-form-magic launch-tooltip" title="<?php  echo t('Toggle Magic Mode'); ?>" ng-click="f.toggle('magic')"></i>
						<input class="form-box-name" type="text" name="name" ng-model="f.name" ng-blur="f.update()" ng-enter="f.update()" />
					</div>
					<h4>
						<em>{{ f.recordCount }}</em>
						<span><?php  echo t('Records'); ?></span>
					</h4>
					<ul>
						<li class="settings">
							<a href="<?php  echo View::url('/dashboard/formify/forms/settings')?>/{{ f.fID }}">
								<i class="fa fa-cog"></i>
								<span><?php  echo t('Settings'); ?></span>
							</a>
						</li>
						<li class="fields">
							<a href="<?php  echo View::url('/dashboard/formify/forms/fields')?>/{{ f.fID }}">
								<i class="fa fa-list-alt"></i>
								<span><?php  echo t('Fields'); ?> ({{ f.fieldCount }})</span>
							</a>
						</li>
						<li class="records">
							<a href="<?php  echo View::url('/dashboard/formify/forms/records')?>/{{ f.fID }}">
								<i class="fa fa-inbox"></i>
								<span><?php  echo t('Records'); ?></span>
							</a>
						</li>
						<li class="notifications">
							<a href="<?php  echo View::url('/dashboard/formify/forms/notifications')?>/{{ f.fID }}">
								<i class="fa fa-paper-plane"></i>
								<span><?php  echo t('Notifications'); ?></span>
							</a>
						</li>
						<li class="integrations">
							<a href="<?php  echo View::url('/dashboard/formify/forms/integrations')?>/{{ f.fID }}">
								<i class="fa fa-puzzle-piece"></i>
								<span><?php  echo t('Integrations'); ?></span>
							</a>
						</li>
						<li class="export">
							<a href="<?php  echo View::url('/dashboard/formify/forms/export')?>/{{ f.fID }}">
								<i class="fa fa-arrow-circle-down"></i>
								<span><?php  echo t('Export'); ?></span>
							</a>
						</li>
					</ul>
				</div><!--/.formify-form-box-->
			</div><!--/#formify-forms-grid-view-->
			
		</div><!--/[ng-show="loadComplete"]-->
		
	</div>

</div><!--/ng-app-->

<?php  Loader::packageElement('dashboard/formify_nav','formify'); ?>