<script type="text/javascript">
$(document).ready(function() {
    var table = $('#table<?= $type ?>').DataTable( {
		dom: 'lBfrtip',
        select:  'multi',
		"paging": true,
        info: true,
		"scrollX": true,
		lengthMenu: [ [10, 20, 50, 100, 200, -1], [10, 20, 50, 100, 200, "All"] ],
		buttons: [ 'selectAll','selectNone','copy', 'csv'],
		"language": <?php echo $this->render('datatableLanguage.html',NULL,get_defined_vars(),0); ?>
    });
} );

</script>

<div class="mt-4">
	<table id="table<?= $type ?>" class="display" width="100%" cellspacing="0">
		<thead>
		  <tr>
		    <th><?= $lang['id'] ?></th>
		    <th><?= $lang['username'] ?></th>
			<th><?= $lang['lname'] ?></th>
			<th><?= $lang['fname'] ?></th>
			<?php if ($tRole=='STUDENT'): ?>
					
						<th><?= $lang['class'] ?></th>			
					
					<?php else: ?>
						<th><?= $lang['formOfAddress'] ?></th>
					
			<?php endif; ?>
		  </tr>
		</thead>
		<tbody>
		<?php foreach (($users?:[]) as $user): ?>
			<tr id="<?= $user['userID'] ?>">
				<td><?= $user['userID'] ?></td>
				<td><?= $user['username'] ?></td>
				<td><?= $user['lname'] ?></td>
				<td><?= $user['fname'] ?></td>
				<?php if ($tRole=='STUDENT'): ?>
					
						<th><?= $user['class'] ?></th>			
					
					<?php else: ?>
						<th><?= $user['formOfAddress'] ?></th>
					
				<?php endif; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
