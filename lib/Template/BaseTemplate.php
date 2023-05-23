<?php

require_once(__DIR__ . "/../ResourceTypes.php");

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
			} else if($type == ResourceTypes::WRAPPED_STRING || $type == ResourceTypes::STRING){
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

		if($this->config->wordwrapType == WrapType::NEVER || ($this->config->wordwrapType == WrapType::WRAPPED_ONLY && $this->params[$param] != ResourceTypes::WRAPPED_STRING)){
			return $_REQUEST[$param];
		}

		return wordwrap($_REQUEST[$param], $this->config->wordwrapLength, "\n");
	}
}