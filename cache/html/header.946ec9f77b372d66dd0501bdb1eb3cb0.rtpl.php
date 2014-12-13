<?php defined('ROOT') or exit(); ?><!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='utf-8'>
<title><?php echo $lang["page_title"];?></title>
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no'>
<meta name='apple-mobile-web-app-capable' content='yes'>
<link href='views/html/css/united.css' rel='stylesheet'>
<?php if( count($core_files["css"]) >= 1 ){ ?>
<?php $counter1=-1; if( isset($core_files["css"]) && is_array($core_files["css"]) && sizeof($core_files["css"]) ) foreach( $core_files["css"] as $key1 => $value1 ){ $counter1++; ?>
<link href='views/html/css/<?php echo $value1;?>.css' rel='stylesheet' type='text/css'>
<?php } ?>
<?php } ?>
</head>
<body>
<div class='container'>