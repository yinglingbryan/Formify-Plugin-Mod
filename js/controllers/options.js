FormifyApp.controller('OptionsController',function($scope,$http,$filter) {
	
	$scope.formifyOptionGroup = function(ogData) {
		for(var p in ogData) {
			this[p] = ogData[p];
		}
	}	
	
	$scope.formifyOptionGroup.prototype.update = function() {
		$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/options/update/' + this.ogID,this);
		this.notSaved = false;
	}
	
	$scope.formifyOptionGroup.prototype.delete = function() {
		var og = this;
		if(confirm('Are you sure you want to delete this option group?')) {
			$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/options/delete/' + og.ogID).success(function() {
				$scope.optionGroups.splice($scope.optionGroups.indexOf(og),1);
				delete $scope.activeOptionGroup;
			});
		}
	}
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/options/all').success(function(ogData) {
		$scope.optionGroups = [];
		for(var i = 0; i < ogData.length; i++) {
			var og = new $scope.formifyOptionGroup(ogData[i]);
			$scope.optionGroups.push(og);
		}
	});
	
	$scope.add = function() {
		$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/options/create?name=' + $scope.newOptionGroupName).success(function(ogData) {
			$scope.newOptionGroupName = '';
			var newOptionGroup = new $scope.formifyOptionGroup(ogData);
			$scope.optionGroups.unshift(newOptionGroup);
		});
	};
	
	$scope.save = function(og) {
		$scope.optionGroups[$scope.optionGroups.indexOf(og)] = $scope.activeOptionGroup;
		delete $scope.activeOptionGroup;
		og.update();
	}
	
	$scope.detail = function(og) {
		$scope.activeOptionGroup = og;
		console.log(og);
	}
	
});