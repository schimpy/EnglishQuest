<?php
    // Include INIT file
    require_once '../init.php';

    // Create a user instance
    $user = new User();

    echo "QUESTIONS<br><br>";

    $questions = loadQuestions(5, $user);

    foreach (json_decode(json_encode($questions), true) as $question) {
        echo $question['question'];
        $answers = loadAnswers($question['id'], $user);
        echo "<br><ol>";        

        foreach (json_decode(json_encode($answers), true) as $answer) {
            if($answer['correct'] == 1) {
                echo "<li><b>" .  $answer['answer'] . "</b></li>";
            } else {
                echo "<li>" . $answer['answer'] . "</li>";
            }
        }
        
        echo "</ol><br><br>";

        $desc = json_decode(json_encode(loadDescription($question['description'], $user)));
        echo $desc['description'];
        echo "<br><br>";
    }

    function loadQuestions($num, $user) {
        $user->getDB()->query("SELECT * FROM cnd_questions LIMIT ?", array($num));
        return $user->getDB()->results();
    }

    function loadAnswers($qID, $user) {
        $user->getDB()->query("SELECT * FROM cnd_answers WHERE questionid = ?", array($qID));
        return $user->getDB()->results();
    }

    function loadDescription($dID, $user) {
        $user->getDB()->query("SELECT * FROM cns_descriptions WHERE id = ?", array($dID));
        return $user->getDB()->first();
    }

    