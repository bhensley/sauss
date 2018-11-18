<?php

/**
 * Shorty: Another URL Shortening Service (S:AUSS)
 * Copyright (C) 2010 Bobby Hensley
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://sauss.info/license.
 */

require_once ("config.php");
require_once ("include/smarty/Smarty.class.php");
require_once ("classes/url_shortener.class.php");

// Initiate (or attempt to anyways) a PDO object
try
{
	$db = new PDO
	(
    "mysql: host={$CONFIG["database"]["server"]}; dbname={$CONFIG["database"]["database"]}",
	  $CONFIG["database"]["username"],
	  $CONFIG["database"]["password"]
	);
}
catch (PDOException $err)
{
	if ($CONFIG["mode"] == "debug")
	{
		die ($err);
	}
	
	die ("Error: We're experiencing issues with the database at the moment.");
}

// Start Smarty
$template = new Smarty ();
$template->template_dir = $CONFIG["template"]["template_dir"];
$template->compile_dir  = $CONFIG["template"]["compile_dir"];

// Is there a URL request going on?
if (isset ($_GET["sid"]))
{
  try
  {
    $urlShortener = new UrlShortener ($db, $_GET["sid"]);
    
    // Show the statistics page?
    if ($urlShortener->_stats)
    {
      $stats = $urlShortener->get_sid_data ();
      
      if (count ($stats) == 3)
      {
        $template->assign ("valid", true);
        $template->assign ("accessed", $stats[0]);
        $template->assign ("visits", $stats[1]);
        $template->assign ("url", $stats[2]);
      }

      $template->assign ("page", "stats");
    }
    else
    {
      $urlShortener->send_user_to_url ();
    }
  }
  catch (UrlShortenerException $err)
  {
    $errorMessage = $err->getMessage ();
    
    $template->assign ("has_error", true);
    $template->assign ("error", $errorMessage);
  }
}

// Or perhaps the form has been submitted and needs to be processed?
if (isset ($_POST["url"]))
{
  try
  {
    $urlShortener = new UrlShortener ($db);
    $sid = $urlShortener->create_new_redirect ($_POST["url"]);
	
    if (!is_array ($sid))
    {
      $template->assign ("sid", $sid);
      $template->assign ("page", "new_redirect");
    }
  }
  catch (UrlShortenerException $err)
  {
    $errorMessage = $err->getMessage ();
    
    $template->assign ("has_error", true);
    $template->assign ("error", $errorMessage);
  }
}

// When all is said and done, output the index template
$template->display ("index.tpl");

?>