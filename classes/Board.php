<?php

class Board {

    private $db = null;
    private $user;
    private $results = array();
    private $offsetTop;
    private $offsetBottom;
    private $usersTop = array();
    private $usersBottom = array(); 
    private $position;

    public function __construct($user) {
        $this->db = Database::getInstance();
        $this->user = $user;
        $this->results = $this->loadScore();
        $this->offsetTop = 3;
        $this->offsetBottom = 3;
     }

    public function loadScore() {
        $this->db->query("SELECT username, points FROM users ORDER BY points");
        $data = $this->db->results();
        return $data;
    }

    public function findBottomUsers() {
        $save = false;
        $counter = 0;
        $usersPosition = 0;

        foreach(json_decode(json_encode(array_reverse($this->results, TRUE)), true) as $r) {
            $usersPosition++;
            if($r['username'] == $this->user->getUsername()) {
                $save = true;
                $this->position = $usersPosition;
            }

            if($save) {
                if($counter < $this->offsetBottom) {
                    array_push($this->usersBottom, $r);
                }
                $counter++;
            }
        }
    }

    public function findTopUsers() {
        $save = false;
        $counter = 0;

        foreach(json_decode(json_encode($this->results), true) as $r) {
            if($r['username'] == $this->user->getUsername()) {
                $save = true;
            }

            if($save) {
                if($counter < $this->offsetTop) {
                    array_push($this->usersTop, $r);
                }
                $counter++;
            }
        }
    }


    public function renderBoard() {

        $this->findBottomUsers();
        $this->findTopUsers();

        $html = '<table style="max-width: 220px;" class="w3-table w3-striped w3-border w3-bordered w3-margin">';
        $start = $this->position - $this->offsetTop + 1;  

        foreach(json_decode(json_encode(array_reverse($this->usersTop, true)), true) as $r) {
            if($r['username'] == $this->user->getUsername()) {
                $html .= "<tr><td>" . $start . "</td><td><b>" . $r['username'] . "</b></td><td><b>" . $r['points'] . "</b></td></tr>";
            } else {
                $html .= "<tr><td>" . $start . "</td><td>" . $r['username'] . "</td><td>" . $r['points'] . "</td></tr>";
            }
            $start++;
        }

        $start -= 1;

        foreach(json_decode(json_encode($this->usersBottom), true) as $r) {
            if($r['username'] == $this->user->getUsername()) {
                $html .= "";
            } else {
                $html .= "<tr><td>" . $start . "</td><td>" . $r['username'] . "</td><td>" . $r['points'] . "</td></tr>";
            }
            $start++;
        }

        $html .= "</table>";
        echo $html;
    }

}