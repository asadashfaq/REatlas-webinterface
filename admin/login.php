<?php
include("AdminInit.php");

if ($session->logged_in)
   header("Location: index.php");

$action = Tools::getValue('action');

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title> REAtlas › Log In</title>
	<link rel="stylesheet" id="reatlas-admin-css" href="css/reatlas-admin.css" type="text/css" media="all"/>
        <link rel="stylesheet" id="buttons-css" href="css/buttons.css" type="text/css" media="all"/>
        <link rel="stylesheet" id="colors-fresh-css" href="css/colors-fresh.css" type="text/css" media="all"/>
	<script>if("sessionStorage" in window){try{for(var key in sessionStorage){if(key.indexOf("wp-autosave-")!=-1){sessionStorage.removeItem(key)}}}catch(e){}};</script>
	<meta name="robots" content="noindex,nofollow"/>
    </head>
	<body class="login login-action-login wp-core-ui">
	<div id="login">
            <h1><a>REAtlas - Admin</a></h1>
             <?php if($form->num_errors >0){
                            ?>
                            <!-- ERROR SHORT-->
                            <div class="message"  >
                                <?php       
                                if(is_array($form->errors)){
                                    foreach ($form->errors as $key => $value) {
                                        echo $value."<br/>";
                                    }
                                }
                                if($form->success)
                                    echo 'Success';
                                ?>
                            </div>
                            <!-- ERROR SHORT END -->
                        <?php 
                        
                        }
                        ?>
       
<form name="loginform" id="loginform" action="<?php echo Configurations::getConfiguration('SITE_DIRECTORY'); ?>process.php" method="post">
    <?php 
    if($action == "lostpassword")
    {
    ?>
    <h3>Forgot password</h3>
    
    <p>
        	<label for="user_login">Username<br/>
		<input name="username" id="user_login" class="input" size="20" type="text"/></label>
	</p>
    <p>
        Password will be sent to your registered email.
    </p>
	<p class="submit">
		<input name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Send" type="submit"/>
		<input name="redirect_to" value="<?php echo Configurations::getConfiguration('SITE_DIRECTORY'); ?>admin/login.php?action=lostpassword" type="hidden"/>
                <input type="hidden" name="subforgot" value="1"/>
	</p>
    <?php 
    }else
    {
    ?>
	<p>
		<label for="user_login">Username<br/>
		<input name="username" id="user_login" class="input" size="20" type="text"/></label>
	</p>
	<p>
		<label for="user_pass">Password<br/>
		<input name="password" id="user_pass" class="input" value="" size="20" type="password"/></label>
	</p>
		<p class="forgetmenot"><label for="rememberme"><input name="remember" id="rememberme" value="forever" type="checkbox"/> Remember Me</label></p>
	<p class="submit">
		<input name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Log In" type="submit"/>
		<input name="redirect_to" value="<?php echo Configurations::getConfiguration('SITE_DIRECTORY'); ?>admin/" type="hidden"/>
                <input type="hidden" name="sublogin" value="1"/>
		<input name="testcookie" value="1" type="hidden"/>
	</p>
       <?php 
        }
        ?>
</form>

<p id="nav">
    <?php 
    if($action != "lostpassword")
    {
    ?>
    
	<a href="<?php echo Configurations::getConfiguration('SITE_DIRECTORY'); ?>admin/login.php?action=lostpassword" title="Password Lost and Found">Lost your password?</a>
        <?php 
    }else {
    ?>
        <a href="<?php echo Configurations::getConfiguration('SITE_DIRECTORY'); ?>admin/login.php" title="Login">Back to Login</a>
        <?php 
    }
 ?>
</p>

<script type="text/javascript">
function attempt_focus(){
    setTimeout( function(){ try{
    d = document.getElementById('user_login');
    d.focus();
    d.select();
    } catch(e){}
}, 200);
}
attempt_focus();
</script>

	</div>

	
	<link rel="stylesheet" id="pe2-display.css-css" href="css/pe2-display.css" type="text/css" media="all"/>
	<div class="clear"></div>
    </body>
</html>