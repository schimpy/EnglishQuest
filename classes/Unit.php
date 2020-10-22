<?php

class Unit
{
    private $db;
    private $user;
    private $unitDetails;
    private $short;
    private $unitID;
    private $table;

    public function __construct($short, $id, $user) {
        $this->db = Database::getInstance();
        $this->user = $user;
        $this->short = $short;
        $this->unitID = $id;
        $this->unitDetails = $this->loadDetails($short, $id);
        $this->table = $short ."_units";
    }

    public function getDB() {
        return $this->db;
    }
    
    public function getUnitID() {
        return $this->unitDetails->id;
    }

    public function getUnitTitle() {
        return $this->unitDetails->title;
    }

    public function getUnitContent() {
        return $this->unitDetails->content;
    }

    public function getModuleNameFromShort($short) {
        $this->db->query("SELECT name FROM modules WHERE short = ?", array($this->short));
        return $this->db->first()->name;
    }


    public function loadDetails($short, $id) {
        $table = $short .'_units';
        $this->db->get($table, array('id', '=', $id));
        return $this->db->first();
    }

    public function render() {
        $html = '';    
        $html .= '<h3><a href="module.php?slug='. $this->short . '">' . $this->getModuleNameFromShort($this->short) . '</a> > ' . $this->getUnitTitle() .'</h3>';
        $html .= '<div id="unit-content">'. $this->getUnitContent() .'</div>';
        $html .= '<a href="quiz.php?slug='. $this->short .'&qtype='. $this->unitDetails->questiontypes .'&uid='. $this->user->data()->id .'" class="w3-button w3-blue">TEST ME!</a>';

        echo $html;
    }


    public function isUnitUnlocked() {
        $table = "user_units";
        $this->db->query("SELECT id FROM user_units WHERE slug = ? AND unitid = ?", array($this->short, $this->unitID));
        if($this->db->count() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function unlock() {
        $table = "user_units";
        $this->getDB()->insert($table, array(
            'unitid' => $this->unitID,
            'slug' => $this->short,
            'userid' => $this->user->getID()));

        $this->getDB()->update('users', $this->user->getID(), array('coins' => ($this->user->getCoins() - $this->unitDetails->price)));
    }



    public function practice($short, $unitID, $numOfQuestions) {}

    public function fight() {}

}