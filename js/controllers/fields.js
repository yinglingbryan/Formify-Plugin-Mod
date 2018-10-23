FormifyApp.controller('FieldsController',function($scope,$http,$filter) {
	
	$scope.defaultValueSources = formifyDefaultValueSources;
	$scope.optionsSources = formifyOptionsSources;
	$scope.fileSets = formifyFileSets;
	$scope.dateFormatOptions = formifyDateFormatOptions;
	$scope.dateInterfaceOptions = formifyDateInterfaceOptions;
	$scope.wysiwygFormatOptions = formifyWysiwygFormatOptions;
	
	$scope.formifyOptionGroup = function(ogData) {
		for(var p in ogData) {
			this[p] = ogData[p];
		}
	}
	
	$scope.formifyForm = function(fData) {
		for(var p in fData) {
			this[p] = fData[p];
		}
	}
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/options/all').success(function(ogData) {
		$scope.optionGroups = [];
		for(var i = 0; i < ogData.length; i++) {
			var og = new $scope.formifyOptionGroup(ogData[i]);
			$scope.optionGroups.push(og);
		}
	});
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/form/all').success(function(fData) {
		$scope.forms = [];
		for(var i = 0; i < fData.length; i++) {
			var f = new $scope.formifyForm(fData[i]);
			$scope.forms.push(f);
		}
	});
	
	$scope.formifyField = function(ffData) {
		for(var p in ffData) {
			this[p] = ffData[p];
		}
	};
	
	$scope.formifyField.prototype.toggle = function(property) {
		if(this[property] == '1') {
			this[property] = '0';
		} else {
			this[property] = '1';
		}
		this.update();
	}
	
	$scope.formifyField.prototype.update = function() {
		this.sortPriority = $scope.fields.indexOf(this) + 1;
		$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/fields/update/' + this.ffID,this);
		this.notSaved = false;
	}
	
	$scope.formifyField.prototype.delete = function() {
		var field = this;
		$scope.fields.splice($scope.fields.indexOf(field),1);
		if(confirm('Are you sure you want to delete this field?')) {
			$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/fields/delete/' + this.ffID);
		}
	}
	
	$scope.formifyField.prototype.addRule = function() {
		var field = this;
		var r = {
			comparisonField:	{},
			comparison:			'',
			value:				''
		};
		this.rules.push(r);
	}
	
	$scope.formifyField.prototype.deleteRule = function(r) {
		this.rules.splice(this.rules.indexOf(r),1);
	}
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/form/get/' + fID).success(function(r) {
		$scope.form = r;
	});


	$scope.working = true;
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/fields/all/' + fID).success(function(ffData) {
		$scope.fields = [];
		for(var i = 0; i < ffData.length; i++) {
			var ff = new $scope.formifyField(ffData[i]);
			$scope.fields.push(ff);
		}
		$scope.working = false;
	});
	
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/fields/types').success(function(r) {
		$scope.fieldTypes = r;
	});
	
	$scope.add = function(ff) {
		
		$scope.working = true;
		
		if(ff) {
			ff.isLoading = true;
		}
		
		$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/fields/create/' + fID).success(function(ffData) {
			var newField = new $scope.formifyField(ffData);
			if(!ff) {
				$scope.fields.unshift(newField);
				$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/fields/sort/',$scope.fields);
			} else {
				ff.isLoading = false;
				$scope.fields.splice($scope.fields.indexOf(ff) + 1,0,newField);
				$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/fields/sort/',$scope.fields);
			}
			$scope.working = false;
		});
	};
	
	$scope.save = function(ff) {
		//$scope.fields[$scope.fields.indexOf(ff)] = $scope.activeField;
		ff.update();
		$.fn.dialog.closeTop();
	}
	
	$scope.edit = function(ff) {
		
		$('#formify-field-settings-tabs-nav li:first-child a').click();
		
		$scope.activeField = ff;
		$scope.activeFieldType = $filter('filter')($scope.fieldTypes, { handle: ff.type }, true)[0];
		
		$scope.activeFieldType.hasProperty = function(property) {
			var hasProperty = false;
			angular.forEach(this.properties,function(value) {
				if(value == property) {
					hasProperty = true;
				}
			});
			
			return hasProperty;
		}
		
		for(var i = 0;i < ff.rules.length; i++) {
			ff.rules[i].comparisonField = $filter('filter')($scope.fields, { ffID: ff.rules[i].comparisonFieldID }, true)[0]
		}
		
		if(ff.ogFormID > 0) {
			$scope.setSourceForm(ff.ogFormID);
		} else {
			$scope.sourceForm = '';
		}
		
		$.fn.dialog.open({
			title: 'Edit Field',
			element: '#formify-field-settings',
			width: 550,
			modal: true,
			height: 550
		});
		
	}
	
	$scope.sort = function() {
		$scope.$apply();
		
		var fieldsToSort = [];
		
		for(var i = 0; i < $scope.fields.length; i++) {
			fieldsToSort.push($scope.fields[i].ffID);
		}
		
		$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/fields/sort/',fieldsToSort);
	}
    
    $scope.fieldsDragStart = function(e, ui) {
        ui.item.data('start', ui.item.index());
    }
    
    $scope.fieldsDragEnd = function(e, ui) {
        var start = ui.item.data('start'),
            end = ui.item.index();
       
        $scope.fields.splice(end,0,$scope.fields.splice(start,1)[0]);
        $scope.$apply();
    }
	
	$('.ui-sortable').sortable({
		handle: 'i.fa-bars',
		cursor: 'move',
		opacity: 0.5,
		start: $scope.fieldsDragStart,
		update: $scope.fieldsDragEnd,
		stop: $scope.sort
	});
	
	$scope.setSourceForm = function (fID) {
		$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/form/get/' + fID).success(function(r) {
			$scope.sourceForm = r;
			$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/fields/all/' + fID).success(function(r) {
				$scope.sourceForm.fields = r;
			});
		});
	}
	
});