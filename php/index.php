<?php session_start(); ?>
<?php
	include("gf.php");
	include("db.php");
	include("process.php");
	include("chat.php");
?>
<?php
	$db = new My_DB('root', 'password', 'chat', 'localhost');
	
	if ( !$db ) {
		 die('Could not connect: ' . mysql_error());
	}
?>
<?php
	$_SESSION['user_id'] = '1';
	$_SESSION['chat_id'] = '2';
?>
<?php
	$page = new my_process();
	$page->process = @$_GET['process'];
	$page->check_process();
	
	$chat = new chat($db);
	$chat->current_user_id = $_SESSION['user_id'];

	if ($page->process == 'get_chat' ) {
		$chat->get_chat(@$_GET['id']);
	} elseif ($page->process == 'get_chat_list' ) {
		$chat->get_chat_list();
	} elseif ($page->process == 'submit_msg' ) {
		$chat->submit_msg();
	} elseif ($page->process == 'create_chat' ) {
		$chat->create_chat();
	}

	$page->return_values();
?>