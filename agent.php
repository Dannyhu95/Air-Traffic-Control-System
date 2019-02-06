<html>
   <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title>Agent Control</title>
        <?php 
       include 'session.php';
      if(isset($_SESSION['custid'])){
           header("Location:customer.php");
       }
       else if(isset($_SESSION['staffid'])){
           header("Location:airlinestaff.php");
       }
       else{
           $agent_uid=$_SESSION['agentid'];
           $result = mysqli_query($conn, "select * from booking_agent where email='$agent_uid'");
           $row = mysqli_fetch_array($result);
           $agentid = $row['booking_agent_id'];
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
        <h2>Customer's Upcoming Flight</h2>
        <?php
        $upcomingquery = mysqli_query($conn,"select *
from (purchases natural join ticket natural join flight)
where booking_agent_id = $agentid and status = 'upcoming'");
          $rowtest = mysqli_num_rows($upcomingquery);;
          if ($rowtest==0){
              echo "No Upcomin Flights!";
          }
          else{
          echo "<table><tr>
             <th>Flight</th><th>Departure Airport</th><th>Departure Time</th><th>Arrival Airport</th><th>Arrival Time</th><th>Customer Email</th><th>AirLine</th>
             </tr>";
          while ($rows = mysqli_fetch_row($upcomingquery)) {
              echo "<tr><td>".$rows[1]."</td><td>".$rows[7]."</td><td>".$rows[8]."</td><td>".$rows[9]."</td><td>".$rows[10]."</td><td>".$rows[3]."</td><td>".$rows[0]."</td></tr>";
          }
        echo "</table>";
          }
        ?>
        
        <h2>Comission</h2>
        
        <?php
        $comissionquery = mysqli_query($conn,"select TRUNCATE(avg(price * 0.10),2)
from (flight natural join ticket natural join purchases)
where purchases.booking_agent_id ='$agentid' and Month(purchases.purchase_date) between Month(CURDATE() - INTERVAL 1 MONTH) and Month(CURDATE())");
        $row = mysqli_fetch_array($comissionquery);
         if($row[0]==0){
             echo"<p> You have not purchased any tickets Past month!</p>";
         }
        else{
             echo "<p>Average Comission for past 30 days: $".$row[0]."</p>";
        }
        $comissionquery2 = mysqli_query($conn,"select TRUNCATE(sum(price * 0.10),2)
from (flight natural join ticket natural join purchases)
where booking_agent_id = '$agentid' and Month(purchases.purchase_date) between Month(CURDATE() - INTERVAL 1 MONTH) and Month(CURDATE())");
        $row2 = mysqli_fetch_array($comissionquery2);
         if($row2[0]==0){
            echo"";
         }
        else{
         echo "<p>Total Comission for past 30 days: $".$row2[0]."</p>";
        }
        $comissionquery3 = mysqli_query($conn,"select count(*) from purchases where booking_agent_id = '$agentid' and Month(purchases.purchase_date) between Month(CURDATE() - INTERVAL 1 MONTH) and Month(CURDATE())");
         $row3 = mysqli_fetch_array($comissionquery3);
        if($row3[0]==0){
            echo"";
         }
        else{
         echo "<p>Total number of tickets sold for past 30 days: ".$row3[0]."</p>";
        }
        ?>
        <form name = "comissionAvg" action="" method="post">
         From: <input type="date" name="dateStart"placeholder="Date" required>
         To: <input type="date" name="dateend"placeholder="Date" required><br>
        <input type="submit" name="comissionSubmit" value="Submit"><br>
        </form>
        <?php
         if(isset($_POST['comissionSubmit'])){
              $starttime = mysqli_real_escape_string($conn,$_POST['dateStart']);
              $endtime = mysqli_real_escape_string($conn,$_POST['dateend']);
              $comissionquery4 = mysqli_query($conn,"select count(*)from purchases where booking_agent_id = $agentid and DATE(purchase_date) between '$starttime' and '$endtime'");
             $row = mysqli_fetch_array($comissionquery4);
             if($row[0]==0){
                 echo"<p> You have not purchased any tickets during this timeframe!</p>";
             }
             else{
              echo "<p>Total number of tickets sold from ".$starttime." to ".$endtime.": ".$row[0]."</p>";
             }
             $comissionquery5 = mysqli_query($conn,"select sum(price * 0.10)
from (flight natural join ticket natural join purchases natural join booking_agent)
where booking_agent_id = $agentid and DATE(purchase_date) between '$starttime' and '$endtime'");
              $row2 = mysqli_fetch_array($comissionquery5);
             if($row2[0]==0){
                 echo"";
             }
             else{
              echo "<p>Total Comission from ".$starttime." to ".$endtime.": $".$row2[0]."</p>";
             } 
         }
        ?>
        <form name = "ticket" action="" method="post">
             <h2>Purchase Tickets</h2>
            <input type="number" name="flightnum" min=0 placeholder="Flight Number" required><br>
             <input type="email" name="custEmail" placeholder="Customer Email" required><br>
           <select id="airline" name="flightairline">
            </select><br>
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
            $custEmail = mysqli_real_escape_string($conn,strtolower($_POST['custEmail']));
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
                        $purchaseDate = $dt->format('Y-m-d');
                        $result = mysqli_query($conn, "INSERT INTO purchases(ticket_id, customer_email,booking_agent_id, purchase_date)VALUES ('$ticketid', '$custEmail','$agentid', '$purchaseDate');");
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