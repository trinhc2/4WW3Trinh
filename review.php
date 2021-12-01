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

        if (isset($_POST['submitReview'])) { //If the user clicks submit button this would be set
        
            $sql = "INSERT INTO `review` (userid, locationid, rating, review, date)
            VALUES (:userid, :locationid, :rating, :review, CURDATE())"; //insert statement
            $stmt = $conn->prepare($sql); //prepared statement
            $stmt->bindParam(':userid', $_SESSION['userid']); //binding values
            $stmt->bindParam(':locationid', $_POST['id']);
            $stmt->bindParam(':rating', $_POST['rating']);
            $stmt->bindParam(':review', $_POST['review']);
            if ($stmt->execute() === TRUE) {
                header('location: individual_sample.php?id=' . $_POST['id']); //redirect to location page
                die();
            }
            else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
    
        }
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

?>

<html lang="en">
    <head>
        <?php include("./includes/head.php")?>
        <link rel="stylesheet" href="./styles/submission.css">
        <meta name="description" content="Review page for a location">
        <title>Review</title>
    </head>
    <?php include("./includes/header.php"); //Include header elements ?>
    <body>
        <?php     
        if (!isset($_SESSION['isLoggedIn'])) {//if user is not logged in, tell them
            echo '<h2 style="color:crimson; text-align:center">Please sign in to submit a review for this location</h2>';
        }
        else { //else display review form

        
        ?>
        <form method="post" action="review.php">
            <div class="subGrid">
                <h1 class="whole title">
                    Review
                </h1>
                <input type="hidden" name="id" value="<?php echo $_POST['locationid'];?>"></input>
                <div class="whole">
                    <label for="rating">Rating out of 5:</label>
                    <select name="rating" id="rating">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <div class="whole">
                    <label for="review">Review</label>
                        <textarea name="review" placeholder="Write your review here!"></textarea>
                </div>
                <div class="fourth">
                    <button class="submit" type="submit" name="submitReview">
                        Submit
                    </button>
                </div>
                <div class="fourth">
                        <button class="cancel" type="button" onclick="location.href='search.php'">
                            Cancel
                        </button>
                </div>
            </div>
        </form>
        <?php 
        } //end else
        include ("./includes/footer.php"); //Include footer elements ?>
    </body>
</html>