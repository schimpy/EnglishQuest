<?php
    // Include INIT file
    require_once '../init.php';

    // Create a user instance
    $user = new User();

    $question = "";
    $answerSet = array();
    $correctAnswerID;
    $description = "";
    $questionSet = array();

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

    $questions = loadQuestions(5, $user);

    foreach (json_decode(json_encode($questions), true) as $q) {
        var_dump($q['question']);
        $question = $q['question'];
        $options = loadAnswers($q['id'], $user);
        $counter = 0;
        foreach (json_decode(json_encode($options), true) as $o) {
            array_push($answerSet, $o['answer']);
            if($o['correct'] == 1) {
                $correctAnswerID = $counter;
            }
            $counter++;
        }
        $counter = 0;
        $desc = loadDescription($q['description'], $user);

        $fullQuestion = [
            'question' => $question,
            'options' => $answerSet,
            'answer' => $correctAnswerID,
            'description' => 'XXX'
        ];

        array_push($questionSet, $fullQuestion);

        $fullQuestion = array();
        $question = "";
        $answerSet = array();
        $description = "";
    }



/* $questionSet = [
    [
        'question'=> 'If you study hard, you _____ the test.',
        'options'=> ['pass', 'will pass', 'would pass', 'has passed'],
        'answer'=> 1, 
        'description'=> 'This is an example of the first conditional.'
    ],
    [
        'question'=> 'If I _____ a million pounds, I would buy a big yacht.',
        'options'=> ['has', 'have', 'will have', 'had'],
        'answer'=> 3, 
        'description'=> 'This is an example of the second conditional.'
    ],
    [
        'question'=> 'If I _____ her, I will tell her',
        'options'=> ['will see', 'see', 'saw', 'have seen'],
        'answer'=> 1, 
        'description'=> 'This is an example of the first conditional.'
    ],
    [
        'question'=> 'If I met the Queen of England, I _____ hello.',
        'options'=> ['would say', 'have said', 'will say', 'say'],
        'answer'=> 0, 
        'description'=> 'This is an example of the second conditional.'
    ],
    [
        'question'=> 'If I _____ you, I wouldn not go out with that man.',
        'options'=> ['am', 'were', 'was', 'will be'],
        'answer'=> 1, 
        'description'=> 'This is an example of the second conditional in the form of subjunctive.'
    ]
]; */

echo json_encode($questionSet);

// SAVE

foreach (json_encode($questions) as $q) {
    var_dump($q['question']);
    $question = $q['question'];
    $options = loadAnswers($q['id']);
    $counter = 0;
    foreach (json_decode(json_encode($options), true) as $o) {
        array_push($answerSet, $o['answer']);
        if($o['correct'] == 1) {
            $correctAnswerID = $counter;
        }
        $counter++;
    }
    $counter = 0;
    $desc = loadDescription($q['description']);

    $fullQuestion = [
        'question' => $question,
        'options' => $answerSet,
        'answer' => $correctAnswerID,
        'description' => 'XXX'
    ];

    array_push($questionSet, $fullQuestion);

    $fullQuestion = array();
    $question = "";
    $answerSet = array();
    $description = "";
    }