<?php
/**
 * This example shows settings to use when submitting a request to get a USSD mobile money PIN
 * prompt to transfer funds from a mobile money user to your Yo! Payments Account without having 
 * to wait for a response from the user
 */

require '../YoAPI.php';

// Create a new YoAPI instance with Yo! Payments Username and Password
//Set below variables to your Yo! Payments username and password accordingly
$username = "";
$password = "";
$mode = "sandbox";//In production, set this to "production"
$yoAPI = new YoAPI($username, $password, $mode);

// Create a unique transaction reference that you will reference this payment with
$transaction_reference = date("YmdHis").rand(1,100);
$yoAPI->set_external_reference($transaction_reference);

// Set nonblocking to TRUE so that you get an instant response
$yoAPI->set_nonblocking("TRUE");

// Set an instant notification url where a successful payment notification POST will be sent
// See documentation on how to handle IPN
$yoAPI->set_instant_notification_url('example.com/ipn.php');

// Set a failure notification url where a failed payment notification POST will be sent
// See documentation on how to handle IPNs
$yoAPI->set_failure_notification_url('example.com/fpn.php');

$response = $yoAPI->ac_deposit_funds('256770000000', 1000, 'Reason for transfer of funds');

//Wait a little and check for the status of the transaction.
sleep(25);
check_transaction($transaction_reference);


if($response['Status']=='OK') {
	echo "Waiting for user to confirm mobile money transfer. You can check using this Transaction Reference = ".$response['TransactionReference'].". Thank you for using Yo! Payments\n";

	// Save this transaction for future reference
}else{
	echo "Yo Payments Error: ".$response['StatusMessage']."\n";
}

function check_transaction($transaction_reference)
{
	global $yoAPI;

	$transaction = $yoAPI->ac_transaction_check_status(NULL, $transaction_reference);
	if(strcmp($transaction['TransactionStatus'],'SUCCEEDED')==0){
		// Transaction was completed and funds were deposited onto the account
		// Save data into the database
		echo "Tranaction was successfull ";
	}else{
		echo "Transaction is still in {$transaction['TransactionStatus']} state.\n";
	}
}