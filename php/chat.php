<?php
	class chat {
		
		var $db;
		var $days_used = array();
		var $ajax_load = false;
		var $load_start_msgs = '0';
		var $total_load_msgs = '1000';
		var $current_user_id = 0;
		
		function __construct($db) {
			$this->db = $db;
		}
		
		public function create_chat () {
			if ( !$_SESSION['user']['w'] ) return;
			$this->ajax_load = true;
			if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
			$chat_name = $_GET['chat_name'];
			
			$this->db->begin_transaction();
			$result1 = $this->db->insert('chats', array('name'=>$chat_name, 'owner_user_id'=>$this->current_user_id), array('%s','%d'));
			$new_chat_id = $this->db->insert_id;
			$result2 = $this->db->insert('chat_user', array('chat_id'=>$new_chat_id, 'user_id'=>$this->current_user_id), array('%d','%d'));
			if ( $result1 !== false && $result1 !== 0 && $result1 !== -1 && $result2 !== false && $result2 !== -1 && $result2 !== 0 ) {
				$this->db->commit_transaction();
				$this->page_add_value('result','1');
				$this->get_chat_list($new_chat_id);
			} else {
				$this->db->rollback_transaction();
				$this->page_add_value('result','0');
			}
		}
		
		public function delete_chat () {
			if ( $_SESSION['user']['d'] != 1 ) return;
			$this->ajax_load = true;
			if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
			$chat_id = $_GET['chat_id'];
			
			$this->db->begin_transaction();
			$result1 = $this->db->delete('messages', array('chat_id'=>$chat_id), array('%d'));
			$result2 = $this->db->delete('chat_user', array('chat_id'=>$chat_id), array('%d'));
			$result3 = $this->db->delete('chats', array('id'=>$chat_id,'owner_user_id'=>$this->current_user_id), array('%d'));
			
			$this->page_add_value('chat_id',$chat_id);
			$this->page_add_value('result1',$result1);
			$this->page_add_value('result2',$result2);
			$this->page_add_value('result3',$result3);
			if ( $result1 !== false && $result1 !== -1 && $result2 !== false && $result2 !== 0 && $result2 !== -1 && $result3 !== false && $result3 !== 0 && $result3 !== -1 ) {
				$this->db->commit_transaction();
				$this->page_add_value('result','1');
			} else {
				$this->db->rollback_transaction();
				$this->page_add_value('result','0');
			}
		}
		
		public function delete_msg () {
			if ( $_SESSION['user']['d'] != 1 ) return;
			$this->ajax_load = true;
			if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
			$msg_id = $_POST['msg_id'];
			$chat_id = $_POST['chat_id'];
			
			$this->db->begin_transaction();
			$result1 = $this->db->delete('messages', array('id'=>$msg_id), array('%d'));
			
			$this->page_add_value('msg_id',$msg_id);
			$this->page_add_value('result1',$result1);
			if ( $result1 !== false && $result1 !== -1 ) {
				$this->db->commit_transaction();
				$this->page_add_value('result','1');
				$this->get_messages($chat_id);
			} else {
				$this->db->rollback_transaction();
				$this->page_add_value('result','0');
			}
		}
		
		public function submit_msg () {
			if ( !$_SESSION['user']['w'] ) return;
			$this->ajax_load = true;
			if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
			$message = $_GET['message'];
			if ( empty($message) ) return;
			$chat_id = $_GET['chat_id'];
			
			$result = $this->db->insert('messages', array('msg'=>$message, 'chat_id'=>$chat_id, 'user_id'=>$this->current_user_id), array('%s','%d','%d'));
			$update = $this->db->update('chat`.`chats', array('timestamp'=>date("Y-m-d H:i:s")), array('id'=>$chat_id));
			$this->page_add_value('update',$update);
			if ( $result !== false && $result !== -1 ) {
				$this->page_add_value('result','1');
				$this->get_messages($chat_id,mysql_insert_id());
			} else {
				$this->page_add_value('result','0');
			}
		}
		
		public function page_add_value($key, $value) {
			global $page;
			$page->add_value($key, $value);
		}
		
		public function get_chat_list ($chat_id = NULL) {
			if ( $chat_id !== NULL ) {
				$sql = "SELECT * FROM chat.chats WHERE `id` = '". $chat_id ."';";
			} else {
				$sql = "SELECT * FROM chat.chats WHERE `id` IN (SELECT chat_id FROM chat.chat_user WHERE `user_id` = '". $this->current_user_id ."');";
			}
			$results = $this->db->get_results($sql,ARRAY_A);
			$this->page_add_value('sql',$sql);
			$this->page_add_value('data',$results);
			$this->page_add_value('resultCount',$this->db->num_rows);
		}
		
		public function get_chat($chat_id) {
			$sql = "SELECT * from chat.chats WHERE `id` = '". $chat_id ."';";
			$results = $this->db->get_results($sql,ARRAY_A);
			$this->page_add_value('chat',$results[0]);
			$this->get_messages($chat_id);
		}

		public function get_messages ($chat_id, $msg_id = NULL) {		
			$where = array();
			$where[] = "`chat_id` = '". $chat_id ."'";
			if ( $msg_id !== NULL ) {
				$where[] = "`id` = '". $msg_id ."'";
			}
			$sql = "SELECT * from chat.messages WHERE ". implode(" AND ",$where) ." ORDER BY `timestamp` DESC LIMIT ". $this->load_start_msgs . ", ". $this->total_load_msgs .";";
			$this->page_add_value('sql',$sql);
			$results = $this->db->get_results($sql,ARRAY_A);
			// Reverset Array list to show in reverse order as latest msg at bottom
			$results = array_reverse($results);
			// Convert date to varchar date format
			foreach ( $results as $key => $value ) {
				$results[$key] = array_merge($results[$key],$this->process_msg($value));
			}
			$this->page_add_value('data',$results);
			$this->page_add_value('resultCount',$this->db->num_rows);
			$this->page_add_value('days',$this->days_used);
			$this->page_add_value('chat_id',$chat_id);
		}
		public function process_msg ($value) {
			$return = array();
			$timestamp = $value['timestamp'];
			$date = new DateTime($timestamp);
			$ts = $date->getTimestamp();
			$return['timestamp'] = $ts;
			$valid_timeago = array('');
			$time_ago = $this->getTimeAgo($ts);
			if ( in_array($time_ago,$this->days_used) || ($this->ajax_load) ) {
				$time_ago = '';
			} else {
				$this->days_used[] = $time_ago;
			}
			$return['timeago'] = $time_ago;
			$return['class'] = ( $value['user_id'] == $this->current_user_id ) ? 'current' : '';
			$return['msg'] = str_replace("\n","<br />",$value['msg']);
			return $return;
		}
		
		public function getTimeAgo ($timestamp) {
			$day = date('l', $timestamp);
			if ( date('Ymd') == date('Ymd', $timestamp) ) {
				$day = "Today";
			} elseif (date("Ymd", strtotime("yesterday")) == date('Ymd', $timestamp)) {
				$day = "Yesterday";
			} elseif (date("Ymd", strtotime("-1 week")) >= date('Ymd', $timestamp)) {
				$day = $this->ago($timestamp);
			}
			return $day;
		}
		
		public function ago($time)
		{
		   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		   $lengths = array("60","60","24","7","4.35","12","10");
		
		   $now = time();
		
			   $difference     = $now - $time;
			   $tense         = "ago";
		
		   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			   $difference /= $lengths[$j];
		   }
		
		   $difference = round($difference);
		
		   if($difference != 1) {
			   $periods[$j].= "s";
		   }
		
		   return "$difference $periods[$j] ago ";
		}
	}
?>
<?php
	/*
	function timeAgo($time_ago){
		$cur_time 	= time();
		$time_elapsed 	= $cur_time - $time_ago;
		$seconds 	= $time_elapsed ;
		$minutes 	= round($time_elapsed / 60 );
		$hours 		= round($time_elapsed / 3600);
		$days 		= round($time_elapsed / 86400 );
		$weeks 		= round($time_elapsed / 604800);
		$months 	= round($time_elapsed / 2600640 );
		$years 		= round($time_elapsed / 31207680 );
		// Seconds
		if($seconds <= 60){
			$time_ago = "$seconds seconds ago";
		}
		//Minutes
		else if($minutes <=60){
			if($minutes==1){
				$time_ago = "one minute ago";
			}
			else{
				$time_ago = "$minutes minutes ago";
			}
			$time_ago = "Today";
		}
		//Hours
		else if($hours <=24){
			if($hours==1){
				$time_ago = "an hour ago";
			}else{
				$time_ago = "$hours hours ago";
			}
			$time_ago = "Today";
		}
		//Days
		else if($days <= 7){
			if($days==1){
				$time_ago = "Yesterday";
			}else{
				$time_ago = "$days days ago";
			}
		}
		//Weeks
		else if($weeks <= 4.3){
			if($weeks==1){
				$time_ago = "a week ago";
			}else{
				$time_ago = "$weeks weeks ago";
			}
		}
		//Months
		else if($months <=12){
			if($months==1){
				$time_ago = "a month ago";
			}else{
				$time_ago = "$months months ago";
			}
		}
		//Years
		else{
			if($years==1){
				$time_ago = "one year ago";
			}else{
				$time_ago = "$years years ago";
			}
		}
		
		return $time_ago;
	}
	*/
?>