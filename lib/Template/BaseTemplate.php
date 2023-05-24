<?php

require_once(__DIR__ . "/../ResourceTypes.php");
require_once(__DIR__ . "/../Utils.php");

abstract class BaseTemplate {
	abstract public function render();

	protected $config;
	protected $params;

	public function __construct($config){
		$this->config = $config;
	}

	public function parseParams(){		
		foreach($this->params as $param => $type){
			$val = null;
			if($type == ResourceTypes::IMAGE){
				$val = $this->getImageParam($param);
			} else if($type == ResourceTypes::STRING){
				$val = $this->getTextParam($param);
			}

			if($val == null){
				throw new Exception("Failed to process parameter '$param'");
			}

			$this->params[$param] = $val;
		}
	}

	private function getImageParam($param){
		if(isset($_FILES[$param])){
			return loadImage($_FILES[$param]['tmp_name']);
		} else if(isset($_REQUEST[$param])) {
			return loadRemoteImage($_REQUEST[$param]);
		}

		return null;
	}

	private function getTextParam($param){
		if(!isset($_REQUEST[$param])){
			return null;
		}

		return trim($_REQUEST[$param]);
	}
}