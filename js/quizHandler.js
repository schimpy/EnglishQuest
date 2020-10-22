const quizHomeBox = document.querySelector(".quiz-home-box");
const quizBox = document.querySelector(".quiz-box");
const quizOverBox = document.querySelector(".quiz-over-box");

const remainingTime = document.querySelector(".remaining-time");
const timeIsUpText = document.querySelector(".time-is-up");
const correctAnswers = document.querySelector(".score-value");

const currentQuestionNumber = document.querySelector(".question-number");
const questionText = document.querySelector(".question-text");
const optionBox = document.querySelector(".option-box");
const description = document.querySelector(".description");

const startQuizButton = document.querySelector(".start-quiz-btn");
const nextQuestionButton = document.querySelector(".next-question-btn");
const seeResultButton = document.querySelector(".see-result-btn");
const continueButton = document.querySelector(".continue-btn");

const XPForCorrectAnswer = 1;
const timeForQuestion = 16;
const numOfQuestions = 5;

var slug = findGetParameter('slug');
var qid = findGetParameter('qtype');
var uid = findGetParameter('uid');
var url = 'functions/quizinit.php?slug=' +  slug + '&id=' + qid + '&num=' + numOfQuestions
var redirect = 'https://schimpy.cz/eq/unit.php?slug=' + slug + '&id=' + qid;

let questionSet = [];
let questionIndex = 0;
let number = 0;
let score = 0;
let questionArray = [];
let interval;
let timeLeft = 0;
let xp= 0;
let coins = 0;

function load() {
    number++;
    questionText.innerHTML = questionSet[questionIndex].question;
    createOptions();
    renderScore();
    currentQuestionNumber.innerHTML = number + " / " + questionSet.length;
}

function createOptions() {
    optionBox.innerHTML = "";
    let animationDelay = 0.2;

    for(let i = 0; i < questionSet[questionIndex].options.length; i++) {
        const option = document.createElement("div");
        option.innerHTML = questionSet[questionIndex].options[i];
        option.classList.add("option");
        option.id = i;
        option.style.animationDelay = animationDelay + "s";
        animationDelay += 0.2;        
        option.setAttribute("onclick", "check(this)");
        optionBox.appendChild(option);
    }
}

function check(el) {
    const id = el.id;
    if(id == questionSet[questionIndex].answer) {
        el.classList.add("correct");
        score++;
        timeDiff = (timeForQuestion * 100) - interval
        timeLeft += timeDiff;
        renderScore();        
    } else {
        el.classList.add("wrong");
        for(let i = 0; i < optionBox.children.length; i++) {
            if(optionBox.children[i].id == questionSet[questionIndex].answer) {
                optionBox.children[i].classList.add("show-correct");
            }
        }
    }

    disableOptions();
    showAnswerDescription();
    showNextQuestionButton();
    stopTimer();

    if(number == questionSet.length) {
        quizOver();
    }
}

function disableOptions() {
    for(let i = 0; i < optionBox.children.length; i++) {
        optionBox.children[i].classList.add("already-answered");
    }
}

function generateRandomQuestion() {
    const randomNumber = Math.floor(Math.random() * questionSet.length);
    let hitDuplicateQuestion = 0;

    if(questionArray.length == 0) {
        questionIndex = randomNumber;        
    } else {

        for(let i = 0; i < questionArray.length; i++) {
            if(randomNumber == questionArray[i]) {
                hitDuplicateQuestion = 1;
            }
        }

        if(hitDuplicateQuestion == 1) {
            generateRandomQuestion();
            return;
        } else {
            questionIndex = randomNumber;
        }
    }
    questionArray.push(randomNumber);
    load();
}

function showAnswerDescription() {
    if(typeof questionSet[questionIndex].description !== 'undefined') {
        description.classList.add("show");
        description.innerHTML = questionSet[questionIndex].description;
    } 
}

function hideAnswerDescription() {
    description.classList.remove("show");
    description.innerHTML = "";
}

function showNextQuestionButton() {
    nextQuestionButton.classList.add("show");
}

function hideNextQuestionButton() {
    nextQuestionButton.classList.remove("show");
}

function showSeeResultButton() {
    seeResultButton.classList.add("show"); 
}

function hideSeeResultButton() {
    seeResultButton.classList.remove("show"); 
}

function showTimeIsUpText() {
    timeIsUpText.classList.add("show"); 
}

function hideTimeIsUpText() {
    timeIsUpText.classList.remove("show"); 
}

function removeTimeColor() {
    remainingTime.classList.remove("time-orange");
    remainingTime.classList.remove("time-red");
}

function renderScore() {
    correctAnswers.innerHTML = score;
}

function showNextQuestion() {
    questionIndex++;
    generateRandomQuestion();
    hideNextQuestionButton();
    hideAnswerDescription();
    hideTimeIsUpText();
    removeTimeColor();
    startTimer();
}

function startTimer() {
    let timeLimit = timeForQuestion;
    interval = setInterval(() => {
        timeLimit--;
        if(timeLimit < 10) {
            timeLimit = "0" + timeLimit;
            remainingTime.classList.add("time-orange");
        }
        if(timeLimit < 6) {
            remainingTime.classList.remove("time-orange");
            remainingTime.classList.add("time-red");
        }
        if(timeLimit == 0) {
            clearInterval(interval);
            timeOver();
        }
        remainingTime.innerHTML = timeLimit;
    }, 1000);
}

function stopTimer() {
    clearInterval(interval);
}

function timeOver() {
    showTimeIsUpText();
    for(let i = 0; i < optionBox.children.length; i++) {
        if(optionBox.children[i].id == questionSet[questionIndex].answer) {
            optionBox.children[i].classList.add("show-correct");
        }
    } 
    disableOptions();
    showAnswerDescription();
    showNextQuestionButton();  
}

function quizOver() {
    hideNextQuestionButton();
    showSeeResultButton();
}

function quizResult() {
    const text = ["Perfect, you made no mistake!", "Very good, you know the stuff!", "Not bad, but you can do better!", "Give it another try!"];
    const color = ["#059e4c", "#249e05", "#ca8216", "#d23723"];
    const evaluationElement = document.querySelector(".total-evaluation");
    const percentage = Math.round((score/questionSet.length) * 100);
    let evaluationText = "";

    if(percentage == 100) {
        evaluationText = text[0];
        evaluationElement.style.color = color[0];
    } else if(percentage > 79) {
        evaluationText = text[1];
        evaluationElement.style.color = color[1];
    } else if(percentage > 49) {
        evaluationText = text[2];
        evaluationElement.style.color = color[2];
    } else {
        evaluationText = text[3];
        evaluationElement.style.color = color[3];
    }

    timeLeft = timeLeft / 100;
    timeSum = timeForQuestion * questionSet.length;
    timeRatio = Math.round((timeLeft * 100)/ timeSum);
    
    if(timeRatio > 89 || percentage > 89) {
        coins += 3;
        xp += 5;
        xp += score * XPForCorrectAnswer;
    } else if(timeRatio > 79 || percentage > 79) {
        coins += 2;
        xp += 4;
        xp += score * XPForCorrectAnswer;
    } else if(timeRatio > 69 || percentage > 69) {
        coins += 2;
        xp += 3;
        xp += score * XPForCorrectAnswer;
    } else if(timeRatio > 59 || percentage > 59) {
        coins += 1;
        xp += 2;
        xp += score * XPForCorrectAnswer;
    } else if(percentage > 49) {
        coins += 1;
        xp += 1;
        xp += score * XPForCorrectAnswer;
    } else {
        xp += score * XPForCorrectAnswer;
    }     

    document.querySelector(".total-questions").innerHTML = questionSet.length;
    document.querySelector(".total-correct").innerHTML = score;
    document.querySelector(".total-wrong").innerHTML = questionSet.length - score;
    document.querySelector(".total-percentage").innerHTML = percentage + "%";
    document.querySelector(".total-evaluation").innerHTML = evaluationText;
    document.querySelector(".rewards-xp").innerHTML = xp + ' <i class="fas fa-star" style="color: gold;"></i>';
    document.querySelector(".rewards-coins").innerHTML = coins + ' <i class="fas fa-coins" style="color: darkorange;"></i>';
}

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    var items = location.search.substr(1).split("&");
    for (var index = 0; index < items.length; index++) {
        tmp = items[index].split("=");
        if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
    }
    return result;
}

startQuizButton.addEventListener("click", () => {
    quizHomeBox.classList.remove("show");
    quizBox.style.display = "block";
    quizBox.classList.add("show");
    startTimer();
    generateRandomQuestion(); 
});

nextQuestionButton.addEventListener("click", showNextQuestion);

seeResultButton.addEventListener("click", () => {
    quizBox.style.display = "none";
    seeResultButton.classList.remove("show");
    quizOverBox.classList.add("show");
    quizResult();
});

continueButton.addEventListener("click", () => {
    $.ajax({
        type: "POST",
        url: "functions/saveresults.php",
        data: { 
            userid: uid,
            xp: xp,
            coins: coins
        },
        success: function(data) {
            history.go(-1);
        }
    });
});

window.onload=()=>{
    

    var slug = findGetParameter('slug');
    var qid = findGetParameter('qtype');
    var uid = findGetParameter('uid');
    var url = 'functions/quizinit.php?slug=' +  slug + '&id=' + qid + '&num=' + numOfQuestions
    var redirect = 'https://schimpy.cz/eq/unit.php?slug=' + slug + '&id=' + qid;


    $.ajax({
        type: 'GET',
        url: url,
        success: function(data) {
            questionSet = jQuery.parseJSON(data);             
        }
    });

    quizBox.style.display = "none";
    quizHomeBox.classList.add("show");
}
