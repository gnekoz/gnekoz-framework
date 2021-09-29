<?php

class Gnekoz_Debug {
	
	private static $instance = null;

	private $enabled = false;
	
	private $messages = array();
	
	private $engine;
	
	private function __construct() {
		$this->engine = new Smarty();
		$this->engine->template_dir = SMARTY_TPL_DIR;
		$this->engine->compile_dir  = SMARTY_TPLC_DIR;
		$this->engine->config_dir   = SMARTY_CFG_DIR;
		$this->engine->cache_dir    = SMARTY_CACHE_DIR;		
	}
	
	private static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new Gnekoz_Debug();
		}
		
		return self::$instance; 
	}
	
	public static function enable() {
		self::getInstance()->enabled = true;
	}
	
	public static function disable() {
		self::getInstance()->enabled = false;
	}
	
	public static function isEnabled() {
		return self::getInstance()->enabled; 
	}
	
	public static function addMessage($message) {
		self::getInstance()->messages[] = $message;
	}
	
	public static function getMessages() {
		return self::getInstance()->messages;
	}
	
	// FIXME: ci sono un pò di scopiazzature da altri componenti di base
	public static function getHtmlOutput() {		
		if (!self::isEnabled()) {
			return;
		}		
		$tpl = SMARTY_TPL_DIR.DIRECTORY_SEPARATOR."debug.tpl";
    $tpl = Gnekoz_Utility_Path::normalizePath($tpl);
    
    // Parametri
    self::getInstance()->engine->assign("post", var_export($_POST, true));
    self::getInstance()->engine->assign("get", var_export($_GET, true));
    
    // Messaggi vari 
		self::getInstance()->engine->assign("messages", self::getMessages());
		return self::getInstance()->engine->fetch($tpl);
	}
}
?>