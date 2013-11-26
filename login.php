
<!--STYLESHEETS-->
<link href="css/login.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/overlay.css"/> 
<!--SCRIPTS-->
<script type="text/javascript" src="js/jquery/jquery-1.9.1.js"></script>
<script src="js/jquery/ui/jquery-ui.js"></script>
<script src="js/overlay.js"></script>

<?php
if (isset($_GET['error'])) {
    echo 'Error Logging In!';
}
?>

<!--Slider-in icons-->
<script type="text/javascript">
    $(document).ready(function() {
        $(".username").focus(function() {
            $(".user-icon").css("left", "-48px");
        });
        $(".username").blur(function() {
            $(".user-icon").css("left", "0px");
        });

        $(".password").focus(function() {
            $(".pass-icon").css("left", "-48px");
        });
        $(".password").blur(function() {
            $(".pass-icon").css("left", "0px");
        });
    });
</script>

<?php /*
  <div id="overlay-inAbox" class="overlay">
  <div class="toolbar"><a class="close" href="#"><span>x</span> close</a></div>
  <div class="wrapper">
  Hello! I'm in a box.
  </div>
  </div>

  <!--a href="#" id="overlaylaunch-inAbox">Launch It!</a-->
 */ ?>
<div id="overlay-inAbox" class="overlay">
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
                <!--TITLE--><h1>REAtlas Login</h1><!--END TITLE-->
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
                <!--USERNAME--><input name="username" type="text" class="input username" value="<?php echo ($form->value("username")) ? $form->value("username") : "Username"; ?>" onfocus="this.value = ''" /><!--END USERNAME-->
                <!--PASSWORD--><input name="password" type="password" class="input password" value="<?php echo ($form->value("password")) ? $form->value("password") : "Password"; ?>" onfocus="this.value = ''" /><!--END PASSWORD-->
                <br/><input type="checkbox" name="remember" <?php if ($form->value("remember") != "") {
    echo "checked";
} ?>>
                &nbsp;&nbsp;<span> Remember me next time </span>
                <input type="hidden" name="sublogin" value="1">
                <input name="redirect_to" value="<?php echo Configurations::getConfiguration('SITE_DIRECTORY'); ?>main.php" type="hidden"/>
               
            </div>
            <!--END CONTENT-->

            <!--FOOTER-->
            <div class="footer">
                <!--LOGIN BUTTON--><input type="submit" name="submit" value="Login" class="button"  /><!--END LOGIN BUTTON-->
                <!--REGISTER BUTTON--><input type="button" name="register" value="Register" class="register" onclick="document.location.href = 'register.php'" /><!--END REGISTER BUTTON-->
            </div>
            <!--END FOOTER-->

        </form>
        <!--END LOGIN FORM-->

    </div>
    <!--END WRAPPER-->

    <!--GRADIENT--><div class="gradient"></div><!--END GRADIENT-->
</div> 