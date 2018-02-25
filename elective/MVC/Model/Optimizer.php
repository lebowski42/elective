<?php
class Optimizer extends Worker
{
    public $data = [];

    public function run()
    {
        echo 'Running '.$this->getStacked().' jobs'.PHP_EOL;
    }

    /**
     * To avoid corrupting the array
     * we use array_merge here instead of just
     * $this->data[] = $html
     */
    public function addData($data)
    {
        $this->data = array_merge($this->data, [$data]);
    }
}
