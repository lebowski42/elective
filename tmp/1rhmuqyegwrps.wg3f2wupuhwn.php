<link href="elective/MVC/View/datatables/datatables.css" rel="stylesheet">
<script src="elective/MVC/View/datatables/datatables.min.js" type="text/javascript"></script>
<script src="elective/MVC/View/js/ajax.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    var table = $('#adminCourseTable').DataTable( {
        dom: 'lBfrtip',
        select:  'multi',
		"paging": true,
        info: true,
		"scrollX": true,
		lengthMenu: [ [10, 20, 50, 100, 200, -1], [10, 20, 50, 100, 200, "All"] ],
		buttons: [ 'selectAll','selectNone','copy', 'csv',
            {
                text: '<?= $lang['admin']['courses']['delete'] ?>',
                action: function () {
					var selectedRows=table.rows( { selected: true } );
					if(selectedRows.ids().toArray() == 0){
						alert("<?= $lang['admin']['courses']['pleaseSelectCourse'] ?>");
					}else{//ajax
						if(window.confirm("<?= $lang['reallyDelete'] ?>")){
							modifyCourse('del',selectedRows.ids().toArray());
						}
						//selectedRows.remove().draw();
					}
                }
            },
			{
                text: '<?= $lang['admin']['courses']['ignore'] ?>',
                action: function () {
					var selectedRows=table.rows( { selected: true } );
					if(selectedRows.ids().toArray() == 0){
						alert("<?= $lang['admin']['courses']['pleaseSelectCourse'] ?>");
					}else{//ajax
						modifyCourse('ignore',selectedRows.ids().toArray());
						//selectedRows.remove().draw();
					}
                }
            },
			{
                text: '<?= $lang['admin']['courses']['activate'] ?>',
                action: function () {
					var selectedRows=table.rows( { selected: true } );
					if(selectedRows.ids().toArray() == 0){
						alert("<?= $lang['admin']['courses']['pleaseSelectCourse'] ?>");
					}else{//ajax
						modifyCourse('activate',selectedRows.ids().toArray());
						//selectedRows.remove().draw();
					}
                }
            },
			{
                text: '<?= $lang['admin']['courses']['changeNumberOfStudents'] ?>',
                action: function () {
					var selectedRows=table.rows( { selected: true } );
					if(selectedRows.ids().toArray() == 0){
						alert("<?= $lang['admin']['courses']['pleaseSelectCourse'] ?>");
					}else{//ajax
						var number = prompt("<?= lang.maxStudents ?>", "1");
						var numberInt = parseInt(number);
						modifyCourse('maxStudents',selectedRows.ids().toArray(),numberInt);
					}
                }
            }
        ],
		"language": <?php echo $this->render('datatableLanguage.html',NULL,get_defined_vars(),0); ?>
    });
} );
/*function courseDescriptionModal(){
	var id = getCourseIDFromTable();
	if(id){
		courseDescription(selectedRow.id());  //ajax.js
	}else{
		alert("<?= $lang['selectCourse'] ?>");	
	}
}

function getCourseIDFromTable(){
	var table = $('#courseTable').DataTable();
	selectedRow=table.row( { selected: true } );
	if(selectedRow.length == 0){
		return false;
	}else{
		return selectedRow.id();
	}
}*/
</script>

<p  class="mb-3 mt-3"><?= $this->raw($lang['admin']['courses']['courseTableDesc']) ?></p>
<div class="clearfix"></div>
<div id="dialog"></div>
	<table id="adminCourseTable" class="display" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th><?= $lang['id'] ?></th>
                <th><?= $lang['courseTitle'] ?></th>
                <th><?= $lang['teacher'] ?></th>
				<th><?= $lang['max'] ?></th>
				<th>&sum;</th>
				<?php foreach (($priorities?:[]) as $priority): ?>
					<th><?= $priority ?></th>
				<?php endforeach; ?>
				<th title="<?= $lang['admin']['users']['ignored'] ?>">i</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th><?= $lang['id'] ?></th>
                <th><?= $lang['courseTitle'] ?></th>
                <th><?= $lang['teacher'] ?></th>
				<th><?= $lang['max'] ?></th>
				<th>&sum;</th>
				<?php foreach (($priorities?:[]) as $priority): ?>
					<th><?= $priority ?></th>
				<?php endforeach; ?>
				<th title="<?= $lang['admin']['users']['ignored'] ?>">i</th>
            </tr>
        </tfoot>
        <tbody>
			<?php foreach (($courses?:[]) as $course): ?>
		        <tr id="<?= $course['courseID'] ?>">
		            <td><?= $course['courseID'] ?></td>
		            <td><?= $course['title'] ?></td>
		            <td><?= $course['teacherList'] ?></td>
					<th><?= $course['maxStudents'] ?></th>
					<td><?= $course['totalNumber'] ?></td>
					<?php foreach (($priorities?:[]) as $priority): ?>
						<td><?= $course[$priority] ?></td>
					<?php endforeach; ?>
					<td><?php if ($course['ignored'] == 1): ?>&#10004;<?php endif; ?></td>
		        </tr>
			<?php endforeach; ?>
        </tbody>
    </table>


<button type="submit" style="float: left;" class="btn btn-lg btn-primary" value="edit" onclick="courseDescriptionModal()"><?= $lang['openDescription'] ?></button>





