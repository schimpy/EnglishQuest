<?php

class ModuleHandler {

    private $db = null;
    private $userID;
    private $userLvl;
    private $allModules = array();
    private $renderedModules = array();

    public function __construct($id) {
        $this->db = Database::getInstance();
        $this->userID = $id;
        $this->userLvl = $this->getUserLevel($id);
    }

    public function getUserLevel($userid) {
        $this->db->query("SELECT lvl FROM users WHERE id = ?", array($userid));
        $return = $this->db->first();
        return $return->lvl;
    }

    public function loadAllModules() {
        $this->db->query("SELECT * FROM modules ORDER BY lvl");
        return $this->db->results();
    }

    public function loopModules($modules) {
        foreach (json_decode(json_encode($modules), true) as $module) {
            $this->renderModule($module);
        }
    }

    public function renderModule($module) {
        $html = "";
        $html .= $this->renderModuleHeader($module);
        $html .= $this->renderModuleBody($module);
        $html .= $this->renderModuleFooter();

        array_push($this->renderedModules, $html);
    }

    public function renderModules($modules) {
        foreach($modules as $module) {
            echo $module;
        }
    }

    public function renderAllModules() {
        $this->renderModules($this->renderedModules);
    }

    public function renderModuleHeader($module) {
        $color = 'w3-'. $module['color'];
        $margin = "w3-margin-bottom";
        $padding = "w3-padding";
        $width = Config::get('defaultModule/width');
        $height = Config::get('defaultModule/height');
        
        if ($module['lvl'] > $this->getUserLevel($this->userID)) {
            $opacity = 'opacity: 0.65';
        } else {
            $opacity = 'opacity: 1';
        }

        $style = 'style="width:'. $width .';height:'. $height .';'. $opacity .'"';
        
        return '<div class="w3-display-container w3-card w3-round '. $color .' '. $margin .' '. $padding .'" '. $style .' id="'. $module['short'] .'">';
    }

    public function renderModuleBody($module) {
        $body = '<h3 class="w3-padding">'. $module['name'] .'</h3>
                 <div class="w3-bar w3-padding w3-dark-gray w3-display-bottommiddle" style="width: 100%;">';

        if($module['lvl'] <= $this->getUserLevel($this->userID)) {
            $body .= '<a href="module.php?slug=' . $module['short'] . '" class="w3-button w3-right">GO!</a>';
        } else {
            $body .= '<p class="w3-right">UNLOCKED FROM LEVEL '. $module['lvl'] .'&nbsp;&nbsp;&nbsp;'; 
        }

        $body .= '</div>';
        return $body;
    }


    public function renderModuleFooter() {
        return '</div>';
    }


}