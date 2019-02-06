<html>
   <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title>Airline</title>
        <?php 
         include 'connection.php';
         $conn = OpenCon();
         session_start();
         if(isset($_SESSION['staffid'])){
           header("Location:airlinestaff.php");
         }
         if(isset($_SESSION['custid'])){
           header("Location:customer.php");
         }
         if(isset($_SESSION['agentid'])){
           header("Location:agent.php");
         }
        ?>
        <?php
         if (isset($_POST['staffSubmit'])){
            $staffname = $_POST['staffuser'];
            $staffpass = md5($_POST['staffpass']);
            $test_query= "SELECT username FROM airline_staff WHERE username= '$staffname' and password = '$staffpass'";
            $result = mysqli_query($conn, $test_query);
             if(mysqli_num_rows($result) == 1) {
                //redirect to invalid usernameor password
                //or alert that invalid username or password
		          session_start();
                  $_SESSION['staffid'] = $staffname;
                  header("location:airlinestaff.php");
                 exit();
            }
            else{
                echo("<script type='text/javascript'> alert('wrong information');</script>");
            }
         }
        if (isset($_POST['custSubmit'])){
            $custname = $_POST['custuser'];
            $custpass = md5($_POST['custpass']);
            $test_query= "SELECT email FROM customer WHERE email= '$custname' and password = '$custpass'";
            $result = mysqli_query($conn, $test_query);
             if(mysqli_num_rows($result) == 1) {
                //redirect to invalid usernameor password
                //or alert that invalid username or password
		          session_start();
                  $_SESSION['custid'] = $custname;
                  header("location: customer.php");
                 exit();
            }
             else{
                echo("<script type='text/javascript'> alert('wrong information');</script>");
            }
         }
        
       if (isset($_POST['agentSubmit'])){
            $agentname = $_POST['agentuser'];
            $agentpass = md5($_POST['agentpass']);
            $test_query= "SELECT email FROM booking_agent WHERE email= '$agentname' and password = '$agentpass'";
            $result = mysqli_query($conn, $test_query);
             if(mysqli_num_rows($result) == 1) {
                //redirect to invalid usernameor password
                //or alert that invalid username or password
		          session_start();
                  $_SESSION['agentid'] = $agentname;
                  header("location: agent.php");
                 exit();
            }
            else{
                echo("<script type='text/javascript'> alert('wrong information');</script>");
            }
         }
        
       ?>
       <style>
             table, th, td {
                border: 1px solid black;
            }
            td {
               padding: 2px;
              text-align: left;
              vertical-align: top;
            }
        </style>
   </head>
   <body>
       <a href ="register.php">Registration</a>
       <form name = "loginStaff" action="" method="post">
            <h2>Airline Staff Login</h2>
            <input type="text" name="staffuser" placeholder="Username" required><br>
            <input type="password" name="staffpass" placeholder="*******"required><br>
            <br>
            <input type="submit" name="staffSubmit" value="Submit"><br>
        </form>
        <form name = "loginCust" action="" method="post">
            <h2>Customer Login</h2>
            <input type="email" name="custuser" placeholder="Email"><br>
            <input type="password" name="custpass" placeholder="*******"><br>
            <br>
            <input type="submit" name="custSubmit" value="Submit"><br>
        </form>
        <form name = "loginCust" action="" method="post">
            <h2>Booking Agent Login</h2>
            <input type="email" name="agentuser" placeholder="Email"><br>
            <input type="password" name="agentpass" placeholder="*******"><br>
            <br>
            <input type="submit" name="agentSubmit" value="Submit"><br>
        </form>
       
       
       <h2>Search Upcoming Flight: </h2>
       <form name = "search1" action="" method="post">
            <input type="text" name="source" placeholder="Source" required><br>
           <input type="text" name="destination" placeholder="Destination" required><br>
           <input type="date" name="datestart" placeholder="Date" required><br>
            <input type="radio" name="choice"  value="a" checked>City<br>
	        <input type="radio" name="choice"  value="b">Airport Name<br>
           <input type="submit" name="search1Submit" value="Submit"><br>
       </form>
       <?php
         if (isset($_POST['search1Submit'])){
              $from = mysqli_real_escape_string($conn,$_POST['source']);
              $to = mysqli_real_escape_string($conn,$_POST['destination']);
              $dstart = mysqli_real_escape_string($conn,$_POST['datestart']);
              $choiceValv2 = mysqli_real_escape_string($conn,$_POST['choice']);
              if($choiceValv2 == "a"){
                 $test_query= "select * from flight where DATE(departure_time) = '$dstart'
		          and flight.departure_airport in (select airport_name from airport where airport_city =  '$from' ) and flight.arrival_airport in (select airport_name from airport where airport_city = '$to')";
                 $result = mysqli_query($conn, $test_query);
                 if(mysqli_num_rows($result) == 0) {
                    echo ("not found");
                 }
                  else{
                echo "<table><tr>
                <th>Flight</th><th>Departure Airport</th><th>Departure Time</th><th>Arrival Airport</th><th>Arrival Time</th><th>Status</th><th>Airline</th>
                </tr>";
                 while($row = mysqli_fetch_array($result)){
                     $status = $row['status'];
                     $flightNum = $row['flight_num'];
                     $departurtime = $row['departure_time'];
                     $departurairport = $row['departure_airport'];
                     $arrivatime = $row['arrival_time'];
                     $arrivaairport = $row['arrival_airport'];
                     $airlinee = $row['airline_name'];
                      echo "<tr><td>".$flightNum."</td><td>".$departurairport."</td><td>".$departurtime."</td><td>".$arrivaairport."</td><td>".$arrivatime."</td><td>".$status."</td><td>".$airlinee."</td></tr>";
                   }
                  echo "</table>";
                  }
              }
              if($choiceValv2 == "b"){
                 $test_query= "SELECT * FROM flight WHERE departure_airport= '$from' and arrival_airport= '$to' and DATE(departure_time) ='$dstart'";
                 //echo($searchterm2);
                 $result = mysqli_query($conn, $test_query);
                 if(mysqli_num_rows($result) == 0) {
                    echo ("not found");
                 }
                  else{
                echo "<table><tr>
                <th>Flight</th><th>Departure Airport</th><th>Departure Time</th><th>Arrival Airport</th><th>Arrival Time</th><th>Status</th><th>Airline</th>
                </tr>";
                 while($row = mysqli_fetch_array($result)){
                     $status = $row['status'];
                     $flightNum = $row['flight_num'];
                     $departurtime = $row['departure_time'];
                     $departurairport = $row['departure_airport'];
                     $arrivatime = $row['arrival_time'];
                     $arrivaairport = $row['arrival_airport'];
                      $airlinee = $row['airline_name'];
                      echo "<tr><td>".$flightNum."</td><td>".$departurairport."</td><td>".$departurtime."</td><td>".$arrivaairport."</td><td>".$arrivatime."</td><td>".$status."</td><td>".$airlinee."</td></tr>";
                   }
                  echo "</table>";
                  }
              }
         }
       ?>
       
       
       
        <h2>Search Status: </h2>
       <form name = "search2" action="" method="post">
            <input type="text" name="seachStatus" placeholder="Here" required>
            <input type="text" name="seachStatus2" placeholder="Here"><br>
            <input type="radio" name="choice2"  value="a" checked>Flight Number/None<br>
	        <input type="radio" name="choice2"  value="b">DepartureDate/Arrival Date<br>
           <input type="submit" name="search2Submit" value="Submit"><br>
       </form>
       <?php
         if (isset($_POST['search2Submit'])){
              $searchterm2 = mysqli_real_escape_string($conn,$_POST['seachStatus']);
              $searchterm3 = mysqli_real_escape_string($conn,$_POST['seachStatus2']);
              $choiceValv2 = mysqli_real_escape_string($conn,$_POST['choice2']);
              if($choiceValv2 == "a"){
                 $test_query= "SELECT * FROM flight WHERE flight_num= '$searchterm2'";
                 $result = mysqli_query($conn, $test_query);
                 if(mysqli_num_rows($result) == 0) {
                    echo ("not found");
                 }
                  else{
                 echo "<table><tr>
                <th>Flight</th><th>Status</th><th>Airline</th>
                </tr>";
                 while($row = mysqli_fetch_array($result)){
                     $status = $row['status'];
                     $flightNum = $row['flight_num'];
                      $airlineNamee = $row['airline_name'];
                     echo ("<tr><td>".$flightNum."</td><td>".$status."</td><td>".$airlineNamee."</td></tr>");
                }
                echo "</table>";
                  }
              }
              if($choiceValv2 == "b"){
                 $test_query= "SELECT * FROM flight WHERE DATE(arrival_time) ='$searchterm2' and DATE(departure_time) ='$searchterm3'";
                 //echo($searchterm2);
                 $result = mysqli_query($conn, $test_query);
                 if(mysqli_num_rows($result) == 0) {
                    echo ("not found");
                 }
                  else{
                 echo "<table><tr>
                <th>Flight</th><th>Status</th><th>Airline</th>
                </tr>";
                 while($row = mysqli_fetch_array($result)){
                     $status = $row['status'];
                     $flightNum = $row['flight_num'];
                      $airlineNamee = $row['airline_name'];
                     echo ("<tr><td>".$flightNum."</td><td>".$status."</td><td>".$airlineNamee."</td></tr>");
                }
                echo "</table>";
                }
              }
         }
       ?>
   </body>
</html>
