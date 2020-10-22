
document.getElementsByClassName("tablink")[0].click();

function openTab(evt, tabName) {
    var i, x, tablinks;
    x = document.getElementsByClassName("tab");

    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }

    tablinks = document.getElementsByClassName("tablink");

    for (i = 0; i < x.length; i++) {
        tablinks[i].classList.remove("w3-light-grey");
    }

    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.classList.add("w3-light-grey");
}

var slideIndex = 1;
showDivs(slideIndex);

function plusDivs(n) {
    showDivs(slideIndex += n);
}

function showDivs(n) {
    var i;
    var x = document.getElementsByClassName("tourSlides");
    if (n > x.length) {
        document.getElementById('initial').style.display = "none";
        
    }
    if (n < 1) {
        slideIndex = x.length
    }
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";  
    }
    x[slideIndex-1].style.display = "block";  
}

function updateAvatar(img)
{
    jQuery.ajax({
    type: "POST",
    url: "functions/updateavatar.php",
    data: 'img='+img,
    cache: false,
    success: function(response)
    {
        if(response == "SUCCESS") {
            openTab(event, 'tour');
        };
    }
    });
}
