<?php
$qs = array();
$qs['term'] = isset($_GET['term']) ? $_GET['term'] : 'bollywood' ;
$qs['limit'] = isset($_GET['limit']) ? $_GET['limit'] : '0' ;
$qs['entity'] = isset($_GET['entity']) ? $_GET['entity'] : 'musicVideo' ;
$url = 'https://itunes.apple.com/search?'. http_build_query($qs);
$data = file_get_contents($url);
$data = trim($data);
echo $data;
?>