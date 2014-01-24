<?php
include("init.php");
$action = Tools::getValue('action');

if ($session->logged_in)
    header("Location: main.php");

?>
<!DOCTYPE html>
<html>
<head>

<!--META-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width;">
<title>REAtlas Login</title>

<!--STYLESHEETS-->
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/login.css" rel="stylesheet" type="text/css" />
<!--SCRIPTS-->
<script type="text/javascript" src="js/jquery/jquery-1.10.2.js"></script>

<!--Slider-in icons-->
<script type="text/javascript">
$(document).ready(function() {
    
	$(".username").focus(function() {
		$(".user-icon").css("left","-48px");
	});
	$(".username").blur(function() {
		$(".user-icon").css("left","0px");
	});
	
	$(".password").focus(function() {
		$(".pass-icon").css("left","-48px");
	});
	$(".password").blur(function() {
		$(".pass-icon").css("left","0px");
	});
        
});
</script>

</head>
<body>

<!--WRAPPER-->
<div id="wrapper">

	<!--SLIDE-IN ICONS-->
    <div class="user-icon"></div>
    <div class="pass-icon"></div>
    <!--END SLIDE-IN ICONS-->

<!--LOGIN FORM-->
<form name="login-form" class="login-form" action="process.php" method="post">
	<!--HEADER-->
    <div class="header">
    <!--TITLE--><h1>REAtlas </h1><!--END TITLE-->
<?php 
    if($action == "lostpassword")
    {
    ?>
 <h3>Forgot password</h3>
<?php 
}
?>
    <!--DESCRIPTION--><span>Please provide login information before continue.</span><!--END DESCRIPTION-->
    </div>
    <!--END HEADER-->
	<!-- ERROR SHORT-->
        <div class="form-error alert alert-danger" <?php if($form->num_errors <=0) echo "style=\"display:none;\""; ?>>
            <?php       
            if(is_array($form->errors)){
                foreach ($form->errors as $key => $value) {
                    echo $value."<br/>";
                }
            }
            
            ?>
        </div>
        <!-- ERROR SHORT END -->
	<!--CONTENT-->
    <div class="content">
<?php 
    if($action == "lostpassword")
    {
    ?>
    <p>
	  <!--USERNAME--><input name="username" type="text" class="input username" value="<?php echo ($form->value("username")) ? $form->value("username") : "Username"; ?>" onfocus="this.value = ''" /><!--END USERNAME-->
	</p>
    <p>
      <span>  Password will be sent to your registered email.</span>
    </p>
                <input type="hidden" name="subforgot" value="1"/>
    <?php 
    }else
    {
    ?>
	  <!--USERNAME--><input name="username" type="text" class="input username" value="<?php echo ($form->value("username")) ? $form->value("username") : "Username"; ?>" onfocus="this.value = ''" /><!--END USERNAME-->
                <!--PASSWORD--><input name="password" type="password" class="input password" value="<?php echo ($form->value("password")) ? $form->value("password") : "Password"; ?>" onfocus="this.value = ''" /><!--END PASSWORD-->
                <br/><input type="checkbox" name="remember" <?php if ($form->value("remember") != "") {
    echo "checked";
} ?>>
                &nbsp;&nbsp;<span> Remember me next time </span>
                <input type="hidden" name="sublogin" value="1">
                <input type="hidden" name="ref" value="front">
<?php 
        }
        ?>
<br/>
  <?php 
    if($action != "lostpassword")
    {
    ?>
    
	<a href="<?php echo Configurations::getConfiguration('SITE_DIRECTORY'); ?>?action=lostpassword" title="Password Lost and Found"><span>Lost your password?</span></a>
        <?php 
    }
    ?>
    </div>
    <!--END CONTENT-->
    
    <!--FOOTER-->
    <div class="footer">
  <?php 
    if($action == "lostpassword")
    {
    ?>
         <input name="redirect_to" value="<?php echo Configurations::getConfiguration('SITE_DIRECTORY'); ?>?action=lostpassword" type="hidden"/>
              
    <!--LOGIN BUTTON--><input type="submit" name="submit" value="Send" class="button" /><!--END LOGIN BUTTON-->
    <!--BACK TO LOGIN BUTTON--><input type="button" name="register" value="Back..." class="register" onclick="document.location.href='index.php'" /><!--END BACK TO LOGIN BUTTON-->
 <?php
    }else {
    ?>

    <!--LOGIN BUTTON--><input type="submit" name="submit" value="Login" class="button" /><!--END LOGIN BUTTON-->
    <!--REGISTER BUTTON--><input type="button" name="register" value="Register" class="register" onclick="document.location.href='register.php'" /><!--END REGISTER BUTTON-->
 <?php
    }
 ?>

    </div>
    <!--END FOOTER-->

</form>
<!--END LOGIN FORM-->

</div>
<!--END WRAPPER-->

<!--GRADIENT--><div class="gradient"></div><!--END GRADIENT-->

</body>
</html>
