<script type="text/javascript">
$(document).ready(function() {
  $('li.active').removeClass('active');
  $('a[href="' + location.pathname + '"]').closest('li').addClass('active'); 
});
</script>

<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand"  href="#"><?= $this->raw($institution) ?></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="/"><?= $lang['home'] ?></a>
          </li>
			<?php if ($SESSION['role'] == 'STUDENT'): ?>
			
				<li class="nav-item">
         			<a class="nav-link" href="/start"><?= $lang['courseSelection'] ?></a>
         		</li>
			
			<?php endif; ?>
			<?php if ($SESSION['role'] == 'TEACHER'): ?>
			
				<li class="nav-item">
         			<a class="nav-link" href="/start"><?= $lang['myCourse'] ?></a>
         		</li>
			
			<?php endif; ?>
			<?php if ($SESSION['role'] == 'ADMIN'): ?>
			
				<li class="nav-item">
         			<a class="nav-link" href="/start"><?= $lang['admin']['panel'] ?></a>
         		</li>
			
			<?php endif; ?>
          <li class="nav-item">
            <a class="nav-link" href="/courses"><?= $lang['allCourses'] ?></a>
          </li>
			<?php if ($SESSION['username'] != NULL): ?>
			
				<li class="nav-item dropdown" >
            		<a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= $SESSION['username'] ?></a>
            		<div class="dropdown-menu" aria-labelledby="dropdown01">
              			<a class="dropdown-item" href="/userPanel"><?= $lang['changePassword'] ?></a>
              			<a class="dropdown-item" href="/logout"><?= $lang['logout'] ?></a>
            		</div>
          		</li>
			
			<?php else: ?>
				<li class="nav-item">
         			<a class="nav-link" href="/login"><?= $lang['login'] ?></a>
         		</li>
			
			<?php endif; ?>
        </ul>
      </div>
    </nav>
