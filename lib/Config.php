<?php

require_once("WrapType.php");

class Config{
	public $wordwrapType;
	public $wordwrapLength;

	public readonly string $fontDir;

	public readonly int $colourPrimary;
	public readonly int $colourOnPrimary;

	public function __construct(){
		$this->colourPrimary = 0x282360;
		$this->colourOnPrimary = 0xFFFFFF;

		$this->fontDir = "../assets/font/";
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