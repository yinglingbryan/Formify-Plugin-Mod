FormifyApp.controller('NotificationsController',function($scope,$http,$filter) {
	
	$scope.notificationTypes = [
		{
			label:'Add',
			value:'add'
		},{
			label:'Update',
			value:'update'
		},{
			label:'Approve',
			value:'approve'
		},{
			label:'Reject',
			value:'reject'
		},
	];
	
	$scope.conditionTypes = [
		{
			label:'Is equal to',
			value:'='
		},{
			label:'Is not equal to',
			value:'!='
		},{
			label:'Contains',
			value:'~'
		},{
			label:'Does not contain',
			value:'!~'
		}
	]
	
	$scope.newNotificationType = 'add';
	
	$scope.formifyField = function(ffData) {
		for(var p in ffData) {
			this[p] = ffData[p];
		}
	};
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/fields/all/' + fID).success(function(ffData) {
		$scope.fields = [];
		for(var i = 0; i < ffData.length; i++) {
			var ff = new $scope.formifyField(ffData[i]);
			$scope.fields.push(ff);
		}
	
		$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/notifications/all/' + fID).success(function(nData) {
			$scope.notifications = [];
			for(var i = 0; i < nData.length; i++) {
				var n = new $scope.formifyNotification(nData[i]);
				
				if(n.conditionFieldID > 0) {
					n.hasCondition = true;
				}
				
				if(isNaN(parseFloat(n.toAddress))) {
					n.toIsDynamic = false;
				} else {
					n.toIsDynamic = true;
					for(var j = 0;j < $scope.fields.length;j++) {
						if($scope.fields[j].ffID == n.toAddress) {
							n.toLabel = $scope.fields[j].label;
						}
					}
				}
				
				if(isNaN(parseFloat(n.replyAddress))) {
					n.replyIsDynamic = false;
				} else {
					n.replyIsDynamic = true;
					for(var j = 0;j < $scope.fields.length;j++) {
						if($scope.fields[j].ffID == n.replyAddress) {
							n.replyLabel = $scope.fields[j].label;
						}
					}
				}
				
				$scope.notifications.push(n);
			}
		});
		
	});
	
	$scope.formifyNotification = function(nData) {
		for(var p in nData) {
			this[p] = nData[p];
		}
	};
	
	$scope.formifyNotification.prototype.toggle = function(property) {
		this[property] = !this[property];
		this.update();
	}
	
	$scope.formifyNotification.prototype.update = function() {
		$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/notifications/update/' + this.nID,this);
		this.notSaved = false;
	}
	
	$scope.formifyNotification.prototype.delete = function() {
		var n = this;
		if(confirm('Are you sure you want to delete this notification?')) {
			$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/notifications/delete/' + this.nID).success(function() {
				delete $scope.activeNotification;
				$scope.notifications.splice($scope.notifications.indexOf(n),1);
			});
		}
	}
	
	$scope.add = function() {
		
		$scope.add.working = true;
		
		$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/notifications/create/' + fID + '/' + $scope.newNotificationType).success(function(nData) {
			var n = new $scope.formifyNotification(nData);
			$scope.notifications.unshift(n);
			$scope.activeNotification = n;
			$scope.add.working = false;
		});
	};
	
	$scope.save = function(n) {
		$scope.notifications[$scope.notifications.indexOf(n)] = $scope.activeNotification;
		delete $scope.activeNotification;
		n.update();
	}
	
	$scope.detail = function(n) {
		$scope.activeNotification = n;	
	}
	
	$scope.toggleCondition = function() {
		if(!$scope.activeNotification.hasCondition) {
			$scope.activeNotification.conditionType = '';
			$scope.activeNotification.conditionFieldID = 0;
			$scope.activeNotification.conditionValue = '';
		}
	}
	
	$scope.toggleDynamic = function() {
		if(!$scope.activeNotification.toIsDynamic) {
			$scope.activeNotification.toAddress = '';
			$scope.activeNotification.toLabel = '';
		} else {
			$scope.activeNotification.toAddress = $scope.fields[0].ffID;
			$scope.activeNotification.toLabel = $scope.fields[0].label;
		}
	}
	
	$scope.toggleDynamicReply = function() {
		if(!$scope.activeNotification.replyIsDynamic) {
			$scope.activeNotification.replyAddress = '';
			$scope.activeNotification.replyLabel = '';
		} else {
			$scope.activeNotification.replyAddress = $scope.fields[0].ffID;
			$scope.activeNotification.replyLabel = $scope.fields[0].label;
		}
	}
	
	$scope.updateToLabel = function() {
		for(var i = 0;i < $scope.fields.length;i++) {
			if($scope.fields[i].ffID == $scope.activeNotification.toAddress) {
				$scope.activeNotification.toLabel = $scope.fields[i].label;
			}
		}
	}
	
	$scope.updateReplyLabel = function() {
		for(var i = 0;i < $scope.fields.length;i++) {
			if($scope.fields[i].ffID == $scope.activeNotification.replyAddress) {
				$scope.activeNotification.replyLabel = $scope.fields[i].label;
			}
		}
	}
	
	$scope.formifyTemplate = function(tData) {
		for(var p in tData) {
			this[p] = tData[p];
		}
	}
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/templates/all/').success(function(tData) {
		$scope.templates = [];
		for(var i = 0; i < tData.length; i++) {
			var t = new $scope.formifyTemplate(tData[i]);
			$scope.templates.push(t);
		}
	});
	
});