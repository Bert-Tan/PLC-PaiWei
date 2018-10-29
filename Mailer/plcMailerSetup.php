<?php
	require_once('mail-signature.config.php');
	require_once('mail-signature.class.php');

	function plcSendMail ( $to, $subject, $msg ) {
		// This is the API to send a signed email from the Web Server to the recipient		
		$plcSig = new mail_signature	(	MAIL_RSA_PRIV,
																		MAIL_RSA_PASSPHRASE,
																		MAIL_DOMAIN,
																		MAIL_SELECTOR	);	
	
	  $plcHeaderArray = array (
	  	"MIME-Version: 1.0",
	  	"Content-type: text/html; charset=utf-8",
	  	"From: " . '"Pure Land Center & Buddhist Library"' . ' <library@amitabhalibrary.org>',
	  	"Cc: " . "library@amitabhalibrary.org",
	  	"Reply-To: library@amitabhalibrary.org",
	  	"X-Mailer: PHP/" . phpversion(),
	  	'' 	
	  );
	
		$message = preg_replace('/(?<!\r)\n/', "\r\n", $msg);
		$headers = preg_replace('/(?<!\r)\n/', "\r\n", implode( "\r\n", $plcHeaderArray ) );  	
	  $signed_headers = $plcSig -> get_signed_headers( $to, $subject, $message, $headers);
	  
	  return ( mail( $to, $subject, $message, $signed_headers.$headers ) );
	  
	} // plcSendMail()
?>