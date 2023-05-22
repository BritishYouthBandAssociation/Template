<?php

class Config{
	public $wordwrapLength;

	public static function load(){
		$config = new Config();
		
		$config->wordwrapLength = isset($_GET['wrap']) ? $_GET['wrap'] : 16;

		return $config;
	}
}