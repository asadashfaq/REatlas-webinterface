<?php
include("init.php");
if (!$session->logged_in)
    include 'login.php';
else {
    $profile = new Profile($session->profileid);
}

$action=isset($_GET['action'])?$_GET['action']:"profile";

?>
<!DOCTYPE html>
<html>
    <head>
        <!--META-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>REAtlas - <?php echo ucfirst($action)." - ".$profile->fullname;?> </title>

        <!--STYLESHEETS-->
        <link href="css/profile.css" rel="stylesheet" type="text/css" />
        <link href="css/passMeter.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="http://js.arcgis.com/3.7/js/dojo/dijit/themes/claro/claro.css">
        <link rel="stylesheet" type="text/css" href="http://js.arcgis.com/3.7/js/esri/css/esri.css">
        <link rel="stylesheet" href="css/layout.css"/> 
        <link rel="stylesheet" href="http://js.arcgis.com/3.7/js/dojo/dojox/grid/resources/claroGrid.css">
        

        <!--SCRIPTS-->
        <script>var dojoConfig = {parseOnLoad: true};</script>
        <script src="http://js.arcgis.com/3.7/"></script>
        <script src="js/jquery/jquery-1.9.1.js"></script>
        <script src="js/jquery/ui/jquery-ui.js"></script>
        <script type="text/javascript" src="js/passMeter.js"></script>
        <script src="js/jquery/form-validator/jquery.form-validator.js"></script>
       
    </head>
    <body class="claro">
        <?php
        /**
         * The user is already logged in, not allowed to register.
         */
        if (!$session->logged_in) {
            echo "<p>We're sorry <b>$session->username</b>, but you've already registered. "
            . "<a href=\"main.php\">Main</a>.</p>";
        } else {
            ?>
            <script type="text/javascript">
            $(document).ready(function() {
              
                $.validate({
                    form: '#registration-form',
                    modules: 'security',
                    validateOnBlur: true, // disable validation when input looses focus
                    errorMessagePosition: 'top', // Instead of 'element' which is default
                    scrollToTopOnError: false, // Set this property to true if you have a long form
                    onError: function() {
                        alert('Validation failed');
                    },
                    onSuccess: function() {
                        return true; // Will stop the submission of the form
                    },
                    onValidate: function() {
                        return true;
                    }
                });

                require([
                    "dojo/ready", 
                    "dijit/form/Button", 
                    "dijit/layout/ContentPane", 
                    "dijit/layout/BorderContainer"],
                        function(ready) {
                            ready(function() {
                               
                            });
                        }
                );

                $( "#<?php echo $action; ?>" ).parent().addClass( "selected" );
            });

            </script>
            <div id="mainWindow" 
                 data-dojo-type="dijit.layout.BorderContainer" 
                 data-dojo-props="design:'headline', gutters:false" 
                 style="width:100%; height:100%;">

                <div id="header" 
                     data-dojo-type="dijit.layout.ContentPane" data-dojo-props="region:'top'">
                    <div id="headerLeft">
                        REAtlas
                        <div id="subheader">Aarhus University,Aarhus Denmark</div>
                    </div>

                    <div id="headerRight"><?php if ($session->logged_in): ?>
                            Welcome, <?php echo $profile->fullname;?><br/>
                            <a href="main.php?ref=profile">Map</a>
                            <a href="process.php?ref=front">Logout</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div id="contentDiv" data-dojo-type="dijit.layout.ContentPane" data-dojo-props="region:'center', splitter:false" style="width: 100%;">

                    <!--WRAPPER-->
                    <div id="wrapper">
                        <?php if($form->num_errors >0){
                            ?>
                            <!-- ERROR SHORT-->
                            <div class="form-error alert alert-danger"  >
                                <?php       
                                if(is_array($form->errors)){
                                    foreach ($form->errors as $key => $value) {
                                        echo $value."<br/>";
                                    }
                                }

                                ?>
                            </div>
                            <!-- ERROR SHORT END -->
                        <?php 
                        
                        }
                        if($form->success) {
                            $form->success = false;
                        ?>
                            <!-- SUCCESS SHORT-->
                            <div class="form-success" >
                               Setting updated.
                            </div>
                            <!-- SUCCESS SHORT END -->
                        <?php 
                    
                        } ?>
                        <!--SLIDE-IN ICONS-->
                        <div class="user-icon"></div>
                        <div class="pass-icon"></div>
                        <!--END SLIDE-IN ICONS-->

                        <!--LOGIN FORM-->
                        <form id="registration-form" name="registration-form" class="register-form" action="process.php" method="post">

                            <!--HEADER-->
                            <div class="header">
                                <!--TITLE--><h1>
                                    <?php 
                                    echo ucfirst($action);
                                    ?>
                                </h1><!--END TITLE-->
                                <!--DESCRIPTION--><span>Please fill up following information.</span><!--END DESCRIPTION-->
                                <br/><span>(<span style="color: red;font-size: x-large;">*</span>) is mandatory.</span>
                            </div>
                            <!--END HEADER-->

                            <!--CONTENT-->
                            <div class="content">
                                <?php 
                                if($action == 'settings')
                                {
                                ?>
                                 <script>
                                    $(document).ready(function(){
                                                    $('input[name="newpass"]').passwordStrength();
                                                    $('input[name="newpass2"]').passwordMatch({sourceDiv:'input[name="newpass"]'});
                                                    //$('input[name="password2"]').passwordStrength({targetDiv: '#passwordStrengthDiv2',classes : Array('is10','is20','is30','is40')});

                                            });

                                    </script>
                                <div id="settingdiv" >
                                    <div>
                                        <!--USERNAME--><label for="username">User Name <span style="color: red;font-size: x-large;">*</span>: </label>
                                        <input name="username" type="text" class="input readonly" placeholder="Username" 
                                               value="<?php echo $session->username;?>" readonly="readonly" 
                                              /><!--END USERNAME-->
                                    </div>
                                    <br/>
                                    <!--OLD PASSWORD-->
                                    <div><label for="curpass">Current Password <span style="color: red;font-size: x-large;">*</span>: </label>
                                        <input name="curpass" type="password" class="input password" placeholder="Password" 
                                               data-validation-optional="true"/>
                                      </div>
                                    <!--END PASSWORD-->
                                    <br/>
                                    <!--PASSWORD-->
                                    <div><label for="newpass">New Password <span style="color: red;font-size: x-large;">*</span>: </label>
                                        <input name="newpass" type="password" class="input password" placeholder="Password" 
                                               data-validation-optional="true"/>
                                        <br/><span class="description" style="float: right;">Provide password if you wanted to change it.</span>
                                        <div id="passwordStrengthDiv" class="is0" ></div></div>
                                    <!--END PASSWORD-->
                                    <br/>
                                    <!--PASSWORD CONFIRM-->
                                    <div><label for="newpass2">Repeat password <span style="color: red;font-size: x-large;">*</span>: </label>
                                        <input name="newpass2" type="password" class="input password" placeholder="Password" data-validation-optional="true"
                                               data-validation-error-msg="You did not enter a password confirmation"/>
                                        <div id="passwordMatchDiv" style="margin-left: 75px;" ></div></div>
                                    <!--END PASSWORD-->
                                    <br/>
                                    <!--EMAIL-->
                                    <div><label for="email">E-Mail <span style="color: red;font-size: x-large;">*</span>: </label>
                                        <input name="email" type="text" class="input username" placeholder="E-Mail" 
                                               value="<?php echo $session->userinfo['email']; ?>"
                                               data-validation="email" 
                                               data-validation-error-msg="E-mail is empty or not valid"/>
                                    </div><!--END EMAIL--><br/>
                                     <input type="hidden" name="subedit" value="1">
                                     <input name="redirect_to" value="<?php echo Configurations::getConfiguration('SITE_DIRECTORY'); ?>profile.php?action=settings" type="hidden"/>
                                </div>
                                <?php 
                                }
                                else if($action == 'profile' || $action == NULL)
                                {
                                      $profile = new Profile($session->profileid);
                                ?>
                                <div id="profilediv">
                                    <!--FULLNAME-->
                                    <div><label for="fullname">Full Name <span style="color: red;font-size: x-large;">*</span>: </label>
                                        <input name="fullname" type="text" class="input username" placeholder="Full Name" data-validation="custom" 
                                           data-validation-regexp="^([a-zA-Z ]+)$" data-validation-error-msg="Full name is not valid"
                                           value="<?php echo $profile->fullname;?>"/>
                                    </div>
                                    <!--END FULLNAME-->
                                    <br/>
                                    <!--ORGANIZATION-->
                                    <div><label for="organization">Organization: </label><input name="organization" type="text" class="input username" placeholder="Organization" value="<?php echo $profile->organization;?>"/>
                                    </div><!--END ORGANIZATION-->
                                    <br/>
                                    <!--ADDRESS-->
                                    <div><label for="address">Address: </label><input name="address" type="text" class="input username" placeholder="Address" value="<?php echo $profile->address;?>"/>
                                    </div><!--END ADDRESS-->
                                    <br/>
                                    <!--ADDRESS2-->
                                    <div><label for="address2">&nbsp; </label><input name="address2" type="text" class="input username" placeholder="Address" value="<?php echo $profile->address2;?>"/>
                                    </div><!--END ADDRESS2--><br/>
                                    <!--REGION-->
                                    <div><label for="region">Region/State: </label><input name="region" type="text" class="input username" placeholder="Region/State"  value="<?php echo $profile->region;?>"/>
                                    </div><!--END REGION--><br/>
                                    <!--ZIP-->
                                    <div><label for="postalcode">Postal Code <span style="color: red;font-size: x-large;">*</span>: </label>
                                        <input name="postalcode" type="text" class="input username"  placeholder="Postal Code" data-validation="number" 
                                               data-validation-error-msg="Postal code is empty or not valid"  value="<?php echo $profile->postalcode;?>"/>
                                    </div><!--END ZIP--><br/>
                                    <!--CITY-->
                                    <div><label for="city">City <span style="color: red;font-size: x-large;">*</span>: </label>
                                        <input name="city" type="text" class="input username"  placeholder="City" value="<?php echo $profile->city;?>" 
                                               data-validation="required" data-validation-error-msg="City is required."/>
                                    </div><!--END CITY--><br/>
                                    <!--COUNTRY-->
                                    <div><label for="country">Country <span style="color: red;font-size: x-large;">*</span>: </label>
                                        <select id="country" name="country" class="input username">
                                            <option value="Afghanistan">Afghanistan</option>
                                            <option value="Åland Islands">Åland Islands</option>
                                            <option value="Albania">Albania</option>
                                            <option value="Algeria">Algeria</option>
                                            <option value="American Samoa">American Samoa</option>
                                            <option value="Andorra">Andorra</option>
                                            <option value="Angola">Angola</option>
                                            <option value="Anguilla">Anguilla</option>
                                            <option value="Antarctica">Antarctica</option>
                                            <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                            <option value="Argentina">Argentina</option>
                                            <option value="Armenia">Armenia</option>
                                            <option value="Aruba">Aruba</option>
                                            <option value="Australia">Australia</option>
                                            <option value="Austria">Austria</option>
                                            <option value="Azerbaijan">Azerbaijan</option>
                                            <option value="Bahamas">Bahamas</option>
                                            <option value="Bahrain">Bahrain</option>
                                            <option value="Bangladesh">Bangladesh</option>
                                            <option value="Barbados">Barbados</option>
                                            <option value="Belarus">Belarus</option>
                                            <option value="Belgium">Belgium</option>
                                            <option value="Belize">Belize</option>
                                            <option value="Benin">Benin</option>
                                            <option value="Bermuda">Bermuda</option>
                                            <option value="Bhutan">Bhutan</option>
                                            <option value="Bolivia">Bolivia</option>
                                            <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                            <option value="Botswana">Botswana</option>
                                            <option value="Bouvet Island">Bouvet Island</option>
                                            <option value="Brazil">Brazil</option>
                                            <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                                            <option value="Brunei Darussalam">Brunei Darussalam</option>
                                            <option value="Bulgaria">Bulgaria</option>
                                            <option value="Burkina Faso">Burkina Faso</option>
                                            <option value="Burundi">Burundi</option>
                                            <option value="Cambodia">Cambodia</option>
                                            <option value="Cameroon">Cameroon</option>
                                            <option value="Canada">Canada</option>
                                            <option value="Cape Verde">Cape Verde</option>
                                            <option value="Cayman Islands">Cayman Islands</option>
                                            <option value="Central African Republic">Central African Republic</option>
                                            <option value="Chad">Chad</option>
                                            <option value="Chile">Chile</option>
                                            <option value="China">China</option>
                                            <option value="Christmas Island">Christmas Island</option>
                                            <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
                                            <option value="Colombia">Colombia</option>
                                            <option value="Comoros">Comoros</option>
                                            <option value="Congo">Congo</option>
                                            <option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option>
                                            <option value="Cook Islands">Cook Islands</option>
                                            <option value="Costa Rica">Costa Rica</option>
                                            <option value="Cote D'ivoire">Cote D'ivoire</option>
                                            <option value="Croatia">Croatia</option>
                                            <option value="Cuba">Cuba</option>
                                            <option value="Cyprus">Cyprus</option>
                                            <option value="Czech Republic">Czech Republic</option>
                                            <option value="Denmark" selected="selected">Denmark</option>
                                            <option value="Djibouti">Djibouti</option>
                                            <option value="Dominica">Dominica</option>
                                            <option value="Dominican Republic">Dominican Republic</option>
                                            <option value="Ecuador">Ecuador</option>
                                            <option value="Egypt">Egypt</option>
                                            <option value="El Salvador">El Salvador</option>
                                            <option value="Equatorial Guinea">Equatorial Guinea</option>
                                            <option value="Eritrea">Eritrea</option>
                                            <option value="Estonia">Estonia</option>
                                            <option value="Ethiopia">Ethiopia</option>
                                            <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
                                            <option value="Faroe Islands">Faroe Islands</option>
                                            <option value="Fiji">Fiji</option>
                                            <option value="Finland">Finland</option>
                                            <option value="France">France</option>
                                            <option value="French Guiana">French Guiana</option>
                                            <option value="French Polynesia">French Polynesia</option>
                                            <option value="French Southern Territories">French Southern Territories</option>
                                            <option value="Gabon">Gabon</option>
                                            <option value="Gambia">Gambia</option>
                                            <option value="Georgia">Georgia</option>
                                            <option value="Germany">Germany</option>
                                            <option value="Ghana">Ghana</option>
                                            <option value="Gibraltar">Gibraltar</option>
                                            <option value="Greece">Greece</option>
                                            <option value="Greenland">Greenland</option>
                                            <option value="Grenada">Grenada</option>
                                            <option value="Guadeloupe">Guadeloupe</option>
                                            <option value="Guam">Guam</option>
                                            <option value="Guatemala">Guatemala</option>
                                            <option value="Guernsey">Guernsey</option>
                                            <option value="Guinea">Guinea</option>
                                            <option value="Guinea-bissau">Guinea-bissau</option>
                                            <option value="Guyana">Guyana</option>
                                            <option value="Haiti">Haiti</option>
                                            <option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option>
                                            <option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
                                            <option value="Honduras">Honduras</option>
                                            <option value="Hong Kong">Hong Kong</option>
                                            <option value="Hungary">Hungary</option>
                                            <option value="Iceland">Iceland</option>
                                            <option value="India">India</option>
                                            <option value="Indonesia">Indonesia</option>
                                            <option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option>
                                            <option value="Iraq">Iraq</option>
                                            <option value="Ireland">Ireland</option>
                                            <option value="Isle of Man">Isle of Man</option>
                                            <option value="Israel">Israel</option>
                                            <option value="Italy">Italy</option>
                                            <option value="Jamaica">Jamaica</option>
                                            <option value="Japan">Japan</option>
                                            <option value="Jersey">Jersey</option>
                                            <option value="Jordan">Jordan</option>
                                            <option value="Kazakhstan">Kazakhstan</option>
                                            <option value="Kenya">Kenya</option>
                                            <option value="Kiribati">Kiribati</option>
                                            <option value="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option>
                                            <option value="Korea, Republic of">Korea, Republic of</option>
                                            <option value="Kuwait">Kuwait</option>
                                            <option value="Kyrgyzstan">Kyrgyzstan</option>
                                            <option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option>
                                            <option value="Latvia">Latvia</option>
                                            <option value="Lebanon">Lebanon</option>
                                            <option value="Lesotho">Lesotho</option>
                                            <option value="Liberia">Liberia</option>
                                            <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
                                            <option value="Liechtenstein">Liechtenstein</option>
                                            <option value="Lithuania">Lithuania</option>
                                            <option value="Luxembourg">Luxembourg</option>
                                            <option value="Macao">Macao</option>
                                            <option value="Macedonia, The Former Yugoslav Republic of">Macedonia, The Former Yugoslav Republic of</option>
                                            <option value="Madagascar">Madagascar</option>
                                            <option value="Malawi">Malawi</option>
                                            <option value="Malaysia">Malaysia</option>
                                            <option value="Maldives">Maldives</option>
                                            <option value="Mali">Mali</option>
                                            <option value="Malta">Malta</option>
                                            <option value="Marshall Islands">Marshall Islands</option>
                                            <option value="Martinique">Martinique</option>
                                            <option value="Mauritania">Mauritania</option>
                                            <option value="Mauritius">Mauritius</option>
                                            <option value="Mayotte">Mayotte</option>
                                            <option value="Mexico">Mexico</option>
                                            <option value="Micronesia, Federated States of">Micronesia, Federated States of</option>
                                            <option value="Moldova, Republic of">Moldova, Republic of</option>
                                            <option value="Monaco">Monaco</option>
                                            <option value="Mongolia">Mongolia</option>
                                            <option value="Montenegro">Montenegro</option>
                                            <option value="Montserrat">Montserrat</option>
                                            <option value="Morocco">Morocco</option>
                                            <option value="Mozambique">Mozambique</option>
                                            <option value="Myanmar">Myanmar</option>
                                            <option value="Namibia">Namibia</option>
                                            <option value="Nauru">Nauru</option>
                                            <option value="Nepal">Nepal</option>
                                            <option value="Netherlands">Netherlands</option>
                                            <option value="Netherlands Antilles">Netherlands Antilles</option>
                                            <option value="New Caledonia">New Caledonia</option>
                                            <option value="New Zealand">New Zealand</option>
                                            <option value="Nicaragua">Nicaragua</option>
                                            <option value="Niger">Niger</option>
                                            <option value="Nigeria">Nigeria</option>
                                            <option value="Niue">Niue</option>
                                            <option value="Norfolk Island">Norfolk Island</option>
                                            <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                                            <option value="Norway">Norway</option>
                                            <option value="Oman">Oman</option>
                                            <option value="Pakistan">Pakistan</option>
                                            <option value="Palau">Palau</option>
                                            <option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
                                            <option value="Panama">Panama</option>
                                            <option value="Papua New Guinea">Papua New Guinea</option>
                                            <option value="Paraguay">Paraguay</option>
                                            <option value="Peru">Peru</option>
                                            <option value="Philippines">Philippines</option>
                                            <option value="Pitcairn">Pitcairn</option>
                                            <option value="Poland">Poland</option>
                                            <option value="Portugal">Portugal</option>
                                            <option value="Puerto Rico">Puerto Rico</option>
                                            <option value="Qatar">Qatar</option>
                                            <option value="Reunion">Reunion</option>
                                            <option value="Romania">Romania</option>
                                            <option value="Russian Federation">Russian Federation</option>
                                            <option value="Rwanda">Rwanda</option>
                                            <option value="Saint Helena">Saint Helena</option>
                                            <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                            <option value="Saint Lucia">Saint Lucia</option>
                                            <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                                            <option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option>
                                            <option value="Samoa">Samoa</option>
                                            <option value="San Marino">San Marino</option>
                                            <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                            <option value="Senegal">Senegal</option>
                                            <option value="Serbia">Serbia</option>
                                            <option value="Seychelles">Seychelles</option>
                                            <option value="Sierra Leone">Sierra Leone</option>
                                            <option value="Singapore">Singapore</option>
                                            <option value="Slovakia">Slovakia</option>
                                            <option value="Slovenia">Slovenia</option>
                                            <option value="Solomon Islands">Solomon Islands</option>
                                            <option value="Somalia">Somalia</option>
                                            <option value="South Africa">South Africa</option>
                                            <option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option>
                                            <option value="Spain">Spain</option>
                                            <option value="Sri Lanka">Sri Lanka</option>
                                            <option value="Sudan">Sudan</option>
                                            <option value="Suriname">Suriname</option>
                                            <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
                                            <option value="Swaziland">Swaziland</option>
                                            <option value="Sweden">Sweden</option>
                                            <option value="Switzerland">Switzerland</option>
                                            <option value="Syrian Arab Republic">Syrian Arab Republic</option>
                                            <option value="Taiwan, Province of China">Taiwan, Province of China</option>
                                            <option value="Tajikistan">Tajikistan</option>
                                            <option value="Tanzania, United Republic of">Tanzania, United Republic of</option>
                                            <option value="Thailand">Thailand</option>
                                            <option value="Timor-leste">Timor-leste</option>
                                            <option value="Togo">Togo</option>
                                            <option value="Tokelau">Tokelau</option>
                                            <option value="Tonga">Tonga</option>
                                            <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                            <option value="Tunisia">Tunisia</option>
                                            <option value="Turkey">Turkey</option>
                                            <option value="Turkmenistan">Turkmenistan</option>
                                            <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                                            <option value="Tuvalu">Tuvalu</option>
                                            <option value="Uganda">Uganda</option>
                                            <option value="Ukraine">Ukraine</option>
                                            <option value="United Arab Emirates">United Arab Emirates</option>
                                            <option value="United Kingdom">United Kingdom</option>
                                            <option value="United States">United States</option>
                                            <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
                                            <option value="Uruguay">Uruguay</option>
                                            <option value="Uzbekistan">Uzbekistan</option>
                                            <option value="Vanuatu">Vanuatu</option>
                                            <option value="Venezuela">Venezuela</option>
                                            <option value="Viet Nam">Viet Nam</option>
                                            <option value="Virgin Islands, British">Virgin Islands, British</option>
                                            <option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
                                            <option value="Wallis and Futuna">Wallis and Futuna</option>
                                            <option value="Western Sahara">Western Sahara</option>
                                            <option value="Yemen">Yemen</option>
                                            <option value="Zambia">Zambia</option>
                                            <option value="Zimbabwe">Zimbabwe</option>
                                        </select>
                                    </div>
                                    <!--END COUNTRY--><br/>
                                    <!--PHONE-->
                                    <div><label for="phone">Phone <span style="color: red;font-size: x-large;">*</span>: </label>
                                        <input name="phone" type="text" class="input username"  
                                               placeholder="Phone" data-validation="number" data-validation-error-msg="Phone number is empty or not valid" value="<?php echo $profile->phone;?>"/>
                                     </div><!--END PHONE--><br/>
                                    <!--WEBSITE-->
                                    <div><label for="website">Website : </label>
                                          <input name="website" type="text" class="input username"  
                                               placeholder="WebSite" data-validation="url" data-validation-optional="true" 
                                               data-validation-error-msg="WebSite is not valid" value="<?php echo $profile->website;?>"/>
                                    </div><!--END WEBSITE--><br/>
                                     <input type="hidden" name="member_profile" value="1">
                                     <input name="redirect_to" value="<?php echo Configurations::getConfiguration('SITE_DIRECTORY'); ?>profile.php?action=profile" type="hidden"/>
                                </div>
                                <?php 
                                }
                                else if($action == 'preferences')
                                {
                                ?>
                                 <input type="hidden" name="member_preference" value="1">
                                 <input name="redirect_to" value="<?php echo Configurations::getConfiguration('SITE_DIRECTORY'); ?>profile.php?action=preferences" type="hidden"/>
                                 <?php 
                                    }                                
                                ?>
                            </div>
                            <!--END CONTENT-->

                            <!--FOOTER-->
                            <div class="footer">
                                <!--LOGIN BUTTON--><input type="submit" name="submit" value="Save" class="button" /><!--END LOGIN BUTTON-->
                                <!--RESET BUTTON--><input type="reset" name="reset" value="Reset" class="register" /><!--END RESET BUTTON-->
                             </div>
                            <!--END FOOTER-->

                        </form>
                        <!--END LOGIN FORM-->

                    </div>
                    <!--END WRAPPER-->
                </div>
                <div data-dojo-type="dijit.layout.ContentPane" id="leftPane" data-dojo-props="region:'left', splitter:false" style="width: 300px;overflow:hidden;">
                    <!--WRAPPER-->
                    <div id="leftmenu">
                        <ul>
                            <li ><a href="profile.php?action=profile" id="profile" >Profile</a></li>
                            <li><a href="profile.php?action=preferences" id="preferences" >Preferences</a></li>
                            <li><a href="profile.php?action=settings" id="settings">Settings</a></li>
                            
                            
                        </ul>
                    </div>
                </div>
            <?php
        }
        ?>
             <div id="footer" data-dojo-type="dijit.layout.ContentPane" data-dojo-props="region:'bottom'">
                © 2013-2014 Aarhus University - au.dk
            </div>
      </div>
           
    </body>
</html>