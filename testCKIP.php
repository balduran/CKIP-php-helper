<?php

require_once 'PHPUnit/Framework.php';
require_once 'CKIP.php';

class testCKIP extends PHPUnit_Framework_TestCase
{
	private $remote;
	public function setUP()
	{
		$this->remote = new CKIP('140.109.19.104','1501','','');
		$this->assertNotNull($this->remote);
		$this->assertNotNull($this->remote->getsock());
	}
	public function testquery()
	{
		$entry = "中文啦，有意見嗎？";
		$ret = $this->remote->query($entry);
		//print_r($ret);
		$retlen = mb_strlen($ret,'utf-8');
		$expect = 268;
		$this->assertEquals($retlen,$expect);
	}
	/**
	 * @depends testquery
	 */
	public function testgetTerm()
	{

	}



}
?>
