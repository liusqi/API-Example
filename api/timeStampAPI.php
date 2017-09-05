<?php

class TimeStampAPI extends EndPoint {
    
    public function execute(){
        return array('Timestamp' => time ());
    }

}