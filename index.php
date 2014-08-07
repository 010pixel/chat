<?php session_start(); ?>
<?php
	include("php/timezone.php");
	define("DB_NAME","chat");
	define('ROOT_DIR_URL', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http' . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/' );
?>
<?php
	if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
		include("php/login.php");
		die();
	} elseif (isset($_GET['action']) && $_GET['action'] == 'logout') {
		session_destroy();
		$page = ROOT_DIR_URL;
		$sec = "0";
		header("Refresh: $sec; url=$page");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="IE=edge" http-equiv="X-UA-Compatible" />
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">

<!-- jQuery -->
<script type="text/javascript" src="js/jquery-1.10.1.min.js"></script>

<!-- BootStrap -->
<link rel="stylesheet" href="css/bootstrap-3.1.1.min.css" />
<script type="text/javascript" src="js/bootstrap-3.1.1.min.js"></script>

<!-- AngularJS -->
<script type="text/javascript" src="js/angular.min.js"></script>
<script type="text/javascript" src="js/app.js"></script>
<script type="text/javascript" src="js/controller.js"></script>
<script type="text/javascript" src="js/route.js"></script>

<!-- Main CSS -->
<link rel="stylesheet" type="text/css" href="css/chat.css">

</head>

<body>
    <div ng-app="notes">
        
    	<div ng-controller="chatCtrl">
        
            <title>{{sitename}} {{author}}</title>

            <div class="page-header">
                <h1>{{sitename}} <small>{{author}}</small></h1>
            </div>

            <div ng-show="processing" class="loading"></div>

            <div class="chat-container">
                <div class="chat clearfix" ng-show="!processing">
                    
                    <div class="chat-list">
                        <ul>
                            <li ng-repeat="result in chats" ng-show="hasChats">
                                <span>
									<?php
										if ( $_SESSION['user']['w'] == true ) {
											echo '<a href="#" chatid="{{result.id}}">Edit</a>';
										}
										if ( $_SESSION['user']['w'] == true ) {
											echo '<a href="#" class="delete" chatid="{{result.id}}">Delete</a>';
										}
                                    ?>
                                </span>
                                <div chatid="{{result.id}}" id="chatid_{{result.id}}" ng-click="loadMessages({{result.id}})" class="item {{result.class}}"><img src="http://placehold.it/48x48" class="img-circle"> {{result.name}}
                                </div>
                            </li>
                            <?php
								if ( $_SESSION['user']['w'] == true ) {
							?>
                            <form ng-submit="createChat(chat_form,'form_data')">
                            <li>
                                <div class="new-chat-form"><img src="http://placehold.it/48x48" class="img-circle"> <input type="text" placeholder="{{newnote_placeholder}}" ng-model="chat_name" required /></div>
                            </li>
                            </form>
                            <?php
								}
							?>
                        </ul>
                    </div>
                    
                    <div ng-view></div>
                    
                </div>
            </div>
        </div>
        <div style="margin:10px auto; text-align:center;"><a class="btn btn-success" style="color:#FFF !important;" href="?action=logout">Logout</a></div>

    </div>
    
</body>
</html>