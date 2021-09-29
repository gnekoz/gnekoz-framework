<?php

abstract class Gnekoz_Controller {
  
    private $engine       = null;
    private $cssRef       = array();
    private $jsRef        = array();
    private $css          = array();
    private $js           = array();
    private $children     = array();
    private $template     = "";
    private $templateFile = "";
    private $keywords     = "";
    private $renderable   = true;

    public function setRenderable($renderable) {
        $this->renderable = $renderable;
    }

    public function isRenderable() {
        return $this->renderable;
    }

    public function getEngine() {
        if ($this->engine == null) {
            $this->engine = new Smarty();
            $this->engine->template_dir = SMARTY_TPL_DIR;
            $this->engine->compile_dir  = SMARTY_TPLC_DIR;
            $this->engine->config_dir   = SMARTY_CFG_DIR;
            $this->engine->cache_dir    = SMARTY_CACHE_DIR;
        }
        return $this->engine;
    }

    public function setTemplate($template) {
        $this->templateFile = SMARTY_TPL_DIR.DIRECTORY_SEPARATOR.$template.".tpl";
        $this->templateFile = Gnekoz_Utility_Path::normalizePath($this->templateFile);
        $this->template = $template;
    }

    public function getTemplate() {
        return $this->template;
    }

    public function setKeywords($keywords) {
        $this->keywords = $keywords;
    }

    public function getKeywords() {
        return $this->keywords;
    }

    public function getAllKeywords() {
        // Lettura ricorsiva delle keyword dei figli
        $childrenKw = "";
        foreach ($this->children as $child) {
            $childrenKw .= ",{$child->getAllKeywords()}";
        }

        // Composizione della stringa di tutte le keyword
        $allKw = $this->getKeywords().$childrenKw;

        // Creazione array delle keyword senza doppioni
        $allKwList = array_unique(split(",", $allKw));

        // Creazione della stringa definitiva
        $resultKw = "";
        foreach ($allKwList as $kw) {
            $kw = trim($kw);
            if ($kw != "") {
                $resultKw .= ",$kw";
            }
        }

        // Ritorno della stringa complessiva con rimozione della prima virgola
        return substr($resultKw, 1);
    }
        
    public function render($caller = null) {
        //var_dump($this->children);
        foreach($this->children as $childId => $child) {
            $childOutput = $child->render($this);
            $this->getEngine()->assign($childId, $childOutput);
        }        
        return $this->getEngine()->fetch($this->templateFile);
    }
     
    public final function getCssRef() {
        return $this->cssRef;
    }

    public final function getJsRef() {
        return $this->jsRef;
    }

    public final function addCssRef($cssFile) {
        if (substr($cssFile, 0, strlen(CSS_WEBDIR)) != CSS_WEBDIR ) {
            $cssFile = CSS_WEBDIR."/$cssFile";
        }
        if (!in_array($cssFile, (array) $this->cssRef)) {
            $this->cssRef[count($this->cssRef)] = $cssFile;
        }
    }

    public final function addJsRef($jsFile) {
        if (substr($jsFile, 0, strlen(JS_WEBDIR)) != JS_WEBDIR ) {
            $jsFile = JS_WEBDIR."/$jsFile";
        }
        if (!in_array($jsFile, (array) $this->jsRef)) {
            $this->jsRef[count($this->jsRef)] = $jsFile;
        }
    }

    public final function getCss() {
        return $this->css;
    }

    public final function getJs() {
        return $this->js;
    }

    public final function addCss($css) {
        if (!in_array($css, (array) $this->css)) {
            $this->css[count($this->css)] = $css;
        }
    }

    public final function addJs($js) {
        if (!in_array($js, (array) $this->js)) {
            $this->js[count($this->js)] = $js;
        }
    }

    public final function getChildren() {
        return $this->children;
    }

    public final function addChild($id, $comp) {
        if (!is_a($comp, __CLASS__)) {
            throw new Exception("L'oggetto indicato non e' un controller");
        }
        $this->children[$id] = $comp;
        $childJs = $comp->getJsRef();
        foreach ($childJs as $script) {
            $this->addJsRef($script);
        }
        $childCss = $comp->getCssRef();
        foreach ($childCss as $script) {
            $this->addCssRef($script);
        }
    }
}

?>
