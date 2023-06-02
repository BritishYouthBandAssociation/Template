<?php

require_once("BaseTemplate.php");
require_once(__DIR__ . "/../ResourceTypes.php");

class DatesCoverTemplate extends BaseTemplate {
	const WIDTH = 2160;
	const HEIGHT = 1216;

	protected $params = array(
		"text" => ResourceTypes::STRING,
		"shows" => [ResourceTypes::STRING, ResourceTypes::ARRAY]
	);

	public function render(){
		imagefill($this->canvas, 0, 0, $this->colours["primary"]);
		alternatingTextStroke($this->canvas, 24, -20, 10, self::WIDTH, $this->fonts["title"], "BYBA 2023", $this->colours["light"], $this->colours["light"], 2);
		alternatingTextStroke($this->canvas, 24, -350, self::HEIGHT - 34, self::WIDTH, $this->fonts["title"], "BYBA 2023", $this->colours["light"], $this->colours["light"], 2);

		$this->drawNextShow();
		$this->drawDates();
	}

	private function drawNextShow(){
		$top = self::HEIGHT / 3;
		$left = self::WIDTH / 8;
		$width = self::WIDTH / 3;

		$padding = 20;
		$headerSize = 45;
		$dateSize = 250;

		writeCenteredTtfText($this->canvas, $this->fonts["title"], "NEXT SHOW", $this->colours["onPrimary"], $left, $top, $width, $headerSize);
		$top += $padding + $headerSize;

		writeCenteredTtfText($this->canvas, $this->fonts["title"], $this->params["text"], $this->colours["onPrimary"], $left, $top, $width, $dateSize);
		$top += $padding + $dateSize;

		writeCenteredTtfText($this->canvas, $this->fonts["subtitle"], "Music Revolution", $this->colours["onPrimary"], $left, $top, $width, $headerSize);
		$top += $padding + $headerSize;

		writeCenteredTtfText($this->canvas, $this->fonts["subtitle"], "Belle Vue, Wakefield", $this->colours["onPrimary"], $left, $top, $width, $headerSize);
	}

	private function drawDates(){
		$top = self::HEIGHT / 4;
		$left = self::WIDTH * 0.5;
		$width = self::WIDTH / 3;
		
		$padding = 50;
		$size = 30;
		$subtitle = $size * 0.8;

		foreach($this->params["shows"] as $show){
			writeCenteredTtfText($this->canvas, $this->fonts["title"], strtoupper($show), $this->colours["onPrimary"], $left, $top, $width, $size, $size);
			$top += $size + ($padding / 2);
			writeCenteredTtfText($this->canvas, $this->fonts["subtitle"], "Belle Vue, Wakefield - 18th June", $this->colours["onPrimary"], $left, $top, $width, $subtitle);
			$top += $size + $padding;
		}
	}
}