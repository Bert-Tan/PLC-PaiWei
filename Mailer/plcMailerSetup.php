<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of the script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// require "../pgConstants.php";
require "Exception.php";
require "PHPMailer.php";
require "SMTP.php";

// We are sending; so define the From, Reply-To, and Name Appearance
DEFINE ( 'FROM_ADDR', 'library@amitabhalibrary.org' );
DEFINE ( 'REPLY_TO', 'library@amitabhalibrary.org' );
DEFINE ( 'APPEARANCE', 'Pure Land Center' );

// Below is the Web Printing capable printer at the Pure Land Center
$prtTo = array (
    array (
        'email' => 'plc-mfc@hpeprint.com',
        'name' => "Printer @ PLC"
    )
);
/* BEGIN test parameters
$testTo = array (
    array (
        'email' => 'bert.tan@comcast.net',
        'name'  => '譚祖德'
    )
);
$testCc = array (
    array (
        'email' => 'tsutetan@gmail.com',
        'name'  => '郭春輝'
    )
);
$testAttachments = array (
    array (
        'path' => '/Users/berttan/MacPLC/Annc/pandemic.pdf',
        'name' =>  'stay-at-home'
    )
);
$testSubject = "Test sending email attachments using SMTP";
$testHtml_Msg = "Test sending email attachments using SMTP: <b>Success!</b>";
$testTxt_Msg = "Test sending email attachments using SMTP: Success!";
  End test parameters
 */

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);    // $mail->setLanguage( 'ch' ); needed for debug only
$mail->CharSet = 'UTF-8';

// DKIM settings
$mail->DKIM_selector = DKIM_selector;
$mail->DKIM_domain = DKIM_domain;
$mail->DKIM_identity = DKIM_identity;
$mail->DKIM_passphrase = DKIM_passphrase;
$mail->DKIM_private = DKIM_private;

// Server settings
$mail->SMTPDebug  = SMTP::DEBUG_OFF;
$mail->isSMTP();
$mail->Host       = SMTP_host;  // 'smtp.gmail.com';
$mail->Port       = SMTP_port;
$mail->SMTPAuth   = true;
$mail->Username   = SMTP_user;
$mail->Password   = SMTP_pass;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

// From me
$mail->setFrom( FROM_ADDR, APPEARANCE );
$mail->addReplyTo( REPLY_TO, APPEARANCE );

function plcSendEmailAttachment( $to, $cc, $subject, $html_msg, $txt_msg, $attachments ) {
    global $mail;
    /*
     * $to, $cc:    Array of recipients. each element has ( <email>, <name> )
     * $html_msg:   Message Body in HTML format
     * $txt_msg:    Message Body in plain text format
     * $attachments:Array of files, each element has ( <full path to the file>, <filename to appear> )
     */

    //Recipients
    if ( ( $to == null ) || ( sizeof( $to ) == 0 ) ) return;
    foreach ( $to as $recipient ) {
        $mail->addAddress( $recipient[ 'email' ], $recipient[ 'name' ] );     // Add a recipient; name is optional
    }
    
    // Cc Recipients
    if ( ( $cc != null ) || ( sizeof( $cc ) > 0 ) ) {
        foreach ( $cc as $recipient ) {
            $mail->addCC( $recipient[ 'email' ], $recipient[ 'name' ] );     // Add a recipient; name is optional
        }
    }
    
    // Subject
    $mail->Subject = $subject;
    
    // Content body
    $mail->isHTML(true);
    $mail->Body = $html_msg;
    if ( $txt_msg != null && strlen( $txt_msg ) > 0 ) {
        $mail->AltBody = $txt_msg;
    }
 
    // Attachments
    if ( ( $attachments != null ) || ( sizeof($attachments) > 0 ) ) {
        foreach ( $attachments as $attachment  ) {
            $mail->addAttachment( $attachment[ 'path' ], $attachment[ 'name' ] );  
        }
    } // have attachments

    // send it now
    try {
        $mail->send();
        return true;
    } catch (Exception $e ) {
        return false;
//        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
    }    
} // function plcSendEmailAttachment()

// test call to the function
//    plcSendEmailAttachment( $prtTo, null, $testSubject, $testHtml_Msg, $testTxt_Msg, $testAttachments );

?>