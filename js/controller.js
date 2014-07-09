notesApp.controller('chatCtrl', function ($scope, $http) {
	
	$scope.sitename = 'Notes';
	$scope.author = '- by 010pixel';
	$scope.theme = 'theme-default';
	
	$scope.results = [];
	$scope.processing = true;
	$scope.resultCount = -1;
	
	$scope.hasMessages = false;
	$scope.hasChats = false;
	
	$scope.loadChatList = function() {
		
		$scope.processing = true;
		
		$scope.url = 'php/?process=get_chat_list';
		console.log($scope.url);
		
		$http.get(
			$scope.url
		).success(function(data) {
			console.log(data);
			$scope.data = data;
			$scope.resultCount = data.resultCount;
			$scope.chats = data.data;
			if ( data.resultCount > 0 ) {
				$scope.hasChats = true;
			} else {
				$scope.hasChats = false;
			}
			$scope.processing = false;
		});
    };
	
	$scope.loadMessages = function(id) {
		
		$scope.processing = true;
		
		$scope.url = 'php/?process=get_chat&id='+id;
		console.log($scope.url);
		
		$http.get(
			$scope.url
		).success(function(data) {
			console.log(data);
			$scope.data = data;
			$scope.resultCount = data.resultCount;
			$scope.messages = data.data;

			$scope.hasMessages = true;
			$(".chat").addClass("active-chat");

			$scope.processing = false;
		});
    };
	
	$scope.loadChatList();
	
	$scope.general_functions = function () {
		$(document).ready(function(e) {
			$(".chat-list").on('click','li > div.item',function(){
				$scope.loadMessages($(this).attr("chatid"));
			});
			$(".messages").on('click','.back-to-list',function(){
				$(".chat").removeClass("active-chat");
			});
		});
	};
	$scope.general_functions();
	
	// Function to Submit Message
	$scope.submitData = function (chat_form, resultVarName) {
		
		var params = {
			chat_id: $scope.data.chat_id,
			message: $scope.form_message
		}
		
		var config = {
			params: params
		};
	
		$http.post("php/?process=submit_msg", params, config)
		.success(function (data, status, headers, config)
		{
			console.log(data);
			$scope[resultVarName] = data;
			if ( data.result == 1 ) {
				$scope.form_message = null;
				$scope.messages.push(data.data[0]);
				console.log($scope.messages);
			} else {
				alert("Please try again.");
			}
		})
		.error(function (data, status, headers, config)
		{
			$scope[resultVarName] = "SUBMIT ERROR";
		});
	};
	
	// Function to Create a New Chat
	$scope.createChat = function (chat_form, resultVarName) {
		
		var params = {
			chat_name: $scope.chat_name
		}
		
		var config = {
			params: params
		};
	
		$http.post("php/?process=create_chat", params, config)
		.success(function (data, status, headers, config)
		{
			console.log(data);
			$scope[resultVarName] = data;
			if ( data.result == 1 ) {
				$scope.chat_name = null;
				console.log($scope.chats.length);
				if ( $scope.chats.length <= 1 ) {
					$scope.loadChatList();
				} else {
					$scope.chats.push(data.data[0]);
					console.log($scope.chats);
				}
			} else {
				alert("Please try again.");
			}
		})
		.error(function (data, status, headers, config)
		{
			$scope[resultVarName] = "SUBMIT ERROR";
		});
	};
});