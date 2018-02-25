<link href="elective/MVC/View/bootstrap/css/accordion.css" rel="stylesheet">
<div class="accordion">
<div class="panel-group" id="accordion">

		<!-- Teachers without course -->
		<div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTeachersWithoutCourse"><b><?= $lang['admin']['statistics']['teachersWithoutCourse'] ?></b></a>
                </h3>
            </div>
            <div id="collapseTeachersWithoutCourse" class="panel-collapse collapse">
                <div class="panel-body ml-2">
					<a href="/rtf?type=teachersWithoutCourse" target="_blank"><?= $lang['admin']['downloadAsFile'] ?></a>
					<div id="ajax-teachersWithoutCourse"></div>
                </div>
            </div>
        </div>



		<!-- Students without selection -->
		<div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseStudentsWithoutSelection"><b><?= $lang['admin']['statistics']['studentsWithoutSelection'] ?></b></a>
                </h4>
            </div>
            <div id="collapseStudentsWithoutSelection" class="panel-collapse collapse">
                <div class="panel-body ml-2">
					<b><?= $lang['admin']['downloadAsFile'] ?>: </b><a href="/rtf?type=studentsWithoutSelection&orderBy=class" target="_blank" class="mr-2"><?= $lang['admin']['users']['rtfSortClass'] ?>  </a><a href="/rtf?type=studentsWithoutSelection&orderBy=lname" target="_blank"><?= $lang['admin']['users']['rtfSortName'] ?>  </a>
					<div id="ajax-studentsWithoutSelection"></div>
                </div>
            </div>
        </div>

		<!-- course description -->
		<div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseCourseCard"><b><?= $lang['admin']['courseCard'] ?></b></a>
                </h4>
            </div>
            <div id="collapseCourseCard" class="panel-collapse collapse">
                <div class="panel-body ml-2">
					<a href="/rtf?type=courseCard" target="_blank"><?= $lang['admin']['downloadAsFile'] ?></a>
                </div>
            </div>
        </div>

</div>
</div>


<script>
$(document).ready(function(){
	renderStatisticsTables('teachersWithoutCourse');
	renderStatisticsTables('studentsWithoutSelection');
    });
</script>
