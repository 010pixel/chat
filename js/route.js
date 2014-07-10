notesApp.config(['$routeProvider',function($routeProvider){
	$routeProvider.
	when('/',{controller:'chatCtrl'}).
	when('/chat',{controller:'chatCtrl'}).
	when('/message',{templateUrl:'templates/messages.html', controller:'messageCtrl'}).
	when('/message/:chatId',{templateUrl:'templates/messages.html', controller:'messageCtrl'}).
	otherwise({redirectTo:'/'})
}]);
/*
notesApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/chat', {
        templateUrl: 'templates/chat.html',
        controller: 'chatCtrl'
      }).
      when('/message', {
        templateUrl: 'templates/messages.html',
        controller: 'messageCtrl'
      }).
      otherwise({
        redirectTo: '/chat'
      });
  }]);
*/