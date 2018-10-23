<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php   $al = Loader::helper('concrete/asset_library'); ?>
	
	<?php   if($action == '') { ?>
	<div class="ccm-ui ccm-dashboard-content">
		<form id="import-form" action="<?php   echo View::URL('dashboard/formify/import','parse'); ?>" method="post">
			<h2>Formify Import</h2>
			
			<hr />
			
			<fieldset>
				<legend>1. Select a file</legend>
				<div class="help-block">Import files must be in CSV format.</div>
	            <?php   echo $al->file('formify-file-id', 'fileID', t('Choose File'));?>
			</fieldset>
			
			<br /><br />
			
			<fieldset>
				<legend>2. Select a form</legend>
				<select id="fID" name="fID" class="form-control">
					<option value=""></option>
					<option value="0">Create new form</option>
					<?php   if(count($forms) > 0) {?>
						<?php   foreach($forms as $f) { ?>
						<option value="<?php   echo $f->fID; ?>"><?php   echo $f->name; ?></option>
						<?php   } ?>
					<?php   } ?>
				</select>
			</fieldset>
			
			<br /><br />
			
			<fieldset id="form-name-container" style="display:none">
				<legend>3. Give the new form a name</legend>
				<input type="text" name="formName" id="form-name" class="form-control"/>
			</fieldset>
			
			<br /><br />
			
			<input id="import-submit" type="submit" class="btn btn-primary" value="Continue" style="display:none" />
		</form>
	</div>
	
	<?php   } elseif($action == 'parse') { ?>
	
	<div class="ccm-ui ccm-dashboard-content">
		<form id="import-form" action="<?php   echo View::URL('dashboard/formify/import','run'); ?>" method="post">
			
			<input type="hidden" name="fID" value="<?php   echo $fID; ?>" />
			<input type="hidden" name="formName" value="<?php   echo $formName; ?>" />
			<input type="hidden" name="fileID" value="<?php   echo $fileID; ?>" />
			
			<h2>Formify Import</h2>
			
			<hr />
			
			<legend>1. Map columns to fields</legend>
		
			<table class="ccm-search-results-table">
				<thead>
					<tr>
						<th class="false"><span>Import?</span></th>
						<th class="false"><span>Sample Data</span></th>
						<th class="false"><span>Map to Field</span></th>
						<th class="false"><span>New Field Name</span></th>
					</tr>
				</thead>
				<tbody>
				<?php   $i = 1; ?>
				<?php   foreach($rows[0] as $columnHeader) { ?>
				<?php   $hasMatch = false; ?>
				<tr>
					<td style="text-align:center">
						<input name="import[<?php   echo $i; ?>]" value="true" class="import-<?php   echo $i; ?> ccm-flat-checkbox" type="checkbox" checked="checked" />
					</td>
					<td><?php   echo $columnHeader; ?></td>
					<td>
						<select name="ffID[<?php   echo $i; ?>]" class="ffID form-control" data-row="<?php   echo $i; ?>" data-label="<?php   echo htmlentities($columnHeader); ?>">
							<option value="0">New Field</option>
							<?php   if(is_object($f)) { ?>
								<?php   if(count($f->getFields()) > 0) { ?>
									<?php   foreach($f->getFields() as $ff) { ?>
										<?php   if($ff->label == $columnHeader) { $hasMatch = true; } ?>
										<option value="<?php   echo $ff->ffID; ?>" <?php   if($ff->label == $columnHeader) { ?>selected="selected"<?php   } ?>><?php   echo htmlentities($ff->label); ?></option>
									<?php   } ?>
								<?php   } ?>
							<?php   } ?>
						</select>
					</td>
					<td>
						<input name="label[<?php   echo $i; ?>]" type="text" class="form-control label-<?php   echo $i; ?>" placeholder="Field Name" <?php   if($hasMatch) { ?>disabled="disabled"<?php   } ?> value="<?php   echo $columnHeader; ?>" />
					</td>
				</tr>
				<?php   $i++; ?>
				<?php   } ?>
				</tbody>
			</table>
			
			<br /><br />
			
			<legend>2. Import options</legend>
			
			<input type="checkbox" name="ignoreFirstRow" value="true" /> Ignore first row?
			
			<br /><br />
			
			<input id="import-submit" type="submit" class="btn btn-primary" value="Run Import" />
			
		</form>
	
	</div>
	
	<?php   } elseif($action == 'run') { ?>
	
	<div class="ccm-ui ccm-dashboard-content">
		
		<h2>Import Complete.</h2>
		<p><?php   echo $count; ?> records were imported.</p>
		<a class="btn btn-success" href="<?php   echo View::url('/dashboard/formify/forms/records',$fID)?>">View Records</a>

	</div>
	
	<?php   } ?>

<?php   Loader::packageElement('dashboard/formify_nav','formify'); ?>