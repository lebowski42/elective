<?php

class StudentController extends UserController {
	
	 /**
     * Renders studentSelect. get information from database
     **/
    function studentsCourseSelection() {
			// Get all available courses
			$this->f3->set('courses',$this->db->exec('SELECT courseID, title FROM course WHERE course.courseID NOT IN (SELECT selection.courseID FROM selection WHERE selection.userID = ?) AND course.ignored = 0 ORDER BY title ASC', $this->f3->get('SESSION.userID')));
			$this->f3->set('scourses',$this->getSelection());
			$this->f3->set('content','student/selectCourse.html');
			$this->mainTemplate();
    }

	/**
	*	Writes the students course selection to database (see studentSelect.html-template)
	**/
	function selectionToDatabase($id){
		if(($this->studentOK() || $this->adminOK()) && $this->csrfTokenOK()){// Check if allowed to store selection
			if(!isset($id)) $id=$this->f3->get('SESSION.userID');
			$max = $this->f3->get('maxNumberOfCoursesToChoose');
			$min = $this->f3->get('minNumberOfCoursesToChoose');
			$len = count($this->f3->get('POST.selectedCourses'));
			if($len>=$min && $len<=$max){
				$this->db->exec('DELETE FROM selection WHERE selection.userID = ?',$id);
				// Build inserts
				$inserts = "";
				$priority=0;
				foreach ($this->f3->get('POST.selectedCourses') as $item){
					$priority++;
					if($priority>1) $inserts =$inserts.","; // we need a comma
					$inserts = $inserts."('".$id."','".$item."','".$this->timestamp()."','".$priority."', '0')";
				}
				
				$this->db->exec('INSERT INTO `selection` (`userID`, `courseID`, `last_save`, `priority`, `fix`) VALUES '.$inserts);
				$this->f3->set('SESSION.info',$this->f3->get('lang.selectionSaved'));
				$this->log("Student ".($user->username)." new selection:".implode(',',$inserts));
			}else{
				$this->f3->set('SESSION.warning',$this->f3->get('lang.alertCourseNumber',array($min,$max))." ".$len);
			}	
		}
		$this->f3->reroute('/');
	}
	/*
	 * Renders page if studend deadline achived
	 * 
	 */
	function selectionEndOfProcess(){
		$selection = $this->getSelection();
		$scourses = array();
		foreach($selection as $select){
			$course = new Course($this->db);
			$course->courseByID($select['courseID']);
			array_push($scourses,$course->courseToArray());
		}
		$this->f3->set('courses',$scourses);
		$this->f3->set('content','student/coursesList.html');;
		$this->f3->set('info',$this->f3->get('lang.selectionProcessEnd',$this->f3->get('deadlineCourseSelection')));
		$this->mainTemplate();
	}
	
	 /**
     * Returns the course selection for current user (SESSION.userID). 
     * @return: array
     **/
    function getSelection(){
			return $this->db->exec('SELECT courseID, title, priority FROM course JOIN selection USING(courseID) WHERE userID = ? AND course.ignored = 0 ORDER BY priority ASC', $this->f3->get('SESSION.userID'));
	}
	
	

}
