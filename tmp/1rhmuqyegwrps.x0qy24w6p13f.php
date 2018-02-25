<link href="elective/MVC/View/datatables/datatables.css" rel="stylesheet">
<script src="elective/MVC/View/datatables/datatables.min.js" type="text/javascript"></script>
<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="statistics-tab" data-toggle="tab" href="#statistics" role="tab" aria-controls="statistics" aria-selected="true"><?= $lang['admin']['statistics']['tab'] ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="users-tab" data-toggle="tab" href="#users" role="tab" aria-controls="users" aria-selected="false"><?= $lang['admin']['users']['tab'] ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="courses-tab" data-toggle="tab" href="#courses" role="tab" aria-controls="courses" aria-selected="false"><?= $lang['courses'] ?></a>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
	<div class="tab-pane fade show active" id="statistics" role="tabpanel" aria-labelledby="statistics-tab">
		<?php echo $this->render('admin/statistics/statistics.html',NULL,get_defined_vars(),0); ?>
	</div>
	<div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
		<?php echo $this->render('admin/users/users.html',NULL,get_defined_vars(),0); ?>
	</div>
  <div class="tab-pane fade" id="courses" role="tabpanel" aria-labelledby="courses-tab">
	<?php echo $this->render('admin/courses/courses.html',NULL,get_defined_vars(),0); ?>
  </div>
</div>

<script src="elective/MVC/View/js/ajax.js"></script>
<script type="text/javascript">
var url = window.location.href;
var activeTab = url.substring(url.indexOf("#") + 1);
$(".tab-pane").removeClass("active");
$("#" + activeTab).addClass("active");
$('a[href="#'+ activeTab +'"]').tab('show')

$('#myTab a').click(function (e) {
  e.preventDefault();
  $(this).tab('show');
});
</script>