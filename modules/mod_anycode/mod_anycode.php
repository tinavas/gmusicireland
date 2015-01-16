<?php
# HD-AnyCode          	          	          	              
# Copyright (C) 2012 by Hyde-Design  	   	   	   	   
# Homepage   : www.hyde-design.co.uk		   	   	   
# Author     : Hyde-Design    		   	   	   	   
# Email      : rich@hyde-design.co.uk 	   	   	   
# Version    : 3.0                        	   	    	
# License    : http://www.gnu.org/copyleft/gpl.html GNU/GPL    

// no direct access
defined('_JEXEC') or die('Restricted access');

//get the param
 
$any_code    	= $params->get("anycode", "");
$urlsniffer     = $params->get("urlsniffer", "");
$browsersniffer = $params->get("browsersniffer", "all");
$browser        = strtolower($_SERVER['HTTP_USER_AGENT']);
$current_url    = $_SERVER['REQUEST_URI'];

global $mainframe;

// if urlsniffer not needed
if ($urlsniffer=="")             { if ($browsersniffer=="all") { echo $any_code; } 
                                       elseif (ereg($browsersniffer, $browser)) { echo $any_code; };}

// if urlsniffer is needed
elseif (strstr($current_url, $urlsniffer)) 
                                     { if ($browsersniffer=="all") { echo $any_code; } 
                                       elseif (ereg($browsersniffer, $browser)) { echo $any_code; }; };