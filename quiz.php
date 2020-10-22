<?php
    require 'header.php';

    if(!$user->isLoggedIn()) {
        Redirect::to('index.php');
    }

?>

<div class="quiz-home-box custom-box">
    <h4>You will have 15 seconds to answer each question.</h4>    
    <button class="start-quiz-btn btn">Start the Quiz</button>
</div>

<div class="quiz-box custom-box">

    <div class="stats">
        <div class="quiz-time">
            <div class="remaining-time"></div>
            <span class="time-is-up">Time's up!</span>
        </div>
        <div class="score-board">
            <span class="score-text">Score:</span>
            <span class="score-value">0</span>
        </div>
    </div>

    <div class="question-box">
        <div class="question-number"></div>
        <div class="question-text">
           
        </div>
    </div>

    <div class="option-box">
    </div>

    <div class="description">        
    </div>

    <div class="controls">
        <button type="button" class="next-question-btn btn">Next Question</button>
        <button type="button" class="see-result-btn btn">See Your Result</button>
    </div>

</div>

<div class="quiz-over-box custom-box">
    <h3>Quiz Result</h2>
    <h4>Total Questions: <span class="total-questions"></span></h4>
    <h4>Correct: <span class="total-correct"></span></h4>
    <h4>Wrong: <span class="total-wrong"></span></h4>
    <h4>Percentage: <span class="total-percentage"></span></h4>
    <h3 class="total-evaluation"></h3>
    <div class="rewards">
        <h4>You have earned: </h4>
        <div class="rewards-xp"></div>
        <div class="rewards-coins"></div>
    </div>
    <button type="button" class="continue-btn btn">Continue</button>
</div>

<script src="js/quizHandler.js"></script>

<?php 

include 'footer.php';

