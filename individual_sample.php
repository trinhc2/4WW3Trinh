<!DOCTYPE html>

<?php
    //MySQL information
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "arcades";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password); //connect to database

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //https://phpdelusions.net/mysqli_examples/prepared_select

        $sql = "SELECT location.*, COUNT(review.locationid) AS reviews, AVG(review.rating) as rating
        FROM `location`
        LEFT JOIN review ON location.id = review.locationid
        WHERE location.id=:id"; //Query for specific row
        $stmt = $conn->prepare($sql);//Preparing statement
        $stmt->bindParam(':id', $_GET['id']);//binding id 
        $stmt->execute(); //executing query
        $row = $stmt->fetch(PDO::FETCH_ASSOC);//obtain row as associative array

        //Query to find location hours
        $hoursSQL = "SELECT * FROM `hours`
        WHERE locationid = :id
        ORDER BY day ASC";
        $hoursSTMT = $conn->prepare($hoursSQL);
        $hoursSTMT->bindParam(':id', $_GET['id']);
        $hoursSTMT->execute();
    }
    catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    }


    ?>

<html lang="en">
    <head>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
        integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
        crossorigin=""/>
         <!-- Make sure you put this AFTER Leaflet's CSS -->
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
        integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
        crossorigin=""></script>
        <?php include("./includes/head.php")?>
        <link rel="stylesheet" href="./styles/object.css">
        <meta name="description" content="Review page for <?php echo $row["name"]?> which contains information about the location and reviews.">
        <title><?php echo $row["name"]?></title>
    </head>
    
    <?php 
    include("./includes/header.php"); //Include header elements
    ?>

    <body>
        <div>
            <!--Wrapper for object page-->
            <div class="objWrapper">
                <!--Wrapper for the main details about object-->
                <div class="objMain">
                    <!--wrapper for the main description part of the object-->
                    <div class="objDesc">
                        <p class="name"><?php echo $row["name"]?></p>
                        <!--star ratings-->
                        <div class="rating">
                            <?php
                            for ($i = 0; $i < floor($row['rating']); $i++) {
                                echo "<span class='material-icons'>star</span>";
                            }

                            for ($i = floor($row['rating']); $i < 5; $i++) {
                                echo "<span class='material-icons'>star_border</span>";
                            }
                            
                            ?>
                            <!--user can click on reviews to jump to review section-->
                            <a href="#reviews">
                                <p class="numReview" style="color: crimson; font-weight: bold;"><?php echo $row["reviews"]?> Reviews</p>
                            </a>
                        </div>
                        <!--Hours-->
                        <?php 
                        while ($hourRow = $hoursSTMT->fetch(PDO::FETCH_ASSOC)) {
                            if ($hourRow['day'] == date('w')){ //finding the hours for todays date
                                break;
                            }
                        }

                        if (isset($hourRow['open']) && isset ($hourRow['close'])) { //if the opening and closing times are defined
                            $open = $hourRow['open'];
                            $close = $hourRow['close'];

                            if (strtotime($open) > strtotime($close)) {//if location closes early morning (before open)
                                if (strtotime(date("H:i:s")) > strtotime($open) && strtotime(date("H:i:s")) > strtotime($close)) {
                                    echo '<p class="open">Open</p>';
                                }
                                else 
                                    echo '<p class="closed">Closed</p>';
                            }
                            else {
                                if (strtotime($open) < strtotime(date("H:i:s")) && strtotime(date("H:i:s")) < strtotime($close)) {
                                    echo '<p class="open">Open</p>';
                                }
                                else {
                                    echo '<p class="closed">Closed</p>';
                                }
                            }
                            echo "<p class='hours'>" . date('g:i A', strtotime($open)) . " - " . date('g:i A', strtotime($close)) . "</p>";
                        }
                        ?>
                        <!--Button to write a review-->
                        <button class="reviewButton" type="button">
                            <span style="margin-right: 10px;" class="material-icons">
                                rate_review
                            </span>
                            <p>Write a Review</p>
                        </button>
                    </div>
                    <!--Image of the object-->
                    <img src="./assets/<?php echo $row["picture"]?>" alt=<?php echo $row["name"]?>>
                </div>
                                <!--Sidebar-->
                <div class="sidebar">
                    <div class="sidebarWrapper">
                        <h3>Address</h3>
                        <p><?php echo $row["address"]?></p>
                        <h3>Phone</h3>
                        <p><?php echo $row["phone"]?></p>
                    </div>
                </div>

                <!--Wrapper for Description-->
                <div class="catWrapper">
                    <h1>Description</h1>
                    <div>
                        <p class="description">
                            <?php echo $row["description"]?>
                        </p>
                    </div>
                </div>

                <!--Category: Location and Hours-->
                <div class="catWrapper">
                    <h1>Location and Hours</h1>
                    <div id="map"></div>
                    <script type="text/javascript">
                        var mymap = L.map('map').setView([<?php echo $row["x"]?>, <?php echo $row["y"]?>], 15);
                        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                        maxZoom: 24,
                        id: 'mapbox/streets-v11',
                        tileSize: 512,
                        zoomOffset: -1,
                        accessToken: 'pk.eyJ1IjoidHJpbmhjMiIsImEiOiJja3ZoNnc1N3IwMW8xMndxZzdxc3ZmM2I1In0.4Nq5jT51ULbarg2xhHdzKQ'
                        }).addTo(mymap);
        
                        //from https://github.com/pointhi/leaflet-color-markers
                        var redIcon = new L.Icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                        });
        
                        var marker = L.marker([35.71286574986599, 139.7034547978866], {icon:redIcon}).addTo(mymap);
                        marker.bindPopup("<a href=./individual_sample.php?id=<?php echo $row['id'];?>><?php echo $row['name'];?></a>").openPopup();
                        
                        setInterval(function () {
                            mymap.invalidateSize();
                        }, 100);
                    </script>
                    <div class="dateGrid">
                        <?php 
                        
                        $days = [
                            0 => 'Sunday',
                            1 => 'Monday',
                            2 => 'Tuesday',
                            3 => 'Wednesday',
                            4 => 'Thursday',
                            5 => 'Friday',
                            6 => 'Saturday'
                          ];

                        $hoursSTMT->execute(); //execute query again
                        while ($hourRow = $hoursSTMT->fetch(PDO::FETCH_ASSOC)) { 
                            //print opening times and days
                            echo "<p>" . $days[$hourRow['day']] . "</p>";
                            echo "<p>" . date('g:i A', strtotime($hourRow['open'])) . " - " . date('g:i A', strtotime($hourRow['close'])) . "</p>";
                        }
                        ?>

                    </div>
                </div>

                <!--Category: Reviews-->
                <div class="catWrapper" id="reviews">
                    <h1>Reviews</h1>
                    <!--Wrapper for Reviews-->
                    <?php 
                        $sql = "SELECT review.*, users.firstname as name FROM `review`
                        LEFT JOIN users ON review.userid = users.id
                        WHERE locationid = :id
                        ORDER BY review.date DESC"; //Query for all reviews in descending order
                        $stmt = $conn->prepare($sql);//Preparing statement
                        $stmt->bindParam(':id', $_GET['id']);//binding id 
                        $stmt->execute(); //executing query
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <div class="revWrapper">
                        <!--Wrapper for reviewer's profile-->
                        <div class="revProfile">
                            <button type="button">
                                <span class="material-icons">
                                    account_circle
                                    </span>
                            </button>
                            <p><?php echo $row['name'];?></p>
                        </div>
                        <!--Wrapper for rating in stars-->
                        <div class="rating">
                            <?php
                                for ($i = 0; $i < floor($row['rating']); $i++) {
                                    echo "<span class='material-icons'>star</span>";
                                }

                                for ($i = floor($row['rating']); $i < 5; $i++) {
                                    echo "<span class='material-icons'>star_border</span>";
                                }
                                
                            ?>
                            <p class="numReview"><?php echo $row['date'];?></p>
                        </div>
                        <p class="review">
                        <?php echo $row['review'];?>
                        </p>
                    </div>
                    <?php
                    } //End while loopp 
                    ?>
                    <div class="revWrapper">
                        <!--Wrapper for reviewer's profile-->
                        <div class="revProfile">
                            <button type="button">
                                <span class="material-icons">
                                    account_circle
                                    </span>
                            </button>
                            <p>Alice</p>
                        </div>
                        <!--Wrapper for rating in stars-->
                        <div class="rating">
                            <span class="material-icons">
                                star
                            </span>
                            <span class="material-icons">
                                star
                            </span>
                            <span class="material-icons">
                                star
                            </span>
                            <span class="material-icons">
                                star
                            </span>
                            <span class="material-icons">
                                star
                            </span>
                            <p class="numReview">10/2/2021</p>
                        </div>
                        <p class="review">
                            I love this arcade!
                        </p>
                    </div>

                    <div class="revWrapper">
                        <div class="revProfile">
                            <button type="button">
                                <span class="material-icons">
                                    account_circle
                                    </span>
                            </button>
                            <p>Bob</p>
                        </div>
                        <div class="rating">
                            <span class="material-icons">
                                star
                            </span>
                            <span class="material-icons">
                                star
                            </span>
                            <span class="material-icons">
                                star
                            </span>
                            <span class="material-icons">
                                star
                            </span>
                            <span class="material-icons">
                                star_border
                            </span>
                            <p class="numReview">10/2/2021</p>
                        </div>
                        <p class="review">
                            "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php 
        include ("./includes/footer.php"); //Include footer elements

        $conn = null;
        ?>
    </body>
</html>