<?php

require_once("BaseTemplate.php");
require_once(__DIR__ . "/../ResourceTypes.php");

class LeagueTableRowTemplate extends BaseTemplate {
	const WIDTH = 8000;
	const HEIGHT = 1500;

	protected $params = array(
		"position" => ResourceTypes::STRING,
		"band" => ResourceTypes::STRING,
		"score" => ResourceTypes::STRING,
		"straight" => [ResourceTypes::OPTIONAL, ResourceTypes::STRING]
	);

	public function render() {
		imagealphablending($this->canvas, false);
		$transparency = imagecolorallocatealpha($this->canvas, 0, 0, 0, 127);
		imagefill($this->canvas, 0, 0, $transparency);
		imagesavealpha($this->canvas, true);

		$padding = self::HEIGHT / 5;
		if($this->params["straight"] != null){
			$slant = 0;
		} else {
			$slant = $padding;
		}

		antialiasPolygon($this->canvas, array(
			$slant, 0,
			self::WIDTH, 0,
			self::WIDTH - $slant, self::HEIGHT,
			0, self::HEIGHT
		), $this->colours["primary"]);

		$posWidth = self::WIDTH / 6;
		antialiasPolygon($this->canvas, array(
			$slant, 0,
			$posWidth, 0,
			$posWidth - $slant, self::HEIGHT,
			0, self::HEIGHT
		), $this->colours["highlight"]);
		fitText($this->canvas, $this->fonts["title"], $this->params["position"], $this->colours["onPrimary"], 0, 0, $posWidth - ($padding / 2) - 20, self::HEIGHT, Alignment::CENTER, self::HEIGHT / 2, true);

		fitText($this->canvas, $this->fonts["title"], strtoupper($this->params["band"]), $this->colours["onPrimary"], $posWidth + ($padding / 2), 0, (self::WIDTH * 0.75) - $posWidth - ($padding / 2), self::HEIGHT, Alignment::CENTER, self::HEIGHT / 2, true);

		antialiasPolygon($this->canvas, array(
			self::WIDTH * 0.8, 0,
			self::WIDTH, 0,
			self::WIDTH - $slant, self::HEIGHT,
			(self::WIDTH * 0.8) - $slant, self::HEIGHT
		), $this->colours["onPrimary"]);

		fitText($this->canvas, $this->fonts["title"], $this->params["score"], $this->colours["primary"], (self::WIDTH * 0.8) + $padding, 0, (self::WIDTH * 0.2) - ($padding * 2), self::HEIGHT, Alignment::CENTER, self::HEIGHT / 2, true);

		imagepolygon($this->canvas, array(
			$slant, 0,
			self::WIDTH, 0,
			self::WIDTH - $slant, self::HEIGHT,
			0, self::HEIGHT
		), $this->colours["dark"]);
	}
}
