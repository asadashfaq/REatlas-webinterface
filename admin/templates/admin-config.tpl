<div id="contentEdit">
    <header id="contentHeader">
    <h3>Configuration -> {$heading}</h3>
    <br/>
    </header>
    <hr/>
    <form name="loginform" id="loginform" action="{$requestURL}" method="post">
       <table>
     {if isset($editGen)}  
          <tr>
            <td><label >Server URL</label></td>
            <td><input name="serverURL" class="input" size="20" type="text" value="{$serverURL}"/></td>
          </tr>
           <tr>
            <td><label >Site Directory</label></td>
            <td><input name="siteDir" class="input" size="20" type="text" value="{$siteDir}"/></td>
          </tr>
          <tr>
            <td><label >Track User login</label></td>
            <td><input name="trackLogin" class="input" type="checkbox" value="1" {if $trackLogin ==1}checked{/if}/></td>
          </tr>
          <tr>
              <td colspan="2">
                <input type="hidden" name="config_gen_edit_save" value="1"/>
               </td>
          </tr>
     {elseif isset($editUser)}  
          <tr>
            <td><label >UserID size (min , max)</label></td>
            <td>
                <input name="useridsizemin" class="input" size="5" type="text" value="{$useridsizemin}"/>
                <input name="useridsizemax" class="input" size="5" type="text" value="{$useridsizemax}"/>
            </td>
          </tr>
          <tr>
            <td><label >UserPass size (min , max)</label></td>
            <td>
                <input name="userpasssizemin" class="input" size="5" type="text" value="{$userpasssizemin}"/>
                <input name="userpasssizemax" class="input" size="5" type="text" value="{$userpasssizemax}"/>
            </td>
          </tr>
          <tr>
            <td><label >Session timeout</label></td>
            <td><input name="usertimeout" class="input" size="10" type="text" value="{$usertimeout}"/></td>
          </tr>
          <tr>
            <td><label >Login Attempts</label></td>
            <td><input name="userloginatmp" class="input" size="10" type="text" value="{$userloginatmp}"/></td>
          </tr>
           <tr>
            <td><label >Force User Name to lower case</label></td>
            <td><input name="usernamelower" class="input" type="checkbox" value="1" {if $usernamelower}checked{/if}/></td>
          </tr>
          <tr>
              <td colspan="2">
                <input type="hidden" name="config_user_edit_save" value="1"/>
               </td>
          </tr>
       {elseif isset($editReatlas)}  
          <tr>
            <td><label >REAtlas client path</label></td>
            <td><input name="reatlasClientPath" class="input" size="20" type="text" value="{$reatlasClientPath}"/></td>
          </tr>
          <tr>
            <td><label >Pepsi Server</label></td>
            <td><input name="pepsiServer" class="input" size="20" type="text" value="{$pepsiServer}"/></td>
          </tr>
          <tr>
            <td><label >Pepsi Server default User Group</label></td>
            <td><input name="pepsiDefUserGrp" class="input" size="20" type="text" value="{$pepsiDefUserGrp}"/></td>
          </tr>
          <tr>
            <td><label >Pepsi Server default SuperUser</label></td>
            <td><input name="pepsiAdminUser" class="input" size="20" type="text" value="{$pepsiAdminUser}"/></td>
          </tr>
           <tr>
            <td><label >Pepsi SuperUser pass</label></td>
            <td><input name="pepsiAdminPass" class="input" size="20" type="text" value="{$pepsiAdminPass}"/></td>
          </tr>
          <tr>
              <td colspan="2">
                <input type="hidden" name="config_reat_edit_save" value="1"/>
               </td>
          </tr>
           {elseif isset($editNotif)} 
          <tr>
              <td colspan="2">
                  <h4>To main admin</h4>
               </td>
          </tr>
          <tr>
            <td><label >Notify user registration</label></td>
            <td><input name="notifUsrReg" class="input" type="checkbox" value="1" {if $notifUsrReg ==1}checked{/if}/></td>
          </tr>
           <tr>
            <td><label >Notify user blocking</label></td>
            <td><input name="notifUsrBlock" class="input" type="checkbox" value="1" {if $notifUsrBlock}checked{/if}/></td>
          </tr>
           <tr>
            <td><label >Notify user password request</label></td>
            <td><input name="notifUsrPasReq" class="input" type="checkbox" value="1" {if $notifUsrPasReq}checked{/if}/></td>
          </tr>
          <tr>
            <td><label >Notify user login</label></td>
            <td><input name="notifUsrLogin" class="input" type="checkbox" value="1" {if $notifUsrLogin}checked{/if}/></td>
          </tr>
          <tr>
            <td><label >Notify to Registered Admin User</label></td>
            <td>{$notifUser}</td>
          </tr>
           <tr>
            <td><label >Notify to Email</label></td>
            <td><input name="notifEmail" class="input" size="20" type="text" value="{$notifEmail}"/></td>
          </tr>
          <tr>
              <td colspan="2">
                  <h4>To Users</h4>
               </td>
          </tr>
           <tr>
            <td><label >Send Welcome mail to user</label></td>
            <td><input name="notifToUsrWelcome" class="input" type="checkbox" value="1" {if $notifToUsrWelcome}checked{/if}/></td>
          </tr>
           <tr>
            <td><label >Notify user when activated</label></td>
            <td><input name="notifToUsrActive" class="input" type="checkbox" value="1" {if $notifToUsrActive}checked{/if}/></td>
          </tr>
           <tr>
            <td><label >Notify user when blocked/unblocked</label></td>
            <td><input name="notifToUsrBlock" class="input" type="checkbox" value="1" {if $notifToUsrBlock}checked{/if}/></td>
          </tr>
          <tr>
              <td colspan="2">
                  <h4>Defaults: Mail from</h4>
               </td>
          </tr>
          <tr>
            <td><label >Name </label></td>
            <td><input name="emailFromName" class="input" size="20" type="text" value="{$emailFromName}"/></td>
          </tr>
          <tr>
            <td><label >Email</label></td>
            <td><input name="emailFromAddr" class="input" size="20" type="text" value="{$emailFromAddr}"/></td>
          </tr>
          <tr>
            <td><label >Email format</label></td>
            <td>
                <input name="mailFormat" class="input" type="radio" value="0" {if $mailFormat==0}checked{/if}>Plain Text</input>
                <input name="mailFormat" class="input" type="radio" value="1" {if $mailFormat==1}checked{/if}>HTML</input>
            </td>
          </tr>
          <tr>
              <td colspan="2">
                <input type="hidden" name="config_notif_edit_save" value="1"/>
               </td>
          </tr>
       {/if}
            <tr>
                <td colspan="2">
                    <input value="Save" type="submit"/>
                </td>
            </tr>
            
            </table>
            </form>
            <br/>
</div><br/>