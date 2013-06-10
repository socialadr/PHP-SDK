<?php

/* The error handling/logging class for the SocialAdr API SDK */

class SocialAdrErrors {
    
    public $levels = array(
        'FATAL' => 1,
        'WARN' => 2,
        'DEBUG' => 3
    );
    
    public $errors = array(
        'APP_ID_REQUIRED' => array(
            'message' => 'App ID must be set with SocialAdrAPI->setAppId() or in API object instantiation'
        )
    );
    
    public $errorHistory = array();
    
    public function level($level){
        return $this->levels[$level];
    }
    
    public function error($error){
        return (object)$this->errors[$error];
    }
    
    public function logError($level, $error){
            $trace = debug_backtrace(null, 7);
            array_splice($trace, 0, 2);
            $logEntry = new stdClass();
            $logEntry->level = $this->level($level);
            $logEntry->error = $this->error($error);
            $logEntry->trace = $trace;
            $this->errorHistory[] = $logEntry;
    }
    
    public function outputLog($response = 'text'){
        foreach($this->errorHistory as $key => $logEntry){
            switch($response){
                case 'text' :
                    echo "[Error $key] " . $logEntry->error->message . "\r\n";
                    echo "Error Trace:\r\n";
                    print_r($logEntry->trace);
                    break;
                case 'html' :
                    echo "<div><h3>[Error $key] " . $logEntry->error->message . "</h3></div>";
                    echo '<div><b>Error Trace:</b><pre>'.print_r($logEntry->trace, true).'</pre></div>';
                    break;
            }
        }
        if($response == 'object'){
            return $this->errorHistory;
        }
    }
}
?>
