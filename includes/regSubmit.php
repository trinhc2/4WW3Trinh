<?php
    //MySQL information
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "arcades";
    
    $emailTaken = false;
    $accountCreated = false;
    $errors = array(); //array to keep track of errors

    //Setting form input default values
    //When the user submits the form with an error, the forms that are validated are resused 
    $regFirstName = "";
    $regLastName = "" ;
    $regEmail = "";
    $regDate = "";
    $regPass = "";
    $regPass2 = "";
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password); //connect to database

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($_POST['reg_user'])) { //If the user clicks submit button this would be set
            //Retrieve form values via post
            $regFirstName = $_POST['firstName'];
            $regLastName = $_POST['lastName'];
            $regEmail = $_POST['email'];
            $regPass = $_POST['password'];
            $regPass2 = $_POST['confirmPass'];
            $regDate = $_POST['date'];


            //SERVERSIDE VALIDATION
            if (empty($regFirstName)) {
                array_push($errors, "First name is required");
            }
            if (empty($regLastName)) {
                array_push($errors, "Last name is required");
            }
            if (empty($regPass)) {
                array_push($errors, "Password is required");
            }
            if ($regPass != $regPass2) {
                array_push($errors, "Passwords do not match");
            }
            if (empty($regDate)) {
                array_push($errors, "Date is required");
            }
            if (!preg_match('/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/'
                            , $regEmail)) {
                                array_push($errors, "Email invalid (Example: abc@domain.com)");
                            }
    
            //Query to check if the user exists in the database
            $user_exist_query = "SELECT * FROM users WHERE email=:email LIMIT 1"; 
            $stmt = $conn->prepare($user_exist_query); //preparing sql statement
            $stmt->bindParam(':email', $regEmail); //binding param
            $stmt->execute(); //executing query
    
            if ($stmt->rowCount() != 0) { //if user exists
                $emailTaken = true; //email is taken
            }
    
            if ($emailTaken == false) { //if email is not taken
                if (count($errors) == 0) {
                    $salt = bin2hex(random_bytes(20)); //generate salt
                    $passhash = sha1($regPass . $salt); //hash password
                    $query = "INSERT INTO users (email, firstname, lastname, birth, salt, passwordhash)
                                VALUES (:email, :firstname, :lastname, :birth, :salt, :passwordhash)"; //insert values
                    $insert = $conn->prepare($query);
                    $insert->bindParam(':email', $regEmail);
                    $insert->bindParam(':firstname', $regFirstName);
                    $insert->bindParam(':lastname', $regLastName);
                    $insert->bindParam(':birth', $regDate);
                    $insert->bindParam(':salt', $salt);
                    $insert->bindParam(':passwordhash', $passhash);
                    if ($insert->execute() === TRUE) {
                        $accountCreated = true;
                        $regFirstName = "";
                        $regLastName = "" ;
                        $regEmail = "";
                        $regDate = "";
                        $regPass = "";
                        $regPass2 = "";
                    }
                    else {
                        echo "Error: " . $query . "<br>" . $conn->error;
                    }
                }
            
            }
        }
    }
    catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    }

    $conn = null; //close connection

?>