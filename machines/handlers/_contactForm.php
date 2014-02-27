<?php

// Get PHP Mail Class
require 'mailCLass.php';

$formData = $_POST['formData'];
parse_str($formData, $formArray);

$body = 

// Prepage mail to sent
$to      = "rmerino@bermangrp.com";
$from    = $formArray['email'];
$subject = "IHMM Contact Form Submission";
$body    = "This is a test email body that will be in text format";
$mail    = new Mail($to, $from, $subject, $body);

if ($mail->send()) {
	echo 'success';
} else {
	echo 'fail';
}