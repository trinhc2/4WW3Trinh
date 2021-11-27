<header>
        <!--Desktop navbar-->
        <div class="nav">
            <a href="./search.php">
                <h1 class="home">Home</h1>
            </a>
            <div class="dropdown">
                <div class="dropdownbtn">
                    <h1 class="dropdowntxt">Arcade</h1>
                    <span class="material-icons">
                        arrow_drop_down
                    </span>
                </div>
                <div class="dropdownContent">
                    <a href="./individual_sample.php">
                        <p>Sample Object</p>
                    </a>
                    <a href="./submission.html">
                        <p>Submission</p>
                    </a>
                    <a href="./results_sample.php">
                        <p>Results</p>
                    </a>
                    <a>
                        <p>Video Arcade Machine</p>
                    </a>
                    <a>
                        <p>Pinball Machines</p>
                    </a>
                    <a>
                        <p>Interactive Games</p>
                    </a>
                    <a>
                        <p>Ticket Games</p>
                    </a>
                    
                </div>
            </div>
        </div>

        <!--Navigation bar for mobile-->
        <div class="navmobile">
            <div class="dropdown">
                <div class="dropdownbtn">
                    <h1 class="dropdowntxt">Menu</h1>
                    <span class="material-icons">
                        arrow_drop_down
                    </span>
                </div>
                <div class="dropdownContent">
                    <a href="./search.php">
                        <p>Home</p>
                    </a>
                    <a href="individual_sample.php">
                        <p>Sample Object</p>
                    </a>
                    <a href="./submission.html">
                        <p>Submission</p>
                    </a>
                    <a href="results_sample.php">
                        <p>Results</p>
                    </a>
                    <a>
                        <p>Video Arcade Machine</p>
                    </a>
                    <a>
                        <p>Pinball Machines</p>
                    </a>
                    <a>
                        <p>Interactive Games</p>
                    </a>
                    <a>
                        <p>Ticket Games</p>
                    </a>
                    
                </div>
            </div>
        </div>
        <form class="search" action="results_sample.php" method="get">
            <input type="text" placeholder="Find an arcade..." name="search">
            <button type="submit">
                <!-- icon within the button-->
                <span class="material-icons">
                    search
                </span>
            </button>
        </form>

        <!--bundling the buttons used for user auth-->
        <div>
            <!--Login button-->
            <form style="display: inline" action="login.php" method="post">
                <button class="user login" type="submit">
                    Log In
                </button>
            </form>
            <!--Sign up button-->
            <form style="display: inline"  action="registration.php" method="post">
                <button class="user signup" type="submit">
                    Sign Up
                </button>
            </form>
        </div>
    </header>