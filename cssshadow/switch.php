<?php
$set = $_GET['set'];
$set = preg_replace('#[^a-zA-Z0-9_-]#', '', $set);
setcookie ('sitestyle', $set, time()+31536000, '/', 'floatinginspace.za.org', '0');
if ($_SERVER['HTTP_REFERER']) {
    $parsed_url = parse_url($_SERVER['HTTP_REFERER']);
}
else $parsed_url = parse_url('http://www.floatinginspace.za.org');
$url = $parsed_url[scheme].'://'.$parsed_url[host].$parsed_url[path];
header('Location: '.$url);
?>
