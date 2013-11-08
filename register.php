<?php
include("init.php");
?>
<!DOCTYPE html>
<html>
    <head>

        <!--META-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Register to REAtlas</title>

        <!--STYLESHEETS-->
        <link href="css/register.css" rel="stylesheet" type="text/css" />
        <link href="css/passMeter.css" rel="stylesheet" type="text/css" />

        <!--SCRIPTS-->
        <script type="text/javascript" src="js/jquery/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="js/passMeter.js"></script>
        <script src="js/jquery/form-validator/jquery.form-validator.js"></script>
        <!--Slider-in icons-->

    </head>
    <body>

        <!--WRAPPER-->
        <div id="wrapper">
         <!--LOGIN FORM-->
            <form id="registration-form" name="registration-form" class="register-form" action="process.php" method="post">



                <?php
                /**
                 * The user is already logged in, not allowed to register.
                 */
                if ($session->logged_in) {
                    ?>
                    <!--HEADER-->
                    <div class="header">
                        <!--TITLE--><h1>REAtlas - Registered</h1><!--END TITLE-->
                        <?php
                        echo "<p>We're sorry <b>$session->username</b>, but you've already registered. "
                        . "<a href=\"main.php\">Main</a>.</p>";
                        ?>
                    </div>
                    <!--END HEADER-->
                    <div class="footer">

                    <?php
                }
                /**
                 * The user has submitted the registration form and the
                 * results have been processed.
                 */ else if (isset($_SESSION['regsuccess'])) {
                    // Registration was successful 
                    if ($_SESSION['regsuccess']) {
                        ?>
                        <!--HEADER-->
                        <div class="header">
                            <!--TITLE--><h1>REAtlas - Register</h1><!--END TITLE-->
                            <?php
                                echo "<p>Thank you <b>" . $_SESSION['reguname'] . "</b>, your information has been registerd."
                                        ." Very soon you will get a confirmation  from administrator to activate the account"
                                        ."</p>";
                                ?>
                        </div>
                        <!--END HEADER-->
                        <div class="footer">

        <?php
    }
    // Registration failed 
    else {
        ?>
                        <!--HEADER-->
                        <div class="header">
                            <!--TITLE--><h1>Register to REAtlas</h1><!--END TITLE-->
                            <!--DESCRIPTION--><h2>Registration Failed</h2><!--END DESCRIPTION-->
                            <br/><p>We're sorry, but an error has occurred and your registration for the username <b> <?php echo $_SESSION['reguname']; ?> </b>, 
                            could not be completed.<br>Please try again at a later time.</p>
                        </div>
                        <!--END HEADER-->
                        <!--FOOTER-->
                        <div class="footer">
                       
        <?php
       
    }
    unset($_SESSION['regsuccess']);
    unset($_SESSION['reguname']);
    
    
}

/**
 * The user has not filled out the registration form yet.
 * Below is the page with the sign-up form, the names
 * of the input fields are important and should not
 * be changed.
 */ else {
    ?>
        <!--HEADER-->
      <div class="header">
          <!--TITLE--><h1>Register to REAtlas</h1><!--END TITLE-->
          <!--DESCRIPTION--><span>Please fill up following information.</span><!--END DESCRIPTION-->
          <br/><span>(<span style="color: red;font-size: x-large;">*</span>) is mandatory.</span>
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

          <script type="text/javascript">
            $(document).ready(function() {
                $.validate({
                    form: '#registration-form',
                    modules: 'security',
                    validateOnBlur: true, // disable validation when input looses focus
                    errorMessagePosition: 'top', // Instead of 'element' which is default
                    scrollToTopOnError: false, // Set this property to true if you have a long form
                    onError: function() {
                      //  alert("Validation error");

                    },
                    onSuccess: function() {
                        //   alert($('#registration-form').attr('action'));
                        
                        $('#registration-form').submit();
                        return false; // Will stop the submission of the form
                    },
                    onValidate: function() {
                        if($('input[name="password2"]').val() != $('input[name="password"]').val()) {
                        return {
                            element : $('input[name="password2"]'),
                            message : 'Confirmation password does not match.'
                          }
                      }
                        return true;
                    }
                });

                $('input[name="password"]').passwordStrength();
                $('input[name="password2"]').passwordMatch({sourceDiv: 'input[name="password"]'});
            });

        </script>

         
            
                    <!--CONTENT-->
                    <div class="content">
                        <div>
                            <!--USERNAME-->
                            <label for="username">User Name <span style="color: red;font-size: x-large;">*</span>: </label>
                            <input name="username" type="text" class="input username" placeholder="Username" 
                                   data-validation="alphanumeric required server" data-validation-allowing="-_"
                                   data-validation-url="validate-user.php"
                                   data-validation-error-msg="Username is empty or not valid"/>
                            <!--END USERNAME-->
                        </div>
                        <br/>
                        <!--PASSWORD-->
                        <div><label for="password">Password <span style="color: red;font-size: x-large;">*</span>: </label>
                            <input name="password" type="password" class="input password" placeholder="Password" data-validation="required" 
                                   data-validation-error-msg="You did not enter a password"/>
                            <div id="passwordStrengthDiv" class="is0" style="float: left;"></div></div>
                        <!--END PASSWORD-->
                        <br/>
                        <!--PASSWORD CONFIRM-->
                        <div><label for="password2">Repeat password <span style="color: red;font-size: x-large;">*</span>: </label>
                            <input name="password2" type="password" class="input password" placeholder="Password" data-validation="required"
                                   data-validation-error-msg="You did not enter a password confirmation"/>
                            <div id="passwordMatchDiv" style="float: left;margin-top: 75px;" ></div></div>
                        <!--END PASSWORD-->
                        <br/>
                        <!--EMAIL-->
                        <div><label for="email">E-Mail <span style="color: red;font-size: x-large;">*</span>: </label>
                            <input name="email" type="text" class="input username" placeholder="E-Mail" data-validation="email" 
                                   data-validation-error-msg="E-mail is empty or not valid"/>
                        </div><!--END EMAIL--><br/>
                    </div>
                    <!--END CONTENT-->
                    <!--FOOTER-->
                    <div class="footer">
                        <input type="hidden" name="member_subjoin" value="1">
                        <!--LOGIN BUTTON--><input type="submit" name="submit" value="Register" class="button" /><!--END LOGIN BUTTON-->
                         <!--RESET BUTTON--><input type="reset" name="reset" value="Reset" class="register" /><!--END RESET BUTTON-->
    <?php
}
?>
                        <!--RESET BUTTON--><input type="button" name="login" value="Back to Login" class="button" style="float: left;" onclick="document.location.href = 'index.php'"/><!--END RESET BUTTON-->
                    </div>
                    <!--END FOOTER-->
            </form>
            <!--END LOGIN FORM-->

        </div>
        <!--END WRAPPER-->

        <!--GRADIENT--><div class="gradient"></div><!--END GRADIENT-->

    </body>
</html>