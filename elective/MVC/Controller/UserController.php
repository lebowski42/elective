<?php

class UserController extends Controller{
	
    function login(){
		$this->f3->set('content','login.html');
		$this->mainTemplate();
    }

	function logout(){
		 if($this->f3->get('SESSION.username') !== null ) {
			$this->f3->clear('SESSION');
			$this->f3->set('alert.info',$this->f3->get('loggedout'));
		}
		$this->f3->reroute('/');
	}



    function beforeroute(){
    }

    function authenticate() {

        $username = $this->f3->get('POST.username');
        $password = $this->f3->get('POST.password');

        $user = new User($this->db);
        $user->getByName($username);

        if($user->dry()) {
            $this->f3->reroute('/login');
        }
		$this->user = $user;

        if(password_verify($password, $user->password)) {
			$this->f3->set('SESSION.userID', $user->userID);            
			$this->f3->set('SESSION.username', $user->username);
			$this->f3->set('SESSION.role', $user->role);
			$this->f3->set('SESSION.fname', $user->fname);
			$this->f3->set('SESSION.lname', $user->lname);
			$this->f3->set('SESSION.email', $user->email);
			$this->f3->set('SESSION.class', $user->class);
			$this->f3->set('SESSION.csrf',uniqid('', true));
            $this->f3->reroute('/');
        } else {
			$this->f3->set('SESSION.warning',$this->f3->get('lang.passwordWrong'));
            $this->f3->reroute('/login');
        }
    }
    
    /**
     * Renders the userPanel
     **/
    function userPanel(){
		if($this->userOK()){
			$this->f3->set('content','userPanel.html');
			$this->mainTemplate();
		}else{
			$this->f3->reroute('/login');
		}
	}
	
	/**
	 * Performs password change
	 **/
	function changePassword(){
		if($this->userOK() && $this->csrfTokenOK()){
			if($this->f3->get('POST.password') != $this->f3->get('POST.passwordAgain')){
				$this->f3->set('SESSION.warning',$this->f3->get('lang.passwordNoMatch'));
			}else{
				$user = new User($this->db);
				$user->getById($this->f3->get('SESSION.userID'));
				if(password_verify($this->f3->get('POST.oldPassword'), $user->password)){
					$user->changePassword($this->f3->get('POST.password'));
					$this->f3->set('SESSION.info',$this->f3->get('lang.passwordChanged'));
				}else{
					$this->f3->set('SESSION.warning',$this->f3->get('lang.passwordWrong'));
				}
			}
		}
		$this->f3->reroute('/userPanel');
	}
     
    /**
	*	Checks, if the current user ist allowed to perform task.
	*	@return boolean
	**/
	function userOK(){
		$result = true;
		if($this->f3->get('SESSION.userID') == null){
			$result = false;
			$this->f3->set('SESSION.warning',$this->f3->get('lang.notLoggedIn'));
		}
		return $result;
	}
}
