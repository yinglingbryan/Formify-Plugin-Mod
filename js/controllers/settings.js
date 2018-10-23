FormifyApp.controller('SettingsController',function($scope,$http) {
	
	Concrete.event.bind('ConcreteSitemap', function(e, instance) {
	
	    Concrete.event.bind('SitemapSelectPage', function(e, data) {
	        if (data.instance == instance) {
	            Concrete.event.unbind(e);
	
	            $scope.form.submitActionCollectionID = data.cID;
	            $scope.form.submitActionCollectionName = data.title;
	            $scope.$apply();
	            $.fn.dialog.closeTop();
	        }

	    });	
	    
	});
	
	$scope.formifyForm = function(fData) {
		for(var p in fData) {
			this[p] = fData[p];
		}
	};
	
	$scope.formifyForm.prototype.toggle = function(property) {
		this[property] = !this[property];
		this.update();
	}
	
	$scope.formifyForm.prototype.checkPermission = function(type,gID) {
		for(var i = 0;i < this.permissions[type].length;i++) {
			if(this.permissions[type][i] == gID) {
				return true;
			}
		}
		return false;
	}
	
	$scope.formifyForm.prototype.togglePermission = function(type,gID) {
		$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/form/permission/' + this.fID + '/' + type + '/' + gID).success(function() {
			//$scope.form.permissions[type].splice($scope.form.permissions[type].indexOf(gID),1);
		});
	}
	
	$scope.formifyForm.prototype.toggleIntegration = function(handle) {
		$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/form/integration/' + this.fID + '/' + handle).success(function() {
			$scope.form.integrations[handle] = 'active';
		});
	}
	
	$scope.formifyForm.prototype.update = function() {
		var f = this;
		f.working = true;
		$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/form/update/' + this.fID,this).success(function() {
			f.working = false;
		})
		this.notSaved = false;
	}
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/form/get/' + fID).success(function(r) {
		$scope.form = new $scope.formifyForm(r);
	});
	
	$scope.openSitemap = function(section) {
		$.fn.dialog.open({
			title: 'Select a page',
			href: CCM_TOOLS_PATH + '/sitemap_search_selector',
			width: '80%',
			modal: true,
			height: 550
		});
	}
	
});