<?php
    // Include INIT file
    require_once '../init.php';


    if(isset($_GET['slug'])) {
        $slug = $_GET['slug'];
    } else {
        $slug = 'cnd';
    }

    if(isset($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        $id = 1;
    }

    if(isset($_GET['num'])) {
        $num = $_GET['num'];
    } else {
        $num = 5;
    }

    $idArray = array();
    array_push($idArray, $id);

    $quizLoader = new QuizLoader($slug, $num, $idArray);

    echo json_encode($quizLoader->getQuestionSet());