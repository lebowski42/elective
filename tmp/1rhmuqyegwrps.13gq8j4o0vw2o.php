<script src="elective/MVC/View/js/ajax.js"></script>
<script type="text/javascript">
 $(document).ready(function(){
	popularCourses($("#limit" ).val());
})
$(document).on('keyup mouseup','#limit', function(){
	popularCourses($("#limit" ).val());
});
</script>


	<h3 class="mt-4"><?= $lang['admin']['statistics']['StudentsAndTeacher'] ?></h3>
	<ol class="list-group">
		<li  class="list-group-item"><b><?= $lang['admin']['statistics']['teacherTotalNumber'] ?></b><span class="ml-2 badge badge-pill badge-primary"><?= $teacherTotalNumber ?></span></li>
		<li  class="list-group-item"><b><?= $lang['admin']['statistics']['studentTotalNumber'] ?></b><span class="ml-2 badge badge-pill badge-primary"><?= $studentTotalNumber ?></span></li>
		<li  class="list-group-item"><b><?= $lang['admin']['statistics']['avgStudentsPerTeacher'] ?></b><span class="ml-2 badge badge-pill badge-primary"><?= $studentPerTeacher ?></span></li>
	</ol>

	<h3 class="mt-4"><?= $lang['admin']['statistics']['popularCourses'] ?></h3>
	<!--<label for="limit" class="desc" ><?= $lang['quantity'] ?></label>-->
	<input  type="number" class="form-control" name="limit" id="limit"  min="1" style="width:100px;" value="3">
	<div id="popularCourses">
		<div class="loader"></div>
	</div> 


<div class="card mt-4">
  <div class="card-header">
    <h4><?= $lang['admin']['statistics']['lists'] ?></h4>
  </div>
  <div class="card-block p-2">
   <!-- <h4 class="card-title">Special title treatment</h4>-->
	<?php echo $this->render('admin/statistics/lists.html',NULL,get_defined_vars(),0); ?>
	
  </div>
</div>
