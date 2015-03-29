<?php
/**
 * MemcacheÀà
 * @author Ç®Ö¾Î° 2012-10-10
 */
class ZOL_Memcache {
    private $server = '';
    private $port = '';
    
    public function __construct($server, $port=11211) {
        $this->server = $server;
        $this->port = $port;
    }
    
    private function sendMemcacheCommands($command){
        $result = array();
        
        $server = $this->server;
        $port = $this->port;
        
        $result[$server] = $this->sendMemcacheCommand($server,$port,$command);
        return $result;
    }
    private function sendMemcacheCommand($command){

        $s = @fsockopen($this->server,$this->port);
        if (!$s){
            die("Cant connect to:".$this->server.':'.$this->port);
        }

        fwrite($s, $command."\r\n");

        $buf='';
        while ((!feof($s))) {
            $buf .= fgets($s, 256);
            if (strpos($buf,"END\r\n")!==false){ // stat says end
                break;
            }
            if (strpos($buf,"DELETED\r\n")!==false || strpos($buf,"NOT_FOUND\r\n")!==false){ // delete says these
                break;
            }
            if (strpos($buf,"OK\r\n")!==false){ // flush_all says ok
                break;
            }
            if (strpos($buf, "STORED\r\n")!==false){ // store ok
                break;
            }
            if (strpos($buf, "ERROR\r\n")!=false) { //error
                break;
            }
        }
        fclose($s);
        return $this->parseMemcacheResults($buf);
    }
    
    public function set($key, $value, $expire=0){
        $bytes = strlen($value);
        $cmd = "set {$key} 1 {$expire} {$bytes}\r\n{$value}";
        return $this->sendMemcacheCommand($cmd);
    }
    
    public function get($key){
        $cmd = "get {$key}";
        return $this->sendMemcacheCommand($cmd);
    }
    
    private function parseMemcacheResults($str){

        $res = array();
        $str = rtrim($str, "\r\n");
        $lines = explode("\r\n",$str);
        $cnt = count($lines);
        
        if(end($lines) == 'END'){
            if ($cnt==3) return $lines[1];
            else {
                array_pop($lines);
                return $lines;
            }
        }
        if(end($lines) == 'STORED' || end($lines) == 'DELETED') {
            return true;
        }
        if(end($lines) == 'NOT_FOUND') {
            return false;
        }
        return false;
    }
    
    public function getStats($key=''){
        $resp = $this->sendMemcacheCommand('stats');
        $res = array();
        foreach($resp as $stat) {
            $arr = explode(' ', $stat);
            $res[$arr[1]] = $arr[2];
        }
        return $key ? (isset($res[$key]) ? $res[$key] : '') : $res;
    }
    
    public function getSlabs(){
        $resp = $this->sendMemcacheCommand('stats slabs');
        $res = array();
        if ($resp) {
            foreach ($resp as $row) {
                $arr = explode(' ', $row);
                if(!preg_match('/^\d+:\w+$/', $arr[1])) { continue; }
                list($slabId, $paramName) = explode(':', $arr[1]);
                $res[$paramName][$slabId] = trim($arr[2]);
            }
        }
        return $res;
    }
}