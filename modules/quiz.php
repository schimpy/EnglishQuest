<?php
    // Include INIT file
    require_once '../init.php';

    $quizLoader = new QuizLoader('trg', "3", array("0", "1"));

    echo json_encode($quizLoader->getQuestionSet(), JSON_PRETTY_PRINT);
