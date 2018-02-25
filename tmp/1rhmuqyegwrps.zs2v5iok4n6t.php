<link href="elective/MVC/View/datatables/datatables.css" rel="stylesheet">
<script src="elective/MVC/View/datatables/datatables.min.js" type="text/javascript"></script>
<script src="elective/MVC/View/js/ajax.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#courseTable').DataTable( {
        select: {
            info: false,
            style: 'single'
        },
		"language": <?php echo $this->render('datatableLanguage.html',NULL,get_defined_vars(),0); ?>
    });
} );
function courseDescriptionModal(){
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
}
</script>
<p class="mb-3 mt-3"><strong><?= $lang['showCourseDescription'] ?></strong></p>

<div class="clearfix"></div>
<div id="dialog"></div>
	<table id="courseTable" class="display" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th><?= $lang['courseID'] ?></th>
                <th><?= $lang['courseTitle'] ?></th>
                <th><?= $lang['teacher'] ?></th>
				<?php if ($showRooms): ?>
				
                	<th><?= $lang['room'] ?></th>
				
				<?php endif; ?>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th><?= $lang['courseID'] ?></th>
                <th><?= $lang['courseTitle'] ?></th>
                <th><?= $lang['teacher'] ?></th>
                <?php if ($showRooms): ?>
				
                	<th><?= $lang['room'] ?></th>
				
				<?php endif; ?>
            </tr>
        </tfoot>
        <tbody>
			<?php foreach (($courses?:[]) as $course): ?>
		        <tr id="<?= $course['courseID'] ?>">
		            <td><?= $course['courseID'] ?></td>
		            <td><?= $course['title'] ?></td>
		            <td><?= $course['teacherList'] ?></td>
					<?php if ($showRooms): ?>
					
        	        	<td><?= $course['room'] ?></td>
					
					<?php endif; ?>
		        </tr>
			<?php endforeach; ?>
        </tbody>
    </table>


<button type="submit" style="float: left;" class="btn btn-lg btn-primary" value="edit" onclick="courseDescriptionModal()"><?= $lang['openDescription'] ?></button>





