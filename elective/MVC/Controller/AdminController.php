<?php

class AdminController extends UserController {
	
	function panel(){
		if($this->adminOK()){
			$this->f3->set('content','admin/panel.html');
			$this->statistics();
			$this->mainTemplate();
		}else{
			$this->f3->reroute('/');
		}
	}
	
	function statistics(){
		if($this->adminOK()){
			$result = $this->db->exec('SELECT Count(*) AS tn FROM `user` WHERE role="TEACHER"'); 
			$this->f3->set('teacherTotalNumber',$result[0]['tn']);
			$result = $this->db->exec('SELECT Count(*) AS tn FROM `user` WHERE role="STUDENT"'); 
			$this->f3->set('studentTotalNumber',$result[0]['tn']);
			if($this->f3->get('teacherTotalNumber')==0){
				$studentPerTeacher = 0;
			}else{
				$studentPerTeacher = round($this->f3->get('studentTotalNumber')/$this->f3->get('teacherTotalNumber'),2);
			}
			$this->f3->set('studentPerTeacher',$studentPerTeacher);
		}
	}
	
	function hashTimeTest(){
		/**
	 * This code will benchmark your server to determine how high of a cost you can
	 * afford. You want to set the highest cost that you can without slowing down
	 * you server too much. 10 is a good baseline, and more is good if your servers
	 * are fast enough.
	 */
	$timeTarget = 0.5; 

	$cost = 9;
	do {
		$cost++;
		$start = microtime(true);
		password_hash("test", PASSWORD_BCRYPT, ["cost" => $cost]);
		$end = microtime(true);
	} while (($end - $start) < $timeTarget);

	echo "Appropriate Cost Found: " . $cost . "\n";
	}
	
	function popularCourses(){
		if($this->adminOK()){
			$limit = $this->f3->get('POST.limit');
			if(!isset($limit) || $limit<1) $limit=3;
			$result =$this->db->exec('SELECT DISTINCT Count(courseID) AS tn, courseID, title, priority, userID FROM course JOIN selection USING(courseID) GROUP BY courseID ORDER BY tn DESC LIMIT ?',intval($limit));
			$scourses = array();
			foreach($result as $s){
				$course = new Course($this->db);
				$course->courseByID($s['courseID']);
				$courseArray = $course->courseToArray();
				$courseArray['countSelection'] = $s['tn'];
				array_push($scourses,$courseArray);
			}
			$this->f3->set('courses',$scourses);
			echo \Template::instance()->render('student/coursesList.html');
		} 
	}
	
	function addUser(){
		if($this->adminOK() && $this->csrfTokenOK()){
			$this->f3->clear('POST.token'); // because of copyFromPost in User.php
			$user=new User($this->db);
			$user->add();
			$this->f3->set('SESSION.info',$this->f3->get('lang.admin.users.userAdded',$user->username));
			$this->f3->reroute('/administrator#users');
		}
	}
	
	function modifyUser(){
		if($this->adminOK() && $this->csrfTokenOK()){
			$this->f3->clear('POST.token'); // because of copyFromPost in User.php
			$user=new User($this->db);
			$user->edit($this->f3->get('POST.userID'));
			$this->f3->set('SESSION.info',$this->f3->get('lang.admin.users.userModified',$user->username));
			$this->f3->reroute('/administrator#users');
		}
	}
	
	function csvUsersUpload(){
		//admin, token, test ob token ok, Datei vorhanden
		$this->f3->set('SESSION.csvInfo','');
		$row = 1;
		$csv = array_map('str_getcsv', file($_FILES['csvUsers']['tmp_name']),array(',','')); // READ csv File 
		$row = 0;
		$this->f3->set('SESSION.csvRole',$this->f3->get('POST.role')); // STUDENT TEACHER ADMIN?
		$error=array();
		foreach($csv as $key => $user){
			// empty username?
			if(empty($user[0]) && !empty($user[2])){
				$csv[$key][0] = $user[3].$user[2];
			}
			// empty Passwort
			if(empty($user[1])){
				$csv[$key][1] = $this->randomPassword($this->f3->get('minPasswordLength'));
			}
			// no username? use lastname
			if(empty($user[2]) && !empty($user[0])){
				$csv[$key][2] = $user[0];
			}
			//no username and lname.
			if(empty($user[0])||empty($user[2])){
					array_push($error,$this->f3->get('lang.admin.users.row').' '.($row+1).': '.$this->f3->get('lang.admin.users.missingUsername'));
			}
			$row++;
		}
		// Same username
		foreach($csv as $tKey => $tUser ){
			for($i=$tKey+1; $i < count($csv); $i++) {
				if($tUser[0] == $csv[$i][0]){
					array_push($error,$this->f3->get('lang.admin.users.row').' '.($tKey+1).' & '.($i+1).': '.$this->f3->get('lang.admin.users.sameUsername'));
				}
			}
		}
		// Serachlist for sql
		$usernames=array_column($csv,'0');
		$searchList = '\''.str_replace(",","','",implode(',', $usernames)).'\''; 
		// username in db?
		$result = $this->db->exec('SELECT username FROM user WHERE username IN ('.$searchList.')');
		// Find line
		foreach($usernames as $row=>$username){
				foreach($result as $dbuser){
						if($username == $dbuser['username']){
								array_push($error,$this->f3->get('lang.admin.users.row').' '.($row+1).' ('.$this->f3->get('lang.username').' '.$username.'): '.$this->f3->get('lang.admin.users.inDatabase'));
						}
				}
		}
		// if errors occured, set SESSION var and reroute to admin panel view/admin/users.html renders them.
		if(!empty($error)){
			$this->f3->set('errors',$error);
			$this->f3->set('SESSION.csvInfo',\Template::instance()->render('admin/users/csvUserError.html'));
		}else{
			// show everting and wait for confirmation
			$this->f3->set('SESSION.csvUser',$csv);// We need them later to write them to db.
			$this->f3->set('SESSION.csvInfo',\Template::instance()->render('admin/users/csvUserList.html'));
		}
		$this->f3->reroute('/administrator#users');
	}
	
	/*
	 * Sort a userlist by given criteria (username, lname,fname, class) 
	 * $users array([0]=>{[0]=>username, [1]=>password, [2]=>lname, [3]=>fname, [4]=>... ,[5]=>class})
	 * 
	 * returns sorted userlist.
	 */
	private function sortUserList($users, $orderByCriteria){
		if($this->adminOK()){
			$orderBy = array();
			//  map  order criteria to array index
			switch($orderByCriteria){
				case 'username': $orderBy[0] = 0; $orderBy[1] = 2; $orderBy[2] = 3; break; //username, lName, fname (meaningless but necessary)
				case 'fName': $orderBy[0] = 3; $orderBy[1] = 2; $orderBy[2] = 0; break; // fname, lname, username
				case 'lName': $orderBy[0] = 2; $orderBy[1] = 3; $orderBy[2] = 0; break; // lname, fname, username
				case 'class': $orderBy[0] = 5; $orderBy[1] = 2; $orderBy[2] = 3; break; // class, lname, fname
				default: $orderBy[0] = 2; $orderBy[1] = 3; $orderBy[2] = 0; break;
			}
			// sort multidim array using three columns specified in $orderBy
			usort($users, function ($a, $b) use ($orderBy) {
				if (strcasecmp($a[$orderBy[0]], $b[$orderBy[0]])== 0){
					if (strcasecmp($a[$orderBy[1]], $b[$orderBy[1]]) == 0){
						if (strcasecmp($a[$orderBy[2]], $b[$orderBy[2]])==0){
							echo $a[$orderBy[0]]."-".$b[$orderBy[0]];
							return 0;
						}else{
							return (strcasecmp($a[$orderBy[2]], $b[$orderBy[2]]));
						}
					}else{
						return strcasecmp($a[$orderBy[1]], $b[$orderBy[1]]);
					}
				} 
				return strcasecmp($a[$orderBy[0]], $b[$orderBy[0]]);
			});
			if($orderByCriteria == 'class'){//all classes
				$pagebreaker = array_unique(array_column($users, $orderBy[0]));
			}else{
				// All first letters
				$columns = array_column($users, $orderBy[0]);
				$onlyFirstLetter = array_map(function($val){
					return strtolower(substr($val,0,1)); //return first letter as lowercase
				}, $columns);
				$pagebreaker = array_unique($onlyFirstLetter);
			}
			$usersD = array();
			/*foreach($pagebreaker as $item){
				array_push($usersD, array_filter($csv,function($user){
					return (strpos($item, $user[$orderBy[0]]) === 0);
				}));	
			}*/
			$further=array(); // For users with empty item (e.g. no class)
			foreach($pagebreaker as $item){// $item => {users with this item}
				$group = array();
				foreach($users as $user){
					if(!empty($item)){
						if(strpos(strtolower($user[$orderBy[0]]),$item) === 0){
							array_push($group,$user);
						}
					}else{// users without class
						if(empty($user[$orderBy[0]])){
							array_push($further,$user);
						}
					}
				}
				if(!empty($item)){
					$usersD[strtoupper($item)] = $group;
				}
			}
			$usersD[$this->f3->get('lang.further')]= $further;
		}
		return $usersD;
	}
	
	public function generateUserListRTF(){
		if($this->adminOK()){
			$usersD = sortUsersList($this->f3->get('SESSION.csvUser'),$this->f3->get('GET.orderBy'));
			
			/*$orderBy = array();
			//  map  order criteria to array index
			switch(){
				case 'username': $orderBy[0] = 0; $orderBy[1] = 2; $orderBy[2] = 3; break; //username, lName, fname (meaningless but necessary)
				case 'fName': $orderBy[0] = 3; $orderBy[1] = 2; $orderBy[2] = 0; break; // fname, lname, username
				case 'lName': $orderBy[0] = 2; $orderBy[1] = 3; $orderBy[2] = 0; break; // lname, fname, username
				case 'class': $orderBy[0] = 5; $orderBy[1] = 2; $orderBy[2] = 3; break; // class, lname, fname
				default: $orderBy[0] = 2; $orderBy[1] = 3; $orderBy[2] = 0; break;
			}
			// We need a array-reference
			$csv = $this->f3->get('SESSION.csvUser');
			// sort multidim array using three columns specified in $orderBy
			usort($csv, function ($a, $b) use ($orderBy) {
				if (strcasecmp($a[$orderBy[0]], $b[$orderBy[0]])== 0){
					if (strcasecmp($a[$orderBy[1]], $b[$orderBy[1]]) == 0){
						if (strcasecmp($a[$orderBy[2]], $b[$orderBy[2]])==0){
							echo $a[$orderBy[0]]."-".$b[$orderBy[0]];
							return 0;
						}else{
							return (strcasecmp($a[$orderBy[2]], $b[$orderBy[2]]));
						}
					}else{
						return strcasecmp($a[$orderBy[1]], $b[$orderBy[1]]);
					}
				} 
				return strcasecmp($a[$orderBy[0]], $b[$orderBy[0]]);
			});
			if($this->f3->get('GET.orderBy') == 'class'){
				$pagebreaker = array_unique(array_column($csv, $orderBy[0]));
			}else{
				$columns = array_column($csv, $orderBy[0]);
				$onlyFirstLetter = array_map(function($val){
					return strtolower(substr($val,0,1)); //return first letter as lowercase
				}, $columns);
				$pagebreaker = array_unique($onlyFirstLetter);
			}
			$usersD = array();
			$further=array(); // For users with empty item (e.g. no class)
			foreach($pagebreaker as $item){
				$group = array();
				foreach($csv as $user){
					if(!empty($item)){
						if(strpos(strtolower($user[$orderBy[0]]),$item) === 0){
							array_push($group,$user);
						}
					}else{
						if(empty($user[$orderBy[0]])){
							array_push($further,$user);
						}
					}
				}
				if(!empty($item)){
					$usersD[strtoupper($item)] = $group;
				}
			}
			$usersD[$this->f3->get('lang.further')]= $further;
		}*/
		//$this->f3->set('sortCol',$orderBy[0]);
			$this->f3->set('usersD',$usersD);
			header('Content-Type: application/rtf;charset=utf-8');
			header('Content-Disposition: attachment; filename="'.$this->f3->get('lang.user').'_'.$this->f3->get('title').'.odt"');
			echo \Template::instance()->render("rtf/passwordList.rtf");
		}
	}
	
	function csvUserToDatabase(){
			if($this->adminOK() && $this->csrfTokenOK()){
				$inserts = array();
				$date = $this->timestamp();
				$role = $this->f3->get('SESSION.csvRole');
				// make this configurable
				$options = [
					'cost' => 10,
				];
				foreach($this->f3->get('SESSION.csvUser') as $user){
					$pass = password_hash($user[1],PASSWORD_DEFAULT,$options);
					array_push($inserts,"('NULL','".$user[0]."','".$pass."','".$user[3]."','".$user[2]."','".$user[4]."','".$user[5]."','".$role."','".$user[6]."','0','".$date."','".$date."')");
				}
				$this->db->exec('INSERT INTO `user` (`userID`, `username`, `password`, `fname`, `lname`, `formOfAddress`, `class`, `role`, `email`, `email_verified`, `last_login`, `created_at`) VALUES '.(implode(',',$inserts)));
				//echo 'INSERT INTO `user` (`userID`, `username`, `password`, `fname`, `lname`, `formOfAddress`, `class`, `role`, `email`, `email_verified`, `last_login`, `created_at`) VALUES ? '.implode(',',$inserts);
				$this->f3->set('SESSION.csvRole','');
				$this->f3->set('SESSION.csvUser','');
				$this->f3->set('SESSION.success',$this->f3->get('lang.admin.users.usersAdded'));
				echo $this->f3->get('lang.admin.users.usersAdded');
				// NOch testen ob alles geklappt hat
				//echo $this->f3->get('lang.admin.users.usersAdded');
			}
	}
	
	function calculateCourseList(){
		$result = $this->db->exec('SELECT `userID`, `courseID` FROM `selection` ORDER BY userID ASC , priority ASC ');
		$selection=array();
		// Create array $selection [userID] => {selection 1, selection2, ...}
        foreach($result as $s){
			$userID = $s['userID'];
			if(!array_key_exists($userID, $selection)){
				$courses = array();
			}else{
					$courses=$selection[$userID];
			}
			array_push($courses,$s['courseID']);
			$selection[$userID] = $courses;
		}
		// Build array $courses[courseID]=>maxStudents
		$courses = array();
		$result = $this->db->exec('SELECT `courseID`,`maxStudents` FROM `course` ORDER BY courseID ASC');
		foreach($result as $course){
			$courses[$course['courseID']] = array('maxStudents'=>$course['maxStudents'], 'member'=>array());
		}
		// First Round: Put students into the course with priority 1
		foreach($selection as $userID => $s){
				array_push($courses[$s[0]]['member'], $userID);
		}
		// radomize list of members of each course
		// count member (we need it later)
		foreach($courses as $courseID => $course){
			$member = $course['member'];
			shuffle($member);
			$courses[$courseID]['member'] = $member;
			$courses[$courseID]['size'] = count($member);
		}
		
		// if course size bigger then maxStudents, put students into course with priority 2 (if possible)
		$priority = 2;
		$this->cl_dump($courses);
		while($priority <= $this->f3->get('maxNumberOfCoursesToChoose')){
		//$this->userCount($courses);
		foreach($courses as $courseID => $course){
			foreach($course['member'] as $key => $member){
				if($this->courseFull($course)){
					if(isset($selection[$member][$priority-1])){// Priority 2 is index 1 and so on;
						$nextCourse = $selection[$member][$priority-1];
						if(!($this->courseFull($courses[$nextCourse]))){//We only move student to another course, if this course is not full
							array_push($courses[$nextCourse]['member'], $member); // current user gets a new course
							unset($course['member'][$key]);   // remove them from old course
							$course['size'] = count($course['member']); //new size
							$courses[$nextCourse]['size'] = count($courses[$nextCourse]['member']); //same
							
						}
					}
				}else{
					break;
				}
				$courses[$courseID] = $course; // write changes into the array
			}
			
		}
		$priority++;
		$this->cl_dump($courses);
	}
	}
	
	function courseFull($course){
		return ($course['size'] >= $course['maxStudents']);
	}
	
	// For testing
	function userCount($list){
		$all = array();
		foreach($list as $course){
			foreach($course['member'] as $member ){
				array_push($all,$member);
			}
		}
		asort($all);
		$i=1;
		$error = "";
		foreach($all as $m){
			if($i!=$m){
				$error .= $m;
			}
			$i++;
		}
		print_r($all);
		echo count($all)."-".$error."<br>";
	}
	
	//display courslist 
	function cl_dump($list){
		echo "<table><thead><tr><th>courseID</th><th>members</th></thead><tbody>";
		foreach($list as $courseID => $course){
				
				echo "<tr><td>".$courseID."(".$course['size']."/".$course['maxStudents'].")</td><td>".(implode(',',$course['member']))."</td></tr>";
		}
		echo "</tbody></table>";
	}

	
	private	function randomPassword($length) {
	   //Possible chars
	   $chars = '0123456789';
	   $chars .= 'abcdefghijklmnopqrstuvwxyz';
	   $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	   //$zeichen .= '!?$&%';
	   //generate
	   $pass = '';
	   $lenChars = strlen($chars);
	   for ($i=0; $i<$length; $i++) {
			$pass .= $chars[rand(0,$lenChars-1)];
	   }
	   return $pass;
	}

	/**
	*	Tests, if the current user ist allowed to perform admin tasks.
	*	@return boolean
	**/
    private function adminOK(){
		$result = true;
		if($this->f3->get('SESSION.userID') == null){
				$result = false;
				$this->f3->set('SESSION.warning',$this->f3->get('lang.notLoggedIn'));
		}
		if($this->f3->get('SESSION.role') != 'ADMIN'){
				$result = false;
				$this->f3->set('SESSION.warning',$this->f3->get('lang.notAllowed'));
		}
		return $result;
	}
	
	// for ajax in change user data
	function usernameExists(){
			if($this->adminOK()){
				$result = $this->db->exec('SELECT username FROM user WHERE username = ?', $this->f3->get('POST.username'));
				echo (!empty($result));
			}
	}
	
	public function getAllUsers(){
			return $this->db->exec('SELECT * FROM user');
	}
	
	public function renderUsersTable(){
		if($this->adminOK()){
			$users=$this->getAllUsers();
			$this->f3->set('users', $users);
			echo \Template::instance()->render('admin/users/usersTable.html');
		}
	}
	
	public function userTask(){
		if($this->adminOK()){
			if($this->f3->get('POST.type') == 'edit'){
				$user = new User($this->db);
				$user->getById($this->f3->get('POST.userID'));
				$this->f3->set('user',$user);
				echo \Template::instance()->render('admin/users/editUserModal.html');
				return true;
			}
			if($this->f3->get('POST.type') == 'add'){
				echo \Template::instance()->render('admin/users/addUserModal.html');
				return true;
			}
			if($this->f3->get('POST.type') == 'del'){
				//var_dump($this->f3->get('POST.userID'));
				foreach($this->f3->get('POST.userID') as $userID){
					$user = new User($this->db);
					$user->delete($userID);	
				}
				return true;
			}
		}
	}
	
	public function courseTask(){
		if($this->adminOK()){
			if($this->f3->get('POST.type') == 'del'){
				foreach($this->f3->get('POST.courseID') as $courseID){
					$course = new Course($this->db);
					$course->delete($courseID);	
				}
				return true;
			}
			if($this->f3->get('POST.type') == 'ignore'){
				foreach($this->f3->get('POST.courseID') as $courseID){
					$course = new Course($this->db);
					$course->ignore($courseID);	
				}
				return true;
			}
			if($this->f3->get('POST.type') == 'activate'){
				foreach($this->f3->get('POST.courseID') as $courseID){
					$course = new Course($this->db);
					$course->activate($courseID);	
				}
				return true;
			}
			if($this->f3->get('POST.type') == 'maxStudents'){
				foreach($this->f3->get('POST.courseID') as $courseID){
					$course = new Course($this->db);
					$course->maxStudents($courseID,$this->f3->get('POST.data'));	
				}
				return true;
			}
		}	
	}
	
	
	
	public function rtf(){
		if($this->adminOK()){
			if($this->f3->get('GET.type') == 'csvUsers'){
				$this->generateUserListRTF();
				return true;
			}
			if($this->f3->get('GET.type') == 'teachersWithoutCourse'){
				$this->f3->set('teachersWithoutCourse',$this->teachersWithoutCourse());
				header('Content-Type: application/rtf;charset=utf-8');
				header('Content-Disposition: attachment; filename="'.$this->f3->get('lang.admin.statistics.teachersWithoutCourse').'_'.$this->f3->get('title').'.odt"');
				echo \Template::instance()->render("rtf/teachersWithoutCourse.rtf");
				return true;
			}
			if($this->f3->get('GET.type') == 'studentsWithoutSelection'){
				$students = $this->studentsWithoutSelection();
				foreach($students as $key=>$student){//assozitaiv to numbered array
					$students[$key]=array_values($student);
				}
				$this->f3->set('studentsWithoutSelection',$this->sortUserList($students,$this->f3->get('GET.orderBy')));//sort them
				header('Content-Type: application/rtf;charset=utf-8');
				header('Content-Disposition: attachment; filename="'.$this->f3->get('lang.admin.statistics.studentsWithoutSelection').'_'.$this->f3->get('title').'.odt"');
				echo \Template::instance()->render("rtf/studentsWithoutSelection.rtf");
				//var_dump($students);
				return true;
			}
			if($this->f3->get('GET.type') == 'courseCard'){
				$this->f3->set('courses',$this->allCourses());
				header('Content-Type: application/rtf;charset=utf-8');
				header('Content-Disposition: attachment; filename="'.$this->f3->get('lang.admin.courseCard').'_'.$this->f3->get('title').'.odt"');
				echo \Template::instance()->render("rtf/courseCard.rtf");
				return true;
			}		
		}
	}
	
	public function teachersWithoutCourse(){
		if($this->adminOK()){
			$result = $this->db->exec('SELECT userID, username, formOfAddress, fname, lname From user WHERE role="TEACHER" AND ignored=0 AND userID NOT IN (SELECT userID FROM courseLeader) ORDER BY lname ASC');
			return $result;
		}
	}
	
	/*
	 * Renders table for teachers without course and students without selection
	 *  
	 */
	public function renderStatisticsTable(){
		if($this->adminOK()){
			$this->f3->set('type',$this->f3->get('POST.type'));
			if($this->f3->get('POST.type') == 'teachersWithoutCourse'){
				$this->f3->set('users',($this->teachersWithoutCourse()));
				$this->f3->set('tRole','TEACHER');
				echo \Template::instance()->render("admin/statistics/usersTable.html");
				return true;
			}
			if($this->f3->get('POST.type') == 'studentsWithoutSelection'){
				$this->f3->set('users',$this->studentsWithoutSelection());
				$this->f3->set('tRole','STUDENT');
				echo \Template::instance()->render("admin/statistics/usersTable.html");
				return true;
			}
			
			/*$coursesUserCount=array();
			for($priority=1;$i<$this->f3->get('maxNumberOfCoursesToChoose');$priority++){
				$coursesUserCount[$priority] = $this->studentWithoutSelection($priority);
			}
			$this->f3->set('students',$coursesUserCount);
			echo \Template::instance()->render("admin/lists.html");*/
		}
	}
	
	public function studentsWithoutSelection(){
		if($this->adminOK()){
			return $this->db->exec('SELECT username, userID, lname, fname, formOfAddress, class FROM user WHERE role="STUDENT" AND ignored=0 AND userID NOT IN (SELECT userID FROM selection) GROUP BY class ORDER BY lname ASC');
		}
	}
	
	/**
	 * Queries all courses, count priority. Selections of ignored students not shown.
	 **/ 
	public function courseTable(){
		$priorities = array();
        $query = "SELECT courseID, title, maxStudents, course.ignored, COUNT(priority) AS totalNumber, GROUP_CONCAT(DISTINCT ' ',tCourseLeader.formOfAddress,' ',tCourseLeader.lname ) AS teacherList";
		for($i=1; $i<= $this->f3->get('maxNumberOfCoursesToChoose');$i++){
			$query .=", COUNT(CASE WHEN priority =".$i." THEN priority END) AS P".$i;
			array_push($priorities, "P".$i); // For admin/courses/courseTable.hmtl template
			
		}
		$query .= " FROM course ";
		$query .= "LEFT JOIN selection USING(courseID) ";
		$query .= "LEFT JOIN courseLeader USING(courseID) ";
		$query .= "LEFT JOIN user AS tCourseLeader ON tCourseLeader.userID = courseLeader.userID ";
		$query .= "LEFT JOIN user AS tStudents ON tStudents.userID = selection.userID ";
		$query .= "WHERE tStudents.ignored != 1 ";
		$query .= "GROUP BY selection.courseID ";
		$query .= "ORDER BY `selection`.`courseID` ";
		$result = $this->db->exec($query);
		$this->f3->set('courses', $result);
		$this->f3->set('priorities',$priorities);
		echo \Template::instance()->render('admin/courses/courseTable.html');
	}
	
	
	
	public function setMaxStudentsPerCourse(){
		if($this->adminOK() && $this->csrfTokenOK()){
			$number = $this->f3->get('POST.maxStudents');
			if($this->f3->get('POST.multiplyNumberOfTeacher')){
				$this->db->exec('UPDATE course SET maxStudents = (SELECT COUNT(*)*? FROM courseLeader WHERE course.courseID = courseLeader.courseID)',$number);
			}else{
				$this->db->exec('UPDATE course SET maxStudents = ?',$number);
			}
			$this->f3->set('SESSION.info',$this->f3->get('lang.admin.courses.setStudentsPerCourseDone'));
			$this->f3->reroute('/administrator#courses');
		}
	}
		
	public function setupDatabase(){
            $this->db->begin();
            $this->db->exec("CREATE TABLE `user` (
                            `userID` INT NOT NULL AUTO_INCREMENT,
                            `username` VARCHAR(180) NOT NULL,
                            `password` VARCHAR(255) NOT NULL,
                            `fname` VARCHAR(180),
                            `lname` VARCHAR(180),
                            `formOfAddress` VARCHAR(190),
                            `class` VARCHAR(180),
                            `role` VARCHAR(45) NOT NULL DEFAULT 'USER',
                            `ignored` tinyint(1) NOT NULL DEFAULT '0',
                            `email` VARCHAR(180),
                            `email_verified` BOOLEAN NOT NULL DEFAULT 0,
                            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `last_login` TIMESTAMP,
                            PRIMARY KEY (`userID`),
                            UNIQUE (username)
                            );
                          ");
            $this->db->exec("CREATE TABLE `course` (
                            `courseID` INT NOT NULL AUTO_INCREMENT,
                            `title` VARCHAR(1024),
                            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `last_modified` TIMESTAMP,
                            `description` MEDIUMTEXT,
                            `note` TEXT,
                            `room` VARCHAR(255),
                            `maxStudents` INT,
                            `ignored` tinyint(1) NOT NULL DEFAULT '0',
                            PRIMARY KEY (`courseID`)
                            );
                          ");
            $this->db->exec("CREATE TABLE `selection` (
                            `userID` INT NOT NULL,
                            `courseID` INT NOT NULL,
                            `last_save` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `priority` INT NOT NULL,
                            `fix` BOOLEAN DEFAULT 0,
                            PRIMARY KEY (`userID`,`courseID`)
                            );
                          ");
            $this->db->exec("CREATE TABLE `courseLeader` (
                            `userID` INT NOT NULL,
                            `courseID` INT NOT NULL,
                            `add_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (`userID`,`courseID`)
                            );
                          ");
            $this->db->exec("INSERT INTO `user` (`userID`, `username`, `password`, `fname`, `lname`, `formOfAddress`, `class`, `role`, `email`, `email_verified`, `last_login`, `created_at`) VALUES (NULL, 'admin', '$2y$10$ugbwkA6bTYqre1IGz1BN.eD3B7RnCo04Cr9UuPA7DKpBfiQmcYoRy', NULL, '', NULL, NULL, 'ADMIN', NULL, '0', NULL, CURRENT_TIMESTAMP)");
            $this->db->commit();
            $this->f3->set('SESSION.info',$this->f3->get('lang.admin.createdDB'));
			$this->mainTemplate();
			$this->f3->reroute('/');
		}
}
