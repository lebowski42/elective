<?php

// Represents a Course

class Course{
	private $courseID;
	public $courseTitle;
	public $courseDescription;
	public $courseNote;
	public $teachers = array();
	public $last_modified;
	public $created_at;
	public $maxStudents;
	public $maxStudentsFix;
	public $room;

	private $db;
	private $f3;

	function __construct($db){
		$this->db = $db;
		$f3=Base::instance();
        $this->f3=$f3;
	}
	
	public function getCourseID(){
			return $this->courseID;
	}
	
	// Get course from db using the courseID
	public function courseByID($id){
		$result = $this->db->exec('SELECT courseID, userID, description, title, last_modified, course.created_at, maxStudents, lname, fname, formOfAddress, room FROM course LEFT JOIN courseLeader USING(courseID) LEFT JOIN user USING(userID) WHERE courseID=? ORDER BY last_modified DESC',$id);
		$this->courseID = $id;
		if(!empty($result)){
			$this->courseTitle = $result[0]['title'];
			$this->courseDescription = $result[0]['description'];
			$this->last_modified = $result[0]['last_modified'];
			$this->created_at = $result[0]['created_at'];
			$this->maxStudents = $result[0]['maxStudents'];
			$this->room = $result[0]['room'];
			foreach($result as $teacher){
					array_push($this->teachers, array("teacherID"=>$teacher['userID'], "teacherFormOfAddress"=>$teacher['formOfAddress'], "teacherLName"=>$teacher['lname'], "teacherFName"=>$teacher['fname']));
			}
			return true;
		}else{
			return false;
		}
	}

	public function courseRender($view){
		$course = $this->courseToArray();
		$this->f3->set('course',$course);
		echo \Template::instance()->render($view);
	}

	public function courseFromPost(){
			$this->courseID = $this->f3->get('POST.courseID');
			$this->courseTitle = $this->f3->get('POST.courseTitle');
			$this->courseDescription = $this->f3->get('POST.courseDescription');
			$this->courseNote = $this->f3->get('POST.courseNote');
			$this->room=$this->f3->get('POST.room');
			if(null!==$this->f3->get('POST.maxStudents')){
				$this->maxStudents=$this->f3->get('POST.maxStudents');
			}else{
				$this->maxStudents=-1;
			}
			array_push($this->teachers, array("teacherID"=>$this->f3->get('POST.userID')));
			if(null!==$this->f3->get('POST.selected-teacher')){
				foreach($this->f3->get('POST.selected-teacher') as $teacher){
						array_push($this->teachers, array("teacherID"=>$teacher));
				}
			}
	}

	
	public function saveCourseToDatabase(){
		//$values = array(date('Y-m-d G:i:s'),$this->courseDescription,$this->courseNote,$this->maxStudents,$this->courseID);
		$new = true;
		if($this->courseID !=null){
			// better check twice
			$result = $this->db->exec('SELECT * FROM course WHERE courseID =?',$this->courseID);
			if(!empty($result)){
					$new = false;
			}
		}
		$timestamp = Controller::timestamp();
		$values = array($this->courseTitle,$timestamp, $this->courseDescription, $this->courseNote, $this->room, $this->maxStudents);
		if($new){
			$this->db->exec('INSERT INTO `course` (`courseID`, `title`, `created_at`, `last_modified`, `description`, `note`, `room`, `maxStudents`) VALUES (NULL, ?, CURRENT_TIMESTAMP, ?, ?, ?, ?, ?)',$values);
			$this->courseID =  $this->db->lastInsertId();
		}else{
			array_push($values,$this->courseID);
			$this->db->exec('UPDATE `course` SET `title`=?, `last_modified` = ?, `description` = ?, `note` = ?,`room` = ?, `maxStudents` = ? WHERE `course`.`courseID` = ?',$values);
		}
		// Add into table courseLeaders
		//First delete all for this courseID
		$this->db->exec('DELETE FROM `courseLeader` WHERE courseID=?',$this->courseID);
		//$this->db->begin();
		foreach($this->teachers as $teacher){
				$this->db->exec('INSERT INTO `courseLeader` (`userID`, `courseID`, `add_at`) VALUES (?, ?, ?)',array($teacher['teacherID'],$this->courseID,$timestamp));
		}
		//$this->db->commit();
	}
	
	function courseToArray(){
		$tmp ='';
		foreach($this->teachers as $teacher){
				$tmp .= " ".$teacher['teacherFormOfAddress']." ".$teacher['teacherLName'].",";
		}
		$tmp = substr($tmp, 0, -1);
		$course = array(
				'courseID'=> $this->courseID,
				'courseTitle'=>$this->courseTitle,
				'courseDescription'=>$this->courseDescription,
				'courseNote'=>$this->courseNote,
				'teachers'=>$tmp,
				'room'=>$this->room,
				'maxStudents'=>$this->maxStudents,
				'last_modified'=>$this->last_modified,
				'created_at'=>$this->created_at,
		);
		return $course;	
	}
	
	public function delete($id){
		$this->db->begin();
		$this->db->exec('DELETE FROM `selection` WHERE `selection`.`courseID` = ?',$id);
		$this->db->exec('DELETE FROM `courseLeader` WHERE `courseLeader`.`courseID` = ?',$id);
		$this->db->exec('DELETE FROM `course` WHERE `course`.`courseID` = ?',$id);
		$this->db->commit();
	}
	
	public function ignore($id){
		$this->db->exec('UPDATE `course` SET `ignored`=1 WHERE courseID = ?',$id);
	}
	
	public function activate($id){
		$this->db->exec('UPDATE `course` SET `ignored`=0 WHERE courseID = ?',$id);
	}
	
	public function maxStudents($id, $number){
		$values = array($number,$id);
		$this->db->exec('UPDATE `course` SET `maxStudents`=? WHERE courseID = ?',$values);
	}
	
	
	
}
