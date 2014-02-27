<?php

function slugify($text){
	$text = strtolower($text);
	$text = preg_replace('/\+/', '', $text); // Replace spaces with 
	$text = preg_replace('/\s+/', '-', $text); // Replace spaces with -
	$text = preg_replace('/[^\w\-]+/', '', $text); // Remove all non-word chars
	$text = preg_replace('/\-\-+/', '-', $text); // Replace multiple - with single -
	$text = preg_replace('/^-+/', '', $text); // Trim - from start of text
	$text = preg_replace('/-+$/', '', $text); // Trim - from end of text
	return $text;
}

$formData = $_POST['formData'];
parse_str($formData, $formArray);

$message = '<html><body>';
$csvReturn = "";
foreach ($formArray as $key => $value) {
	if(is_array($value)){
		foreach ($value as $innerKey => $innerValue) {
			$message .= "<strong>" . $key . "</strong> " . $innerValue . "<br />";
			$csvSafeValue = str_replace(",", "", $innerValue);
			$csvReturn .= $csvSafeValue . ",";
		}
	} else {
		$message .= "<strong>" . $key . "</strong> " . $value . "<br />";
		$csvSafeValue = str_replace(",", "", $value);
		$csvReturn .= $csvSafeValue . ",";
	}
}

$csvReturn = "\n" . substr($csvReturn, 0, -1);

$fp = fopen('formData.csv', 'a');
fwrite($fp, $csvReturn);
fclose($fp);

$ccString = "CC: renemerino4@gmail.com";

$xml = json_decode(file_get_contents("http://sandbox.ingerman.com/_data/contacts/"));
foreach ($xml as $key => $value) {
	if($formArray['inquiryType'] == slugify($value->the_title)){
		$ccString .= ", " . $value->email_address;
	}
}

$to = 'rmerino@bermangrp.com';
//$to = 'wfaye@bermangrp.com';
$subject = 'Contact Form Submission';
$headers = "From: " . strip_tags($_POST['req-email']) . "\r\n";
$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
$headers .= $ccString . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

$message .= '</body></html>';

if (mail($to, $subject, $message, $headers)) {
	echo("success");
} else {
	echo("fail");
}


?>