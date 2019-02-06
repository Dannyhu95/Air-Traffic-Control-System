<html>
   <head>
        <title>Customer Control</title>
       <?php 
        include 'session.php';
        if(isset($_SESSION['staffid'])){
           header("Location:airlinestaff.php");
       }
       else if(isset($_SESSION['agentid'])){
           header("Location:agent.php");
       }
       else{
           $cust_uid=$_SESSION['custid'];
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
          <h2>My Upcoming Flight</h2>
          <?php
            $upcomingquery = mysqli_query($conn,"select *
from (purchases natural join ticket natural join flight)
where customer_email = '$cust_uid' and status = 'upcoming'");
          $rowtest = mysqli_num_rows($upcomingquery);
          if ($rowtest==0){
              echo "No Upcomin Flights!";
          }
          else{
          echo "<table><tr>
             <th>Flight</th><th>Departure Airport</th><th>Departure Time</th><th>Arrival Airport</th><th>Arrival Time</th><th>AirLine</th>
             </tr>";
          while ($rows = mysqli_fetch_row($upcomingquery)) {
              echo "<tr><td>".$rows[1]."</td><td>".$rows[7]."</td><td>".$rows[8]."</td><td>".$rows[9]."</td><td>".$rows[10]."</td><td>".$rows[0]."</td></tr>";
          }
           echo "</table>";
          }
          ?>
         <form name = "ticket" action="" method="post">
         <h2>Purchase Tickets</h2>
            <input type="number" name="flightnum" min=0 placeholder="Flight Number" required><br>
            <select id="airline" name="flightairline">
            </select><br><br>
            <?php
                $result = mysqli_query($conn, "select * from airline");
                $airlineArr = array();
                while ($row = mysqli_fetch_row($result)) {
                     array_push($airlineArr, $row[0]);
                }
            ?>
            <script type=text/javascript>
            //handle airline
            var airlineArray = <?php echo json_encode($airlineArr); ?>;
            var airlineTBL=document.getElementById('airline');
            for (var i=0; i<airlineArray.length; i++) {
                                    airlineTBL.options[airlineTBL.length] = new Option(airlineArray[i],airlineArray[i]);
            }
            </script>
            <input type="submit" name="ticketSubmit" value="Submit"><br>
        </form>
        <?php
         if(isset($_POST['ticketSubmit'])){
              $flightNum = mysqli_real_escape_string($conn,$_POST['flightnum']);
              $custAirline = mysqli_real_escape_string($conn,$_POST['flightairline']);
              $query = mysqli_query($conn,"SELECT MIN(ticket_id),flight_num FROM `ticket` WHERE flight_num='$flightNum' and airline_name='$custAirline' and booked =0");
               while ($row = mysqli_fetch_row($query)) {
                   if($row[0]== null){
                       $haveAccount = "All seats Booked or flight doesn't Exist";
			           echo "<script type='text/javascript'>alert('$haveAccount');</script>";
                   }
                   else{
                       $ticketid= $row[0];
                        $query2 = "UPDATE ticket SET booked=1 where ticket_id= $ticketid";
                        $result = mysqli_query($conn, $query2); 
                        date_default_timezone_set('America/New_York');
                        $dt = new DateTime();
                        $purchaseDate = $dt->format('Y-m-d H:i:s');
                        $result = mysqli_query($conn, "INSERT INTO purchases(ticket_id, customer_email,booking_agent_id, purchase_date)VALUES ('$ticketid', '$cust_uid',null, '$purchaseDate');");
				        $success = "Congrats, ticket purchased!";
				        echo "<script type='text/javascript'>alert('$success');</script>";
                   }
               }    
         }
        ?>
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
                  $test_query= "SELECT * FROM flight WHERE DATE(departure_time) ='$searchterm2' and DATE(arrival_time) ='$searchterm3'";
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
        <br>
        <a href="logout.php">Logout</a>
    </body>
</html>   