<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../lib/Utils.php");
require_once("../lib/Template/InstagramNewsTemplate.php");

$wrapLength = isset($_GET['wrap']) ? $_GET['wrap'] : 16;
$text = wordwrap(strtoupper($_GET['text']), $wrapLength, "\n");

$template = new InstagramNewsTemplate();
$template->parseParams();

$canvas = $template->render();

header("Content-Type: image/png");
imagepng($canvas);