FormifyApp.controller('RulesController',function($scope,$http,$filter) {
	
	$scope.comparisonOptions = formifyComparisonOptions;
	$scope.actionOptions = formifyActionOptions;
	
	$scope.formifyRule = function(rData) {
		for(var p in rData) {
			this[p] = rData[p];
		}
	}	
	
	$scope.formifyRule.prototype.update = function() {
		$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/rules/update/' + this.rID,this);
		this.notSaved = false;
	}
	
	$scope.formifyRule.prototype.delete = function() {
		var r = this;
		if(confirm('Are you sure you want to delete this rule?')) {
			$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/rules/delete/' + r.rID).success(function() {
				$scope.rules.splice($scope.rules.indexOf(r),1);
			});
		}
	}
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/rules/all/' + fID).success(function(rData) {
		$scope.rules = [];
		for(var i = 0; i < rData.length; i++) {
			var r = new $scope.formifyRule(rData[i]);
			$scope.rules.push(r);
		}
	});
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/fields/all/' + fID).success(function(ffData) {
		$scope.fields = [];
		for(var i = 0; i < ffData.length; i++) {
			$scope.fields.push(ffData[i]);
		}
	});
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/options/all').success(function(ogData) {
		$scope.optionGroups = [];
		for(var i = 0; i < ogData.length; i++) {
			$scope.optionGroups.push(ogData[i]);
		}
	});
	
	$scope.add = function() {
		$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/rules/create/' + fID).success(function(rData) {
			var newRule = new $scope.formifyRule(rData);
			$scope.rules.unshift(newRule);
		});
	}
	
	$scope.save = function(r) {
		$scope.rules[$scope.rules.indexOf(r)] = $scope.activeRule;
		delete $scope.activeRule;
		r.update();
	}
	
	$scope.detail = function(r) {
		$scope.activeRule = r;
	}
	
});