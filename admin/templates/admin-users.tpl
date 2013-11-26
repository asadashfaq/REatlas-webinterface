{$listPagination}
<div id="content">
    <table cellspacing="0">
    <tr>
        <th>ID</th>
        <th>User Name</th>
        <th>User Level</th>
        <th>E-mail</th>
     {*   <!--th>Parent Dir</th-->'*}
         <th>AU login</th>
        <th>Active</th>             
        <th>Blocked</th>
        <th>&nbsp;</th>
    </tr>
   {$listFilter}
   {$userList}
</table>
</div>