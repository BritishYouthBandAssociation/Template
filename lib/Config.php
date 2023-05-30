<?php

require_once("WrapType.php");

class Config{
	public $wordwrapType;
	public $wordwrapLength;

	public readonly string $fontDir;
	public readonly string $imageDir;

	public readonly int $colourPrimary;
	public readonly int $colourOnPrimary;
	public readonly int $colourHighlight;
	public readonly int $colourLight;
	public readonly int $colourDark;

	public function __construct(){
		$this->colourLight = 0xFFFFFF;
		$this->colourDark = 0x222222;

		$this->colourPrimary = 0x282360;
		$this->colourOnPrimary = $this->colourLight;

		$this->colourHighlight = 0xFF0000;

		$this->fontDir = __DIR__ . "/../assets/font/";
		$this->imageDir = __DIR__ . "/../assets/image/";
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