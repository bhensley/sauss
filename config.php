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
 
$CONFIG = array ();

/**
 * Where are we within the server's filesystem?  Leave off the trailing slash.
 *
 * Example:
 *   /var/www/html/sauss
 *   C:/Web/html/sauss
 */
$CONFIG["base"] = "";

/**
 * What mode the site is running in.  Accepted choices are:
 *  "debug" -> Errors are outputted to the screen, causing a potential security
 *    risk.
 *  "production" -> Either no errors are outputted to the screen or they're
 *    explicitly written to be not contain information about the server.
 *    
 *  Run in production mode whenever possible.
 */
$CONFIG["mode"] = "debug";

/**
 * This script assumes you're using MySQL but it uses the PDO library so
 * changing between different RDBMS's is painless enough.  Either way, the
 * below is the standard server address, database name, username and password
 * needed to connect to a MySQL database.
 */
$CONFIG["database"]["server"]   = "";
$CONFIG["database"]["database"] = "";
$CONFIG["database"]["username"] = "";
$CONFIG["database"]["password"] = "";

/**
 * The following are the directoy locations needed for the Smarty template
 * engine.  As before, leave off any trailing slashes.
 */
$CONFIG["template"]["template_dir"] = "{$CONFIG["base"]}/templates";
$CONFIG["template"]["compile_dir"]  = "{$CONFIG["base"]}/templates_c";