<?php
  	/*
			Anisha Gupta
    		Email : ag4203@nyu.edu
 
  	*/

require "paypal_class.php";
include("_assets/connect.php");
	
	$email = '';
	$amount = '';
	$currency = '';
	$total_sales = 0;
	$request_payment=0;
	$senderPaypalId= "dan_api1.paidvaca.com";
	$Paypal_Password = "VVAXDGQ3LVBWA9UL";
	$API_Key = "AT1x0c9z-HQld3NS8fmlKVZpSlXHA2FQzlNAYp6mh.0ATbit83.FLN.F";
	$Database_Server = "localhost";
	$Database_User = "root";
	$Database_Password = "";
	$Database_Name = "paidvaca_1";
	$money_sent= 0;
	$money_not_sent = 0;
	
try{
	echo "atleast called the class";
	//$paypal = new Paypal("dan_api1.paidvaca.com", "VVAXDGQ3LVBWA9UL", "AT1x0c9z-HQld3NS8fmlKVZpSlXHA2FQzlNAYp6mh.0ATbit83.FLN.F"); 	
	$paypal = new Paypal($senderPaypalId, $Paypal_Password, $API_Key); 	

	try{
		//$con=mysql_connect($Database_Server,$Database_User,$Database_Password,$Database_Name);
		echo "Connected to the database", "<br>";
		$log = date(DATE_RFC822)."Connected to the database. ".PHP_EOL;
		file_put_contents('log.txt', $log, FILE_APPEND);
		$result = mysql_query("SELECT * FROM users");

		while($row = mysql_fetch_array($result)) { 
			$email = (string)$row['paypal_email'];
			echo "email id : ".$email
			$amount = (string)$row['commission'];
			$total_sales = (string)$row['total_sales'];
			$request_payment = (string)$row['request_payment'];
			//$currency = urlencode("USD");
			$currency= urlencode((string)$row['currency']);
			  
			if ($request_payment == 1){
			
				if($total_sales>=1000){
					try{
						$send_payment = $paypal->pay($email, $amount, $currency, "Thanks for an amazing service");
						echo "money sent to".$email , "\n";

						if("SUCCESS" == strtoupper($send_payment["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($send_payment["ACK"])) {
							//echo "money sent to ".$email , "\n";
							$stmt = "UPDATE users SET request_payment=0 WHERE paypal_email= '$email'";
							if (mysql_query($stmt)){
								$money_sent = 1;
								$log = date(DATE_RFC822)."Money sent to ".$email.PHP_EOL;
								file_put_contents('log.txt', $log, FILE_APPEND);
								//exit('MassPay Completed Successfully: '.print_r($send_payment, true));
							}
						} else {
							$money_not_sent = 1;
							//echo "Could not send money to ".$email , "\r\n";
							$log = date(DATE_RFC822)."Could not send money to ".$email.PHP_EOL;
							file_put_contents('log.txt', $log, FILE_APPEND);
							//exit('MassPay failed: ' . print_r($send_payment, true));
						}
					}
					catch(Exception $e){
						echo '<script language="javascript">';
						echo 'alert("There is some problem in MassPay. Please check if you have sufficient balance in Paypal account."<br>",)';
						echo '</script>';
						//echo 'Cannot send money to ', $email,' Reason: ',  $e->getMessage() , "\n";
						$log = date(DATE_RFC822).'Cannot send money to '.$email.' Reason: '.  $e->getMessage().PHP_EOL;
						file_put_contents('log.txt', $log, FILE_APPEND);
						continue;
					}

				}
			  }
			else
				continue;
			echo "<br>";
		}
			mysqli_close($con);
	}
	catch (Exception $e){
		//echo 'Caught exception: ',  $e->getMessage() , "\n";
		if (mysqli_connect_errno()) {
			//echo "Failed to connect to MySQL: " . mysqli_connect_error() , "\n";
			$log = date(DATE_RFC822).'Failed to connect to MySQL: '.mysqli_connect_error().PHP_EOL;
			file_put_contents('log.txt', $log, FILE_APPEND);
			return;
		}
	}
}
catch(Exception $e){
	//echo 'Caught exception: ',  $e->getMessage() , "\n";
	$log = date(DATE_RFC822).'Caught exception: '.$e->getMessage().PHP_EOL;
	file_put_contents('log.txt', $log, FILE_APPEND);
	return;
}

finally
{	
		if ($money_sent == 0 && $money_not_sent==0){
			echo '<script language="javascript">';
			echo 'alert("No requests!")';
			echo '</script>';
			$log = date(DATE_RFC822).'No requests!'.PHP_EOL;
			file_put_contents('log.txt', $log, FILE_APPEND);
			return;
			
		}
		elseif ($money_sent == 1 && $money_not_sent==0) {
			echo '<script language="javascript">';
			echo 'alert("Masspay Completed Successfully. Please check the log for confirmation.")';
			echo '</script>';
			$log = date(DATE_RFC822).'Masspay Completed Successfully. Please check the log for confirmation.'.PHP_EOL;
			file_put_contents('log.txt', $log, FILE_APPEND);

			return;

		}
		elseif ($money_sent==0 && $money_not_sent == 1) {
			echo '<script language="javascript">';
			echo 'alert("Could not send money! Please check log and your paypal account and make sure it has sufficient balance or please make sure the amount to be sent is upto 2 decimal place and currency is 3 letter code.")';
			echo '</script>';
			$log = date(DATE_RFC822).'MassPay failed: ' .print_r($send_payment, true).PHP_EOL;
			file_put_contents('log.txt', $log, FILE_APPEND);
			//exit('MassPay failed: ' . print_r($send_payment, true));
			return;
		}
		elseif ($money_sent==1 && $money_not_sent == 1) {
			echo '<script language="javascript">';
			echo 'alert("Money sent to some recepients and could not send to some. Please check log and your paypal account. Make sure it has sufficient balance, the amount to be sent is upto 2 decimal place and Currency is 3 letter code.")';
			echo '</script>';
			$log = date(DATE_RFC822).'MassPay failed: ' .print_r($send_payment, true).PHP_EOL;
			file_put_contents('log.txt', $log, FILE_APPEND);
			//exit('MassPay failed: ' . print_r($send_payment, true));
			return;
		}
		return;

}

   /*$log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
            "Attempt: ".($result[0]['success']=='1'?'Success':'Failed').PHP_EOL.
            "User: ".$username.PHP_EOL.
            "Pass: ".$password.PHP_EOL.
            "-------------------------".PHP_EOL;
    //-
    file_put_contents('./log_'.date("j.n.Y").'.txt', $log, FILE_APPEND); */
	

?>


