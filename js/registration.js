//Validates form, changes css to show errors and show required sections
function validate(form) {
    //if first name is blank
    if (form.firstName.value == "") {
        //Change background to red
        document.getElementById("firstName").style.backgroundColor="rgb(214, 85, 109)";
        //Reveal that this area is required
        document.getElementById("firstNameValid").style.display="block";
        return false;
    }
    else {
        //Set errors back to normal
        document.getElementById("firstName").style.backgroundColor="white";
        document.getElementById("firstNameValid").style.display="none";

    }
    if (form.lastName.value == "") {
        document.getElementById("lastName").style.backgroundColor="rgb(214, 85, 109)";
        document.getElementById("lastNameValid").style.display="block";
        return false;
    }
    else {
        document.getElementById("lastName").style.backgroundColor="white";
        document.getElementById("lastNameValid").style.display="none";
    }
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
    //If passwords do not match
    if (form.password.value != form.confirmPass.value) {
        document.getElementById("confirmPass").style.backgroundColor="rgb(214, 85, 109)";
        document.getElementById("confirmPassValid").style.display="block";
        return false;
    }
    else {
        document.getElementById("confirmPass").style.backgroundColor="white";
        document.getElementById("confirmPassValid").style.display="none";
    }
    if (form.date.value == ""){
        document.getElementById("date").style.backgroundColor="rgb(214, 85, 109)";
        document.getElementById("dateValid").style.display="block";
        return false;
    }
    else {
        document.getElementById("date").style.backgroundColor="white";
        document.getElementById("dateValid").style.display="none";
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