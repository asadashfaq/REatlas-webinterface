<?php
/**
 * Database.php
 * 
 * The Database class is meant to simplify the task of accessing
 * information from the website's database.
 *
 */
include_once 'Configurations.class.php';
class MySQLDB
{
   var $connection;         //The MySQL database connection
   var $num_active_users;   //Number of active users viewing site
   var $num_active_guests;  //Number of active guests viewing site
   var $num_members;        //Number of signed-up users
   /* Note: call getNumMembers() to access $num_members! */

   /* Class constructor */
   function MySQLDB(){
      /* Make connection to database */
      $this->connection = DB::getInstance();
      
      /**
       * Only query database to find out number of members
       * when getNumMembers() is called for the first time,
       * until then, default value set.
       */
      $this->num_members = -1;
      
      if(Configurations::getConfiguration('TRACK_VISITORS')){
         /* Calculate number of users at site */
         $this->calcNumActiveUsers();
      
         /* Calculate number of guests at site */
         $this->calcNumActiveGuests();
      }
   }

    /**
    * confirmUserPass - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given password is the same password in the database
    * for that user. If the user doesn't exist or if the
    * passwords don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserActive($username){
      /* Add slashes if necessary (for query) */
      if(!_MAGIC_QUOTES_GPC_) {
	      $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT active FROM ".TBL_USERS." WHERE username = '$username' and active=1";
      $result = $this->connection->executeS($q);
      if(!$result || ($this->connection->numRows()< 1)){
         return 1; //Indicates username failure
      }
      else{
         return 0; //Indicates active user
      }
   }
   
   /**
    * confirmUserPass - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given password is the same password in the database
    * for that user. If the user doesn't exist or if the
    * passwords don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserPass($username, $password){
      /* Add slashes if necessary (for query) */
      if(!_MAGIC_QUOTES_GPC_) {
	      $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT password FROM ".TBL_USERS." WHERE username = '$username'";
      $result = $this->connection->executeS($q);
      if(!$result || ($this->connection->numRows()< 1)){
         return 1; //Indicates username failure
      }
     
      /* Retrieve password from result, strip slashes */
      $dbarray = $result[0];
      $dbarray['password'] = stripslashes($dbarray['password']);
      $password = stripslashes($password);

      /* Validate that password is correct */
      if($password == $dbarray['password']){
        
         return 0; //Success! Username and password confirmed
      }
      else{
         return 2; //Indicates password failure
      }
   }
   
   /**
    * confirmUserKey - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given userkey is the same userkey in the database
    * for that user. If the user doesn't exist or if the
    * userkeys don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserKey($username, $userkey){
      /* Add slashes if necessary (for query) */
      if(!_MAGIC_QUOTES_GPC_) {
	      $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT userkey FROM ".TBL_USERS." WHERE username = '$username'";
      $result =$this->connection->executeS($q);
      if(!$result || ($this->connection->numRows() < 1)){
         return 1; //Indicates username failure
      }

      /* Retrieve userkey from result, strip slashes */
      $dbarray = $result[0];
      $dbarray['userkey'] = stripslashes($dbarray['userkey']);
      $userkey = stripslashes($userkey);

      /* Validate that userkey is correct */
      if($userkey == $dbarray['userkey']){
         return 0; //Success! Username and userkey confirmed
      }
      else{
         return 2; //Indicates userkey invalid
      }
   }
   
   /**
    * usernameTaken - Returns true if the username has
    * been taken by another user, false otherwise.
    */
   function usernameTaken($username){
      if(!_MAGIC_QUOTES_GPC_){
         $username = addslashes($username);
      }
      $q = "SELECT username FROM ".TBL_USERS." WHERE username = '$username'";
      $result = $this->connection->executeS($q);
      return ($this->connection->numRows() > 0);
   }
   
   /**
    * usernameBanned - Returns true if the username has
    * been banned by the administrator.
    */
   function usernameBanned($username){
      if(!_MAGIC_QUOTES_GPC_){
         $username = addslashes($username);
      }
      $q = "SELECT username FROM ".TBL_BANNED_USERS." WHERE username = '$username'";
      $result = $this->connection->executeS($q);
      return ($this->connection->numRows() > 0);
   }
   
   /**
    * addNewUser - Inserts the given (username, password, email)
    * info into the database. Appropriate user level is set.
    * Returns true on success, false otherwise.
    */
   function addNewUser($username, $password, $email){
      $time = time();
      /* If admin sign up, give admin user level */
      if(strcasecmp($username, ADMIN_NAME) == 0){
         $ulevel = ADMIN_LEVEL;
      }else{
         $ulevel = MASTER_LEVEL;
      }
      $q = "INSERT INTO ".TBL_USERS." (`username`, `password`, `userkey`, `userlevel`, `email`, `timestamp`, `parent_directory`) ";
       $q .=" VALUES ('$username', '$password', '0', $ulevel, '$email', $time)";
      return $this->connection->executeS($q);
   }
   
   // add new Master
   function addNewMaster($username, $password, $email, $parent_directory){
  
      $time = time();
      $ulevel = MASTER_LEVEL;   //8
      $q = "INSERT INTO ".TBL_USERS." (`username`, `password`, `userkey`, `userlevel`, `email`, `timestamp`, `parent_directory`) ";
       $q .=" VALUES ('$username', '$password', '0', $ulevel, '$email', $time, '$parent_directory')";
      return $this->connection->executeS($q); 
   }
   
   
   // add new Agent
   function addNewAgent($username, $password, $email, $parent_directory){
 
      $time = time();
      $ulevel = AGENT_LEVEL;   //2
      $q = "INSERT INTO ".TBL_USERS."(`username`, `password`, `userkey`, `userlevel`, `email`, `timestamp`, `parent_directory`) ";
       $q .="  VALUES ('$username', '$password', '0', $ulevel, '$email', $time, '$parent_directory')";
      return $this->connection->executeS($q); 
   }
   
   //add new Member
   function addNewMember($username, $password, $email, $parent_directory){
   
      $time = time();
      $ulevel = AGENT_MEMBER_LEVEL;
       $q = "INSERT INTO ".TBL_USERS." (`username`, `password`, `userkey`, `userlevel`, `email`, `timestamp`, `parent_directory`) ";
       $q .=" VALUES ('$username', '$password', '0', $ulevel, '$email', $time, '$parent_directory')";
   
      return $this->connection->executeS($q); 
   }
   
   /**
    * updateUserField - Updates a field, specified by the field
    * parameter, in the user's row of the database.
    */
   function updateUserField($username, $field, $value){
      $q = "UPDATE ".TBL_USERS." SET ".$field." = '$value' WHERE username = '$username'";
      return $this->connection->executeS($q);
   }
   
   /**
    * getUserInfo - Returns the result array from a mysql
    * query asking for all information stored regarding
    * the given username. If query fails, NULL is returned.
    */
   function getUserInfo($username){
      $q  =" SELECT * FROM ".TBL_USERS." usr ";
      $q .=" LEFT JOIN ".TBL_USER_PROFILE." up on (up.userid = usr.id) ";
      $q .=" WHERE username = '$username' "; 
      
      $result = $this->connection->executeS($q);
      
      /* Error occurred, return given name by default */
      if(!$result || ($this->connection->numRows() < 1)){
         return NULL;
      }
      /* Return result array */
      $dbarray = $result[0];
      return $dbarray;
   }
   
      function getUserOnly($username){
      $q = "SELECT username FROM ".TBL_USERS." WHERE username = '$username'";
      $result = $this->connection->executeS($q);
      /* Error occurred, return given name by default */
      if(!$result || ($this->connection->numRows() < 1)){
         return NULL;
      }
      /* Return result array */
      $dbarray = $result[0];
      return $dbarray;
   }
   
   /**
    * getNumMembers - Returns the number of signed-up users
    * of the website, banned members not included. The first
    * time the function is called on page load, the database
    * is queried, on subsequent calls, the stored result
    * is returned. This is to improve efficiency, effectively
    * not querying the database when no call is made.
    */
   function getNumMembers(){
      if($this->num_members < 0){
         $q = "SELECT * FROM ".TBL_USERS;
         $result = $this->connection->executeS($q);
         $this->num_members = $this->connection->numRows();
      }
      return $this->num_members;
   }
   
   /**
    * calcNumActiveUsers - Finds out how many active users
    * are viewing site and sets class variable accordingly.
    */
   function calcNumActiveUsers(){
      /* Calculate number of users at site */
      $q = "SELECT * FROM ".TBL_ACTIVE_USERS;
      $result = $this->connection->executeS($q);
      $this->num_active_users = $this->connection->numRows();
   }
   
   /**
    * calcNumActiveGuests - Finds out how many active guests
    * are viewing site and sets class variable accordingly.
    */
   function calcNumActiveGuests(){
      /* Calculate number of guests at site */
      $q = "SELECT * FROM ".TBL_ACTIVE_GUESTS;
      $result = $this->connection->executeS($q);
      $this->num_active_guests = $this->connection->numRows();
   }
   
   /**
    * addActiveUser - Updates username's last active timestamp
    * in the database, and also adds him to the table of
    * active users, or updates timestamp if already there.
    */
   function addActiveUser($username, $time){
      $q = "UPDATE ".TBL_USERS." SET timestamp = '$time' WHERE username = '$username'";
      $this->connection->execute($q);
      
      if(!Configurations::getConfiguration('TRACK_VISITORS')) return;
      $q = "REPLACE INTO ".TBL_ACTIVE_USERS." VALUES ('$username', '$time')";
      $this->connection->executeS($q);
      $this->calcNumActiveUsers();
   }
   
   /* addActiveGuest - Adds guest to active guests table */
   function addActiveGuest($ip, $time){
      if(!Configurations::getConfiguration('TRACK_VISITORS')) return;
      $q = "REPLACE INTO ".TBL_ACTIVE_GUESTS." VALUES ('$ip', '$time')";
      $this->connection->execute($q);
      $this->calcNumActiveGuests();
   }
   
   /* These functions are self explanatory, no need for comments */
   
   /* removeActiveUser */
   function removeActiveUser($username){
      if(!Configurations::getConfiguration('TRACK_VISITORS')) return;
      $q = "DELETE FROM ".TBL_ACTIVE_USERS." WHERE username = '$username'";
      $this->connection->execute($q);
      $this->calcNumActiveUsers();
   }
   
   /* removeActiveGuest */
   function removeActiveGuest($ip){
      if(!Configurations::getConfiguration('TRACK_VISITORS')) return;
      $q = "DELETE FROM ".TBL_ACTIVE_GUESTS." WHERE ip = '$ip'";
      $this->connection->execute($q);
      $this->calcNumActiveGuests();
   }
   
   /* removeInactiveUsers */
   function removeInactiveUsers(){
      if(!Configurations::getConfiguration('TRACK_VISITORS')) return;
      $timeout = time()-Configurations::getConfiguration('USER_TIMEOUT')*60;
      $q = "DELETE FROM ".TBL_ACTIVE_USERS." WHERE timestamp < $timeout";
      $this->connection->execute($q);
      $this->calcNumActiveUsers();
   }

   /* removeInactiveGuests */
   function removeInactiveGuests(){
      if(!Configurations::getConfiguration('TRACK_VISITORS')) return;
      $timeout = time()-Configurations::getConfiguration('GUEST_TIMEOUT')*60;
      $q = "DELETE FROM ".TBL_ACTIVE_GUESTS." WHERE timestamp < $timeout";
      $this->connection->execute($q);
      $this->calcNumActiveGuests();
   }
   
  /* Add login attempts */
   function addLoginAttempts($username, $time,$failure=false){
       
       $q = "";
       
       if($failure){
        $q = "INSERT INTO ".TBL_LOGIN_ATTEMPTS." (username, count, timestamp) "
               . " VALUES ('$username', 1, '$time') ON DUPLICATE KEY UPDATE count = count + 1,timestamp='$time'";
       }else {
        $q = "INSERT INTO ".TBL_LOGIN_ATTEMPTS." (username, count, timestamp) "
               . " VALUES ('$username', 0, '$time') ON DUPLICATE KEY UPDATE count = 0,timestamp='$time'";
       }
    
      $this->connection->execute($q);
      
      /* Calculate number of users at site */
      $q = "SELECT count FROM ".TBL_LOGIN_ATTEMPTS." WHERE username = '$username'";
      $result = $this->connection->executeS($q);
      $failure_attempts = $result[0]['count'];
      
      if($failure_attempts >= Configurations::getConfiguration('LOGIN_ATTEMPTS')){
        $q = "INSERT INTO ".TBL_BANNED_USERS." VALUES ('$username', '$time')";
        $this->connection->execute($q);
      }
   }
   
};

/* Create database connection */
$database = new MySQLDB;

?>
