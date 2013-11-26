<div id="contentEdit">
    <header id="contentHeader">
    <h1>Edit User</h1>
    <a href="{$requestURL}?action=users{if isset($limit)}&limit={$limit}{/if}{if isset($page)}&page={$page}{/if}{if isset($query)}&query={$query}{/if}" >Back to List</a>
    </header>
    <hr/>
    <form name="loginform" id="loginform" action="{$requestURL}?action=users{if isset($limit)}&limit={$limit}{/if}{if isset($page)}&page={$page}{/if}{if isset($query)}&query={$query}{/if}" method="post">
            <table>
            <tr>
            <td><label >Username</label></td>
            <td>{$user.username}</td>
            </tr>
            <tr>
            <td><label for="user_login">User level</label></td>
            <td>{$userLevelComboFilter}</td>
            </tr>
            <tr>
            <td><label >E-mail</label></td>
            <td><input name="email" class="input" size="20" type="text" value="{$user.email}"/></td>
            </tr>
            <tr>
            <td><label >AU unix user name</label></td>
            <td><input name="aulogin"  class="input" size="20" type="text" value="{$user.aulogin}"/></td>
            </tr>
            <tr>
            <td><label >AU unix user pass</label></td>
            <td><input name="aupass"  class="input" size="20" type="text" value="{$user.aupass}"/></td>
            </tr>
            <tr>
            <td><label >Active</label></td>
            <td>{$activeComboFilter}</td>
            </tr>
            <tr><td colspan="2"></td></tr>
            <input type="hidden" name="id" value="{$user.id}"/>
            <input type="hidden" name="user_edit_save" value="1"/>
            <tr><td colspan="2">
            <input value="Save" type="submit"/>
            </td></tr>
            </table>
            </form>
            <br/>
</div><br/>