<?php

class CourseListCalculator extends Threaded
{
	protected $f3;
	protected $db;
	protected $studentSelection;
	
    public function __construct($db)
    {
        $f3=Base::instance();
        $this->f3=$f3;
        $this->db=$db;
        
        $result = $this->db->exec('SELECT `userID`, `courseID` FROM `selection` ORDER BY userID ASC ');
        /*foreach($result as $userID=>$courseID){
			$selection[$userID]=array_push
		}*/
		var_dump($result);
    }

    public function run()
    {
        echo microtime(true).PHP_EOL;

        $this->worker->addData(
            file_get_contents('http://google.fr?q='.$this->query)
        );
    }
}
