<html>
  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title>Airline</title>
       <?php 
         include 'connection.php';
         $conn = OpenCon();
       ?>
    </head>
    <body>
         <a href ="login.php">Back</a>
        <h1>Registeration</h1>
        <form name = "staff" action="" method="post">
            <p>Airline Staff Register</p>
            <input type="text" name="fnameStaff" placeholder="First Name"><br>
            <input type="text" name="lnameStaff" placeholder="Last Name"><br>
            <input type="text" name="usrnameStaff" placeholder="Username"><br>
            <input type="date" name="dobStaff" placeholder="Date of birth"><br>
            <input type="password" name="passStaff" placeholder="*******"><br>
            <select id="airline" name="airlineName">
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
            <input type="submit" name="staffSubmit" value="Submit"><br>
        </form>
        <?php
            if(isset($_POST['staffSubmit'])){
                $firstnameStaff = mysqli_real_escape_string($conn,$_POST['fnameStaff']);
		        $lastnameStaff = mysqli_real_escape_string($conn,$_POST['lnameStaff']);
		        $userStaff = mysqli_real_escape_string($conn,strtolower($_POST['usrnameStaff']));
                $dobStaff = mysqli_real_escape_string($conn,$_POST['dobStaff']);
                $passwordStaff = md5(mysqli_real_escape_string($conn,$_POST['passStaff']));
                $airlineStaff = trim(mysqli_real_escape_string($conn,$_POST['airlineName']));
                $verif = mysqli_query($conn,"SELECT * FROM airline_staff WHERE username = '$userStaff'");
                $numrows = mysqli_num_rows($verif);
                if($numrows == 1) {	//If number of rows is 1, then you have an email address registered already
			     $haveAccount = "You already have an account! Please go back and login with it.";
			     echo "<script type='text/javascript'>alert('$haveAccount');</script>";
		        }
                else{
                $result = mysqli_query($conn, "INSERT INTO airline_staff(username, password, first_name, last_name, date_of_birth, airline_name)
				VALUES ('$userStaff', '$passwordStaff', '$firstnameStaff', '$lastnameStaff', '$dobStaff', '$airlineStaff');");
				if (!$result) { //If query doesnt work
					$errormessage = mysqli_error($conn);
					echo "<script type='text/javascript'>alert('$errormessage');</script>";
					exit();
				}

				$success = "Congrats, account created!";
				echo "<script type='text/javascript'>alert('$success');</script>";
			}
            }
        ?>
        <br>
         <form name = "cust" action="" method="post">
            <p>Customer Register</p>
            <input type="text" name="nameCust" placeholder="Name"><br>
            <input type="email" name="emailCust" placeholder="Email"><br>
            <input type="password" name="passCust" placeholder="*******"><br>
            <input type="text" name="addressbdNumCust" placeholder="Building Number"><br>
            <input type="text" name="addressst" placeholder="Street"><br>
            <input type="text" name="addresscity" placeholder="City"><br>
            <input type="text" name="addressstate" placeholder="State"><br>
            <input type="tel" name="phoneCust" placeholder="Phone"><br>
            <input type="text" name="passportNum" placeholder="Passport Number"><br>
            <input type="date" name="passExpiration" placeholder="Passport Expiration"><br>
            <input type="text" name="passCountry" placeholder="Passport Country"><br>
            <input type="date" name="dobCust" placeholder="Date of Birth"><br>
            <br>
            <input type="submit" name="custSubmit" value="Submit"><br>
        </form>
        <?php
            if(isset($_POST['custSubmit'])){
                $namecust = mysqli_real_escape_string($conn,$_POST['nameCust']);
		        $usercust = mysqli_real_escape_string($conn,strtolower($_POST['emailCust']));
                $passwordcust = md5(mysqli_real_escape_string($conn,$_POST['passCust']));
                $buildingNum = mysqli_real_escape_string($conn,$_POST['addressbdNumCust']);
                $street = mysqli_real_escape_string($conn,$_POST['addressst']);
                $city = mysqli_real_escape_string($conn,$_POST['addresscity']);
                $state = mysqli_real_escape_string($conn,$_POST['addressstate']);
                $phone = mysqli_real_escape_string($conn,$_POST['phoneCust']);
                $passport_num = mysqli_real_escape_string($conn,$_POST['passportNum']);
                $passport_expiration = mysqli_real_escape_string($conn,$_POST['passExpiration']);
                $passport_country = mysqli_real_escape_string($conn,$_POST['passCountry']);
                $dobcust = mysqli_real_escape_string($conn,$_POST['dobCust']);
                $verif = mysqli_query($conn,"SELECT * FROM customer WHERE email = '$usercust'");
                $numrows = mysqli_num_rows($verif);
                if($numrows == 1) {	//If number of rows is 1, then you have an email address registered already
			     $haveAccount = "You already have an account! Please go back and login with it.";
			     echo "<script type='text/javascript'>alert('$haveAccount');</script>";
		        }
                else{
                $result = mysqli_query($conn, "INSERT INTO customer(email, name, password, building_number, street, city, state, phone_number, passport_number, passport_expiration, passport_country, date_of_birth)
				VALUES ('$usercust', '$namecust', '$passwordcust', '$buildingNum', '$street', '$city', '$state', '$phone', '$passport_num', ' $passport_expiration', '$passport_country', '$dobcust');");
				if (!$result) { //If query doesnt work
					$errormessage = mysqli_error($conn);
					echo "<script type='text/javascript'>alert('$errormessage');</script>";
					exit();
				}

				$success = "Congrats, account created!";
				echo "<script type='text/javascript'>alert('$success');</script>";
			}
            }
        ?>
        <br>
        <form name = "agent" action="" method="post">
            <p>Booking Agent Register</p>
            <input type="email" name="emailagent" placeholder="Email"><br>
            <input type="password" name="passagent" placeholder="********"><br>
            <br>
            <input type="submit" name="agentSubmit" value="Submit"><br>
        </form>
         <?php
            
            if(isset($_POST['agentSubmit'])){
               $useragent = mysqli_real_escape_string($conn,strtolower($_POST['emailagent']));
               $passwordagent = md5(mysqli_real_escape_string($conn,$_POST['passagent']));
               $agentID= mysqli_real_escape_string($conn,hexdec(uniqid(rand())));      
                $verif = mysqli_query($conn,"SELECT * FROM booking_agent WHERE email = '$useragent'");
                $numrows = mysqli_num_rows($verif);
                if($numrows == 1) {	//If number of rows is 1, then you have an email address registered already
			     $haveAccount = "You already have an account! Please go back and login with it.";
			     echo "<script type='text/javascript'>alert('$haveAccount');</script>";
		        }
                else{
                    $result = mysqli_query($conn, "INSERT INTO booking_agent(email, password)
				VALUES ('$useragent', '$passwordagent');");
				if (!$result) { //If query doesnt work
					$errormessage = mysqli_error($conn);
					echo "<script type='text/javascript'>alert('$errormessage');</script>";
					exit();
				}

				$success = "Congrats, account created!";
				echo "<script type='text/javascript'>alert('$success');</script>";
			}
        }
        ?>
    </body>
</html> 