<?php
// TO DO OUTPUT

class Validator
{
    private $passed = false;
    private $errors = array();
    private $editedValues = array();
    private $db = null;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function check($source, $items, $user = null) {

        foreach($items as $item => $rules) {
            foreach($rules as $rule => $rule_value) {

                $value = $source[$item];
                $item = escape($item);

                if($rule === 'required' && empty($value)) {
                    $this->addError("{$item} is required.");

                } else if(!empty($value)) {

                    switch($rule) {

                        case 'min':
                            if(strlen($value) < $rule_value) {
                                $this->addError("{$item} must contain at least {$rule_value} characters.");
                            }
                            break;

                        case 'max':
                            if(strlen($value) > $rule_value) {
                                $this->addError("{$item} must contain at most {$rule_value} characters.");
                            }
                            break;

                        case 'matches':
                            if($value != $source[$rule_value]) {
                                $this->addError("{$rule_value} must match {$item}.");
                            }
                            break;

                        case 'unique':
                            $check = $this->db->get($rule_value, array($item, '=', $value));
                            if($check->count()) {
                                if($user !== null) {
                                    switch ($item) {
                                        case 'username':
                                            $oldvalue = escape($user->data()->username);
                                            if($oldvalue != $value) {
                                                $this->addError("{$item} already exists.");
                                            }
                                            break;

                                        case 'email':
                                            $oldvalue = escape($user->data()->email);
                                            if($oldvalue != $value) {
                                                $this->addError("{$item} already exists.");
                                            }
                                            break;
                                    }
                                } else {
                                    $this->addError("{$item} already exists.");
                                }
                            }
                            break;

                        case 'email':
                            if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $this->addError("{$item} is malformed.");
                            }
                            break;

                        case 'oldValue' :
                            $this->db->get('users', array($item, '=', $value));
                            $old = $this->db->first()->{$item};
                            if($old != $rule_value) {
                                array_push($this->editedValues, $item);
                            }
                    }
                }
            }
        }

        if(empty($this->errors)) {
            $this->passed = true;
        }
    }

    private function addError($error) {
        $this->errors[] = $error;
    }


    // TO DO replace tokens and capitalize
    public function errors() {
        $oldTokens = array('username', 'email',  'password_check', 'current_password', 'new_password_again', 'new_password', 'password');
        $newTokens = array('username', 'e-mail', 'repeated password', 'current password', 'repeated new password', 'new password', 'password');
        $output = str_replace($oldTokens, $newTokens, $this->errors);
        $output2 = array();
        foreach($this->errors as $error) {
            $capitalized = ucfirst($error);
            array_push($output2, $capitalized);
        }
        return $output2;
    }

    public function passed() {
        return $this->passed;
    }

    public function getEditedValues() {
        return $this->editedValues;
    }

    public function getNumOfErrors() {
        return count($this->errors);
    }
}