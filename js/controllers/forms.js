FormifyApp.controller('FormsController',function($scope,$http) {
	
	$scope.formifyForm = function(fData) {
		for(var p in fData) {
			this[p] = fData[p];
		}
	};
	
	$scope.formifyForm.prototype.toggle = function(property) {
		this[property] = !this[property];
		this.update();
	}
	
	$scope.formifyForm.prototype.update = function() {
		$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/form/update/' + this.fID,this);
		this.notSaved = false;
	}
	
	$scope.formifyForm.prototype.delete = function() {
		var f = this;
		if(confirm('Are you sure you want to delete this form?')) {
			$scope.forms.splice($scope.forms.indexOf(f),1);
			$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/form/delete/' + this.fID);
		}
	}
	
	$scope.itemsLoading = 0;
	$scope.newForm = {};
	
	$scope.itemsLoading++;
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/form/all').success(function(fData) {
		$scope.forms = [];
		for(var i = 0; i < fData.length; i++) {
			var f = new $scope.formifyForm(fData[i]);
			$scope.forms.push(f);
		}
		$scope.itemsLoading--;
	});
	
	$scope.add = function() {
		if(!$scope.newForm.name) {
			$scope.newForm.hasError = true;
		} else {
			$scope.add.working = true;
			$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/form/create',$scope.newForm).success(function(fData) {
				var f = new $scope.formifyForm(fData);
				$scope.forms.push(f);
				$scope.newForm = {};
				$scope.add.working = false;
			});
		}
	};
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/config/get/magic').success(function(value) {
		$scope.magic = (value == "true");
	});
	
	$scope.toggleMagic = function() {
		$scope.magic = !$scope.magic;
		$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/config/set/magic/' + $scope.magic);
	}
	
	$scope.formifyGroup = function(gData) {
		for(var p in gData) {
			this[p] = gData[p];
		}
	};
	
	$scope.formifyGroup.prototype.update = function() {
		$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/groups/update/' + this.gID,this);
		this.edit = false;
		this.notSaved = false;
	}
	
	$scope.formifyGroup.prototype.delete = function() {
		var g = this;
		if(confirm('Are you sure you want to delete this group?')) {
			for(var i=0;i<$scope.forms.length;i++) {
      	if($scope.forms[i].gID == g.gID) {
        	$scope.forms[i].gID = '0';
        }
    	}
    	$scope.activeGroupID = '0';
			$scope.groups.splice($scope.groups.indexOf(g),1);
			$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/groups/delete/' + this.gID);
		}
	}
	
	$scope.formifyGroup.prototype.filterBy = function() {
  	$scope.activeGroupID = this.gID;
	}
	
	$scope.addFormToGroup = function($e,f,gID) {
  	for(var i=0;i<$scope.forms.length;i++) {
    	if($scope.forms[i].fID == f.fID) {
      	$scope.forms[i].gID = gID;
      	$scope.forms[i].update();
    	}
  	}
	}
	
	$scope.itemsLoading++;
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/groups/all').success(function(gData) {
		$scope.groups = [];
		for(var i = 0; i < gData.length; i++) {
			var g = new $scope.formifyGroup(gData[i]);
			$scope.groups.push(g);
		}
		$scope.itemsLoading--;
	});
	
	$scope.newGroup = {};
	$scope.activeGroupID = '0';
	
	$scope.addGroup = function() {
		if(!$scope.newGroup.name) {
			$scope.newGroup.hasError = true;
		} else {
			$scope.addGroup.working = true;
			$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/groups/create',$scope.newGroup).success(function(gData) {
				var g = new $scope.formifyGroup(gData);
				$scope.groups.push(g);
				$scope.newGroup = {};
				$scope.addGroup.working = false;
			});
		}
	};
});