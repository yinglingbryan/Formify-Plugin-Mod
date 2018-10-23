FormifyApp.controller('RecordsController',function($scope,$http,$filter) {
	
	$scope.selectedRecords = [];
	$scope.lastPageLoaded = 1;
	$scope.moreRecords = true;
	
	$scope.formifyRecord = function(rData) {
		for(var p in rData) {
			this[p] = rData[p];
		}
	};
	
	$scope.formifyRecord.prototype.toggle = function(property) {
		this[property] = !this[property];
		this.update();
	}
	
	$scope.formifyRecord.prototype.update = function() {
		$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/records/update/' + this.rID,this);
		this.notSaved = false;
	}
	
	$scope.formifyRecord.prototype.rebuild = function() {
		$scope.activeRecord.scanning = 1;
		$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/records/rebuild/' + this.rID).success(function(rData) {
			var r = new $scope.formifyRecord(rData);
			$scope.records[$scope.records.indexOf(r)] = r;
			$scope.activeRecord.scanning = 0;
			$scope.activeRecord = r;
		});
		
	}
	
	$scope.formifyRecord.prototype.toggleApprove = function() {
		if(this.approval == '1') {
			$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/records/pend/' + this.rID);
			this.approval = '0';
		} else {
			$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/records/approve/' + this.rID);
			this.approval = '1';
		}
	}
	
	$scope.formifyRecord.prototype.toggleReject = function() {
		if(this.approval == '-1') {
			$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/records/pend/' + this.rID);
			this.approval = '0';
		} else {
			$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/records/reject/' + this.rID);
			this.approval = '-1';
		}
		this.update();
	}
	
	$scope.formifyRecord.prototype.toggleSelected = function() {
		delete $scope.activeRecord;
		if(this.isSelected) {
			this.isSelected = false;
			$scope.selectedRecords.splice($scope.selectedRecords.indexOf(this),1);
		} else {
			this.isSelected = true;
			$scope.selectedRecords.push(this);
		}
	}
	
	$scope.formifyRecord.prototype.delete = function() {
		var record = this;
		if(confirm('Are you sure you want to delete this record?')) {
			$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/records/delete/' + this.rID).success(function() {
				delete $scope.activeRecord;
				$scope.records.splice($scope.records.indexOf(record),1);
			});
		}
	}
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/records/all/' + fID + '/1/25').success(function(rData) {
		$scope.loadingMore = true;
		$scope.records = [];
		for(var i = 0; i < rData.length; i++) {
			var r = new $scope.formifyRecord(rData[i]);
			$scope.records.push(r);
		}
		$scope.loadMoreRecords();
	});
	
	$scope.detail = function (r) {
		$scope.activeRecord = r;
		$scope.selectedRecords = [];
		$('.record-checkbox').attr('checked',false);
		for(var i = 0; i < $scope.records.length; i++) {
			$scope.records[i].isSelected = false;
		}		
	}
	
	$scope.search = function () {
		$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/records/search/' + fID + '/' + $scope.query).success(function(rData) {
			$scope.records = [];
			for(var i = 0; i < rData.length; i++) {
				var r = new $scope.formifyRecord(rData[i]);
				$scope.records.push(r);
			}
		});
	}
	
	$scope.processSelectedRecords = function(action) {
		
		var proceed = true;
		
		if(action == 'delete') {
			if(!confirm('Are you sure you want to delete these records?')) {
				proceed = false;
			}
		}
			
		if(proceed) {
			for(var i = 0; i < $scope.selectedRecords.length; i++) {
				switch(action) {
					case 'delete':
						$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/records/delete/' + $scope.selectedRecords[i].rID);
						$scope.records.splice($scope.records.indexOf($scope.selectedRecords[i]),1);
						break;
					case 'approve':
						if($scope.selectedRecords[i].approval != 1) {
							$scope.selectedRecords[i].toggleApprove();
						}
						break;
					case 'reject':
						if($scope.selectedRecords[i].approval != -1) {
							$scope.selectedRecords[i].toggleReject();
						}
						break;
				}
			}
		
			if(action == 'delete') {
				delete $scope.activeRecord;
				$scope.selectedRecords = [];
			}
			
		}
	}
	
	$scope.index = function() {
		$scope.index.working = true;
		$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/records/index/' + fID).success(function() {
			$scope.index.working = false;
		});
	}
	
	$scope.loadMoreRecords = function() {
		$scope.loadingMore = true;
		$scope.lastPageLoaded++;
		$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/records/all/' + fID + '/' + $scope.lastPageLoaded + '/25').success(function(rData) {
			if(rData.length > 0) {
				for(var i = 0; i < rData.length; i++) {
					var r = new $scope.formifyRecord(rData[i]);
					$scope.records.push(r);
				}
			} else {
				$scope.moreRecords = false;
			}
			$scope.loadingMore = false;
		});
	}
    
    $scope.recordsDragStart = function(e, ui) {
        ui.item.data('start', ui.item.index());
    }
    
    $scope.recordsDragEnd = function(e, ui) {
        var start = ui.item.data('start'),
            end = ui.item.index();
            
        $scope.records.splice(end, 0, 
        $scope.records.splice(start, 1)[0]);
        
        if(end > start) {
	        var adjacentRecordID = $scope.records[end - 1].rID;
        } else {
	        var adjacentRecordID = $scope.records[end + 1].rID;
        }
        
        var rID = $scope.records[end].rID;
        
        $scope.$apply();
        
        $http.get(CCM_DISPATCHER_FILENAME + '/formify/api/records/sort/' + rID + '/' + adjacentRecordID);
        
    }
	
	$('.ui-sortable').sortable({
		handle: 'span',
		cursor: 'move',
		opacity: 0.5,
		start: $scope.recordsDragStart,
		update: $scope.recordsDragEnd
	});
	
	$scope.showUsers = function(username) {
		$.fn.dialog.open({
			title: 'Edit Field',
			element: '#formify-user-search',
			width: 550,
			modal: true,
			height: 550
		});
	}
	
});