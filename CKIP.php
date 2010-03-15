<?php

class Term{
	public $term;
	public $pos;
	function __construct ($t,$p){
		$this->term = $t;
		$this->pos = $p;
	}
}
class CKIP{

	public $user;
	protected $passwd;
	public $serverip;
	public $serverport;

	protected $sock;
	public $data_send;
	public $data_recv;
	public function __construct ($ip,$port,$user,$passwd){
		$this->user = $user;
		$this->passwd = $passwd;
		$this->serverip = $ip;
		$this->serverport= $port;
		$this->connect();
	}
	function connect(){
		$addr='tcp://'.$this->serverip.':'.$this->serverport;
		$this->sock = stream_socket_client($addr);
		//stream_set_blocking ($this->sock,0);

	}
	function disconnect(){
		if ($this->sock) fclose($this->sock);
	}
	function query($text){
		$head = <<<_HEADER_
<?xml version="1.0" ?>
<wordsegmentation version="0.1">
<option showcategory="1" />
<authentication username="$this->user" password="$this->passwd" />
<text>
_HEADER_;

		$footer = <<<_FOOT_
</text>
</wordsegmentation>
_FOOT_;

		$this->data_send = $text;
		$text = str_replace ("&"," ",$text);
		$querystr = $head.$text.$footer;
		$tempxml = simplexml_load_string($querystr);
		$resp =array();
		if ($tempxml){
			if (stream_socket_sendto($this->sock, $tempxml->asXML())){
				do {
					$ttt=stream_socket_recvfrom($this->sock, 65525);
					$resp[] = $ttt;
				}while  (! simplexml_load_string( implode ($resp)));
						$this->data_recv = html_entity_decode(implode($resp));
			}
			
		}else{
			$this->data_recv =0;
		}
	} 
	function getTerm(){
		$term = Array();
		$resp = $this->data_recv;
		$resp = htmlspecialchars_decode($resp);
		$xml_resp = simplexml_load_string($resp);
		if ($xml_resp){
		$sentence = $xml_resp->xpath('result/sentence');
		
			if ($sentence){
				foreach ($sentence as $line){
					$line = (string)$line;

					foreach(  split("　",$line) as $word){
					//echo count($temp);
						if ($word != ""){
							preg_match("/(\S*)\((\S*)\)/",$word,$pos);
							$t = new Term(strtolower($pos[1]),$pos[2]);
							$term[] = $t;
						}
					}
				}
			}
		}
			return $term;

		}
	function getSents(){

		$sents = Array();
		$resp = $this->data_recv;
		$xml_resp = simplexml_load_string($resp);
		if ($xml_resp){
		$sentence = $xml_resp->xpath('result/sentence');
			foreach ($sentence as $line){
				$line = (string)$line;
				$sent = Array();
				foreach(  split("　",$line) as $word){
				//echo count($temp);
					if ($word != ""){
						preg_match("/(\S*)\((\S*)\)/",$word,$pos);
						$br_array = Array('PARENTHESISCATEGORY',
										'COMMACATEGORY',
										'PERIODCATEGORY',
										'ETCCATEGORY',
										'QUESTIONCATEGORY',
										'PAUSECATEGORY',
										'SEMICOLONCATEGORY');
						if ( in_array ($pos[2] ,$br_array )){
							//break;	

						}else{
						$t = new Term(strtolower($pos[1]),$pos[2]);
						$sent[] = $t;
						}
					}
				}
				$sents[] = $sent;
			}
		}
		return $sents;
	}	
}

?>
