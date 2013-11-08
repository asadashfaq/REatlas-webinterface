<?php
/**
 * Process.php
 * 
 * The Process class is meant to simplify the task of processing
 * user submitted forms, redirecting the user to the correct
 * pages if errors are found, or if form is successful, either
 * way. Also handles the logout procedure.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 19, 2004
 * Modified by: Arman G. de Castro, October 3, 2008
 * email: armandecastro@gmail.com
 */
require_once 'init.php';

class Process
{
   /* Class constructor */
   function Process(){
      global $session;
      /* User submitted login form */
      if(isset($_POST['sublogin'])){
         $this->procLogin();
      }
      /* User submitted registration form */
      else if(isset($_POST['subjoin'])){
         $this->procRegister();
      }
	  
	    /* User submitted registration form */
      else if(isset($_POST['member_subjoin'])){
         $this->procMemberRegister();
      }
	  
	      /* User submitted registration form */
      else if(isset($_POST['master_subjoin'])){
         $this->procMasterRegister();
      }
	  
	      /* User submitted registration form */
      else if(isset($_POST['agent_subjoin'])){
         $this->procAgentRegister();
      }
	  
      /* User submitted forgot password form */
      else if(isset($_POST['subforgot'])){
         $this->procForgotPass();
      }
      /* User submitted edit account form */
      else if(isset($_POST['subedit'])){
         $this->procEditAccount();
      }
     
      /* Change user setting */ 
      else if(isset($_POST['member_profile'])){
         $this->procEditProfile();
      }
      /* Change user setting */ 
      else if(isset($_POST['member_preference'])){
         $this->procEditPreference();
      }
      /**
       * The only other reason user should be directed here
       * is if he wants to logout, which means user is
       * logged in currently.
       */
      else if($session->logged_in){
         $this->procLogout();
      }
      /**
       * Should not get here, which means user is viewing this page
       * by mistake and therefore is redirected.
       */
       else{
          header("Location: main.php");
       }
   }

   /**
    * procLogin - Processes the user submitted login form, if errors
    * are found, the user is redirected to correct the information,
    * if not, the user is effectively logged in to the system.
    */
   function procLogin(){
      global $session, $form;
      /* Login attempt */
      $retval = $session->login($_POST['username'], $_POST['password'], isset($_POST['remember']));
       $referer = isset($_REQUEST['ref'])?$_REQUEST['ref']:NULL;
      
     if(isset($_REQUEST['redirect_to']))
          $session->referrer = $_POST['redirect_to'];
      
      /* Login successful */
      if($retval){
          if($referer == "admin" || $session->isAdmin())
            header("Location: admin/");
          else if($referer == "front")
            header("Location: main.php");
          else 
             header("Location: ".$session->referrer); 
      }
      /* Login failed */
      else{
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer);
      }
   }
   
   /**
    * procLogout - Simply attempts to log the user out of the system
    * given that there is no logout form to process.
    */
   function procLogout(){
      global $session;
       $referer = isset($_REQUEST['ref'])?$_REQUEST['ref']:NULL;
       
       if(isset($_REQUEST['redirect_to']))
          $session->referrer = $_POST['redirect_to'];
       
      $retval = $session->logout();
       if($referer == "front")
            header("Location: index.php");
          else
             header("Location: ".$session->referrer); 
   }
   
   /**
    * procRegister - Processes the user submitted registration form,
    * if errors are found, the user is redirected to correct the
    * information, if not, the user is effectively registered with
    * the system and an email is (optionally) sent to the newly
    * created user.
    */
   function procRegister(){
      global $session, $form;
      /* Convert username to all lowercase (by option) */
      if(ALL_LOWERCASE){
         $_POST['username'] = strtolower($_POST['username']);
      }
      /* Registration attempt */
      $retval = $session->register($_POST['username'], $_POST['password'], $_POST['email']);
      
      if(isset($_REQUEST['redirect_to']))
          $session->referrer = $_POST['redirect_to'];
      
      /* Registration Successful */
      if($retval == 0){
         $_SESSION['reguname'] = $_POST['username'];
         $_SESSION['regsuccess'] = true;
         header("Location: ".$session->referrer);
      }
      /* Error found with form */
      else if($retval == 1){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer);
      }
      /* Registration attempt failed */
      else if($retval == 2){
         $_SESSION['reguname'] = $_POST['username'];
         $_SESSION['regsuccess'] = false;
         header("Location: ".$session->referrer);
      }
   }
   
    function procMasterRegister(){
      global $session, $form;
      /* Convert username to all lowercase (by option) */
      if(ALL_LOWERCASE){
         $_POST['username'] = strtolower($_POST['username']);
      }
      /* Registration attempt */
      $retval = $session->SessionMasterRegister($_POST['username'], $_POST['password'], $_POST['email']);
      
      if(isset($_REQUEST['redirect_to']))
          $session->referrer = $_POST['redirect_to'];
      
      /* Registration Successful */
      if($retval == 0){
         $_SESSION['reguname'] = $_POST['username'];
         $_SESSION['regsuccess'] = true;
         header("Location: ".$session->referrer.'?'.$session->username);
      }
      /* Error found with form */
      else if($retval == 1){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer.'?'.$session->username);
      }
      /* Registration attempt failed */
      else if($retval == 2){
         $_SESSION['reguname'] = $_POST['username'];
         $_SESSION['regsuccess'] = false;
         header("Location: ".$session->referrer.'?'.$session->username);
      }
   }
   
   
   
    function procMemberRegister(){
      global $session, $form;
      /* Convert username to all lowercase (by option) */
      if(ALL_LOWERCASE){
         $_POST['username'] = strtolower($_POST['username']);
      }
      
     
      $session->referrer = "register.php";
      
      /* Registration attempt */
      $retval = $session->SessionMemberRegister($_POST['username'], $_POST['password'], $_POST['email']);
      
      if(isset($_REQUEST['redirect_to']))
          $session->referrer = $_POST['redirect_to'];
     
      /* Registration Successful */
      if($retval == 0){
         $_SESSION['reguname'] = $_POST['username'];
         $_SESSION['regsuccess'] = true;
         header("Location: ".$session->referrer.'?'.$session->username);
      }
      /* Error found with form */
      else if($retval == 1){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer.'?'.$session->username);
      }
      /* Registration attempt failed */
      else if($retval == 2){
         $_SESSION['reguname'] = $_POST['username'];
         $_SESSION['regsuccess'] = false;
         header("Location: ".$session->referrer.'?'.$session->username);
      }
   }
   
      
    function procAgentRegister(){
      global $session, $form;
      /* Convert username to all lowercase (by option) */
      if(ALL_LOWERCASE){
         $_POST['username'] = strtolower($_POST['username']);
      }
      /* Registration attempt */
      $retval = $session->SessionAgentRegister($_POST['username'], $_POST['password'], $_POST['email']);
      
      if(isset($_REQUEST['redirect_to']))
          $session->referrer = $_POST['redirect_to'];
      
      /* Registration Successful */
      if($retval == 0){
         $_SESSION['reguname'] = $_POST['username'];
         $_SESSION['regsuccess'] = true;
         header("Location: ".$session->referrer.'?'.$session->username);
      }
      /* Error found with form */
      else if($retval == 1){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer.'?'.$session->username);
      }
      /* Registration attempt failed */
      else if($retval == 2){
         $_SESSION['reguname'] = $_POST['username'];
         $_SESSION['regsuccess'] = false;
         header("Location: ".$session->referrer.'?'.$session->username);
      }
   }
   /**
    * procForgotPass - Validates the given username then if
    * everything is fine, a new password is generated and
    * emailed to the address the user gave on sign up.
    */
   function procForgotPass(){
      global $database, $session, $mailer, $form;
      /* Username error checking */
      $subuser = $_POST['username'];
      $field = "user";  //Use field name for username
      if(!$subuser || strlen($subuser = trim($subuser)) == 0){
         $form->setError($field, "* Username not entered<br>");
      }
      else{
         /* Make sure username is in database */
         $subuser = stripslashes($subuser);
         if(strlen($subuser) < 5 || strlen($subuser) > 30 ||
            !eregi("^([0-9a-z])+$", $subuser) ||
            (!$database->usernameTaken($subuser))){
            $form->setError($field, "* Username does not exist<br>");
         }
      }
      
      /* Errors exist, have user correct them */
      if($form->num_errors > 0){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
      }
      /* Generate new password and email it to user */
      else{
         /* Generate new password */
         $newpass = $session->generateRandStr(8);
         
         /* Get email of user */
         $usrinf = $database->getUserInfo($subuser);
         $email  = $usrinf['email'];
         
         /* Attempt to send the email with new password */
         if($mailer->sendNewPass($subuser,$email,$newpass)){
            /* Email sent, update database */
            $database->updateUserField($subuser, "password", md5($newpass));
            $_SESSION['forgotpass'] = true;
         }
         /* Email failure, do not change password */
         else{
            $_SESSION['forgotpass'] = false;
         }
      }
      
     if(isset($_REQUEST['redirect_to']))
          $session->referrer = $_POST['redirect_to'];
     
          header("Location: ".$session->referrer); 
     
   }
   
   /**
    * procEditAccount - Attempts to edit the user's account
    * information, including the password, which must be verified
    * before a change is made.
    */
   function procEditAccount(){
      global $session, $form;
      if(isset($_POST['curpass'])){
           $retval = $session->editAccount($_POST['curpass'], $_POST['newpass'], $_POST['email']);
      }else {
      /* Account edit attempt */
        $retval = $session->editAccountEmail($_POST['email']);
      }
      
      if(isset($_REQUEST['redirect_to']))
          $session->referrer = $_POST['redirect_to'];
      
      /* Account edit successful */
      if($retval){
         $_SESSION['useredit'] = true;
         header("Location: ".$session->referrer);
      }
      /* Error found with form */
      else{
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer);
      }
   }
   
    /**
    * procRegister - Processes the user submitted registration form,
    * if errors are found, the user is redirected to correct the
    * information, if not, the user is effectively registered with
    * the system and an email is (optionally) sent to the newly
    * created user.
    */
   function procEditProfile(){
      global $session, $form;
      
      if($session->profileid)
        $profile = new Profile($session->profileid);
      else
          $profile = new Profile();
      
      $profile->userid = $session->userid;
      
      $profile->populateFromPost($_POST);
      
      $retval = $profile->save();
     
      if(isset($_REQUEST['redirect_to']))
          $session->referrer = $_POST['redirect_to'];
      
      /* Registration Successful */
      if($retval == 0){
         $session->profileid = $profile->profileid;
         $_SESSION['updsuccess'] = true;
         header("Location: ".$session->referrer);
      }
      /* Error found with form */
      else if($retval == 1){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer);
      }
      /* Registration attempt failed */
      else if($retval == 2){
         $_SESSION['updsuccess'] = false;
         header("Location: ".$session->referrer);
      }
   }
   
   function procEditPreference() {
        global $session, $form;
       $retval = 0;
       
       if(isset($_REQUEST['redirect_to']))
          $session->referrer = $_POST['redirect_to'];
      
        /* Registration Successful */
      if($retval == 0){
         $_SESSION['updsuccess'] = true;
         header("Location: ".$session->referrer);
      }
      /* Error found with form */
      else if($retval == 1){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer);
      }
      /* Registration attempt failed */
      else if($retval == 2){
         $_SESSION['updsuccess'] = false;
         header("Location: ".$session->referrer);
      }
   }
};

/* Initialize process */
$process = new Process;

?>
