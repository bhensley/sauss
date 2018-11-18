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

class UrlShortenerException extends Exception {}

class UrlShortener
{
	/**
	 * @access public
	 * @var string
	 */
	public $_sid;
	
	/**
	 * @access public
	 * @var boolean
	 */
	public $_stats = false;
	
	/**
	 * @access private
	 * @var string
	 */
	private $_targetUrl;
	
	/**
	 * @access private
	 * @var PDO resource
	 */
	private $_db;

	/**
	 * Set the PDO resource object and check if we're working with a specific SID or not.
	 *
	 * @access  public
	 * @param   PDO Resource    $dbResource
	 * @param   integer         $sid
	 */
	public function __construct ($dbResource, $sid = null) {
		$this->_db  = $dbResource;
		
		if ($sid != null) {
			$this->_sid = $this->clean_sid ($sid);
			
			if (!$this->_targetUrl = $this->validate_sid ()) {
				throw new UrlShortenerException ("Invalid SID given.");
			}
		}
	}
	
	/**
	 * Update, or create, the statistics field for the current SID.  And then send the user
	 * to the URL it corresponds to.
	 *
	 * @access  public
	 */
	public function send_user_to_url () {
		if (strlen ($this->_targetUrl) > 0)	{
			$sql = $this->_db->prepare ("SELECT COUNT(*), number_of_visits FROM statistics WHERE sid = :target");
			$sql->bindValue (":target", $this->_sid);
			$sql->execute ();
			
			$resultSet = $sql->fetchAll ();

			if (count ($resultSet) > 0 && $resultSet[0][0] == 1) {
				$newVisited = ++$resultSet[0][1];
				
				$sql = $this->_db->prepare ("UPDATE statistics SET number_of_visits = :num WHERE sid = :target");
				$sql->bindValue (":num", $newVisited);
				$sql->bindValue (":target", $this->_sid);
				$sql->execute ();
			} else {
				$sql = $this->_db->prepare ("INSERT INTO statistics (sid, created_at, last_accessed_at, number_of_visits) VALUES (:sid, NOW(), NOW(), 1)");
				$sql->bindValue (":sid", $this->_sid);
				$sql->execute ();
			}

			// If http:// or https:// isn't stored, set up http://.  Otherwise the redirect
			// will put them at our-domain.tld/desired-domain.tld.
			// Let their server determine if it should be https or not.
			if (substr($this->_targetUrl, 0, 4) !== 'http')
				$this->_targetUrl = 'http://' . $this->_targetUrl;
			
			header ("Location: {$this->_targetUrl}");
		}
		
		throw new UrlShortenerException ("No SID given.");
	}
	
	/**
	 * Grab the statistics corresponding to the current SID.
	 *
	 * @access public
	 * @return array    An array containing the statistics found
	 *
	 * @TODO: Modularize this method by just grabbing everything from the database
	 *        and return an appropriate array.  New statistics could then be added
	 *        with ease and without need to modify this class.
	 */
	public function get_sid_data () {
		$sql = $this->_db->prepare ("SELECT COUNT(*), last_accessed_at, number_of_visits FROM statistics WHERE sid = :target");
		$sql->bindValue (":target", "dsa323rf");
		$sql->execute ();

		$resultSet = $sql->fetchAll ();

		if (count ($resultSet) > 0 && $resultSet[0][0] == 1) {
			return array ($resultSet[0][1], $resultSet[0][2], $this->_targetUrl);
		}

		throw new UrlShortenerException ("Could not find the statistics for the given SID.");
	}
	
	/**
	 * Build a new redirect.
	 *
	 * @access  public
	 * @param   string    $target The URL the redirect represents
	 * @return  integer   The new SID
	 */
	public function create_new_redirect ($target) {
		// Clean URL?
		if (!preg_match ("/^[http:\/\/]*[www\.]*[a-z0-9\-]{3,}\.[a-z]{2,4}.*$/i", $target)) {
			throw new UrlShortenerException ("Invalid URL given.");
		}
				
		// Check to see if the target URL has already been shortened
		$sql = $this->_db->prepare ("SELECT COUNT(*), sid FROM shortened_urls WHERE url = :url");
		$sql->bindValue (":url", $target);
		$sql->execute ();
		
		$resultSet = $sql->fetchAll ();
		
		if (count ($resultSet) > 0 && $resultSet[0][0] == 1) {
			// Found an pre existing SID, return it.
			return $resultSet[0][1];
		} else {
			$this->_sid = $this->generate_rand_string ();
			$foundSid = false;
			
			while (!$foundSid) {
				if ($this->validate_sid ()) {
					$this->_sid = $this->generate_rand_string ();
				} else {
					$foundSid = true;
				}
			}
			
			// Register the SID
			$sql = $this->_db->prepare ("INSERT INTO shortened_urls (sid, url) VALUES (:sid, :url)");
			$sql->bindValue (":sid", $this->_sid, PDO::PARAM_STR);
			$sql->bindValue (":url", $target, PDO::PARAM_STR);
			$sql->execute ();
			
			if ($sql->rowCount () == 0) {
				throw new UrlShortenerException ("Failed to register the new redirect.");
			}
			
			return $this->_sid;
		}
	}

	/**
	 * Check to make sure the current SID exists.
	 *
	 * @access  private
	 * @return  string    The URL the valid SID represents.
	 * @return  boolean   Could not  find the SID in the database, return false.
	 */
	private function validate_sid () {
	    $sql = $this->_db->prepare ("SELECT COUNT(*), url FROM shortened_urls WHERE sid = :target");
	    $sql->bindValue (":target", $this->_sid);
	    $sql->execute ();

	    $resultSet = $sql->fetchAll ();
	    
	    // Check to see if we found a URL
	    if (count ($resultSet) > 0 && $resultSet[0][0] == 1) {
			return $resultSet[0][1];
	    }
	    
	    return false;
	}

	/**
	 * Generate an appopriate random string for a new SID.
	 *
	 * @access  private
	 * @return  string    New random SID.
	 */
	private function generate_rand_string () {
		$chars = "abcdefghijklmnopqrstuvwxyz1234567890";
		$tmpString = "";
		
		while (strlen ($tmpString) < 8) {
			$rand = mt_rand (0, (strlen ($chars) - 1));
			$tmpString .= substr ($chars, $rand, 1);
		}
		
		return $tmpString;
	}
	
	/**
	 * Check that we have a clean SID.  Also look for the statistics specifier and mark
	 * this as a statistics viewing instead of redirect if necassary.
	 *
	 * @access  private
	 * @param   string    The SID to check.
	 * @return  string    The SID checked out.
	 */
	private function clean_sid ($sid) {
		if (preg_match ("/^[a-z0-9]+(!{0,1})$/i", $sid, $matches)) {
			if ($matches[1] == "!") {
				$this->_stats = true;
				$sid = substr ($sid, 0, (strlen ($sid) - 1));
			}
			
			return $sid;
		}
		
		throw new UrlShortenerException ("Improper SID given.");
	}
}