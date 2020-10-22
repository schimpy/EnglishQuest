const form = document.getElementById('customAvaratUpload');
const file = document.getElementById('customAvatar');

form.addEventListener('submit', e => {
    e.preventDefault();

    const endpoint = 'functions/uploadavatar.php';
    const formData = new FormData();

    formData.append('file', file.files[0]);
    fetch(endpoint, {
        method: 'post',
        body: formData
    }).catch(console.error);

    document.getElementById('updateAvatarModal').style.display='block';
})

function openTab(evt, tabName) {
    var i, x, tablinks;
    x = document.getElementsByClassName("tab");

    for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }

    tablinks = document.getElementsByClassName("tablink");
        for (i = 0; i < x.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" w3-deep-orange", "");
        }

    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " w3-deep-orange";
}

function checkEmail() {
    document.getElementById("emailSubmit").disabled = true;
    var email = document.getElementById("emailChange").value;
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;

    if (regex.test(email) == false) {                
        $('#email-status').html("Invalid format of e-mail!");
        return false;
    } else {
        if(email) {
            $.ajax({
                type: 'post',
                url: 'functions/checkemail.php',
                data: {
                    verify_email: email
                },
                success: function (response) {
                    $('#email-status').html(response);                            
                    if(response == "E-mail is available!") {   
                        document.getElementById("emailSubmit").disabled = false;                             
                        return true;
                    } else {
                        return false;
                    }
                }
            });
        } else {
            $('#email-status').html("");
            return false;
        }
    }
}

function updateAvatar(img) {
    $.ajax({
        type: "POST",
        url: "functions/updateavatar.php",
        data: 'img='+img,
        cache: false,
        success: function(response)
        {
            if(response == "SUCCESS") {
                openTab(event, 'avatar');
                document.getElementById('updateAvatarModal').style.display='block';
            };
        }
    });
}

function checkPassword() {
    document.getElementById("pwdSubmit").disabled = true;
    var pwdOld = document.getElementById("password_old").value;
    var pwdNew = document.getElementById("password_new").value;
    var pwdCheck = document.getElementById("password_check").value;

    if(pwdOld) {
        $.ajax({
            type: 'post',
            url: 'functions/checkpassword.php',
            data: {
                verify_password: pwdOld
            },
            success: function (response) {
                $('#pwd-status').html(response);                 
                if(response == "OK") {   
                    if (pwdNew != pwdCheck) {
                        $('#pwd-status').html("Passwords must be identical!");
                        return false;
                    } else {
                        $('#pwd-status').html("");  
                        document.getElementById("pwdSubmit").disabled = false;
                        return true;
                    }
                } else {
                    return false;
                }
            }
        });
    } else {
        $('#pwd-status').html("Type in your old password!");
        return false;
    }
}

function logout() {
    $.ajax({
                type: 'post',
                url: 'logout.php',
                success: function () {
                    window.location.href = 'https://schimpy.cz/eq/';
                }
            });
}