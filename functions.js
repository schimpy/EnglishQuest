function checkEmail() {

    alert("OK");
    var email = document.getElementById("emailChange").value;

    if(email) {
        alert("OK2");
        $.ajax({
            type: 'post',
            url: 'functions/checkemail.php',
            data: {
                verify_email: email
            },
            success: function (response) {
                $('#email-status').html(response);
                if(response == "OK") {
                    alert("OK3");
                    return true;
                } else {
                    alert("OK4");
                    return false;
                }
            }
        });
    } else {
        alert("OK5");
        $('#email-status').html("NOK");
        return false;
    }
}