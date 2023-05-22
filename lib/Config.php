<?php

class Config{
	public $wordwrapLength;
	public readonly int $brandingBlue;

	public function __construct(){
		$this->brandingBlue = 0x282360;
	}

	public static function load(){
		$config = new Config();

		$config->wordwrapLength = isset($_GET['wrap']) ? $_GET['wrap'] : 16;

		return $config;
	}
}