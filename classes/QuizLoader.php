<?php

    class QuizLoader {

        private $db = null;

        private $short;
        private $numOfQuestions;
        private $questionTypes = array(); 

        private $questionSet = array();
        private $question = "";
        private $answerSet = array();
        private $correctAnswerID;
        private $description = "";

        public function __construct($short, $num, $types = array()) {
            $this->db = Database::getInstance();
            $this->short = $short;
            $this->numOfQuestions = $num;
            $this->questionTypes = $types;
        }

        private function loadQuestions() {
            $in = str_repeat('?,', count($this->questionTypes) - 1) . '?';
            $sql = "SELECT id, question, description FROM ". $this->short ."_questions WHERE type IN ($in) ORDER BY RAND() LIMIT ?";
            $stmt = $this->db->getPDO()->prepare($sql);

            $counter = 1;
            foreach($this->questionTypes as $qType) {
                $stmt->bindValue($counter, $qType, PDO::PARAM_INT);
                $counter++;
            }

            $stmt->bindValue($counter, $this->numOfQuestions, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll();
            return $result;        
        }
    
        private function loadAnswers($qID) {
            $this->db->query("SELECT * FROM ". $this->short . "_answers WHERE questionid = ?", array($qID));
            $result = $this->db->results();
            return $result;
        }
    
        private function loadDescription($dID) {
            $this->db->get($this->short . '_descriptions', array('id', '=', $dID));
            return $this->db->first()->description;
            //return 'XXX';
        }

         private function loadQuestionSet() {
            $questions = $this->loadQuestions();
            foreach(json_decode(json_encode($questions), true) as $q) {
                $this->question = $q['question'];
                $answers = $this->loadAnswers($q['id']);
                $counter = 0;
                foreach (json_decode(json_encode($answers), true) as $a) {
                    array_push($this->answerSet, $a['answer']);
                    if($a['correct'] == 1) {
                        $this->correctAnswerID = $counter;
                    }
                    $counter++;
                }                
                $desc = $this->loadDescription($q['description']);
            
                $fullQuestion = [
                    'question' => $this->question,
                    'options' => $this->answerSet,
                    'answer' => $this->correctAnswerID,
                    'description' => $desc
                ];            
                array_push($this->questionSet, $fullQuestion);
            
                $this->fullQuestion = array();
                $this->question = "";
                $this->answerSet = array();
                $this->description = "";
                $counter = 0;
            }
            return $this->questionSet;          
        }

        public function getQuestionSet() {
            return $this->loadQuestionSet();

        }

        public function getQuestions() {
            return $this->loadQuestions();
        }

    }