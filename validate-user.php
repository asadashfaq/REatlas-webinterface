<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include("include/classes/session.php");

$response = array(
  'valid' => false,
  'message' => 'Post argument "username" is missing.'
);

if( isset($_POST['username']) ) {
  $error = $session->checkUserName($_POST['username']);

  if( $error ) {
    // User name is registered on another account
    $response = array('valid' => false, 'message' => 'This user name is already registered.');
  } else {
    // User name is available
    $response = array('valid' => true);
  }
}
echo json_encode($response);