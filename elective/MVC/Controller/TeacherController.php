<?php

class TeacherController extends UserController {
	
	/**
	 *  Renders the teachers startpage.
	 **/
	function teacherStartpage(){
		//-> is coursemember? -> select: editcourse, deletecourse, leavecourse
		//-> no coursemember? -> newcourse, ask for enter course
		$courseID=$this->isCourseLeader();
		if($courseID){
			$course = new Course($this->db);
			$course->courseByID($courseID);
			$this->f3->set('course',$course);
			$this->f3->set('courseCard',$course->courseToArray());
			$this->f3->set('teacherCourseOverviewHeader',$this->f3->get('lang.teacherStartpageCourse'));
			$this->f3->set('content','teacher/startpageExistingCourse.html');
			
		}else{
			$this->f3->set('content','teacher/startpageWithoutCourse.html');
		}
		$this->mainTemplate();
	}
	
	
	/**
	 * Renders the teachers course overview page 
	 * 
	 */
	function teacherCourseOverview(){
		$courseID=$this->isCourseLeader();
		if($courseID){
			$course = new Course($this->db);
			$course->courseByID($courseID);
			$this->f3->set('courseCard',$course->courseToArray());
			$this->f3->set('teacherCourseOverviewHeader',$this->f3->get('lang.deadlineEditCourseAchieved',$this->f3->get('deadlineEditCourse')));
			$this->f3->set('content','teacher/courseOverview.html');
		}else{
			$this->f3->set('message',array($this->f3->get('lang.deadlineEditCourseAchieved',$this->f3->get('deadlineEditCourse')),$this->f3->get('lang.teacherNoCourse')));
			$this->f3->set('content','message.html');
		}
		$this->mainTemplate();
	}
	
	/**
	*	Tests, if current user (SESSIOn.userID) is course leader in a course. Returns the corresponding courseID if course leader or false if not.
	*	@return integer/boolean 
	**/
	function isCourseLeader(){
		$result = $this->db->exec('SELECT courseID FROM courseLeader WHERE userID = ?', $this->f3->get('SESSION.userID'));
		if(empty($result)){
			return false;
		}else{
			return $result[0]['courseID'];
		}
	}
	
	/*
	 * Renders the edit course page 
	 *
	 */	
	function editCourse(){
		$new = true;
		$course = new Course($this->db);
		$this->f3->set('course',$course);
		// new course?
		if($this->f3->get('POST.courseID') !== null){
			// Get course from db. If empty result, we have a new course
			$exists = $course->courseByID($this->f3->get('POST.courseID'));
			if($exists){
				$this->f3->set('courseLeaders',$course->teachers);
				$new = false;
			}else{
				$this->f3->set('SESSION.warning',$this->f3->get('lang.courseNotExists'));
				$this->f3->reroute('/');	
			}
		}
		if($this->isAllowedToEditCourse($new, $course)){
			$this->f3->set('userID',$this->f3->get('SESSION.userID'));
			$this->f3->set('availableTeachers',$this->db->exec('SELECT userID AS teacherID, formOfAddress AS teacherFormOfAddress, lName AS teacherLName, fName AS teacherFName FROM user WHERE role="TEACHER" AND user.userID NOT in (SELECT userID FROM courseLeader) AND user.ignored = 0 AND user.userID != ?', $this->f3->get('SESSION.userID')));
			$this->f3->set('content','teacher/editCourse.html');
			$this->mainTemplate();
		}
	}
	
	public function saveCourse(){
		if($this->csrfTokenOK()){
			$course = new Course($this->db, $this->f3);
			$course->courseFromPost();
			//check twice
			if($this->isAllowedToEditCourse($course->getCourseID()===null,$course)){
				$course->saveCourseToDatabase();
				$this->f3->set('SESSION.info',$this->f3->get('lang.courseSaved'));
			}else{
				$this->f3->set('SESSION.warning',$this->f3->get('lang.courseSaveFaild'));
			}
		}
		$this->f3->reroute('/');
	}
	
	/**
	 *  Renders courseTable.html. Get information from Database.
	 **/
	function joinCourse(){
		$this->f3->set('courses',$this->allCourses());
		$this->f3->set('content','teacher/joinCourse.html');
		$this->mainTemplate();
	}
	
	/*
	 * 	Delete teacher and course given by GET from table courseLeader. Deletes course, if no courseLeader left.
	 **/
	function leaveCourse(){
		if($this->teacherOK() && $this->csrfTokenOK()){
			$courseID=$this->f3->get('GET.courseID');
			$result = $this->db->exec('DELETE FROM `courseLeader` WHERE `courseLeader`.`userID` = ? AND `courseLeader`.`courseID` = ?', array($this->f3->get('SESSION.userID'), $courseID));
			// Delete courses without courseleader
			if($this->countCourseLeader($courseID) == 0){
				$this->db->exec('DELETE FROM `course` WHERE course.courseID=? AND course.courseID NOT IN (SELECT courseLeader.courseID FROM courseLeader)',$courseID);
				$this->f3->set('SESSION.warning',$this->f3->get('lang.courseDeleted'));
			}
			$this->f3->set('SESSION.info',$this->f3->get('lang.leftCourse'));
		}
		$this->f3->reroute('/');
	}
	
	/**
	*	Returns the nummber of course leaders of given $courseID
	* 	@param integer
	*	@return integer
	**/
	function countCourseLeader($courseID){
		$result = $this->db->exec('SELECT COUNT(*) AS number FROM courseLeader WHERE courseID = ?', $courseID);
		return $result[0]['number'];
	}
	
	/**
	*	Calls countCourseLeader() with Parameter from Get (for ajax in teacher/startpageExistingCourse.html)
	**/
	function countCourseLeaderGet(){
		$ret = $this->countCourseLeader($this->f3->get('GET.courseID'));
		echo $ret;
	}
	
	/**
	 *  Save course leader from Get.
	 **/
	function saveJoinCourse(){
		if($this->teacherOK() && !($this->isCourseLeader())&& $this->csrfTokenOK()){
			$this->db->exec('INSERT INTO `courseLeader` (`userID`, `courseID`, `add_at`) VALUES (?, ?, ?)',array($this->f3->get('SESSION.userID'),$this->f3->get('GET.courseID'),Controller::timestamp()));
			$this->f3->set('SESSION.info',$this->f3->get('lang.joinedCourse',$this->f3->get('GET.courseID')));
		}
		$this->f3->reroute('/');
	}
	
	/**
	 * Tests, if a logged in user ist allowed to edit/save a course. Rules:
	 *  - must login
	 *  - is course leader in this course
	 *  - userrole ist TEACHER or ADMIN
	 *  - if course is new, user ist not leader in any other course.
	 *  - deadline is not achieved.
	 * 
	 * @param boolean $new  Is it a new course?
	 * @param $course Course The corrseponding Course-object.
	 * @return boolean
	 * **/
	function isAllowedToEditCourse($new, $course){
		// Only loggedin user can edit courses
		if($this->f3->get('SESSION.username') === null ){
			$this->f3->set('SESSION.warning',$this->f3->get('lang.mustLogin'));
			$this->f3->reroute('/login');
			return false;
		}
		// only teacher of a course can edit
		$loggedInUserIsCourseLeader=false;
		foreach($course->teachers as $teacher){
				if($teacher["teacherID"] == $this->f3->get('SESSION.userID')){
						$loggedInUserIsCourseLeader=true;
				}
		}
		if(!$new && !$loggedInUserIsCourseLeader &&  $this->f3->get('SESSION.role') != 'ADMIN' && $this->f3->get('SESSION.role') != 'TEACHER'){
			$this->f3->set('SESSION.warning',$this->f3->get('lang.editNotAllowed'));
			$this->f3->reroute('/');
			return false;
		}
		//only one course per teacher allowed
		$result = $this->db->exec('SELECT courseID FROM courseLeader WHERE userID =?',$this->f3->get('SESSION.userID'));
		if($new && !empty($result)){
			$which = $this->db->exec('SELECT title, courseID FROM course WHERE courseID =?',$result[0]['courseID']);
			$this->f3->set('SESSION.warning',$this->f3->get('lang.onlyOneCoursePerTeacher',$which[0]['title'].' ('.$this->f3->get('lang.courseID').': '.$which[0]['courseID'].')'));
			$this->f3->reroute('/');
			return false;
		}
		if($this->f3->get('SESSION.role') == 'TEACHER' && $this->deadlineTeacherAchieved()){
			$this->f3->set('SESSION.warning',$this->f3->get('lang.noEdit'));
			$this->f3->reroute('/');
			return false;
		}
		return true;
	}

	
}
