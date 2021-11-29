<!DOCTYPE html>

<?php
    //MySQL information
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "arcades";
    $search = "";
    $nearYou = false;
    $xMap = 0; //Map latitude
    $yMap = 0; //Map longitude
    $count = 0; //Counter of how many records for average

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password); //connect to database

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (isset($_GET['search'])) {//if search is defined then update our search variable
            $search = $_GET['search'];
        }
    
        if (isset($_GET['x']) and isset($_GET['y'])) {
            $nearYou = true;
        }

        if ($nearYou) { //If user is looking for arcades near them
            $x = $_GET['x'];
            $y = $_GET['y'];

            //Set map to their location
            $xMap = $x;
            $yMap = $y;

            //Search for locations within their area
            $sql = "SELECT location.*, COUNT(review.locationid) AS reviews, sample.review AS reviewsample, AVG(review.rating) as rating
            FROM `location` 
            LEFT JOIN review ON location.id = review.locationid
            LEFT JOIN (
                SELECT sample.locationid, sample.review
                FROM review AS sample
                ORDER BY rating DESC
                LIMIT 1
                ) AS sample
                ON location.id = sample.locationid
            WHERE location.x >= :x1 AND location.x <= :x2
            AND location.y >= :y1 AND location.y <= :y2";
            $stmt = $conn->prepare($sql); //preparing statement
            $searchLike = "%" . $search . "%"; //Adding wildcard to search term
            $x1 = $x - 0.5;
            $x2 = $x + 0.5;
            $y1 = $y - 0.5;
            $y2 = $y + 0.5;
            $stmt->bindParam(':x1', $x1); //binding params
            $stmt->bindParam(':x2', $x2); 
            $stmt->bindParam(':y1', $y1); 
            $stmt->bindParam(':y2', $y2); 
            $stmt->execute();
        }
        else {
                        //https://stackoverflow.com/questions/2514548/how-to-search-multiple-columns-in-mysql
            //https://stackoverflow.com/questions/12526194/mysql-inner-join-select-only-one-row-from-second-table
            //https://stackoverflow.com/questions/4847089/mysql-joins-and-count-from-another-table
            //https://stackoverflow.com/questions/1392479/using-where-and-inner-join-in-mysql
    
            /*
            review left join to find the total number of reviews (COUNT)
            sample left join to provide a sample, highest rated review of the location
            Where clause scans most of the location table columns for matches
            */
            $sql = "SELECT location.*, COUNT(review.locationid) AS reviews, sample.review AS reviewsample, AVG(review.rating) as rating
            FROM `location` 
            LEFT JOIN review ON location.id = review.locationid
            LEFT JOIN (
                SELECT sample.locationid, sample.review
                FROM review AS sample
                ORDER BY rating DESC
                LIMIT 1
                ) AS sample
                ON location.id = sample.locationid
            WHERE CONCAT_WS('', `name`, `country`, `state`, `city`, `postal code`, `address`, `x`, `y`) 
            LIKE :search;";
            $stmt = $conn->prepare($sql); //preparing statement
            $searchLike = "%" . $search . "%"; //Adding wildcard to search term
            $stmt->bindParam(':search', $searchLike); //binding param
            $stmt->execute();
            //calculate average latitude and longitude of query
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row['id'] == null){
                    continue;
                }
                $xMap += $row['x'];
                $yMap += $row['y'];
                $count += 1;
            }
            $xMap = $xMap / $count; //Focus map at the average latitude
            $yMap = $yMap / $count; //Focus map at average longitude
        }
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

        <link rel="stylesheet" href="./styles/results.css">
        <?php include("./includes/head.php")?>
        <meta name="description" content="Results page shown to user after inputting a search term.">
        <title>Results</title>
    </head>
    <?php 
    include("./includes/header.php"); //Include header elements
    ?>
    <body>
        <!--Wrapper for page contents-->
        <div class="resultsWrapper">
            <!--Wrapper for Map-->
            <div id="map"></div>
            <script type="text/javascript">
                //Setting map location to include the three arcades
                var mymap = L.map('map').setView([<?php echo $xMap;?>, <?php echo $yMap;?>], 13);
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

                //Markers for each of the hard coded arcades


                var taitoMarker = L.marker([35.69002017350261, 139.70220112486624], {icon:redIcon}).addTo(mymap);
                taitoMarker.bindPopup("<a href=./individual_sample.html>Taito Game Station</a>");
                
                var segaMarker = L.marker([35.68979753397573, 139.6973720825395], {icon:redIcon}).addTo(mymap);
                segaMarker.bindPopup("<a href=./individual_sample.html>Club Sega</a>");
                
                //Prevents leaflet loading error
                setInterval(function () {
                    mymap.invalidateSize();
                }, 100);
            </script>
            <!--Wrapper for the results that come up from search-->
            <div class="resultsMain">
                <div class="topText">
                    <?php
                    if ($nearYou) {
                        echo "<h1>Results Near You</h1>";
                    }
                    else {
                    ?>
                    <h1>Results For "</h1>
                    <h1 id="searchResult"><?php echo $search;?></h1>
                    <h1>"</h1>
                    <?php 
                    } //end else
                    ?>
                    <a href="./submission.php">
                        <p style="color: crimson;">Location not listed?</p>
                    </a>
                </div>
                <!--Wrapper for object that came up from search-->
                <?php
                //Iterating through every row
                $stmt->execute(); //execute the query again to reset the pointer
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($row['id'] == null){
                        continue;
                    }
                ?>
                <script type="text/javascript">
                    //Create a marker for the location
                    var Marker = L.marker([<?php echo $row['x'];?>, <?php echo $row['y'];?>], {icon:redIcon}).addTo(mymap);
                    Marker.bindPopup("<a href=./individual_sample.php?id=<?php echo $row['id'];?>><?php echo $row['name'];?></a>");
                </script>

                <!--Create the result entry -->
                <div class="object">
                    <img src="assets/<?php echo $row['picture'];?>" alt="Mikado store front">
                    <div class="objectDesc">
                        <a href="./individual_sample.php?id=<?php echo $row['id'];?>">
                            <h1><?php echo $row['name'];?></h1>
                        </a>
                        <div class="rating">
                        <?php
                            for ($i = 0; $i < floor($row['rating']); $i++) {
                                echo "<span class='material-icons'>star</span>";
                            }

                            for ($i = floor($row['rating']); $i < 5; $i++) {
                                echo "<span class='material-icons'>star_border</span>";
                            }
                            
                            ?>
                
                            <p class="numReview"><?php echo $row['reviews']?> Reviews</p>
                        </div>
                        <p>"<?php echo $row['reviewsample']?>"</p>
                    </div>
                </div>
                    <?php
                    } //End while loopp 
                
                    ?>
                <div class="object">
                    <img src="assets/clubsega.jpeg" alt="Club Sega store front">
                    <div class="objectDesc">
                        <a href="./individual_sample.html">
                            <h1>Club Sega test test test test test test test</h1>
                        </a>
                        
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
                            <!--user can click on reviews to jump to review section-->
                            <p class="numReview">1 Reviews</p>
                        </div>
                        <p>"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."</p>
                    </div>
                </div>
                <div class="object">
                    <img src="assets/taito.jpg" alt="Taito Station store front">
                    <div class="objectDesc">
                        <a href="./individual_sample.html">
                            <h1>Taito Game Station</h1>
                        </a>
                        
                        <div class="rating">
                            <span class="material-icons">
                                star_border
                            </span>
                            <span class="material-icons">
                                star_border
                            </span>
                            <span class="material-icons">
                                star_border
                            </span>
                            <span class="material-icons">
                                star_border
                            </span>
                            <span class="material-icons">
                                star_border
                            </span>
                            <!--user can click on reviews to jump to review section-->
                            <p class="numReview">0 Reviews</p>
                        </div>
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