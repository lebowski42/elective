<?php

class Controller {
    protected $logger;
    protected $f3;
    protected $db;

    function beforeroute(){
        /**if($this->f3->get('SESSION.username') === null ) {
            $this->f3->reroute('/login');
            exit;
        }*/

    }

    function afterroute(){
        //echo '- After routing';
    }

    function __construct() {

        $f3=Base::instance();
        $this->f3=$f3;

        $db=new DB\SQL(
            $f3->get('db'),
            $f3->get('dbusername'),
            $f3->get('dbpassword'),
            array( \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION )
        );


        $this->db=$db;
	$this->logger= new Log('sys.log');
    }
    
    //Not loggedin -> login
	//Student -> before selectionEndTime: studentsCourseSelection
	//        -> after selectionEndTime: finalSelection
	//Teacher -> before editendtime
	//             -> teacherStartpage
	//        -> after editendtime
	//             -> courseoverview
	//Admin -> Adminpage
    function startpage(){
		if($this->f3->get('SESSION.username') === null ){
			// Noch überlegen: Text für neue Besucher
			$this->f3->set('content','login.html');
			$this->f3->reroute('/login');
			return true;
		}
		if($this->f3->get('SESSION.role') == 'STUDENT'){
			$studentController = new StudentController();
			if($this->deadlineStudentsAchieved()){
				// after endtime
				$studentController->selectionEndOfProcess();
			}else{
				//before endtime
				$studentController->studentsCourseSelection();
			}
			return true;
	    }
		if($this->f3->get('SESSION.role') == 'TEACHER'){
			$teacherController = new TeacherController();
			if($this->deadlineTeacherAchieved()){
				// after endtime
				$teacherController->teacherCourseOverview();
			}else{
				//before endtime
				$teacherController->teacherStartpage();
			}
			return true;
	    }
		if($this->f3->get('SESSION.role') == 'ADMIN'){
			//$admin = new Admincontroller();
			$admin = new AdminController();
			$admin->panel();
			return true;
		}
	}
	
	function courseDescriptionAsModalDialog(){
		$course = new Course($this->db);
		$course->courseByID($this->f3->get('GET.courseID'));
		$course->courseRender('courseDescModal.html');
	}
	
	/**
	*	Returns all courses as array (array([0]=>array([courseID]=>..., [title]=>...,...), ...))
	*	@return array
	**/
	function allCourses(){
		return $this->db->exec('SELECT courseID, description, userID, title, last_modified, course.created_at, maxStudents, room, GROUP_CONCAT(" ",formOfAddress," ",lname) AS teacherList FROM course JOIN courseLeader USING(courseID) JOIN user USING(userID) GROUP BY courseID ORDER BY courseID ASC ');
	}
	
	/*
	 * Renders all courses as DataTable
	 * 
	 */
	function allCoursesTable(){
		$this->f3->set('courses',$this->allCourses());
		$this->f3->set('content','courseTable.html');
		$this->f3->set('showRooms',false);
		$this->mainTemplate();
	}
    
    /**
	*	Create a timestamp of current date and time. Format according 'dateformat' in config-file
	*	@return date
	**/
	static function timestamp(){
			return date(Base::instance()->get('dateformat'));
	}
	
	/**
	*	Create a DateTime of given date. Format according 'dateformat' in config-file
	*	@return DateTime
	*	@param $date string
	**/
	static function dateformat($date){
				return DateTime::createFromFormat(Base::instance()->get('dateformat'),$date);
	}
    
    /**
     * Tests, if the csrf-token from POST or GET matches SESSION.csrf.
     * Request method is autodetect.
     * 
     * @return boolean true if token match else false
     **/
    function csrfTokenOK(){
		if ($this->f3->VERB=='POST') {
			if ($this->f3->get('POST.token')==$this->f3->get('SESSION.csrf')){
				return true;
			}
		}
        if ($this->f3->VERB=='GET') {
			if ($this->f3->get('GET.token')==$this->f3->get('SESSION.csrf')){
				return true;
			}
		}
        $this->f3->set('SESSION.danger',$this->f3->get('lang.sessionTimeout'));
        return false;
    }
    
    /**
	*	Renders the main template. Clears all alerts (stored in session). 
	**/
	function mainTemplate(){
		echo \Template::instance()->render('template.html');
		$this->f3->set('SESSION.info','');
		$this->f3->set('SESSION.warning','');
		$this->f3->set('SESSION.danger','');
		$this->f3->set('SESSION.success','');
		$this->f3->set('SESSION.csvError','');
	}
	
/**
	*	Returns true, if current date is bigger then 'deadlineCourseSelection' (config-file)
	*	@return boolean
	**/
	function deadlineStudentsAchieved(){
		return (strtotime(self::timestamp())>strtotime($this->f3->get('deadlineCourseSelection')));
	}
	
	/**
	*	Tests, if the current user ist allowed to perform student tasks.
	*	@return boolean
	**/
	function studentOK(){
			$result = true;
			if($this->f3->get('SESSION.userID') == null){
					$result = false;
					$this->f3->set('SESSION.warning',$this->f3->get('lang.notLoggedIn'));
			}
			if($this->f3->get('SESSION.role') != 'STUDENT'){
					$result = false;
					$this->f3->set('SESSION.warning',$this->f3->get('lang.notAllowed'));
			}
			if($this->deadlineStudentsAchieved()){
					$result = false;
					$this->f3->set('SESSION.warning',$this->f3->get('lang.noEdit'));
			}
			return $result;
	}
	
	
	/**
	*	Tests, if the current user ist allowed to perform teacher tasks.
	*	@return boolean
	**/
	function teacherOK(){
			$result = true;
			if($this->f3->get('SESSION.userID') == null){
					$result = false;
					$this->f3->set('SESSION.warning',$this->f3->get('lang.notLoggedIn'));
			}
			if($this->f3->get('SESSION.role') != 'TEACHER'){
					$result = false;
					$this->f3->set('SESSION.warning',$this->f3->get('lang.notAllowed'));
			}
			if($this->deadlineTeacherAchieved()){
					$result = false;
					$this->f3->set('SESSION.warning',$this->f3->get('lang.noEdit'));
			}
			return $result;
	}
	
	/**
	*	Returns true, if current date is bigger then 'deadlineEditCourse' (config-file)
	*	@return boolean
	**/
	function deadlineTeacherAchieved(){
		return (strtotime(self::timestamp())>strtotime($this->f3->get('deadlineEditCourse')));
	}
	
	/*
	 * Returns true, if a user is logged in
	 * @return boolean
	 */
	 static function userLoggedIn(){
		return ($this->f3->get('SESSION.username') != NULL);
	 }

	/*
	 * Writes to logfile ($this->logger)
	*/
	function log($text){
		$this->logger->write($text);
	}
}
