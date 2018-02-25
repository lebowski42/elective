<script type="text/javascript">
$(document).ready(function() {
    var table = $('#usersTable').DataTable( {
		dom: 'lBfrtip',
        select:  'multi',
		"paging": true,
        info: true,
		"scrollX": true,
		lengthMenu: [ [10, 20, 50, 100, 200, -1], [10, 20, 50, 100, 200, "All"] ],
		buttons: [ 'selectAll','selectNone','copy', 'csv',
            {
                text: '<?= $lang['admin']['users']['editUser'] ?>',
                action: function () {
					var selectedRow=table.row( { selected: true } );
					if(selectedRow.length == 0){
						alert("<?= $lang['admin']['users']['pleaseSelectUser'] ?>");
					}else{//ajax
						modifyUser('edit',selectedRow.id());
					}
                }
            },
			{
                text: '<?= $lang['admin']['users']['addUser'] ?>',
                action: function () {
					//ajax
					modifyUser('add',-1);
                }
            },
			{
                text: '<?= $lang['admin']['users']['delUser'] ?>',
                action: function () {
					var selectedRows=table.rows( { selected: true } );
					if(selectedRows.ids().toArray() == 0){
						alert("<?= $lang['admin']['users']['pleaseSelectUser'] ?>");
					}else{//ajax
						if(window.confirm("<?= $lang['reallyDelete'] ?>")){
							modifyUser('del',selectedRows.ids().toArray());
							selectedRows.remove().draw();
						}
					}
                }
            }
        ],
		"language": <?php echo $this->render('datatableLanguage.html',NULL,get_defined_vars(),0); ?>
    });
} );

</script>

<div class="mt-4" >
	<table id="usersTable" class="display" width="100%" cellspacing="0">
		<thead>
		  <tr>
		    <th><?= $lang['id'] ?></th>
		    <th><?= $lang['username'] ?></th>
			<th><?= $lang['lname'] ?></th>
			<th><?= $lang['fname'] ?></th>
			<th><?= $lang['formOfAddress'] ?></th>
			<th><?= $lang['class'] ?></th>
		    <th><?= $lang['email'] ?></th>
			<th><?= $lang['admin']['users']['role'] ?></th>
			<!--<th><?= $lang['admin']['users']['ignored'] ?></th>
			<th><?= $lang['lastLogin'] ?></th>
			<th><?= $lang['createdAt'] ?></th>-->
		  </tr>
		</thead>
		<tbody>
		<?php foreach (($users?:[]) as $user): ?>
			<tr id="<?= $user['userID'] ?>">
				<td><?= $user['userID'] ?></td>
				<td><?= $user['username'] ?></td>
				<td><?= $user['lname'] ?></td>
				<td><?= $user['fname'] ?></td>
				<td><?= $user['formOfAddress'] ?></td>
				<td><?= $user['class'] ?></td>
				<td><?= $user['email'] ?></td>
				<td><?= $user['role'] ?></td>
				<!--<td><?php if ($user['ignored'] == 1): ?>&#10004;<?php endif; ?></td>
				<td><?= $user['last_login'] ?></td>
				<td><?= $user['created_at'] ?></td>-->
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
