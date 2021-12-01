<!DOCTYPE html>
<?php
    if (session_id() == "") { //if no session, creat one
        session_start();
    }
    require './aws_sdk/aws-autoloader.php';
    use Aws\S3\S3Client;

    use Aws\Exception\AwsException;

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "arcades";
    
     //S3 SETUP
    $bucket = "trinhc2bucket";
    $key = getenv("AWS_ACCESS_KEY"); //key stored in environment variables
    $secret = getenv("AWS_SECRET_ACCESS_KEY"); //stored in environment varibles

    //Create a S3Client
    $s3Client = new S3Client([
        'region' => 'us-east-2',
        'version' => 'latest',
        'credentials' => [
            'key' => $key,
            'secret' => $secret,
        ]
    ]);

    $errors = array(); //array to keep track of errors
    $locationAdded = false;

    $name = "";
    $description = "";
    $phone = "";
    $country = "";
    $state = "";
    $city = "";
    $postalcode = "";
    $address = "";
    $x = "";
    $y = "";
    $URL = "";

    if (isset($_FILES['image'])) {
        $result = $s3Client->putObject([
            'Bucket' => $bucket,
            'Key' => $_FILES['image']['name'],
            'SourceFile' => $_FILES['image']['tmp_name'],
        ]);

        $bucketURL = "https://trinhc2bucket.s3.us-east-2.amazonaws.com/";
        $URL = $bucketURL . $_FILES['image']['name'];
    }

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password); //connect to database

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($_POST['locationSubmit'])) { //If the user clicks submit button this would be set
        
            $name = $_POST['name'];
            $description = $_POST['description'];
            $phone = $_POST['phoneNum'];
            $country = $_POST['country'];
            $state = $_POST['state'];
            $city = $_POST['city'];
            $postalcode = $_POST['postalCode'];
            $address = $_POST['address'];
            $x = $_POST['xcoord'];
            $y = $_POST['ycoord'];

            //SERVERSIDE VALIDATION
            if (empty($name)) {
                array_push($errors, "Name is required");
            }
            if (empty($description)) {
                array_push($errors, "Description is required");
            }
            if (empty($phone)) {
                array_push($errors, "Phone number is required");
            }
            if (empty($country)) {
                array_push($errors, "Country is required");
            }
            if (empty($state)) {
                array_push($errors, "State is required");
            }
            if (empty($city)) {
                array_push($errors, "City is required");
            }
            if (empty($postalcode)) {
                array_push($errors, "Postal Code is required");
            }
            if (empty($address)) {
                array_push($errors, "Address is required");
            }
            if (empty($x)) {
                array_push($errors, "Latitude is required");
            }
            if (empty($y)) {
                array_push($errors, "Longitude is required");
            }
            if (empty($URL)) {
                array_push($errors, "Image is required");
            }

            if (count($errors) == 0) {
                $sql = "INSERT INTO `location` (name, description, phone, country, state, city, `postal code`, address, x, y, picture)
                VALUES (:name, :description, :phone, :country, :state, :city, :postalcode, :address, :x, :y, :picture)"; //insert statement
                $stmt = $conn->prepare($sql); //prepared statement
                $stmt->bindParam(':name', $name); //binding values
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':country', $country);
                $stmt->bindParam(':state', $state);
                $stmt->bindParam(':city', $city);
                $stmt->bindParam(':postalcode', $postalcode);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':x', $x);
                $stmt->bindParam(':y', $y);
                $stmt->bindParam(':picture', $URL);
    
                if ($stmt->execute() === TRUE) { //if execution was successful
                 $locationAdded = true;
                }
                else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }

    
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
        <!--Form for submitting a location
    https://stackoverflow.com/questions/37182801/does-input-type-files-appear-in-post-->
        <form method="post" action="submission.php" enctype="multipart/form-data">
            <div class="subGrid">
                <h1 class="whole title">
                    Submission
                </h1>
                <div class="whole">
                    <label for="name">Name</label>
                    <input type="text"  name="name" required pattern="[A-Za-z\s]+">
                </div>
                <div class="whole">
                    <label for="phoneNum">Phone Number</label>
                    <!--https://stackoverflow.com/questions/16699007/regular-expression-to-match-standard-10-digit-phone-number-->
                    <input type="text" name="phoneNum" required pattern="[^A-Za-z]+" placeholder="Ex: 123 456 7890">
                </div>
                <div class="fourth">
                    <label for="country">Country</label>
                    <input type="text"  name="country" required pattern="[A-Za-z\s]+">
                </div>
                <div class="fourth">
                    <label for="state">State</label>
                    <input type="text"  name="state" required pattern="[A-Za-z\s]+">
                </div>
                <div class="fourth">
                    <label for="city">City</label>
                    <input type="text"  name="city" required pattern="[A-Za-z\s]+">
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
                    <input type="text" id="xcoord" name="xcoord" required pattern="^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$" placeholder="Up to 6 decimal places">
                    <button onclick="getLocation()" type="button" class="geoButton">Get Coordinates</button>
                </div>
                <div class="half">
                    <label for="ycoord">Y Coordinate</label>
                    <input type="text" id="ycoord" name="ycoord" required pattern="^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$" placeholder="Up to 6 decimal places">
                </div>
                <div class="whole">
                    <label for="description">Description</label>
                        <textarea name="description" placeholder="Tell us something about this place!" required></textarea>
                </div>
                <div class="whole">
                    <label for="locImg">Location Image</label>
                    <input type="file" accept="image/*" name="image" required>
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
                    //If there are errors with submission, print them to the user
                    if ($locationAdded) {
                        echo "<p>Location successfully added.</p>";
                    }
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
        } //end else
        include ("./includes/footer.php"); //Include footer elements
        ?>
    </body>
</html>