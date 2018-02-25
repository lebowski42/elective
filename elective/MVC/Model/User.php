<?php

class User extends DB\SQL\Mapper{


    public function __construct(DB\SQL $db) {
        parent::__construct($db,'user');
    }

	protected $selections;

    public function all() {
        $this->load();
        return $this->query;
    }

    public function getById($id) {
        $this->load(array('userID=?',$id));
		$this->getSelections($this->userID);
        return $this->query;
    }

    public function getByName($name) {
        $this->load(array('username=?', $name));
		$this->getSelections($this->userID);
    }

    public function add() {
        $this->copyFrom('POST',function($val) {
			// Somebody forges the form?
    		return array_intersect_key($val, array_flip(array('userID','username','password','fname','lname','formOfAddress','class','role','email')));
		});
		$this->password = password_hash($this->password,PASSWORD_DEFAULT);
        $this->save();
    }
    
    public function changePassword($password){
			$this->password=password_hash($password,PASSWORD_DEFAULT);
			$this->update();
	}

    public function edit($id) {
        $this->load(array('userID=?',$id));
        $this->copyFrom('POST',function($val) {
			// Somebody forges the form?
    		return array_intersect_key($val, array_flip(array('username','password','fname','lname','formOfAddress','class','role','email')));
		});
		//Should password be changed
		if(empty($this->password)){
			$result = $this->db->exec('SELECT password FROM user WHERE userID = ?', $id);
			$this->password = $result[0]['password'];
		}else{
			$this->password = password_hash($this->password,PASSWORD_DEFAULT);
		}
        $this->update();
    }

    public function delete($id) {
        $this->load(array('userID=?',$id));
        $this->erase();
		$this->db->exec('DELETE FROM `selection` WHERE `selection`.`userID` = ?',$id);
		$this->db->exec('DELETE FROM `courseLeader` WHERE `courseLeader`.`userID` = ?',$id);
    }

	function getSelections($id){
			$this->selections= $this->db->exec('SELECT courseID, title, priority FROM course JOIN selection USING(courseID) WHERE userID = ? ORDER BY priority ASC', $id);
	}
}
