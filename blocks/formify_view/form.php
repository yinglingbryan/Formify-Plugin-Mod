<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  
$ps = Loader::helper('form/page_selector');
?>
<div class="ccm-ui">
	<p>
	<?php   print Loader::helper('concrete/ui')->tabs(array(
		array('formify-general', t('General'), true),
		array('formify-data', t('Data')),
		array('formify-user-controls', t('User Controls'))
	));?>
	</p>
	
	<div class="ccm-tab-content" id="ccm-tab-content-formify-general" style="display:block">
		
        <div class="form-group">
            <?php   echo $form->label('fID', t('Form')); ?>
            <select class="form-control ccm-select" name="fID" id="formify-view-fid">
	            <?php   foreach($forms as $formifyForm) { ?>
	            	<option value="<?php   echo $formifyForm->fID; ?>" <?php   if($formifyForm->fID == $fID) { ?>selected="selected"<?php   } ?>><?php   echo $formifyForm->name; ?></option>
	            <?php   } ?>
            </select>
        </div>
		
        <div class="form-group">
            <?php   echo $form->label('listTemplateID', t('List Template')); ?>
			<select class="form-control ccm-select" name="listTemplateID">
	            <?php   foreach($templates as $t) { ?>
	            	<option value="<?php   echo $t->tID; ?>" <?php   if($t->tID == $listTemplateID) { ?>selected="selected"<?php   } ?>><?php   echo $t->name; ?></option>
	            <?php   } ?>
            </select>
        </div>
        
        <div class="form-group">
            <?php   echo $form->label('detailDestination', t('Detail Destination')); ?>
			<select class="form-control ccm-select" name="detailDestination" id="formify-view-detail-destination">
            	<option value="block" <?php   if($detailDestination == 'block') { ?>selected="selected"<?php   } ?>><?php   echo t('This page'); ?></option>
            	<option value="page" <?php   if($detailDestination == 'page') { ?>selected="selected"<?php   } ?>><?php   echo t('Another page'); ?></option>
            </select>
        </div>
		
        <div class="form-group" id="formify-view-detail-template-wrapper">
            <?php   echo $form->label('detailTemplateID', t('Detail Template')); ?>
			<select class="form-control ccm-select" name="detailTemplateID">
	            <?php   foreach($templates as $t) { ?>
	            	<option value="<?php   echo $t->tID; ?>" <?php   if($detailTemplateID == $t->tID) { ?>selected="selected"<?php   } ?>><?php   echo $t->name; ?></option>
	            <?php   } ?>
            </select>
        </div>
        
        <div class="form-group" id="formify-view-detail-cid-wrapper">
	        <?php   echo $form->label('detailCID', t('Detail Destination Page')); ?>
            <?php   echo $ps->selectPage('detailCID', $detailCID); ?>
        </div>
        
	</div>
	
	<div class="ccm-tab-content" id="ccm-tab-content-formify-data">
		
		<div class="form-group">
            <?php   echo $form->label('', t('Sort By')); ?>
            <select class="form-control ccm-select" name="sortBy" id="formify-view-sort-order">
	            <option data-static="true" value="dateCreated" <?php   if($sortBy == 'dateCreated') { ?>selected="selected"<?php   } ?>><?php   echo t('Date Submitted'); ?></option>
	            <option data-static="true" value="dateUpdated" <?php   if($sortBy == 'dateUpdated') { ?>selected="selected"<?php   } ?>><?php   echo t('Date Updated'); ?></option>
	            <?php   if(is_object($f)) { ?>
		            <?php   foreach($f->getFields() as $ff) { ?>
			            <option value="<?php   echo $ff->ffID; ?>" <?php   if($sortBy == $ff->ffID) { ?>selected="selected"<?php   } ?>><?php   echo $ff->ffID . ' ' . $sortBy; ?></option>
		            <?php   } ?>
	            <?php   } ?>
            </select>
        </div>
		
		<div class="form-group">
            <?php   echo $form->label('', t('Sort Order')); ?>
            <select class="form-control ccm-select" name="sortOrder">
            	<option value="ASC" <?php   if($sortOrder == 'ASC') { ?>selected="selected"<?php   } ?>><?php   echo t('Ascending'); ?></option>
            	<option value="DESC" <?php   if($sortOrder == 'DESC') { ?>selected="selected"<?php   } ?>><?php   echo t('Descending'); ?></option>
            	<option value="RAND" <?php   if($sortOrder == 'RAND') { ?>selected="selected"<?php   } ?>><?php   echo t('Random'); ?></option>
            </select>
        </div>
		
		<div class="form-group">
            <?php   echo $form->label('pageSize', t('Items Per Page')); ?>
            <input type="text" class="form-control ccm-input-text" name="pageSize" value="<?php   echo $pageSize; ?>" />
        </div>
		
		<div class="form-group">
            <div class="checkbox">
                <label>
                <?php   echo $form->checkbox('displayPaginator', 1, $displayPaginator); ?>
                <?php   echo t('Show pagination'); ?>
                </label>
            </div>
        </div>
		
		<div class="form-group">
           <div class="checkbox">
                <label>
                <?php   echo $form->checkbox('includeExpired', 1, $includeExpired); ?>
                <?php   echo t('Show expired records'); ?>
                </label>
            </div>
        </div>
        
        <div class="form-group">
	        <div class="checkbox">
                <label>
                <?php   echo $form->checkbox('requireApproval', 1, $requireApproval); ?>
                <?php   echo t('Show only approved records'); ?>
                </label>
            </div>
        </div>
		
		<div class="form-group">
            <div class="checkbox">
                <label>
                <?php   echo $form->checkbox('requireOwnership', 1, $requireOwnership); ?>
                <?php   echo t('Show only records owned by current user'); ?>
                </label>
            </div>
        </div>
		
	</div>
	
	<div class="ccm-tab-content" id="ccm-tab-content-formify-user-controls">
		
		<div class="form-group">
            <div class="checkbox">
                <label>
                <?php   echo $form->checkbox('enableSearch', 1, $enableSearch); ?>
                <?php   echo t('Enable Search'); ?>
                </label>
            </div>
        </div>
        
        <div class="form-group">
           <div class="checkbox">
                <label>
                <?php   echo $form->checkbox('enableSearchReset', 1, $enableSearchReset); ?>
                <?php   echo t('Enable Search Reset'); ?>
                </label>
            </div>
        </div>
        
        <div class="form-group">
            <div class="checkbox">
                <label>
                <?php   echo $form->checkbox('enableDateFilter', 1, $enableDateFilter); ?>
                <?php   echo t('Enable Date Filter'); ?>
                </label>
            </div>
        </div>
        
        <div class="form-group">
            <div class="checkbox">
                <label>
                <?php   echo $form->checkbox('enableUserSort', 1, $enableUserSort); ?>
                <?php   echo t('Enable User Sort'); ?>
                </label>
            </div>
        </div>
        
        <div class="form-group">
            <?php   echo $form->label('', t('Sort Label')); ?>
            <input type="text" class="form-control ccm-input-text" name="sortLabel" value="<?php   echo $sortLabel; ?>" />
        </div>
        
        <div class="form-group">
            <?php   echo $form->label('', t('Sort Button Label')); ?>
            <input type="text" class="form-control ccm-input-text" name="sortButtonLabel" value="<?php   echo $sortButtonLabel; ?>" />
        </div>
        
        <div class="form-group" id="formify-view-sortable-fields">
            <?php   echo $form->label('', t('Sortable Fields')); ?>
            <?php   if(is_object($f)) { ?>
	            <?php   foreach($f->getFields() as $ff) { ?>
	            	<div class="checkbox" data-sortable-field="true">
		            	<label>
		            		<input type="checkbox" class="ccm-input-checkbox" name="sortableFields[]" value="<?php   echo $ff->ffID; ?>" /> <?php   echo $ff->shortLabel; ?>
		            	</label>
	            	</div>
	            <?php   } ?>
            <?php   } ?>
        </div>
		
	</div>
	
</div>

<script type="text/javascript">

function formifySelectSortableFields() {
	<?php   if($sortableFields != '') { ?>
		var sortableFields = <?php   echo $sortableFields; ?>;
	<?php   } else { ?>
		var sortableFields = [];
	<?php   } ?>
	if(sortableFields) {
		for(var i = 0;i < sortableFields.length;i++) {
			var field = $('input[name="sortableFields[]"][value="' + sortableFields[i] + '"]');
			if(field.length > 0) {
				field[0].checked = true;
			}
		}
	}
}

function formifyCheckDetailDestination() {
	switch($('#formify-view-detail-destination').val()) {
		case 'block':
			$('#formify-view-detail-cid-wrapper').hide();
			$('#formify-view-detail-template-wrapper').show();
			break;
		case 'page':
			$('#formify-view-detail-template-wrapper').hide();
			$('#formify-view-detail-cid-wrapper').show();
			break;
	}
}

function formifyPopulateSortableFields(sortBy) {
	var fID = $('#formify-view-fid').val();
	$('#formify-view-sort-order > option').each(function() {
		if(!$(this).attr('data-static')) {
			$(this).remove();
		}
	});
	
	$('div[data-sortable-field]').remove();
	
	$.getJSON(CCM_DISPATCHER_FILENAME + '/formify/api/fields/all/' + fID,function(fields) {
		for(var i = 0;i < fields.length;i++) {
			// Set sort order options
			var o = $('<option />');
			o.attr('value',fields[i].ffID);
			o.text(fields[i].shortLabel);
			
			if(fields[i].ffID == sortBy) {
				o.attr('selected','selected');
			}
			
			$('#formify-view-sort-order').append(o);
			
			// Set sortable fields options
			var ffWrapper = $('<div class="checkbox" data-sortable-field="true"></div>');
			var ffLabel = $('<label></label>');
			var ffInput = $('<input type="checkbox" class="ccm-input-checkbox" name="sortableFields[]" />');
			ffInput.attr('value',fields[i].ffID);
			
			ffLabel.append(ffInput).append(' ' + fields[i].shortLabel);
			ffWrapper.html(ffLabel);
			
			$('#formify-view-sortable-fields').append(ffWrapper);
			formifySelectSortableFields();
		}
	});	
}

$(document).ready(function() {
	
	var formifySortBy = '<?php   echo $sortBy; ?>';
	
	formifyCheckDetailDestination();
	formifyPopulateSortableFields(formifySortBy);
	
	$('#formify-view-fid').on('change',function() {
		formifyPopulateSortableFields(formifySortBy);
	});
	
	
	$('#formify-view-detail-destination').on('change',function() {
		formifyCheckDetailDestination();
	});
	
});

</script>