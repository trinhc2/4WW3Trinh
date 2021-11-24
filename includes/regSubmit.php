<?php
    //MySQL information
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "arcades";

    $conn = new mysqli($servername, $username, $password, $dbname); //connect to databse

    //Checking if connection was succesful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $emailTaken = false;
    $accountCreated = false;

    //Setting form input default values
    //When the user submits the form with an error, the forms that are validated are resused 
    $regFirstName = "";
    $regLastName = "" ;
    $regEmail = "";
    $regDate = "";
    $regPass = "";
    $regPass2 = "";

    if (isset($_POST['reg_user'])) { //If the user clicks submit button this would be set
        //Retrieve form values via post
        $regFirstName = $_POST['firstName'];
        $regLastName = $_POST['lastName'];
        $regEmail = $_POST['email'];
        $regPass = $_POST['password'];
        $regPass2 = $_POST['confirmPass'];
        $regDate = $_POST['date'];

        //Query to check if the user exists in the database
        $user_exist_query = "SELECT * FROM users WHERE email=? LIMIT 1"; 
        $stmt = $conn->prepare($user_exist_query); //preparing sql statement
        $stmt->bind_param("s", $regEmail); //binding param
        $stmt->execute(); //executing query
        $result = $stmt->get_result(); //obtaining result

        if ($result->num_rows != 0) { //if user exists
            $emailTaken = true; //email is taken
        }

        if ($emailTaken == false) { //if email is not taken
            $salt = bin2hex(random_bytes(20)); //generate salt
            $passhash = sha1($regPass . $salt); //hash password
            $query = "INSERT INTO users (email, firstname, lastname, birth, salt, passwordhash)
                        VALUES (?, ?, ?, ?, ?, ?)"; //insert values
            $insert = $conn->prepare($query);
            $insert->bind_param("ssssss", $regEmail, $regFirstName, $regLastName, $regDate, $salt, $passhash);
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

    $stmt->close();
    $insert->close();
    $conn->close(); //close connection

?>