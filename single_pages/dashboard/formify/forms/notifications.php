<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui ccm-dashboard-content-full formify" ng-app="FormifyApp">
	
	<?php   Loader::packageElement('dashboard/form_nav','formify',array('f' => $f)); ?>
	
	<section id="formify-notifications" ng-controller="NotificationsController" ng-cloak>
		<nav class="formify-notifications-nav">
			<ul>
				<li>
					<div class="percent-70">
						<select class="form-control" ng-options="nt.value as nt.label for nt in notificationTypes" ng-model="newNotificationType"></select>
					</div>
					<div class="percent-30">
						<button class="pull-right btn btn-success" type="button" ng-click="add(newNotificationType);"><i class="fa fa-plus-circle" ng-show="!add.working"></i><i class="fa fa-spinner" ng-show="add.working"></i></button>
					</div>
					<div class="clearfix"></div>
				</li>
			</ul>
			
			
			<ul>
				<li ng-repeat="n in notifications" ng-click="detail(n)" ng-class="n.nID == activeNotification.nID ? 'on' : 'off'">
					<span class="subject">{{ n.subject }}</span>
					<span ng-show="!n.toIsDynamic" class="to">Sent to {{ n.toAddress }} on {{ n.type }}</span>
					<span ng-show="n.toIsDynamic" class="to">Sent to {{ n.toLabel }} on {{ n.type }}</span>
					<span ng-show="!n.toAddress" class="to"><em><?php   echo t('No address specified'); ?></em></span>
				</li>
			</ul>
	
		</nav>
		
		<article ng-class="activeNotification ? 'on' : 'off'" class="formify-notification-detail">
			<div class="formify-notification-detail-content">
				<nav>
					<ul class="formify-notification-detail-delete">
						<li><i class="formify-notification-detail-delete fa fa-times" ng-click="activeNotification.delete()"></i></li>
					</ul>
					<div class="clearfix"></div>
				</nav>
				<div>
					
					<div class="formify-notification-detail-setting">
						<label><?php   echo t('Send On'); ?>:</label>
						<div class="formify-notification-input">
							<select class="form-control" ng-options="nt.value as nt.label for nt in notificationTypes" ng-model="activeNotification.type"></select>
						</div>
						<div class="clearfix"></div>
					</div>
					
					<div class="formify-notification-detail-setting">
						<label><?php   echo t('Type'); ?>:</label>
						<div class="formify-notification-input">
							<input type="checkbox" ng-model="activeNotification.toIsDynamic" ng-click="toggleDynamic()" /> <?php   echo t('Dynamic destination'); ?>
						</div>
						<div class="clearfix"></div>
					</div>
					
					<div ng-show="!activeNotification.toIsDynamic" class="formify-notification-detail-setting">
						<label><?php   echo t('To'); ?>:</label>
						<div class="formify-notification-input">
							<input class="form-control" type="text" ng-model="activeNotification.toAddress" placeholder="<?php   echo t('user@domain.com'); ?>" />
						</div>
						<div class="clearfix"></div>
					</div>
					
					<div ng-show="activeNotification.toIsDynamic" class="formify-notification-detail-setting">
						<label><?php   echo t('To'); ?>:</label>
						<div class="formify-notification-input">
							<select class="form-control" ng-options="ff.ffID as ff.label for ff in fields" ng-model="activeNotification.toAddress" ng-change="updateToLabel()"></select>
						</div>
						<div class="clearfix"></div>
					</div>
					
					<div class="formify-notification-detail-setting">
						<label><?php   echo t('From Name'); ?>:</label>
						<div class="formify-notification-input">
							<input class="form-control" type="text" ng-model="activeNotification.fromName" placeholder="<?php   echo t('Name (optional)'); ?>" />
						</div>
						<div class="clearfix"></div>
					</div>
					
					<div class="formify-notification-detail-setting">
						<label><?php   echo t('Reply Type'); ?>:</label>
						<div class="formify-notification-input">
							<input type="checkbox" ng-model="activeNotification.replyIsDynamic" ng-click="toggleDynamicReply()" /> <?php   echo t('Dynamic Reply Destination'); ?>
						</div>
						<div class="clearfix"></div>
					</div>
					
					<div ng-show="!activeNotification.replyIsDynamic" class="formify-notification-detail-setting">
						<label><?php   echo t('Reply To'); ?>:</label>
						<div class="formify-notification-input">
							<input class="form-control" type="text" ng-model="activeNotification.replyAddress" placeholder="<?php   echo t('Email Address (optional)'); ?>" />
						</div>
						<div class="clearfix"></div>
					</div>
					
					<div ng-show="activeNotification.replyIsDynamic" class="formify-notification-detail-setting">
						<label><?php   echo t('Reply To'); ?>:</label>
						<div class="formify-notification-input">
							<select class="form-control" ng-options="ff.ffID as ff.label for ff in fields" ng-model="activeNotification.replyAddress" ng-change="updateReplyLabel()"></select>
						</div>
						<div class="clearfix"></div>
					</div>
					
					<div class="formify-notification-detail-setting">
						<label><?php   echo t('Subject'); ?>:</label>
						<div class="formify-notification-input">
							<input class="form-control" type="text" ng-model="activeNotification.subject" />
						</div>
						<div class="clearfix"></div>
					</div>
					
					<div class="formify-notification-detail-setting">
						<label><?php   echo t('Template'); ?>:</label>
						<div class="formify-notification-input">
							<select class="form-control" ng-options="t.tID as t.name for t in templates" ng-model="activeNotification.tID"></select>
						</div>
						<div class="clearfix"></div>
					</div>
					
					<hr />
					
					<p><input type="checkbox" ng-model="activeNotification.hasCondition" ng-click="toggleCondition()" /> <?php   echo t('Only send this notification under the following condition'); ?></p>
					
					<div ng-show="activeNotification.hasCondition">
						
						<div class="formify-notification-detail-setting">
							<label><?php   echo t('Field'); ?></label>
							<div class="formify-notification-input">
								<select class="form-control" ng-options="ff.ffID as ff.label for ff in fields" ng-model="activeNotification.conditionFieldID"></select>
							</div>
							<div class="clearfix"></div>
						</div>
						
						<div class="formify-notification-detail-setting">
							<label><?php   echo t('Type'); ?></label>
							<div class="formify-notification-input">
								<select class="form-control" ng-options="c.value as c.label for c in conditionTypes" ng-model="activeNotification.conditionType"></select>
							</div>
							<div class="clearfix"></div>
						</div>
						
						<div class="formify-notification-detail-setting">
							<label><?php   echo t('Value'); ?></label>
							<div class="formify-notification-input">
								<input class="form-control" type="text" ng-model="activeNotification.conditionValue" />
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
					
					<hr />
					
					<p>
						<button class="pull-right btn btn-success" type="button" ng-click="save(activeNotification);"><?php   echo t('Save Notification')?></button>
					</p>
					<div class="clearfix"></div>
				</div>
				{{ n.subject }}
			</div>
		</article>
				
	</section>
</div>

<?php   Loader::packageElement('dashboard/formify_nav','formify'); ?>