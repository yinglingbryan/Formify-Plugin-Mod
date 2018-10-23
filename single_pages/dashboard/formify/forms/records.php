<?php  defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php  use \Concrete\Core\Block\View\BlockView; ?>

<div class="ccm-ui ccm-dashboard-content-full formify" ng-app="FormifyApp">
	
	<?php  Loader::packageElement('dashboard/form_nav','formify',array('f' => $f)); ?>
	
	<?php  if($action == '') { ?>
	
	<section id="formify-records" ng-controller="RecordsController" ng-cloak>
		<nav class="formify-records-search">
			<form>
				<input type="text" class="form-control" placeholder="<?php  echo t('Search Records'); ?>" ng-model="query" ng-keyup="$event.keyCode == 13 && search()" />
			</form>
			<a href="<?php  echo View::URL('/dashboard/formify/forms/records/edit/' . $f->getFormID()); ?>" class="btn btn-success"><i class="fa fa-plus"></i> <?php  echo t('Add a Record'); ?></a>
			<a ng-click="index()" class="btn btn-success"><i class="fa fa-spinner" ng-show="index.working"></i><i class="fa fa-check" ng-show="!index.working"></i> <?php  echo t('Re-Index Records'); ?> </a>
			<a href="<?php  echo View::URL('/dashboard/formify/forms/export'); ?>" class="btn btn-primary"><i class="fa fa-arrow-circle-down"></i> <?php  echo t('Export Records'); ?> </a> 
			<div class="clearfix"></div>
		</nav>
		<nav class="formify-records-nav">
			
			<!--<ul infinite-scroll="loadMoreRecords()" class="ui-sortable">-->
			<ul class="ui-sortable">
				<li ng-repeat="r in records | filter:query" ng-click="detail(r)" ng-class="r.rID == activeRecord.rID ? 'on' : 'off'">
					<span class="checkbox"><input type="checkbox" ng-click="r.toggleSelected();$event.stopPropagation();" class="record-checkbox"></span>
					<span class="description">
						<span>{{ r.name }}</span>
						<time>{{ r.created | date:'MMM d, y h:mm a' }}</time>
					</span>
					<span class="status">
						<i class="fa" ng-class="{'fa-check' : r.approval == '1', 'fa-times' : r.approval == '-1'}"></i>
					</span>
					<div class="clearfix"></div>
				</li>
				<li ng-show="loadingMore" class="more"></li>
				<li ng-show="moreRecords" style="text-align:center"><a class="btn btn-primary" ng-click="loadMoreRecords()"><?php  echo t('Load More Records'); ?></a></li>
			</ul>
	
		</nav>
		
		<article ng-class="(activeRecord && (selectedRecords.length == 0)) ? 'on' : 'off'" class="formify-record-detail">
			<div class="formify-record-detail-content">
				<nav>
					<ul class="formify-record-detail-approval">
						<li><i class="formify-record-detail-approve fa fa-check" ng-class="activeRecord.approval == '1' ? 'on' : 'off'" ng-click="activeRecord.toggleApprove()" ></i></li>
						<li><i class="formify-record-detail-reject fa fa-times" ng-class="activeRecord.approval == '-1' ? 'on' : 'off'" ng-click="activeRecord.toggleReject()"></i></li>
					</ul>
					
					<ul class="formify-record-detail-delete">
						<li><i class="formify-record-detail-delete fa fa-times" ng-click="activeRecord.delete()"></i></li>
					</ul>
					<div class="clearfix"></div>
				</nav>
				
				<div class="owner">
					<?php  echo t('ID'); ?>: {{ activeRecord.rID }} | <?php  echo t('Submitted by'); ?> {{ activeRecord.username || 'Guest' }} <?php  echo t('from'); ?> {{ activeRecord.ipAddress }}
				</div>
				
				<div class="edit">
					<a ng-click="activeRecord.rebuild()"><i class="fa fa-spinner" ng-show="activeRecord.scanning == 1"></i> <?php  echo t('Rebuild') ?></a> | 
					<a href="<?php  echo View::URL('dashboard/formify/forms/records/edit/'); ?>/{{ activeRecord.fID }}/{{ activeRecord.rID }}/{{ activeRecord.token }}"><?php  echo t('Edit'); ?></a>
				</div>
				
				<div class="clearfix"></div>
				
				<div ng-repeat="a in activeRecord.answers">
				
					<h3>{{ a.label }}</h3>
					<p>{{ a.friendlyValue }}</p>
	
				</div>
					
				<div ng-show="activeRecord.amountCharged > 0">
					<h3><?php  echo t('Amount Charged'); ?></h3>
					<p>{{ activeRecord.amountCharged }}</p>
					<h3><?php  echo t('Amount Paid'); ?></h3>
					<p>{{ activeRecord.amountPaid }}</p>
				</div>
				
			</div>
		</article>
		
		<article ng-show="selectedRecords.length > 0" class="formify-record-selected-records">
			<p>
				
			<p>{{ selectedRecords.length }} <ng-pluralize count="selectedRecords.length" when="{'1':'record','other':'records'}"></ng-pluralize> <?php  echo t('selected') ?></p>
			<ul>
				<li ng-click="processSelectedRecords('approve')" class="approve">
					<i class="fa fa-check"></i>
					<span><?php  echo t('Approve \'Em!'); ?></span>
				</li>
				<li ng-click="processSelectedRecords('reject')" class="reject">
					<i class="fa fa-times"></i>
					<span><?php  echo t('Reject \'Em!'); ?></span>
				</li>
				<li ng-click="processSelectedRecords('delete')" class="delete">
					<i class="fa fa-times"></i>
					<span><?php  echo t('Delete \'Em!') ?></span>
				</li>
		</article>
				
	</section>
	
	<div id="formify-user-search">
		<?php  Loader::element('users/search', array('controller' => $searchController)); ?>
	</div>
	
	<?php  } elseif($action == 'edit') { ?>
	
	<section id="formify-records">
	
  	<?php  if($_GET['success'] == '1') { ?>
  	<div class="formify-message">
    	Record added.
  	</div>
  	<?php  } ?>
	
		<?php  
		/*
		$a = Area::get($stack, STACKS_AREA_NAME);
	    foreach($stack->getBlocks() as $b) {
		    $btc = $b->getController();
		    $btc->fID = $f->getFormID();
		    $btc->rID = $rID;
		    $btc->token = $token;
		    $btc->context = 'dashboard';
	        $bv = new BlockView($b);
	        $bv->render('view');
	    }
	    */
	    
	    $bt = BlockType::getByHandle('formify_form');
	    $bt->controller->fID = $f->getFormID();
	    $bt->controller->rID = $rID;
	    $bt->controller->token = $token;
	    $bt->controller->context = 'dashboard';
        $bt->render('view');
	    
	    ?>
	    
	</section>
	
	<?php  } ?>
</div>

<?php  Loader::packageElement('dashboard/formify_nav','formify'); ?>