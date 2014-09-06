<?php
	/*
			Anisha Gupta
    		Email : ag4203@nyu.edu
  	*/
  
  class Paypal {
		
		public function __construct($username, $password, $signature) {
			$this->username = urlencode($username);
			$this->password = urlencode($password);
			$this->signature = urlencode($signature);
			$this->version = urlencode("51.0");
			$this->api = "https://api-3t.paypal.com/nvp";
			
			//The functions can be modified but need to be urlencoded
			$this->type = urlencode("EmailAddress");
			//$this->currency = urlencode("USD");
			$this->subject = urlencode("Instant Paypal Payment");
		}
		
		public function pay($email, $amount, $currency, $note="Instant Payment") {
			//print  $email;
			//print  $amount;
			$string = "&EMAILSUBJECT=".$this->subject."&RECEIVERTYPE=".$this->type."&CURRENCYCODE=".$currency;
			$string .= "&L_EMAIL0=".urlencode($email)."&L_Amt0=".urlencode($amount)."&L_NOTE0=".urlencode($note);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->api);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			
			$request = "METHOD=MassPay&VERSION=".$this->version."&PWD=".$this->password."&USER=".$this->username."&SIGNATURE=".$this->signature."$string";
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
			$httpResponse = curl_exec($ch);
			if(!$httpResponse) {
				//print "Not paid to ".$email;
				$log = date(DATE_RFC822).'MassPay failed: '.curl_error($ch).'('.curl_errno($ch).')'.PHP_EOL;
				file_put_contents('log.txt', $log, FILE_APPEND);
				//exit("MassPay failed: ".curl_error($ch).'('.curl_errno($ch).')');
			}
			
				//print "Paid to ".$email;
			
			$httpResponseArray = explode("&", $httpResponse);
			$httpParsedResponse = array();
			foreach ($httpResponseArray as $i => $value) {
				$tempArray = explode("=", $value);
				if(sizeof($tempArray) > 1) {
					$httpParsedResponse[$tempArray[0]] = $tempArray[1];
				}
			}
			
			if((0 == sizeof($httpParsedResponse)) || !array_key_exists('ACK', $httpParsedResponse)) {
				$log = date(DATE_RFC822)."Invalid HTTP Response for POST request($request) to ".$this->api.PHP_EOL;
				file_put_contents('log.txt', $log, FILE_APPEND);
				return;
				exit("Invalid HTTP Response for POST request($request) to ".$this->api);
			}
			
			return $httpParsedResponse;
		}
		
	}
?>
