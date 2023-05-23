<?php

require_once("WrapType.php");

class Config{
	public $wordwrapType;
	public $wordwrapLength;

	public readonly int $brandingBlue;

	public function __construct(){
		$this->brandingBlue = 0x282360;
	}

	public static function load(){
		$config = new Config();

		$config->wordwrapType = self::getWrapType();
		$config->wordwrapLength = isset($_GET['wrapLength']) ? $_GET['wrapLength'] : 16;

		return $config;
	}

	private static function getWrapType(){
		if(isset($_GET['wordwrap'])){
			if(toBool($_GET['wordwrap'])){
				 return WrapType::ALWAYS;
			}
			
			return WrapType::NEVER;
		}
		
		return WrapType::WRAPPED_ONLY;
	}
}