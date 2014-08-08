<?php
	include("connection.php");
	$err_msg = '';
	$redirectURL = isset($_GET['url']) ? $_GET['url'] : "index.php";
	if(isset($_POST['sublogin'])){

		$username = $_POST['username'];
		$password = $_POST['password'];

		if($username == "" || $password == ""){
	   		$err_msg = "Please try again.";
		} else {
			
			$sql = "SELECT * from `". DB_NAME ."`.`users` WHERE `email` = '". $username ."' AND `password` = '". md5($password) ."' LIMIT 1";
			$results = $db->get_results($sql,ARRAY_A);
			if ( empty($results) ) {
				$err_msg = "Please try again.";
			} else {
				$_SESSION['user'] = $results[0];
				$page = ROOT_DIR_URL;
				$sec = "0";
				header("Refresh: $sec; url=$page");
			}
		}
	}
?>
<?php
	echo showLoginForm();
?>
<?php
	function showLoginForm() {
		global $err_msg;
		ob_start();
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta content="IE=edge" http-equiv="X-UA-Compatible" />
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
        </head>
        <body>
            <form method="post">
                <input type="text" name="username" value="<?php echo $_POST['username'] ?>" />
                <input type="password" name="password" />
                <input type="submit" name="sublogin" value="Login" />
                <div class="error"><?php echo $err_msg; ?></div>
            </form>
        </body>
        </html>
		<?php
		$my_var = ob_get_clean();
		return $my_var;
	}
?>