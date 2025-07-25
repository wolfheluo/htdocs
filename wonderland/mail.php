<?php

## CONFIG ##

# LIST EMAIL ADDRESS
$recipient = "gm900411@gmail.com";

# SUBJECT (Subscribe/Remove)
$subject = "Subscribe";

# RESULT PAGE
$location = "enter the URL of the result page here";

## FORM VALUES ##

# SENDER - WE ALSO USE THE RECIPIENT AS SENDER IN THIS SAMPLE
# DON'T INCLUDE UNFILTERED USER INPUT IN THE MAIL HEADER!
$sender = $recipient;

# MAIL BODY
$body .= "name: ".$_REQUEST['name']." \n";
$body .= "email: ".$_REQUEST['email']." \n";
$body .= "anliegen: ".$_REQUEST['anliegen']." \n";

## SEND MESSGAE ##

mail( $recipient, $subject, $body, "From: $sender" ) or die ("Mail could not be sent.");

## SHOW RESULT PAGE ##

header( "Location: $location" );
?>