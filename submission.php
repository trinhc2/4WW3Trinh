<!DOCTYPE html>
<?php
    if (session_id() == "") { //if no session, creat one
        session_start();
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "arcades";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password); //connect to database

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($_POST['locationSubmit'])) { //If the user clicks submit button this would be set
        
            $sql = "INSERT INTO `location` (name, description, phone, country, state, city, postal code, address, x, y, picture)
            VALUES (:name, :description, :phone, :country, :state, :city, :postalcode, :address, :x, :y, :picture)"; //insert statement
            $stmt = $conn->prepare($sql); //prepared statement
            $stmt->bindParam(':name', $_POST['name']); //binding values
            $stmt->bindParam(':description', $_POST['description']);
            $stmt->bindParam(':phone', $_POST['phoneNum']);
            $stmt->bindParam(':country', $_POST['country']);
            $stmt->bindParam(':state', $_POST['state']);
            $stmt->bindParam(':city', $_POST['city']);
            $stmt->bindParam(':postalcode', $_POST['postalCode']);
            $stmt->bindParam(':address', $_POST['address']);
            $stmt->bindParam(':x', $_POST['xcoord']);
            $stmt->bindParam(':y', $_POST['ycoord']);
            $stmt->bindParam(':picture', $_POST['locImg']);
    
        }
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

?>

<html lang="en">
    <head>
        <link rel="stylesheet" href="./styles/submission.css">
        <?php include("./includes/head.php")?>
        <meta name="description" content="Form for users to submit a new arcade to the website.">
        <script type="text/javascript" src="./js/submission.js"></script>
        <title>Submission</title>
    </head>
    <?php 
    include("./includes/header.php"); //Include header elements
    ?>
    <body>
    <?php     
        if (!isset($_SESSION['isLoggedIn'])) {//if user is not logged in, tell them
            echo '<h2 style="color:crimson; text-align:center">Please sign in to submit a location</h2>';
        }
        else { //else display submission form

        
        ?>
        <!--Form for submitting a location-->
        <form method="post" action="submisison.php">
            <div class="subGrid">
                <h1 class="whole title">
                    Submission
                </h1>
                <div class="whole">
                    <label for="name">Name</label>
                    <input type="text"  name="name" required pattern="[A-Za-z]+">
                </div>
                <div class="whole">
                    <label for="phoneNum">Phone Number</label>
                    <!--https://stackoverflow.com/questions/16699007/regular-expression-to-match-standard-10-digit-phone-number-->
                    <input type="text" name="phoneNum" required pattern="\d{3}[\s.-]?\d{3}[\s.-]?\d{4}" placeholder="Ex: 123 456 7890">
                </div>
                <div class="fourth">
                    <label for="country">Country</label>
                    <input type="text"  name="country" required pattern="[A-Za-z]+">
                </div>
                <div class="fourth">
                    <label for="state">State</label>
                    <input type="text"  name="state" required pattern="[A-Za-z]+">
                </div>
                <div class="fourth">
                    <label for="city">City</label>
                    <input type="text"  name="city" required pattern="[A-Za-z]+">
                </div>
                <div class="fourth">
                    <label for="postalCode">
                        <p>
                            Postal Code
                        </p>
                    </label>
                    <input type="text"  name="postalCode" required>
                </div>
                <div class="whole">
                    <label for="address">Address</label>
                    <input type="text" name="address" required>
                </div>
                <div class="half">
                    <label for="xcoord">X Coordinate</label>
                    <input type="text" id="xcoord" name="xcoord" pattern="-?(\d(\.\d+)?|([1-8][0-9])(\.\d+)?|90(\.0)?)">
                    <button onclick="getLocation()" type="button" class="geoButton">Get Coordinates</button>
                </div>
                <div class="half">
                    <label for="ycoord">Y Coordinate</label>
                    <input type="text" id="ycoord" name="ycoord" pattern="-?(\d(\.\d+)?|([1-9][0-9])(\.\d+)?|(1[0-7][0-9])(\.d+)?|180(\.0)?)">
                </div>
                <div class="whole">
                    <label for="description">Description</label>
                        <textarea name="description" placeholder="Tell us something about this place!"></textarea>
                </div>
                <div class="whole">
                    <label for="locImg">Location Image</label>
                    <input type="file" accept="image/*" name="locImg">
                </div>
                <div class="fourth">
                    <button class="submit" type="submit" name="locationSubmit">
                        Submit
                    </button>
                </div>
                <div class="fourth">
                        <button class="cancel" type="button" onclick="location.href='search.php'">
                            Cancel
                        </button>
                </div>
                <div class="whole">
                    <?php
                        if ($stmt->execute() === TRUE) {
                        echo "Location successfully added.";
                        }
                        else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }
                    ?>
                </div>
            </div>
        </form>
        <?php 
        } //end else
        include ("./includes/footer.php"); //Include footer elements
        ?>
    </body>
</html>