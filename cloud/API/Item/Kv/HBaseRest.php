<?php
/**
* Hbase的Rest访问形式
* @author zhongwt
* @copyright (c) 2013-03-05
*/
class API_Item_Kv_HBaseRest
{
    /**
     * 获得连接对象
     */
    private static function getObj(){
        $hbase = new PopHbase(array(
            "host" => "10.15.187.61",
            "port" => "60060",
        ));

        return $hbase;
    }


    public static function test(){
        $hbase = new PopHbase(array(
            "host" => "10.15.187.61",
            "port" => "60060",
        ));

        return "OK";
        #return $hbase->getStatusCluster();
    }

    /**
     * 获得所有的数据表名称
     */
    public static function getAllTables(){
        $hbase = self::getObj();
        return $hbase->tables->names;
    }

    /**
     * 创建表
     * VERSIONS
     */
    public static function createTable(){
        $hbase = self::getObj();

        $tables = $hbase->tables;
        $tables->create(
            array("name"=>"zwt_table2"),
            array("name"=>"my_column_family_1","VERSIONS"=>20)
        );
        return true;
    }
    public static function put(){
        $hbase = self::getObj();

		$row = $hbase->tables->zwt_table2->row('row_test_count_1');
		$row->put('my_column_family_1:my_column',"中文测试斯密大".time());
        return true;
    }
    public static function get(){
        $hbase = self::getObj();

		$row = $hbase->tables->zwt_table2->row('row_test_count_1');
		return $row->getAll('my_column_family_1:my_column',200);
        #return true;
    }

}




/**
 * https://github.com/pop/pop_hbase/
 */
class PopHbase{

	public $options;

	public function __construct($options=array()){
		if(empty($options['connection'])){
			$options['connection'] = 'PopHbaseConnectionCurl';
		}else if (class_exists('PopHbaseConnection'.ucfirst($options['connection']))){
			$options['connection'] = 'PopHbaseConnection'.ucfirst($options['connection']);
		}else if (!class_exists($options['connection'])){
			throw new Exception('Invalid connection class: "'.$options['connection'].'"');
		}
		$this->options = $options;
	}

	/**
	 * Detruct current instance, its potential circular references and close the HBase connection if opened.
	 */
	public function __destruct(){

	}

	public function __get($property){
		switch($property){
			case 'connection':
				return $this->connection = new $this->options['connection']($this->options);
				break;
			case 'tables':
				return $this->getTables();
				break;
			case 'request':
				return new PopHbaseRequest($this);
				break;
		}
	}

	public function __call($method,$args){
		switch($method){
			case 'tables':
				return call_user_func_array(array($this,'getTables'),$args);
		}
	}

	/**
	 * Shortcut to the PHP Magick destruct method.
	 */
	public function destruct(){

	}

	/**
	 * Retrieve the list of all databases in HBase.
	 */
	public function getTables(){
		if(isset($this->tables)) return $this->tables;
		return $this->tables = new PopHbaseTables($this);
	}

	/**
	 * Return the Stargate connection version information
	 */
	public function getVersion(){
		return $this->request->get('version')->getBody();
	}

	/**
	 * Return the Stargate version cluster information
	 */
	public function getVersionCluster(){
		return $this->request->get('version/cluster')->getBody();
	}

	/**
	 * Return the Stargate satus cluster information
	 */
	public function getStatusCluster(){
		return $this->request->get('status/cluster')->getBody();
	}


	/**
	 * Shortcut to the "PopHbaseTables->table" method.
	 *
	 * See "PopHbaseTables->table" for usage information.
	 *
	 */
	public function table($table){
		return $this->tables->table($table);
	}

	/**
	 * Shortcut to the "PopHbaseTables->create" method.
	 *
	 * See "PopHbaseTables->create" for usage information.
	 *
	 * @param $table string Name of the table to create
	 * @param $column string Name of the column family to create
	 * @return PopHbase Current instance
	 */
	public function create($table){
		return call_user_func_array(array($this,'tables'),$args)->hbase;
	}

	/**
	 * Shortcut to the "PopHbaseTables->delete" method.
	 *
	 * See "PopHbaseTables->delete" for usage information.
	 *
	 * @param $table string Name of the table to delete
	 * @return PopHbase Current instance
	 */
	public function delete($table){
		return $this->tables->delete($table)->hbase;
	}

}


interface PopHbaseConnection{

	/**
	 * Connection constructor with its related information.
	 *
	 * Options may include:
	 * -   *host*
	 *	 Hbase server host, default to "localhost"
	 * -   *port*
	 *	 Hbase server port, default to "5984"
	 *
	 * @param $options
	 * @return unknown_type
	 */
	public function __construct(array $options = array());

	/**
	 * Send HTTP REST command.
	 *
	 * @return PopHbaseResponse Response object parsing the HTTP HBase response.
	 */
	public function execute($method,$url,$data=null,$raw=false);

}
class PopHbaseConnectionCurl implements PopHbaseConnection{

	public $options;

	/**
	 * Connection constructor with its related information.
	 *
	 * Options may include:
	 * -   *host*
	 *     Hbase server host, default to "localhost"
	 * -   *port*
	 *     Hbase server port, default to "8080"
	 *
	 * Accorging to Stargate API:
	 *     ./bin/hbase org.apache.hadoop.hbase.stargate.Main -p <port>
	 *     ./bin/hbase-daemon.sh start org.apache.hadoop.hbase.stargate.Main -p <port>
	 * Where <port> is optional, and is the port the connector should listen on. (Default is 8080.)
	 *
	 * @param $options array Connection information
	 * @return null
	 */
	public function __construct(array $options = array()){
		if(!isset($options['host'])){
			$options['host'] = 'localhost';
		}
		if(!isset($options['port'])){
			$options['port'] = 8080;
		}
		if(!isset($options['alive'])){
			$options['alive'] = 1;
		}
		// todo, this suggest that password may be empty like with MySql, need to check this
		if(!empty($this->options['username'])&&!isset($this->options['password'])){
			$this->options['password'] = '';
		}
		$this->options = $options;
	}

	/**
	 * Magick method used to retrieve connected related information
	 */
	public function __get($property){
		switch($property){
			default:
				return $this->options[$property];
		}
	}

	/**
	 * Send HTTP REST command.
	 *
	 * @return PopHbaseResponse Response object parsing the HTTP HBase response.
	 */
	public function execute($method,$url,$data=null,$raw=false) {
		$url = (substr($url, 0, 1) == '/' ? $url : '/'.$url);
		if(is_array($data)){
			$data = json_encode($data);
		}
		$curl = curl_init();
        echo 'http://'.$this->options['host'].':'.$this->options['port'].$url . "\n";
		curl_setopt($curl, CURLOPT_URL, 'http://'.$this->options['host'].':'.$this->options['port'].$url);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json',
			'Connection: ' . ( $this->options['alive'] ? 'Keep-Alive' : 'Close' ),
		));
		curl_setopt($curl, CURLOPT_VERBOSE, !empty($this->options['verbose']));
		switch(strtoupper($method)){
			case 'DELETE':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
			case 'PUT':
				curl_setopt($curl, CURLOPT_PUT, 1);
				$file = tmpfile();
				fwrite($file, $data);
				fseek($file, 0);
                var_dump($data);
				curl_setopt($curl, CURLOPT_INFILE, $file);
				curl_setopt($curl, CURLOPT_INFILESIZE, strlen($data));
				break;
			case 'POST':
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				break;
			case 'GET':
				curl_setopt($curl, CURLOPT_HTTPGET, 1);
				break;
		}

		$data = curl_exec($curl);
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		list($_headers,$body) = explode("\r\n\r\n", $data, 2);
		$_headers = explode("\r\n",$_headers);
		$headers = array();
		foreach($_headers as $_header){
			if ( preg_match( '(^HTTP/(?P<version>\d+\.\d+)\s+(?P<status>\d+))S', $_header, $matches ) ) {
				$headers['version'] = $matches['version'];
				$headers['status']  = (int) $matches['status'];
			} else {
				list( $key, $value ) = explode( ':', $_header, 2 );
				$headers[strtolower($key)] = ltrim( $value );
			}
		}

		curl_close($curl);
		switch(strtoupper($method)){
			case 'PUT':
				fclose($file);
				break;
		}
		return new PopHbaseResponse($headers,$body,$raw);
	}

}

class PopHbaseConnectionSock implements PopHbaseConnection{

	public $options;

	/**
	 * Connection constructor with its related information.
	 *
	 * Options may include:
	 * -   *host*
	 *     Hbase server host, default to "localhost"
	 * -   *port*
	 *     Hbase server port, default to "8080"
	 *
	 * Accorging to Stargate API:
	 *     ./bin/hbase org.apache.hadoop.hbase.stargate.Main -p <port>
	 *     ./bin/hbase-daemon.sh start org.apache.hadoop.hbase.stargate.Main -p <port>
	 * Where <port> is optional, and is the port the connector should listen on. (Default is 8080.)
	 *
	 * @param $options array Connection information
	 * @return null
	 */
	public function __construct(array $options = array()){
		if(!isset($options['host'])){
			$options['host'] = 'localhost';
		}
		if(!isset($options['port'])){
			$options['port'] = 8080;
		}
		if(!isset($options['alive'])){
			$options['alive'] = 1;
		}
		// todo, this suggest that password may be empty like with MySql, need to check this
		if(!empty($this->options['username'])&&!isset($this->options['password'])){
			$this->options['password'] = '';
		}
		$this->options = $options;
	}

	public function __destruct(){
		if(isset($this->options['sock'])){
			$this->disconnect();
		}
	}

	/**
	 * Magick method used to retrieve connected related information
	 */
	public function __get($property){
		switch($property){
			case 'sock':
				if(!isset($this->options['sock'])){
					$this->connect();
				}
			default:
				return $this->options[$property];
		}
	}

	/**
	 * Open the connection to the HBase server.
	 *
	 * @return PopHbaseConnection Current connection instance
	 */
	public function connect() {
		if(isset($this->options['sock'])){
			$this->disconnect();
		}
		$this->options['sock'] = fsockopen($this->options['host'], $this->options['port'], $errNum, $errString);
		if(!$this->options['sock']) {
			throw new PopHbaseException('Failed connecting to '.(!empty($this->options['username'])?$this->options['username'].'@':'').$this->options['host'].':'.$this->options['port'].' ('.$errString.')');
		}
		return $this;
	}

	/**
	 * Destruct the current instance and close its connection if opened.
	 */
	public function destruct() {
		$this->__destruct();
	}

	/**
	 * Close the connection to the HBase server.
	 *
	 * @return PopHbaseConnection Current connection instance
	 */
	public function disconnect() {
		if(!isset($this->options['sock'])){
			// nothing to do
			return $this;
		}
		fclose($this->options['sock']);
		unset($this->options['sock']);
		return $this;
	}

	/**
	 * Send HTTP REST command.
	 *
	 * @return PopHbaseResponse Response object parsing the HTTP HBase response.
	 */
	public function execute($method,$url,$data=null,$raw=false) {
		$url = (substr($url, 0, 1) == '/' ? $url : '/'.$url);
		$request = $method.' '.$url.' HTTP/1.1'."\r\n";
		$request .= 'Host: '.$this->options['host'].':'.$this->options['port']."\r\n";
		// Add authentication only if username is provided and not empty
		if(!empty($this->options['username'])){
			$request .= 'Authorization: Basic '.base64_encode($this->options['username'].':'.$this->options['password'])."\r\n";
		}
		$request .= 'Accept: application/json'."\r\n";
		// Set keep-alive header, which helps to keep to connection
		// initilization costs low, especially when the database server is not
		// available in the locale net.
		$request .= 'Connection: ' . ( $this->options['alive'] ? 'Keep-Alive' : 'Close' ) . "\r\n";
		// Add request data and related headers if needed
		// otherwise add closing mark of the request header section.
		if($data) {
			if(is_array($data)){
				$data = json_encode($data);
			}
			$request .= 'Content-Length: '.strlen($data)."\r\n";
			$request .= 'Content-Type: application/json'."\r\n\r\n";
			$request .= $data."\r\n";
		} else {
			$request .= "\r\n";
		}
//		echo $request."\n\n";
		fwrite($this->sock, $request);
//		$response = '';
//		while(!feof($this->sock)) {
//			$response .= fgets($this->sock);
//		}

		$raw = '';
		$headers = '';
		$body = '';
		// Extract HTTP response headers
		while ( ( ( $line = fgets( $this->sock ) ) !== false ) &&
				   ( ( $line = rtrim( $line ) ) !== '' ) ){
			// Extract HTTP version and response code
			// as well as other headers
			if ( preg_match( '(^HTTP/(?P<version>\d+\.\d+)\s+(?P<status>\d+))S', $line, $matches ) ) {
				$headers['version'] = $matches['version'];
				$headers['status']  = (int) $matches['status'];
			} else {
				list( $key, $value ) = explode( ':', $line, 2 );
				$headers[strtolower( $key )] = ltrim( $value );
			}
		}
		// Extract HTTP response body
		$body = '';
		if ( !isset( $headers['transfer-encoding'] ) ||
			 ( $headers['transfer-encoding'] !== 'chunked' ) ) {
			// HTTP 1.1 supports chunked transfer encoding, if the according
			// header is not set, just read the specified amount of bytes.
			$bytesToRead = (int) ( isset( $headers['content-length'] ) ? $headers['content-length'] : 0 );
			// Read body only as specified by chunk sizes, everything else
			// are just footnotes, which are not relevant for us.
			if($this->options['alive']||$bytesToRead){
				while ( $bytesToRead > 0 ) {
					$body .= $read = fgets( $this->sock, $bytesToRead + 1 );
					$bytesToRead -= strlen( $read );
				}
			}else{
				while ( ( ( $line = fgets( $this->sock, 1024 ) ) !== false ) ){
					$body .= $line;
				}
			}
		} else {
			// When transfer-encoding=chunked has been specified in the
			// response headers, read all chunks and sum them up to the body,
			// until the server has finished. Ignore all additional HTTP
			// options after that.
			while ($chunk_length = hexdec(fgets($this->sock))){
				$responseContentChunk = '';
				$read_length = 0;
				while ($read_length < $chunk_length){
					$responseContentChunk .= fread($this->sock, $chunk_length - $read_length);
					$read_length = strlen($responseContentChunk);
				}
				$body.= $responseContentChunk;
				fgets($this->sock);
			}
		}
        // Reset the connection if the server asks for it.
        if ( isset($headers['connection']) && $headers['connection'] !== 'Keep-Alive' ) {
            $this->disconnect();
        }
        //$this->disconnect();
//        echo '------------------------'."\n";
//		print_r($headers);
//        echo '------------------------'."\n";
//		print_r($body);
//        echo '------------------------'."\n";
		return new PopHbaseResponse($headers,$body,$raw);
	}

}

class PopHbaseException extends Exception{}

abstract class PopHbaseIterator implements Countable, Iterator{


	//public $data = array();

	/**
	 * Implement the "current" method of the PHP native Iterator interface.
	 *
	 * @return mixed Returns current value.
	 */
    public function current(){
    	$this->load();
    	return current($this->__data['data']);
    }

	/**
	 * Return the key of the current element.
	 *
	 * Implement the "key" method of the PHP native Iterator interface.
	 *
	 * @return mixed Returns scalar on success, integer 0 on failure.
	 */
	public function key(){
    	$this->load();
		return key($this->__data['data']);
	}

	/**
	 * Move forward to next element.
	 *
	 * Implement the "next" method of the PHP native Iterator interface.
	 *
	 * @return mixed Returns next value.
	 */
    public function next(){
    	$this->load();
    	return next($this->__data['data']);
    }

	/**
	 * Move backward to previous element.
	 *
	 * Note, this method is not part the PHP navite Iterator interface.
	 *
	 * @return mixed Returns previous value.
	 */
    public function prev(){
    	$this->load();
    	return prev($this->__data['data']);
    }

	/**
	 * Rewind the Iterator to the first element.
	 *
	 * Implement the "rewind" method of the PHP native Iterator interface.
	 *
	 * @return mixed The value of the first array element, or FALSE if the array is empty.
	 */
    public function rewind(){
    	$this->load();
    	return reset($this->__data['data']);
    }

	/**
	 * Checks if current position is valid.
	 *
	 * Implement the "valid" method of the PHP native Iterator interface.
	 *
	 * @return boolean Wether the iterated array is in a valid state or not.
	 */
    public function valid(){
    	return (bool) $this->current();
    }

	/**
	 * Count the number of stored elements.
	 *
	 * Implement the "count" method of the PHP native Countable interface.
	 *
	 * return integer Number of stored elements.
	 */
	public function count(){
    	$this->load();
		return count($this->__data['data']);
	}

}

class PopHbaseRequest{

	public $hbase;

	/**
	 * Request constructor.
	 *
	 * @param PopHbase required $hbase instance
	 */
	function __construct(PopHbase $hbase){
		$this->hbase = $hbase;
	}

	/**
	 * Create a DELETE HTTP request.
	 *
	 * @return PopHbaseResponse Response object
	 */
	public function delete($command){
		return $this->hbase->connection->execute('DELETE',$command);
	}

	/**
	 * Create a GET HTTP request.
	 *
	 * @return PopHbaseResponse Response object
	 */
	public function get($command){
		return $this->hbase->connection->execute('GET',$command);
	}

	/**
	 * Create a POST HTTP request.
	 *
	 * @return PopHbaseResponse Response object
	 */
	public function post($command,$data){
		return $this->hbase->connection->execute('POST',$command,$data);
	}

	/**
	 * Create a PUT HTTP request.
	 *
	 * @return PopHbaseResponse Response object
	 */
	public function put($command,$data=null){
		return $this->hbase->connection->execute('PUT',$command,$data);
	}

}class PopHbaseResponse{

	public function __construct($headers,$body,$raw=false) {
		$this->headers = $headers;
		$this->body = $raw?$body:json_decode($body,true);
		$this->raw = $raw;
//		preg_match('/^(\d{3})/',trim($this->headers['status']),$matches);
//		echo $this->headers['status'].' - '.$matches[1]."\n";
//		$status = $matches[1];
//		switch($status){
//			case '500':
//				throw new PopHbaseException(constant('PurHTTP::CODE_'.$status));
//		}
	}

//	public function __get($property){
//		switch($property){
//			case 'body':
//				return $this->getBody();
//		}
//	}

	public function __call($method,$args) {
		switch($method){
			case 'body':
				return call_user_func_array(array($this,'getBody'),$args);
		}
	}

	public function getRaw() {
		return $this->raw;
	}

    function getHeaders() {
        return $this->headers;
    }

    function getBody() {
		return $this->body;
    }

}

class PopHbaseRow{

	public $hbase;
	public $key;

	/**
	 * Contruct a new row instance.
	 *
	 * The identified row does not have to be yet persisted in HBase, it
	 * will automatically be created if not yet present.
	 *
	 * @param PopHbase $hbase
	 * @param string $table
	 * @param string $key
	 */
	public function __construct(PopHbase $hbase,$table,$key){
		$this->hbase = $hbase;
		$this->table = $table;
		$this->key = $key;
	}

	public function __get($column){
		return $this->get($column);
	}

	/**
	 * Deletes an entire row, a entire column family, or specific cell(s).
	 *
	 * Delete a entire row
	 *    $hbase
	 *        ->table('my_table')
	 *            ->row('my_row')
	 *                ->delete();
	 *
	 * Delete a entire column family
	 *    $hbase
	 *        ->table('my_table')
	 *            ->row('my_row')
	 *                ->delete('my_column_family');
	 *
	 * Delete all the cells in a column
	 *    $hbase
	 *        ->table('my_table')
	 *            ->row('my_row')
	 *                ->delete('my_column_family:my_column');
	 *
	 * Delete a specific cell
	 *    $hbase
	 *        ->table('my_table')
	 *            ->row('my_row')
	 *                ->delete('my_column_family:my_column','my_timestamp');
	 */
	public function delete(){
		$args = func_get_args();
		$url = $this->table .'/'.$this->key;
		switch(count($args)){
			case 1;
				// Delete a column or a column family
				$url .= '/'.$args[0];
			case 2:
				// Delete a specific cell
				$url .= '/'.$args[1];
		}
		return $this->hbase->request->delete($url);
	}


	/**
	 * Retrieve a value from a column row.
	 *
	 * Usage:
	 *
	 *    $hbase
	 *        ->table('my_table')
	 *            ->row('my_row')
	 *                ->get('my_column_family:my_column');
	 */
	public function get($column){
		$body = $this->hbase->request->get($this->table .'/'.$this->key.'/'.$column)->body;
        var_dump($body);
		if(is_null($body)){
			return null;
		}
		return base64_decode($body['Row'][0]['Cell'][0]['$']);
	}

    /**
     * 获得多条记录
     */
    public function getAll($column,$cnt=10){
        
		$body = $this->hbase->request->get($this->table .'/'.$this->key.'/'.$column)->body;
		if(is_null($body)){
			return null;
		}
        $outArr = array();
        $i = 1;
        $bk = false;
        foreach($body['Row'] as $rs){                
            foreach($rs['Cell'] as $c){
                $outArr[] = array(
                    '$'  => base64_decode($c['$']),
                    'tm' => $c['timestamp'],
                );
                if($i++ >= $cnt){
                    $bk = true;
                    break;
                }
            }
            if($bk)break;
        }
        return $outArr;
    }

	/**
	 * Create or update a column row.
	 *
	 * Usage:
	 *
	 *    $hbase
	 *        ->table('my_table')
	 *            ->row('my_row')
	 *                ->put('my_column_family:my_column','my_value');
	 *
	 * Note, in HBase, creation and modification of a column value is the same concept.
	 */
	public function put($column,$value){
		$value = array(
			'Row' => array(array(
				'key' => base64_encode($this->key),
				'Cell' => array(array(
					'column' => base64_encode($column),
					'$' => base64_encode($value)
				))
			))
		);
		$this->hbase->request->put($this->table .'/'.$this->key.'/'.$column,$value);
		return $this;
	}

}

class PopHbaseTable{

	public $hbase;
	public $name;

	public function __construct(PopHbase $hbase,$name){
		$this->hbase = $hbase;
		$this->name = $name;
	}

	public function __get($row){
		return $this->row($row);
	}

	public function create(){
		call_user_func_array(
			array($this->hbase->tables,'create'),
			array_merge(array($this->name),func_get_args()));
		return $this;
	}

	public function delete(){
		$this->hbase->tables->delete($this->name);
		return $this;
	}

	public function exists(){
		return $this->hbase->tables->exists($this->name);
	}

	public function row($row){
		return new PopHbaseRow($this->hbase,$this->name,$row);
	}

}

class PopHbaseTables extends PopHbaseIterator{

	public $hbase;

	public function __construct(PopHbase $hbase){
		$this->hbase = $hbase;
		$this->__data = array();
	}

	/**
	 * Provide a shortcut to the "table" method.
	 *
	 * Usage
	 *     assert($hbase->tables->{'my_table'} instanceof PopHbaseTable);
	 *
	 */
	public function __get($table){
		return $this->table($table);
	}

//	public function __unset($property){
//		unset($this->__data['data'][$property]);
//	}

	public function reload(){
		unset($this->__data['data']);
		unset($this->__data['loaded']);
	}

	public function load(){
		if(isset($this->__data['data'])&&isset($this->__data['loaded'])){
			return $this;
		}
		$tables = $this->hbase->request->get('/')->body;
		$this->__data['data'] = array();
		$this->__data['loaded'] = array();
		if(is_null($tables)){ // No table
			return $this;
		}
		foreach($tables['table'] as $table){
			$this->__data['data'][$table['name']] = new PopHbaseTable($this->hbase,$table['name']);
			$this->__data['loaded'][$table['name']] = true;
		}
		return $this;
	}

	/**
	 * Create a new table and associated column families schema.
	 *
	 * The first argument is expected to be the table name while the following
	 * arguments describle column families.
	 *
	 * Usage
	 *     $hbase->tables->create(
	 *         'table_name',
	 *         'column_1',
	 *         array('name'=>'column_2'),
	 *         array('NAME'=>'column_3'),
	 *         array('@NAME'=>'column_4',...);
	 *
	 * @param $table string Name of the table to create
	 * @param $column string Name of the column family to create
	 * @return PopHbase Current instance
	 */
	public function create(){
		$args = func_get_args();
		if(count($args)===0){
			throw new InvalidArgumentException('Missing table schema definition');
		}
		$table = array_shift($args);
		switch(gettype($table)){
			case 'string':
				$schema = array('name' => $table);
				break;
			case 'array':
                /*
				// name is required
				// other keys include IS_META and IS_ROOT
				$schema = array();
				foreach($table as $k=>$v){
					if($k=='NAME'){
						$k = 'name';
					}else{
						$k = strtoupper($k);
					}
                    echo "\n{$k}\n{$v}\n";
					$schema[$k] = $v;
				}
                var_dump($schema);
				if(!isset($schema['name'])){
					throw new InvalidArgumentException('Table schema definition not correctly defined "'.PurLang::toString($table).'"');
				}*/
                $schema = $table;
				break;
			default:
				throw new InvalidArgumentException('Table schema definition not correctly defined: "'.PurLang::toString($table).'"');
		}
		if(count($args)===0){
			throw new InvalidArgumentException('Missing at least one column schema definition');
		}
		$schema['ColumnSchema'] = array();
		foreach($args as $arg){
			switch(gettype($arg)){
				case 'string':
					$schema['ColumnSchema'][] = array('name' => $arg);
					break;
				case 'array':
					// name is required
					// other keys include BLOCKSIZE, BLOOMFILTER,
					// BLOCKCACHE, COMPRESSION, LENGTH, VERSIONS,
					// TTL, and IN_MEMORY
					$columnSchema = array();
					foreach($arg as $k=>$v){
						/*if(substr($k,0,1)=='@'){
							$k = substr($k,1);
						}
						if($k=='NAME'){
							$k = 'name';
						}else{
							$k = strtoupper($k);
						}*/
						$columnSchema[$k] = $v;
					}
					if(!isset($columnSchema['name'])){
						#throw new InvalidArgumentException('Column schema definition not correctly defined "'.PurLang::toString($table).'"');
					}
					$schema['ColumnSchema'][] = $columnSchema;
					break;
				default:
				#throw new InvalidArgumentException('Column schema definition not correctly defined: "'.PurLang::toString($table).'"');
			}
		}
        #var_dump($schema);exit;
		$this->hbase->request->put($schema['name'].'/schema',$schema);
		$this->reload();
	}

	/**
	 * Delete a table from an HBase server.
	 *
	 * Note, manipulate with care since datas are not recoverable.
	 *
	 * Usage
	 *     $hbase->tables->delete('table_name');
	 */
	public function delete($table){
		$body = $this->hbase->request->delete($table.'/schema');
		$this->reload();
		return $this;
	}

	/**
	 * Check wether a table exist in HBase.
	 */
	public function exists($table){
		foreach($this as $t){
			if($t->name==$table) return isset($this->__data['loaded'][$table]);
		}
		return false;
	}

	/**
	 * List the table names present in HBase.
	 */
	public function names(){
		$tables = array();
		foreach($this as $table){
			if(isset($this->__data['loaded'][$table->name])){
				$tables[] = $table->name;
			}
		}
		return $tables;
	}

	/**
	 * Return a PopHbaseTable instance.
	 *
	 * If the same table is requested twice, the same instance is returned.
	 *
	 * Usage
	 *     assert($hbase->tables->table('my_table') instanceof PopHbaseTable);
	 *
	 */
	public function table($table){
		if(!isset($this->__data['data'][$table])){
			$this->__data['data'][$table] = new PopHbaseTable($this->hbase,$table);
		}
		return $this->__data['data'][$table];
	}

}
?>
