<?php

require_once('BaseTemplate.php');
require_once(__DIR__ . "/../ResourceTypes.php");

class TableTemplate extends BaseTemplate{
	const WIDTH = 1080;
	const HEIGHT = 1080;

	protected $params = array(
		"image" => ResourceTypes::IMAGE
	);

	public function render(){
		$this->drawBg();
		$this->drawTitle();

		$this->drawTable();
	}

	private function drawBg(){
		fitImageToSpace($this->params["image"], $this->canvas, 0, 0, self::WIDTH * 0.4, self::HEIGHT);
		alphaGradient($this->canvas, 0, self::HEIGHT * 0.5, self::WIDTH * 0.4, self::HEIGHT, 127, 10, $this->colours["primary"]);
		imagefilledrectangle($this->canvas, self::WIDTH * 0.4, 0, self::WIDTH, self::HEIGHT, $this->colours["primary"]);

		$logoDark = $this->getImage("dark logo.png", 0.4);
		containImageInSpace($logoDark, $this->canvas, self::WIDTH * 0.45, self::HEIGHT * 0.2, self::WIDTH * 0.5, self::HEIGHT * 0.6);

		$logo = $this->getImage("byba.png");
		fitImageToSpace($logo, $this->canvas, self::WIDTH * 0.625, self::HEIGHT * 0.8, self::WIDTH * 0.15, self::WIDTH * 0.15);
	}

	private function drawTitle(){
		fitText($this->canvas, $this->fonts["title"], "NATIONAL CHAMPIONSHIPS", $this->colours["onPrimary"], self::WIDTH * 0.45, self::HEIGHT * 0.05, self::WIDTH * 0.5, self::HEIGHT * 0.03, fitToOneLine: true);
		fitText($this->canvas, $this->fonts["subtitle"], "Sunday 2nd October 2022", $this->colours["onPrimary"], self::WIDTH * 0.45, self::HEIGHT * 0.08, self::WIDTH * 0.5, self::HEIGHT * 0.025, fitToOneLine: true);
	}

	private function drawTable(){
		$rowH = self::HEIGHT * 0.03;

		$this->drawRow(self::WIDTH * 0.45, self::HEIGHT * 0.15, $rowH, 1, "Beeches", 90.875);
		$this->drawRow(self::WIDTH * 0.45, (self::HEIGHT * 0.15) + $rowH + 10, $rowH, 2, "Warwick Corps of Drums", 11.123);
		$this->drawRow(self::WIDTH * 0.45, (self::HEIGHT * 0.15) + (($rowH + 10) * 2), $rowH, 3, "Revolution", 85.123);
		$this->drawRow(self::WIDTH * 0.45, (self::HEIGHT * 0.15) + (($rowH + 10) * 3), $rowH, 4, "Syston Scout & Guide Band", 51.001);
		
		//imagefilledrectangle($this->canvas, self::WIDTH * 0.45, self::HEIGHT * 0.15, self::WIDTH * 0.95, self::HEIGHT * 0.75, $this->colours["onPrimary"]);
	}

	private function drawRow($x, $y, $rowH, $pos, $band, $score){
		$rowW = self::WIDTH * 0.5;

		$yOffset = $rowH * 0.15;

		imagefilledrectangle($this->canvas, $x, $y, $x + ($rowW * 0.05), $y + $rowH, $this->colours["onPrimary"]);
		fitText($this->canvas, $this->fonts["title"], $pos, $this->colours["primary"], $x + ($rowW * 0.005), $y + $yOffset, $rowW * 0.03, $rowH - ($yOffset * 2), Alignment::CENTER);

		imagefilledrectangle($this->canvas, $x + ($rowW * 0.07), $y, $x + ($rowW * 0.75), $y + $rowH, $this->colours["onPrimary"]);
		fitText($this->canvas, $this->fonts["title"], strtoupper($band), $this->colours["primary"], $x + ($rowW * 0.07), $y + $yOffset, $rowW * 0.68, $rowH - ($yOffset * 2), Alignment::CENTER, fitToOneLine: true);

		imagefilledrectangle($this->canvas, $x + ($rowW * 0.77), $y, $x + $rowW, $y + $rowH, $this->colours["onPrimary"]);
		fitText($this->canvas, $this->fonts["title"], $score, $this->colours["primary"], $x + ($rowW * 0.78), $y + $yOffset, $rowW * 0.2, $rowH - ($yOffset * 2), Alignment::CENTER, fitToOneLine: true);
	}
}