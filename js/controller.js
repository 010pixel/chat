notesApp.controller('chatCtrl', function ($scope, $http) {
	
	$scope.sitename = 'Notes';
	$scope.author = '- by 010pixel';
	$scope.theme = 'theme-default';
	$scope.newnote_placeholder = 'New Chat...';
	
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
	
	// Function to Submit Message
	$scope.deleteChat = function (chat_id, resultVarName) {
		
		var params = {
			chat_id: chat_id
		}
		
		var config = {
			params: params
		};
	
		$http.post("php/?process=delete_chat", params, config)
		.success(function (data, status, headers, config)
		{
			console.log(data);
			$scope[resultVarName] = data;
			if ( data.result == 1 ) {
				var indexOfDelete = $("#chatid_" + chat_id).index();
				//$("#chatid_" + chat_id).remove();
				$scope.chats.splice(indexOfDelete,1);
				console.log($scope.chats);
			} else {
				alert("Please try again. \n\n If this is not the first time you are seeing this message means you might not have permission to write messages.");
			}
		})
		.error(function (data, status, headers, config)
		{
			console.log("Error...");
			$scope[resultVarName] = "SUBMIT ERROR";
		});
	};
	
	$scope.loadChatList();
	
	$scope.general_functions = function () {
		$(document).ready(function(e) {
			$("body").on('click','.delete',function(){
				var chatid =  $(this).attr("chatid");
				$scope.deleteChat(chatid);
			});
			$("body").on('click','.chat-list li > div.item',function(){
				$(this).addClass("active");
				var chatid =  $(this).attr("chatid");
				$scope.chatId = chatid;
				window.location.href = '#/message/'+chatid;
				$(".chat").addClass("active-chat");
				// $scope.loadMessages(chatid);
			});
			$("body").on('click','.back-to-list',function(){
				window.location.href = '#/';
				$(".chat").removeClass("active-chat");
				$(".chat-list li > div.item").removeClass("active");
			});
		});
	};
	$scope.general_functions();
	
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
				alert("Please try again. \n\n If this is not the first time you are seeing this message means you might not have permission to create chat.");
			}
		})
		.error(function (data, status, headers, config)
		{
			$scope[resultVarName] = "SUBMIT ERROR";
		});
	};
});

notesApp.controller('messageCtrl', function ($scope, $http, $routeParams) {
	
	$scope.processing = true;
	$scope.resultCount = -1;
	
	$scope.hasMessages = false;
	$scope.hasChats = false;
	
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
			$("#chatid_"+ id +"").addClass("active");
			$(".chat").addClass("active-chat");

			$scope.processing = false;
		});
    };
	
	// If there is chatId in URL then assign it to scope
	if ( $routeParams.chatId ) {
		$scope.chatId = $routeParams.chatId;
	}
	
	// If there is chatId in scope then load that chat
	// Else redirect to chat list page
	if ( $scope.chatId ) {
		$scope.loadMessages($scope.chatId);
	} else {
		window.location.href = '#/';
	}
	
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
				alert("Please try again. \n\n If this is not the first time you are seeing this message means you might not have permission to write messages.");
			}
		})
		.error(function (data, status, headers, config)
		{
			$scope[resultVarName] = "SUBMIT ERROR";
		});
	};
});