<html>
   <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title>Airline Staff Control</title>
       <?php 
       include 'session.php';
      if(isset($_SESSION['custid'])){
           header("Location:customer.php");
       }
       else if(isset($_SESSION['agentid'])){
           header("Location:agent.php");
       }
       else{
           $staff_uid=$_SESSION['staffid'];
           $result = mysqli_query($conn, "select * from airline_staff where username='$staff_uid'");
           $row = mysqli_fetch_array($result);
           $airlineNam = $row['airline_name'];
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
        <h2>Upcoming Flights</h2>
        <?php
         $upcomingquery = mysqli_query($conn,"select *
         from flight 
         where airline_name = '$airlineNam' and status = 'upcoming' and DATE(departure_time) between CURDATE() and (CURDATE() + INTERVAL 30 DAY)");
          $rowtest = mysqli_num_rows($upcomingquery);
          if ($rowtest==0){
              echo "No Upcomin Flights!<br><br>";
          }
          else{
            echo "<table><tr>
             <th>Flight</th><th>Departure Airport</th><th>Departure Time</th><th>Arrival Airport</th><th>Arrival Time</th>
             </tr>";
            while ($rows = mysqli_fetch_row($upcomingquery)) {
              echo "<tr><td>".$rows[1]."</td><td>".$rows[2]."</td><td>".$rows[3]."</td><td>".$rows[4]."</td><td>".$rows[5]."</td></tr>";
            }
            echo "</table><br>";
          }
        ?>
        <form name = "upcomingflightform" action="" method="post">
         From: <input type="date" name="datefrom"placeholder="Date">
         To: <input type="date" name="dateto"placeholder="Date"><br>
         Source: <input type="text" name="sourceresult" placeholder="Source">
         Destination: <input type="text" name="destinationresult" placeholder="Destination"><br>
         <input type="radio" name="choice"  value="a" checked>City<br>
	     <input type="radio" name="choice"  value="b">Airport Name<br>
         <br>
        <input type="submit" name="upcomingflightSubmit" value="Submit"><br>
        </form>
        <?php
            if(isset($_POST['upcomingflightSubmit'])){
                $fromtime = mysqli_real_escape_string($conn,$_POST['datefrom']);
                $totime = mysqli_real_escape_string($conn,$_POST['dateto']);
                $sourcedata= mysqli_real_escape_string($conn,$_POST['sourceresult']);
                $arrivaldata = mysqli_real_escape_string($conn,$_POST['destinationresult']);
                $choiceValv2 = mysqli_real_escape_string($conn,$_POST['choice']);
                if($fromtime=="" && $totime==""){
                  if($choiceValv2 == "a"){
                       $upcomingquery2 = mysqli_query($conn,"select *
                       from flight
                       where airline_name = '$airlineNam'
		               and flight.departure_airport in (select airport_name from airport where airport_city = '$sourcedata')
		               and flight.arrival_airport in (select airport_name from airport where  airport_city = '$arrivaldata')");
                  }
                  if($choiceValv2 == "b"){
                      $upcomingquery2 = mysqli_query($conn,"select * from flight
                    where airline_name = '$airlineNam' and departure_airport = '$sourcedata' and arrival_airport = '$arrivaldata'");
                  }
                }
                else if($sourcedata=="" && $arrivaldata ==""){
                    $upcomingquery2 = mysqli_query($conn,"select * from flight
                    where airline_name = '$airlineNam' and DATE(departure_time) between '$fromtime' and '$totime'");
                }
                else if($sourcedata!="" && $arrivaldata !="" && $fromtime!="" && $totime!=""){
                    if($choiceValv2 == "a"){
                        $upcomingquery2 = mysqli_query($conn,"select *
                        from flight
                       where airline_name = '$airlineNam' and DATE(departure_time) between '$fromtime' and '$totime' and flight.departure_airport in (select airport_name from airport where airport_city = '$sourcedata')
		               and flight.arrival_airport in (select airport_name from airport where  airport_city = '$arrivaldata') ");
                    }
                    if($choiceValv2 == "b"){
                    $upcomingquery2 = mysqli_query($conn,"select * from flight
                    where airline_name = '$airlineNam' and DATE(departure_time) between '$fromtime' and '$totime' and departure_airport = '$sourcedata' and arrival_airport = '$arrivaldata'");
                    }
                }
                $rowtest2 = mysqli_num_rows($upcomingquery2);
                if ($rowtest2==0){
                    echo "No Upcomin Flights!<br><br>";
                }
                else{
                echo "<table><tr>
                <th>Flight</th><th>Departure Airport</th><th>Departure Time</th><th>Arrival Airport</th><th>Arrival Time</th><th>Status</th>
                </tr>";
                while ($rows2 = mysqli_fetch_row($upcomingquery2)) {
                        echo "<tr><td>".$rows2[1]."</td><td>".$rows2[2]."</td><td>".$rows2[3]."</td><td>".$rows2[4]."</td><td>".$rows2[5]."</td><td>".$rows2[7]."</td></tr>";
                }
                        echo "</table><br>";   
                }
            }
        ?>  
        <form name = "customercheckform" action="" method="post">
            <h2>Passenger List</h2>
            Flight Number: <select id="flight_Num2" name="flightnumcheck">
            </select><br>
            <?php
                $result = mysqli_query($conn, "select * from flight where airline_name='$airlineNam'");
                $flightnumArr = array();
                while ($row = mysqli_fetch_row($result)) {
                     array_push($flightnumArr, $row[1]);
                }
            ?>
            <script type=text/javascript>
            //handle airline
            var flightnumArray = <?php echo json_encode($flightnumArr); ?>;
            var flightnumTBL=document.getElementById('flight_Num2');
            for (var i=0; i<flightnumArray.length; i++) {
                                    flightnumTBL.options[flightnumTBL.length] = new Option(flightnumArray[i],flightnumArray[i]);
            }
            </script>
            <input type="submit" name="custchecksubmit" value="Submit"><br>
        </form>
        <?php
        if(isset($_POST['custchecksubmit'])){
            $flightNumcheck = mysqli_real_escape_string($conn,$_POST['flightnumcheck']);
            $upcomingquery3 = mysqli_query($conn,"select DISTINCT customer.name, customer.email
from (flight natural join ticket natural join purchases natural join customer)
where airline_name = '$airlineNam' and flight_num = '$flightNumcheck'and flight.flight_num=ticket.flight_num and purchases.ticket_id=ticket.ticket_id and customer.email=purchases.customer_email");
         $rowtest3 = mysqli_num_rows($upcomingquery3);
         if ($rowtest3==0){
            echo "No Customer in Flight!<br><br>";
         }
        else{
             echo"<p>Passenger List for flight ".$flightNumcheck.":</p>";
             echo "<table><tr><th>Name</th><th>Email</th></tr>";
             while ($rows3 = mysqli_fetch_row($upcomingquery3)) {
                 echo "<tr><td>".$rows3[0]."</td><td>".$rows3[1]."</td></tr>";
             }
            echo "</table><br>";   
        }
        }
         
        
        ?>
         <!--                  Flights!!!!!!              -->
         <form name = "Flight" action="" method="post">
            <h2>Add Flights</h2>
            Flight Number: <input type="number" min=0 name="flightnum" placeholder="Flight Number"><br>
            Departure Airport: <select id="airport" name="airportDepName"></select><br>
             <?php
                $result = mysqli_query($conn, "select * from airport");
                $airportArr = array();
                while ($row = mysqli_fetch_row($result)) {
                     array_push($airportArr, $row[0]);
                }
            ?>
            <script type=text/javascript>
            var airportArray = <?php echo json_encode($airportArr); ?>;
            var airportTBL=document.getElementById('airport');
            for (var i=0; i<airportArray.length; i++) {
                                    airportTBL.options[airportTBL.length] = new Option(airportArray[i],airportArray[i]);
            }
            </script>
            Departure Time: <input type="datetime" name="deptime" placeholder="Departure Time"><br>
            Arrival Airport: <select id="airport2" name="airportArrivalName"></select><br>
             <?php
                $result = mysqli_query($conn, "select * from airport");
                $airportArr = array();
                while ($row = mysqli_fetch_row($result)) {
                     array_push($airportArr, $row[0]);
                }
            ?>
            <script type=text/javascript>
            var airportArray = <?php echo json_encode($airportArr); ?>;
            var airportTBL=document.getElementById('airport2');
            for (var i=0; i<airportArray.length; i++) {
                                    airportTBL.options[airportTBL.length] = new Option(airportArray[i],airportArray[i]);
            }
            </script>
            Arrival Time: <input type="datetime" name="arrivaltime" placeholder="Arrival Time"><br>
            Price: <input type="number" min="0.01" step="0.01" max="2500" name="price" value="100.00" /><br>
            Status: <select name="status">
                <option value="Upcoming">Upcoming</option>
                <option value="Delayed">Delayed</option>
                <option value="In-Progress">In-Progress</option>
            </select><br>
             
            Airplane ID: <select id="airplane" name="airplaneName">
            </select><br>
            <?php
                $result = mysqli_query($conn, "select * from airplane where airline_name='$airlineNam'");
                $airplaneArr = array();
                while ($row = mysqli_fetch_row($result)) {
                     array_push($airplaneArr, $row[1]);
                }
            ?>
            <script type=text/javascript>
            //handle airline
            var airplaneArray = <?php echo json_encode($airplaneArr); ?>;
            var airplaneTBL=document.getElementById('airplane');
            for (var i=0; i<airplaneArray.length; i++) {
                                    airplaneTBL.options[airplaneTBL.length] = new Option(airplaneArray[i],airplaneArray[i]);
            }
            </script>
            <input type="submit" name="flightSubmit" value="Submit"><br>
         </form>
        <?php
         if(isset($_POST['flightSubmit'])){
             $airlineflight = $airlineNam;
             $flightNum = mysqli_real_escape_string($conn,$_POST['flightnum']);
             $depAirport = trim(mysqli_real_escape_string($conn,$_POST['airportDepName']));
             $departtime = mysqli_real_escape_string($conn,$_POST['deptime']);
             $arrAirport = trim(mysqli_real_escape_string($conn,$_POST['airportArrivalName']));
             $arrtime = mysqli_real_escape_string($conn,$_POST['arrivaltime']);
             $priceFlight = mysqli_real_escape_string($conn,$_POST['price']);
             $status = trim(mysqli_real_escape_string($conn,$_POST['status']));
             $planeID = trim(mysqli_real_escape_string($conn,$_POST['airplaneName']));
             $verif = mysqli_query($conn,"SELECT * FROM flight WHERE flight_num = '$flightNum' and airline_name='$airlineNam'");
             $verif2 = mysqli_query($conn,"SELECT * FROM `airplane` where airline_name='$airlineNam' and airplane_id='$planeID'");
             $verif3 = mysqli_query($conn,"SELECT * FROM `airline_staff` where username='$staff_uid'");
             $numrows = mysqli_num_rows($verif);
             $numrows2 = mysqli_num_rows($verif2);
             $numrows3 = mysqli_num_rows($verif2);
             if($numrows == 1) {	//If number of rows is 1, then you have a flight
			     $haveAccount = "Flight Number already Exist!";
			     echo "<script type='text/javascript'>alert('$haveAccount');</script>";
		     }
             else if($numrows2 ==0){
                 $haveAccount = "Your airline does not own this plane!";
			     echo "<script type='text/javascript'>alert('$haveAccount');</script>";
             }
             else if($numrows3 ==0){
                 $haveAccount = "Internal Error!";
			     echo "<script type='text/javascript'>alert('$haveAccount');</script>";
             }
             else{
                 //create flight
                 $result = mysqli_query($conn, "INSERT INTO flight(airline_name, flight_num, departure_airport, departure_time, arrival_airport, arrival_time, price, status, airplane_id)
				VALUES ('$airlineflight', '$flightNum', '$depAirport', '$departtime', '$arrAirport', ' $arrtime','$priceFlight','$status','$planeID');");
				if (!$result) { //If query doesnt work
					$errormessage = mysqli_error($conn);
					echo "<script type='text/javascript'>alert('$errormessage');</script>";
					exit();
				}
                 //add tickets
                 $query = mysqli_query($conn,"SELECT airplane.seats FROM `flight` natural join airplane where flight.airplane_id='$planeID'");
                  while ($row = mysqli_fetch_row($query)) {
                      $seats= $row[0];
                  }
                 for($i=0; $i<$seats; $i++){
                     $result = mysqli_query($conn, "INSERT INTO ticket(airline_name, flight_num,booked)
				VALUES ('$airlineNam', '$flightNum',0);");
                 }
                 if (!$result) { //If query doesnt work
					$errormessage = mysqli_error($conn);
					echo "<script type='text/javascript'>alert('$errormessage');</script>";
					exit();
				}

				$success = "Congrats, Flight Created!";
				echo "<script type='text/javascript'>alert('$success');</script>";
             }
         }
        ?>
        
        <!--                  Airport!!!!!!              -->
         <form name = "AirportForm" action="" method="post">
             <h2>Add Airport</h2>
             <input type="text" name="airportName" placeholder="Airport Name"><br>
             <input type="text" name="airportCity" placeholder="City"><br>
             <input type="submit" name="AirportSubmit" value="Submit"><br>
        </form>
        <?php
            if(isset($_POST['AirportSubmit'])){
                 $airportNam = mysqli_real_escape_string($conn,strtolower($_POST['airportName']));
                 $airportCiti= mysqli_real_escape_string($conn,$_POST['airportCity']);
                 $verif = mysqli_query($conn,"SELECT * FROM airport WHERE airport_name = '$airportNam'");
                 $verif2 = mysqli_query($conn,"SELECT * FROM `airline_staff` where username='$staff_uid'");
                 $numrows = mysqli_num_rows($verif);
                 $numrows2 = mysqli_num_rows($verif2);
                 if($numrows == 1) {	
			         $haveAccount = "Airport already Exist!";
			         echo "<script type='text/javascript'>alert('$haveAccount');</script>";
		         }
                 else if($numrows2 == 0) {	
			         $haveAccount = "Internal Error!";
			         echo "<script type='text/javascript'>alert('$haveAccount');</script>";
		         }
                 else{
                     $result = mysqli_query($conn, "INSERT INTO airport(airport_name, airport_city)
				VALUES ('$airportNam', '$airportCiti');");
				    if (!$result) { //If query doesnt work
					   $errormessage = mysqli_error($conn);
					   echo "<script type='text/javascript'>alert('$errormessage');</script>";
					   exit();
				    } 
				    $success = "Congrats, Airport Created!";
				    echo "<script type='text/javascript'>alert('$success');</script>";
                 }
            }
        ?>
        
         <!--                  Airplane!!!!!!              -->
        <form name = "AirplaneForm" action="" method="post">
            <h2>Add Airplane</h2>
            <input type="number" min= 1 name="airplane_id" placeholder="Plane ID"><br>
            <input type="number" min = 1 name="seats" placeholder="Seats"><br>
            <input type="submit" name="AirplaneSubmit" value="Submit"><br>
        </form>
        <?php
            if(isset($_POST['AirplaneSubmit'])){
                $airlinePlane = $airlineNam;
                $airplaneID= mysqli_real_escape_string($conn,$_POST['airplane_id']);
                $seats= mysqli_real_escape_string($conn,$_POST['seats']);
                $verif = mysqli_query($conn,"SELECT * FROM airplane WHERE airplane_id = '$airplaneID' and airline_name='$airlineNam'");
                $verif2 = mysqli_query($conn,"SELECT * FROM `airline_staff` where username='$staff_uid'");
                 $numrows = mysqli_num_rows($verif);
                 $numrows2 = mysqli_num_rows($verif2);
                 if($numrows == 1) {	
			         $haveAccount = "Airplane already Exist!";
			         echo "<script type='text/javascript'>alert('$haveAccount');</script>";
		         }
                 else if($numrows2 == 0) {	
			         $haveAccount = "Internal Error!";
			         echo "<script type='text/javascript'>alert('$haveAccount');</script>";
		         }
                 else{
                     $result = mysqli_query($conn, "INSERT INTO airplane(airline_name, airplane_id, seats)
				VALUES ('$airlinePlane', '$airplaneID','$seats');");
				if (!$result) { //If query doesnt work
					$errormessage = mysqli_error($conn);
					echo "<script type='text/javascript'>alert('$errormessage');</script>";
					exit();
				}

				$success = "Congrats, Airplane Created!";
				echo "<script type='text/javascript'>alert('$success');</script>";
                //show all planes
                 $query = mysqli_query($conn,"SELECT * FROM airplane WHERE airline_name = '$airlineNam'"); 
                     echo "<table><tr>
             <th>Airplane ID</th><th>Seats</th></tr>";
                    while ($row = mysqli_fetch_row($query)) {
                        echo"<tr><td>".$row[1]."</td><td> ".$row[2]."</td></tr>";
                    }
                     echo "</table>";
                     
                 }
            }
        ?>
         <form name = "changeStatus" action="" method="post">
            <h2> Change Status:</h2>
            Flight Number: <select id="flight_Num" name="flightnumber">
            </select>
            <?php
                $result = mysqli_query($conn, "select * from flight where airline_name='$airlineNam'");
                $flightnumArr = array();
                while ($row = mysqli_fetch_row($result)) {
                     array_push($flightnumArr, $row[1]);
                }
            ?>
            <script type=text/javascript>
            //handle airline
            var flightnumArray = <?php echo json_encode($flightnumArr); ?>;
            var flightnumTBL=document.getElementById('flight_Num');
            for (var i=0; i<flightnumArray.length; i++) {
                                    flightnumTBL.options[flightnumTBL.length] = new Option(flightnumArray[i],flightnumArray[i]);
            }
            </script>
            Status: <select name="statusChange">
                <option value="Upcoming">Upcoming</option>
                <option value="Delayed">Delayed</option>
                <option value="In-Progress">In-Progress</option>
            </select><br>
            <input type="submit" name="statusSubmit" value="Submit"><br>
        </form>
        <?php
            if(isset($_POST['statusSubmit'])){
            $flightNumm = trim(mysqli_real_escape_string($conn,$_POST['flightnumber']));
            $status2 = trim(mysqli_real_escape_string($conn,$_POST['statusChange']));
            $descQuery = "UPDATE flight SET status ='$status2' where flight_num= $flightNumm";
            $result = mysqli_query($conn, $descQuery); 
            $success = "Status Changed!";
            echo "<script type='text/javascript'>alert('$success');</script>";
            }
        ?>
        <h2> Most Frequent customer:</h2>
        <?php
             $comissionquery = mysqli_query($conn,"select customer.name, customer.email, count(customer.email) as tickets_bought
from (customer natural join purchases natural join ticket)
where airline_name = '$airlineNam' and YEAR(purchase_date) between YEAR(CURDATE() - INTERVAL 1 YEAR) and YEAR(CURDATE())
group by customer.email
order by count(customer.email) DESC
limit 1");
        $row = mysqli_fetch_array($comissionquery);
        echo("Most Frequent Customer for ".$airlineNam." is: ".$row[0]." with ".$row[2]." tickets bought<br>"); 
        ?>
         <form name = "Flight" action="" method="post">
            Search Flights for specific Customer: <input type="text" name="custemailsearch" placeholder="Customer Email">
             <input type="submit" name="custSubmit" value="Submit"><br>
        </form>
        <?php
            if(isset($_POST['custSubmit'])){
                $custEmail= mysqli_real_escape_string($conn,$_POST['custemailsearch']);
                $custsearchQuery = mysqli_query($conn, "select DISTINCT customer.name, flight_num
from (flight natural join ticket natural join purchases natural join customer)
where airline_name = '$airlineNam' and customer.email= '$custEmail'");
                 $numrowz = mysqli_num_rows($custsearchQuery);
                if ($numrowz==0){
                    echo "Customer Not Found!";
                }
                else{
                 echo "<p>Results for ".$custEmail.":</p>";
                echo "<table><tr><td>Flight Taken</td></tr>";
                 while ($rowcust = mysqli_fetch_row($custsearchQuery)) {
                     echo "<tr><td>Flight ".$rowcust[1]."</td></tr>";
                 }
                 echo "</table>";
                }
            }
        ?>
        <h2> Top 5 Booking Agents Past Month by ticket sales:</h2>
        <?php
        $agentQuery = mysqli_query($conn,"select booking_agent_id, count(booking_agent_id) as ticket_sales
        from purchases 
        where booking_agent_id is NOT null
        and Month(purchase_date) between Month(CURDATE() - INTERVAL 1 MONTH) and Month(CURDATE())
        group by booking_agent_id
        order by count(booking_agent_id) desc
        limit 5");
        $count=1;
        echo "<table><tr>
             <th>Rank</th><th>Agent ID</th><th>Amount</th>
             </tr>";
        while ($rowagent1 = mysqli_fetch_row($agentQuery)) {
            echo "<tr><td>".$count."</td><td>".$rowagent1[0]."</td><td>".$rowagent1[1]."</td></tr>";
            $count++;
        }
        echo "</table>";
        echo"<h2> Top 5 Booking Agents Past Year by ticket sales:</h2>";
         $agentQuery2 = mysqli_query($conn,"select booking_agent_id, count(booking_agent_id) as ticket_sales
         from purchases 
         where booking_agent_id is NOT null and Year(purchase_date) between YEAR(CURDATE() - INTERVAL 1 YEAR) and YEAR(CURDATE())
         group by booking_agent_id
         order by count(booking_agent_id) desc
         limit 5");
        $count2=1;
         echo "<table><tr>
             <th>Rank</th><th>Agent ID</th><th>Amount</th>
             </tr>";
        while ($rowagent2 = mysqli_fetch_row($agentQuery2)) {
            echo "<tr><td>".$count2."</td><td>".$rowagent2[0]."</td><td>".$rowagent2[1]."</td></tr>";
            $count2++;
        }
        echo "</table>";
        echo"<h2> Top 5 Booking Agents Past Year by Comission:</h2>";
        $agentQuery3 = mysqli_query($conn,"select booking_agent_id, sum(price * 0.10) as commission
        from (flight natural join ticket natural join purchases)
        where booking_agent_id is NOT null and Year(purchase_date) between YEAR(CURDATE() - INTERVAL 1 YEAR) and YEAR(CURDATE())
        group by booking_agent_id
        order by commission desc
        limit 5");
        $count3=1;
        echo "<table><tr>
             <th>Rank</th><th>Agent ID</th><th>Amount</th>
             </tr>";
        while ($rowagent3 = mysqli_fetch_row($agentQuery3)) {
            echo "<tr><td>".$count3."</td><td>".$rowagent3[0]."</td><td> $".$rowagent3[1]."</td></tr>";
            $count3++;
        }
        echo "</table>";
        ?>
        <h2>Ticket sales</h2>
        <?php
        $ticketQuery = mysqli_query($conn,"select count(*) as ticket_sales
from (purchases natural join ticket)
where airline_name = '$airlineNam' and MONTH(purchase_date) between MONTH(CURDATE() - INTERVAL 1 Month) and MONTH(CURDATE())");
         $rowticket = mysqli_fetch_array($ticketQuery);
         echo "<p> last month: ".$rowticket[0]."</p>";
        $ticketQuery2 = mysqli_query($conn,"select count(*) as ticket_sales
from (purchases natural join ticket)
where airline_name = '$airlineNam' and YEAR(purchase_date) between YEAR(CURDATE() - INTERVAL 1 YEAR) and YEAR(CURDATE())");
         $rowticket2 = mysqli_fetch_array($ticketQuery2);
         echo "<p> last Year: ".$rowticket2[0]."</p>";
        ?>
        <p>Specify Date: </p>
        <form name = "ticketsales" action="" method="post">
         From: <input type="date" name="dateStart"placeholder="Date" required>
         To: <input type="date" name="dateend"placeholder="Date" required><br><br>
        <input type="submit" name="ticketSubmit" value="Submit"><br>
        </form>
        <?php
        if(isset($_POST['ticketSubmit'])){
            $starttime = mysqli_real_escape_string($conn,$_POST['dateStart']);
            $endtime = mysqli_real_escape_string($conn,$_POST['dateend']);
            $ticketQuery3 = mysqli_query($conn,"select count(*) as ticket_sales
from (purchases natural join ticket)
where airline_name = '$airlineNam' and DATE(purchase_date) between '$starttime' and '$endtime'");
            $rowticket3 = mysqli_fetch_array($ticketQuery3);
            echo "<table><tr>
             <th>From</th><th>To</th><th>Amount</th>
             </tr> <tr><td>".$starttime."</td><td>".$endtime."</td><td>".$rowticket3[0]."</td></tr></table>";                            
        }
         echo "<h2>Monthly View for ".date("Y").":</h2>";
              $ticketQuery4 = mysqli_query($conn,"select MONTH(purchase_date) as month, count(*) as ticket_sales
from (purchases natural join ticket)
where airline_name = '$airlineNam' and YEAR(purchase_date) = YEAR(CURDATE())
group by MONTH(purchase_date)");
             echo('<table>  <tr>
             <th>Month</th><th>Ticket Sold</th>
             </tr>');
            while ($rowticket4 = mysqli_fetch_row($ticketQuery4)) {
                echo '<tr><td>'.$rowticket4[0].'</td><td><img border="0" src="shim.gif" width="'.($rowticket4[1]*20).'" height="16"> '.$rowticket4[1].'</td></tr>';
            }  
            echo("</table>")
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