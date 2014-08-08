<?php session_start(); ?>
<?php
	include("timezone.php");
	include("gf.php");
	include("process.php");
	include("chat.php");
	include("connection.php");
?>
<?php
	$page = new my_process();
	$page->process = @$_GET['process'];
	$page->check_process();
	
	$chat = new chat($db);
	$chat->current_user_id = $_SESSION['user']['id'];

	if ($page->process == 'get_chat' ) {
		$chat->get_chat(@$_GET['id']);
	} elseif ($page->process == 'get_chat_list' ) {
		$chat->get_chat_list();
	} elseif ($page->process == 'submit_msg' ) {
		$chat->submit_msg();
	} elseif ($page->process == 'create_chat' ) {
		$chat->create_chat();
	} elseif ($page->process == 'delete_chat' ) {
		$chat->delete_chat();
	} elseif ($page->process == 'delete_msg' ) {
		$chat->delete_msg();
	}

	$page->add_value("timezone",date_default_timezone_get());
	$page->return_values();
?>