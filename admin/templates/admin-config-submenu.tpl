<style>
 #navSublist
{
    background-color: lightgray;
    left: 20%;
    position: relative;
    text-align: center;
    width: 50%
}
#navSublist li
{
    display: inline;
    list-style-type: none;
    padding-right: 20px;

}
</style>
<div id="navSublist">
    <ul>
        <li><a href="{$requestURL}?action=configurations&editGen">General</a></li>
        <li><a href="{$requestURL}?action=configurations&editUser">User/Registration</a></li>
        <li><a href="{$requestURL}?action=configurations&editReatlas">REAtlas</a></li>
        <li><a href="{$requestURL}?action=configurations&editDb">DB</a></li>
        <li><a href="{$requestURL}?action=configurations&editNotif">Notification</a></li>
    </ul>
</div>
    <br/>