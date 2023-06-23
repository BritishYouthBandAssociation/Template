<?php

require_once("WrapType.php");

class Config{
	public readonly WrapType $wordwrapType;
	public readonly int $wordwrapLength;
	public readonly string $outputType;
	public readonly bool $download;

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

		if(isset($_GET['dl'])){
			$this->download = $_GET['dl'] != 0;
		} else {
			$this->download = !(defined('DEBUG') && DEBUG);
		}
	}

	public static function load(){
		$config = new Config();

		$config->wordwrapType = self::getWrapType();
		$config->wordwrapLength = isset($_GET['wrapLength']) ? $_GET['wrapLength'] : 16;

		$config->outputType = self::getOutputType();

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

	private static function getOutputType(){
		$allowedTypes = ["png", "jpeg", "gif", "bmp", "webp"];
		$request = isset($_GET['outputFormat']) ? strtolower($_GET['outputFormat']) : "";

		if(in_array($request, $allowedTypes)){
			return $request;
		}

		return "png";
	}
}