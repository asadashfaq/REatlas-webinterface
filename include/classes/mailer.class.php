<?php 
/**
 * Mailer.php
 *
 * The Mailer class is meant to simplify the task of sending
 * emails to users. Note: this email system will not work
 * if your server is not setup to send mail.
 *
 */
include_once dirname(__FILE__) .'/../../Tools/SwiftMailer/swift_required.php';

class Mailer
{
    var $transport; //the mail transport configuration
   
    
   /**
    * sendWelcome - Sends a welcome message to the newly
    * registered user, also supplying the username and
    * password.
    */
   function Mailer() {
       $this->transport = Swift_MailTransport::newInstance();
   }
   function sendWelcome($user, $email, $pass){
       
       
       // Create the replacements array
        $replacements = array();
          $replacements[$email] = array (
            "{fullname}" => $user
          );
       
         // Create an instance of the plugin and register it
        $plugin = new Swift_Plugins_DecoratorPlugin($replacements);
        $mailer = Swift_Mailer::newInstance($this->transport);
        $mailer->registerPlugin($plugin);

        // Create the message
        $message = Swift_Message::newInstance();
        $message->setSubject("REAtlas registration - Welcome!");
        $body = "{fullname},\n\n"
             ."Welcome! You've just registered at REAtlas "
             ."with the following information:\n\n"
             ."Username: {fullname}\n\n"
             ."You will get an activation mail soon.\n\n"
             ."If you ever lose or forget your password, a new "
             ."password will be generated for you and sent to this "
             ."email address, if you would like to change your "
             ."email address you can do so by going to the "
             ."My Account page after signing in.\n\n"
             ."- AU";
     
        if(EMAIL_HTML)
            $message->setBody($body,'text/html');
         else
            $message->setBody($body,'text/plain');
         
        $message->setFrom(EMAIL_FROM_ADDR,EMAIL_FROM_NAME);

        // Send the email
          $message->setTo($email, $user);
        
        //Pass a variable name to the send() method
        return $mailer->send($message, $failures);

        /*
      
      $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
      $subject = "REAtlas registration - Welcome!";
      $body = $user.",\n\n"
             ."Welcome! You've just registered at REAtlas "
             ."with the following information:\n\n"
             ."Username: ".$user."\n"
             ."Password: ".$pass."\n\n"
             ."If you ever lose or forget your password, a new "
             ."password will be generated for you and sent to this "
             ."email address, if you would like to change your "
             ."email address you can do so by going to the "
             ."My Account page after signing in.\n\n"
             ."- AU";

      $succ =  mail($email,$subject,$body,$from);
       Tools::d("Success");
         * *
         */
   }
   
   /**
    * sendNewPass - Sends the newly generated password
    * to the user's email address that was specified at
    * sign-up.
    */
   function sendNewPass($user, $email, $pass){
       
       
       // Create the replacements array
        $replacements = array();
          $replacements[$email] = array (
            "{fullname}" => $user,
            "{newpass}"  => $pass
          );
       
         // Create an instance of the plugin and register it
        $plugin = new Swift_Plugins_DecoratorPlugin($replacements);
        $mailer = Swift_Mailer::newInstance($this->transport);
        $mailer->registerPlugin($plugin);

        // Create the message
        $message = Swift_Message::newInstance();
        $message->setSubject("REAtlas - Your new password!");
        $body ="{fullname},\n\n"
             ."We've generated a new password for you at your "
             ."request, you can use this new password with your "
             ."username to log in to REAtlas.\n\n"
             ."Username: {fullname}\n"
             ."New Password: {newpass}\n\n"
             ."It is recommended that you change your password "
             ."to something that is easier to remember, which "
             ."can be done by going to the My Account page "
             ."after signing in.\n\n"
             ."- AU";
        
        if(EMAIL_HTML)
            $message->setBody($body,'text/html');
         else
            $message->setBody($body,'text/plain');
            
            
        $message->setFrom(EMAIL_FROM_ADDR,EMAIL_FROM_NAME);

        // Send the email
          $message->setTo($email, $user);
        
        //Pass a variable name to the send() method
        return $mailer->send($message, $failures);
        
        /*
      $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
      $subject = "REAtlas - Your new password";
      $body = $user.",\n\n"
             ."We've generated a new password for you at your "
             ."request, you can use this new password with your "
             ."username to log in to REAtlas.\n\n"
             ."Username: ".$user."\n"
             ."New Password: ".$pass."\n\n"
             ."It is recommended that you change your password "
             ."to something that is easier to remember, which "
             ."can be done by going to the My Account page "
             ."after signing in.\n\n"
             ."- AU";
             
      return mail($email,$subject,$body,$from);
         * *
         */
   }
};

/* Initialize mailer object */
$mailer = new Mailer;
 
?>