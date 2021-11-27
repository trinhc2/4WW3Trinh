//Validates form, changes css to show errors and show required sections
function validate(form) {
    //If email is invalid
    if (!validateEmail(form.email.value)) {
        document.getElementById("email").style.backgroundColor="rgb(214, 85, 109)";
        document.getElementById("emailValid").style.display="block";
        return false;
    }
    else {
        document.getElementById("email").style.backgroundColor="white";
        document.getElementById("emailValid").style.display="none";
    }
    if (form.password.value == "") {
        document.getElementById("password").style.backgroundColor="rgb(214, 85, 109)";
        document.getElementById("passwordValid").style.display="block";
        return false;
    }
    else {
        document.getElementById("password").style.backgroundColor="white";
        document.getElementById("passwordValid").style.display="none";
    }
    return true;
}

//Code from: https://stackoverflow.com/questions/46155/how-to-validate-an-email-address-in-javascript
function validateEmail(email) {
    //Regular expression that matches email addresses
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    //Test the given email with the regular expression
    return re.test(String(email).toLowerCase());
}