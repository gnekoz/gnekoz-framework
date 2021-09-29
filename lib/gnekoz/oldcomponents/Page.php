<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Page
 *
 * @author gneko
 */
class Gnekoz_Page extends Gnekoz_Controller {

    private $title = "";
    private $masterPage = null;
    private $doctype = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
    private $charset = "utf-8";
    private $description = "";
    private $robots = "";
    private $lang = "";
    private $protected = false;
    private $authenticator = null;
    private $titleSeparator = " - ";


    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitleSeparator() {
        return $this->titleSeparator;
    }

    public function setTitleSeparator($titleSep) {
        $this->titleSeparator = $titleSep;
    }

    public function getMasterPage() {
        return $this->masterPage;
    }

    public function setMasterPage($masterPage) {
        $this->masterPage = $masterPage;
    }

    public function setDoctype($doctype) {
        $this->doctype = $doctype;
    }

    public function getDoctype() {
        return $this->doctype;
    }

    public function setCharset($charset) {
        $this->charset = $charset;
    }

    public function getCharset() {
        return $this->charset;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setRobots($robots) {
        $this->robots = $robots;
    }

    public function getRobots() {
        return $this->robots;
    }

    public function setLang($lang) {
        $this->lang = $lang;
    }

    public function getLang() {
        if ($this->lang == "") {
            $this->lang = Gnekoz_Lang::getCurrentLang();
        }
        return $this->lang;
    }

    public function setProtected($protected) {
        $this->protected = $protected;
    }

    public function isProtected() {
        return $this->protected;
    }

    public function setAuthenticator($authenticator) {
        $this->authenticator = $authenticator;
    }

    public function getAuthenticator() {
        return $this->authenticator;
    }

    public function getHead() {
        $result = "";
        $result .= "<head>\n";
        $result .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$this->charset\" />\n";
        $result .= "<meta name=\"description\" content=\"$this->description\" />\n";
        $result .= "<meta name=\"robots\" content=\"$this->robots\" />\n";
        $result .= "<meta name=\"keywords\" content=\"{$this->getAllKeywords()}\" />\n";
        $result .= "<title>{$this->title}</title>\n";
        $jsRef = $this->getJsRef();
        foreach ($jsRef as $script) {
            $result .= "<script type=\"text/javascript\" src=\"$script\"></script>\n";
        }
        $js = $this->getJs();
        foreach ($js as $script) {
            $result .= "<script type=\"text/javascript\">\n$script\n</script>\n";
        }

        if (!DISABLE_CSS) {
            $cssRef = $this->getCssRef();
            foreach ($cssRef as $script) {
                $result .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$script\" />\n";
            }
            $css = $this->getCss();
            foreach ($css as $script) {
                $result .= "<style type=\"text/css\">\n$script\n</script>\n";
            }
        }

        // Firebug-lite
        if (FIREBUG_LITE) {
            $result .= "<script type='text/javascript' src='http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js'></script>\n";
        }

        $result .= "</head>\n";
        return $result;
    }

    public function render($caller) {
        // Impostazioni variabili di utilita' per i template
        $this->getEngine()->assign("_GnekozCurrentUrl", Gnekoz_Caronte::getCurrentUrl());
        $this->getEngine()->assign("_GnekozCurrentPage", Gnekoz_Caronte::getCurrentPage());

        $master = $this->getMasterPage();
        
        if ($caller != $master && $master != null) {
            $master->getTitle();
            $master->setTitle($this->getFinalTitle());
            $master->setDoctype($this->getDoctype());
            $master->addChild("nestedpage", $this);
            $master->setLang($this->getLang());
            return $master->render($this);
        } else {
            if ($master == null) {
                $this->getEngine()->assign("head", $this->getHead());
                $this->getEngine()->assign("doctype", $this->getDoctype());
                $this->getEngine()->assign("lang", $this->getLang());
            }
            if ($this->protected && !$this->authenticator->isAuthenticated()) {
                Gnekoz_Caronte::redirectTo($this->authenticator->getLoginPage());
            } else {
                return parent::render($this);
            }
        }
    }

    private function getFinalTitle() {
        if ($this->getMasterPage() == null) {
            return $this->title;
        }
        $titles = array();
        $masterTitle = $this->getMasterPage()->getTitle();
        $thisTitle = $this->getTitle();
        if ($masterTitle != "") {
            $titles[] = $masterTitle;
        }
        if ($thisTitle != "") {
            $titles[] = $thisTitle;
        }
        return implode($this->getMasterPage()->getTitleSeparator(), $titles);
    }

    public function index() {
        return "";
    }
}
?>
