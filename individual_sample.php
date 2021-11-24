<!DOCTYPE html>

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
    $id = 1;

    //https://phpdelusions.net/mysqli_examples/prepared_select
    $sql = "SELECT * FROM location WHERE id=?"; //Query for specific row
    $stmt = $conn->prepare($sql);//Preparing statement
    $stmt->bind_param("s", $id);//binding id 
    $stmt->execute(); //executing query
    $result = $stmt->get_result(); //retrieving result
    $row = $result->fetch_assoc(); //fetch result row as associative array
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
                                star_half
                            </span>
                            <!--user can click on reviews to jump to review section-->
                            <a href="#reviews">
                                <p class="numReview" style="color: crimson; font-weight: bold;">2 Reviews</p>
                            </a>
                        </div>
                        <!--Hours-->
                        <p class="hours">Open</p>
                        <p class="hours">10:00 AM - 12:00AM</p>
                        <!--Button to write a review-->
                        <button class="reviewButton" type="button">
                            <span style="margin-right: 10px;" class="material-icons">
                                rate_review
                            </span>
                            Write a Review
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
        
                        var mikadoMarker = L.marker([35.71286574986599, 139.7034547978866], {icon:redIcon}).addTo(mymap);
                        mikadoMarker.bindPopup("<a href=./individual_sample.html>Mikado Game Center</a>").openPopup();
                        
                        setInterval(function () {
                            mymap.invalidateSize();
                        }, 100);
                    </script>
                    <div class="dateGrid">
                        <p style="margin-block-start: 0px;">Sun</p>
                        <p style="margin-block-start: 0px;">10:00 AM - 12:00AM</p>
                        <p>Mon</p>
                        <p>10:00 AM - 12:00AM</p>
                        <p>Tue</p>
                        <p>10:00 AM - 12:00AM</p>
                        <p>Wed</p>
                        <p>10:00 AM - 12:00AM</p>
                        <p>Thu</p>
                        <p>10:00 AM - 12:00AM</p>
                        <p>Fri</p>
                        <p>10:00 AM - 12:00AM</p>
                        <p>Sat</p>
                        <p>10:00 AM - 12:00AM</p>

                    </div>
                </div>

                <!--Category: Reviews-->
                <div class="catWrapper" id="reviews">
                    <h1>Reviews</h1>
                    <!--Wrapper for Reviews-->
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

        $conn->close();
        ?>
    </body>
</html>