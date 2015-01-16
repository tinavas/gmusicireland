<?php
	
	/*
	    Version:	1.1
	    Author:		Muhammad Mujtaba
	    Twitter:	@mushti
	    Email:		mujtaba@ilacse.com
	    License: 	Free to use, edit and re-distribute 
	    			without any warranty. If you find that 
	    			this code is out-dated, kindly let me
	    			know so that I may provide an update.
   	*/

	class FBCounts {
		// cURL request timeout time.
		private $timeout = 10;
		// URL for the FBCounts.
		private $url = NULL;
		// cURL response.
		private $response = NULL;
		// Number of times URL was shared.
		public $share_count = 0;
		// Number of times URL was liked.
		public $like_count = 0;
		// Number of comments made inside Facebook on a shared URL.
		public $comment_count = 0;
		// Total Number of shares, likes and comments.
		public $total_count = 0;
		// Constructor for FBCounts class.
		function __construct( $url, $timeout = 10 ) {
			// Encode the URL.
			$this->url = rawurlencode($url);
			// Set cURL request timeout.
			$this->timeout = $timeout;
			// Process the URL.
			if( $this->processURL() ) {
				$json = json_decode($this->response, TRUE);
				if(isset($json[0]['share_count'])) $this->share_count = $json[0]['share_count'];
				if(isset($json[0]['like_count'])) $this->like_count = $json[0]['like_count'];
				if(isset($json[0]['comment_count'])) $this->comment_count = $json[0]['comment_count'];
				if(isset($json[0]['total_count'])) $this->total_count = $json[0]['total_count'];
			}
		}
		// Gets counts for the URL.
		private function processURL() {
			$cURL = curl_init();
			curl_setopt($cURL, CURLOPT_URL, 'http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls='.$this->url);
			curl_setopt($cURL, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			curl_setopt($cURL, CURLOPT_FAILONERROR, 1);
			curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($cURL, CURLOPT_TIMEOUT, $this->timeout);
			$response = curl_exec($cURL);
			if(curl_error($cURL)) {
				return FALSE;
			} else {
				$this->response = $response;
				return TRUE;
			}
		}
	}
?>