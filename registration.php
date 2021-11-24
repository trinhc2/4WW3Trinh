<!DOCTYPE html>
<?php
include("./includes/regSubmit.php"); //registration server side
?>
<html lang="en">
    <head>
        <title>Sign Up</title>
        <link rel="stylesheet" href="./styles/registration.css">
        <?php include("./includes/head.php")?>
        <meta name="description" content="Registration page containing forms for users to enter information required to sign up for an account.">
        <script type="text/javascript" src="./js/registration.js"></script>
    </head>

    <?php 
    include("./includes/header.php"); //Include header elements
    ?>
    <body>
        <!--Form for registering-->
        <form class="register" onsubmit="return validate(this)" method="post" action="registration.php">
        <!--After validating, submits post back to this page, where info gets parsed in regSubmit (see top include line)-->
            <div class="registerGrid">
                <h1 class="whole title">Registration</h1>
                <div class="half">
                    <label for="firstName">First Name</label>
                    <input id="firstName" type="text" name="firstName" value="<?php echo $regFirstName; ?>">
                    <p id="firstNameValid">Required</p>
                </div>
                <div class="half">
                    <label for="lastName">Last Name</label>
                    <input id="lastName" type="text" name="lastName" value="<?php echo $regLastName; ?>">
                    <p id="lastNameValid">Required</p>
                </div>
                <div class="whole">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="<?php echo $regEmail; ?>">
                    <p id="emailValid">Required <br>(Example: abc@domain.com)</p>
                    <?php
                    if ($emailTaken){
                        echo "<p>Email Taken</p>";
                    }
                    ?>
                </div>
                <div class="whole">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" value="<?php echo $regPass; ?>">
                    <p id="passwordValid">Required</p>
                </div>
                <div class="whole">
                    <label for="confirmPass">Confirm Password</label>
                    <input id="confirmPass" type="password" name="confirmPass" value="<?php echo $regPass2; ?>">
                    <p id="confirmPassValid">Passwords do not match</p>
                </div>
                <div class="whole">
                    <label for="date">Date of Birth</label>
                    <input id="date" type="date" name="date" value="<?php echo $regDate; ?>">
                    <p id="dateValid">Required</p>
                </div>
                <div class="fourth">
                    <button class="submit" type="submit" name="reg_user">
                        Sign Up
                    </button>
                </div>
                <div class="fourth">
                        <button class="cancel" onclick="location.href='./search.html'" type="button">
                            Cancel
                        </button>
                </div>
                <div class="whole">
                    <?php
                    if ($accountCreated){
                        echo "<p>Account succesfully created.</p>";
                    }
                    ?>
                </div>
            </div>
        </form>
        <?php 
        include ("./includes/footer.php"); //Include footer elements
        ?>
    </body>
</html>