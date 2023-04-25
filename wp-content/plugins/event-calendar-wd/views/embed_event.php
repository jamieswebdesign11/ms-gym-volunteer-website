<?php
/**
 * Created by PhpStorm.
 * User: Sales1
 * Date: 3/17/2016
 * Time: 11:48 AM
 */
global $ecwd_options;
$gmap_key = (isset($ecwd_options['gmap_key'])) ? trim($ecwd_options['gmap_key']) : "";
echo '<!DOCTYPE html>
<html lang="en-US" class="no-js">
<head>
	<meta charset="UTF-8">

    <link rel="stylesheet" id="ecwd-public-css"  href="'.ECWD_URL.'/css/style.css" type="text/css" media="all"/>

    <script type="text/javascript" src="'.site_url().'/wp-includes/js/jquery/jquery.js?ver=1.11.3"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&#038;sensor=false&#038;libraries=places&#038;ver=5.0.49&key='.$gmap_key.'"></script>
    <script type="text/javascript" src="'.ECWD_URL.'/js/gmap/gmap3.min.js"></script>
    <script type="text/javascript" src="'.ECWD_URL.'/js/ecwd_popup.js"></script>
    <script type="text/javascript">
    /* <![CDATA[ */
    var ecwd = {"ajaxurl":"'.site_url().'wp-admin/admin-ajax.php","plugin_url":"'.ECWD_URL.'"};
    /* ]]> */
    </script>
    <script type="text/javascript" src="'.ECWD_URL.'/js/scripts.js"></script>
</head>
<body>';