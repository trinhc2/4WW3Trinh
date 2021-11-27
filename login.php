<!DOCTYPE html>
<?php
include("./includes/loginSubmit.php"); //registration server side
?>
<html lang="en">
    <head>
        <title>Sign In</title>
        <link rel="stylesheet" href="./styles/registration.css">
        <?php include("./includes/head.php")?>
        <meta name="description" content="Login page for users.">
        <script type="text/javascript" src="./js/login.js"></script>
    </head>

    <?php 
    include("./includes/header.php"); //Include header elements
    ?>
    <body>
        <!--Form for registering-->
        <form class="register" onsubmit="return validate(this)" method="post" action="login.php">
        <!--After validating, submits post back to this page, where info gets parsed in regSubmit (see top include line)-->
            <div class="registerGrid">
                <h1 class="whole title">Login</h1>
                <div class="whole">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="<?php echo $loginEmail; ?>">
                    <p id="emailValid">Required <br>(Example: abc@domain.com)</p>
                </div>
                <div class="whole">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password">
                    <p id="passwordValid">Required</p>
                </div>
                <div class="fourth">
                    <button class="submit" type="submit" name="login_user">
                        Sign In
                    </button>
                </div>
                <div class="fourth">
                        <button class="cancel" onclick="location.href='./search.php'" type="button">
                            Cancel
                        </button>
                </div>
                <div class="whole">
                    <?php

                    //If there are errors with registration, print them to the user
                    if (count($errors) > 0) {
                        foreach ($errors as $error) {
                            echo "<p>$error</p>";
                        }
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