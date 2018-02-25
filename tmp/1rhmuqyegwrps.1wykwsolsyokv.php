<div class="card mt-4 mb-4">
	<div class="card-header">
		<h4><?= $lang['admin']['users']['editUser'] ?></h4>
	</div>
	<div id="usersTableWrapper"><div class="loader">	
	</div></div>
</div>
<div class="card mt-4">
  <div class="card-header">
    <h4><?= $lang['admin']['users']['csvUpload'] ?></h4>
  </div>
  <div class="card-block p-2">
    <div class="card-text"><?= $this->raw($lang['admin']['users']['csvUploadDescription']) ?></div>
	<div id="usersCSV">
	<form enctype="multipart/form-data" action="csvUsersUpload" method="POST">
		<input type="hidden" name="MAX_FILE_SIZE" value="<?= $maxUploadSizeBytes ?>" />
		<input type="hidden"  name="token" value="<?= $SESSION['csrf'] ?>"/>
        <b><?= $lang['admin']['users']['chooseCSV'] ?></b> <input type="file" name="csvUsers" id="usersCSVInput"  accept=".csv" /><br>
		<div class="mt-2">
			<label><strong><?= $lang['admin']['users']['roleDesc'] ?></strong></label><br>
			<div class="ml-4">
				<label><input type="radio" name="role" value="STUDENT" required><?= $lang['student'] ?></label><br>
				<label><input type="radio" name="role" value="TEACHER"><?= $lang['teacher'] ?></label><br>
				<label><input type="radio" name="role" value="ADMIN"><?= $lang['administrator'] ?></label><br>
			</div>
		</div>
<button type="submit" class="btn btn-lg btn-primary" style="display: inline-block;" value="csvAdd" ><?= $lang['admin']['users']['testCSV'] ?></button>
	</form>
    </div>
	<?php if (isset($SESSION['csvInfo']) && !empty($SESSION['csvInfo'])): ?>
    	
			<?= $this->raw($SESSION['csvInfo']).PHP_EOL ?>
		
	<?php endif; ?>
	
  </div>
</div>
<div id="userDialogModal"></div>
<script>
$(document).ready(function(){
	usersTable();
});

</script>
