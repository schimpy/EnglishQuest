const dragSource = document.querySelector(".drag-source");
const dragTarget = document.querySelector(".drag-target");

const draggables = document.querySelector("word");

draggables.forEach(draggable => {
    draggable.addEventListener('dragstart', console.log('DRAG START'))
})

senteceSet = ["Have you ever been in Paris?",
                "May the force be with you!",
                "Never will I see her again.",
                "We live in Prague, and so does she.",
                "Up the hill sits a white castle.",
                "Who is going to help you?",
                "No other city has this particular smell",
                "Little did I dream that I would be the only person awarded.",
                "Should he remember his own name, we will be able to help him",
                "Were he to push the button, we would all have problems.",
                "Had we arrived sooner, we wouldn’t have missed the beginning.",
                "Were the driver faster, we would’ve arrived ages ago."];


let targetSentence = [];
let sourceSentence = "Do you like beer?";
let split = sourceSentence.split(" ");
console.log(split);

let randomized = shuffleArray(split);
console.log(randomized);

function renderSentenceWords() {
    for(let i = 0; i < randomized.length; i++) {
        let word = document.createElement("span");
        word.innerHTML = randomized[i];
        word.classList.add("word");
        dragSource.appendChild(word);
    }
}

function shuffleArray(array) {
    for (var i = array.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var temp = array[i];
        array[i] = array[j];
        array[j] = temp;
    }
    return array;
}

function moveToTarget(el) {
    console.log(el);
}



window.onload=()=>{
    renderSentenceWords();
}