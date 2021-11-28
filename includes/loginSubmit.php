<?php
    if (session_id() == "") { //if no existing session, creat one
        session_start();
    }

    //MySQL information
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "arcades";
    
    $errors = array(); //array to keep track of errors

    //Setting form input default values
    //When the user submits the form with an error, the forms that are validated are resused 
    $loginEmail = "";
    $loginPass = "";
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password); //connect to database

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($_POST['login_user'])) { //If the user clicks submit button this would be set
            //Retrieve form values via post
            $loginEmail = $_POST['email'];
            $loginPass = $_POST['password'];

            //SEVERSIDE VALIDATION
            if (empty($loginEmail)) {
                array_push($errors, "Email is required");
            }
            if (empty($loginPass)) {
                array_push($errors, "Password is required");
            }
    
            if (count($errors) == 0) {
                //Retrieve user information based on email
                $query = "SELECT * from users
                    WHERE email=:email LIMIT 1";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':email', $loginEmail);
                $stmt->execute();

                if ($stmt->rowCount() != 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $hashPass = sha1($loginPass . $row['salt']);

                    $query2 = "SELECT * from users
                    WHERE email=:email AND passwordhash=:password LIMIT 1";
                    $stmt2 = $conn->prepare($query2);
                    $stmt2->bindParam(':email', $loginEmail);
                    $stmt2->bindParam(':password', $hashPass);
                    $stmt2->execute();
                    if ($stmt2->rowCount() != 0) {
                        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
                        $_SESSION['name'] = $row['firstname']; //update session name
                        
                        $_SESSION['isLoggedIn'] = true; //update session status
                        header('location: search.php');

                    }
                    else {
                        array_push($errors, "Incorrect Password");
                    }


                }
                else {
                    array_push($errors, "User does not exist");
                }
            }
            
        }
    }
    catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    }

    $conn = null; //close connection

?>