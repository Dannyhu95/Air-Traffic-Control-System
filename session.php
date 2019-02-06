<?php
  include 'connection.php';
  $conn = OpenCon();
   session_start();
       $staff_check = @$_SESSION['staffid'];
       //find login_session
        $result = mysqli_query($conn,"select username from airline_staff where username = '$staff_check'");
        $row = mysqli_fetch_array($result,MYSQL_ASSOC);
        $staff_session= $row['username']; //store login session from array
        $cust_check = @$_SESSION['custid'];

       $result = mysqli_query($conn,"select email from customer where email = '$cust_check'");
       $row = mysqli_fetch_array($result,MYSQL_ASSOC);
       $cust_session= $row['email']; //store login session from array
        $agent_check =@$_SESSION['agentid'];
        $result = mysqli_query($conn,"select email from booking_agent where email = '$agent_check'");
        $row = mysqli_fetch_array($result,MYSQL_ASSOC);
        $agent_session= $row['email']; //store login session from array
   //set homepage
   if(!isset($_SESSION['staffid']) && !isset($_SESSION['custid']) && !isset($_SESSION['agentid'])) {
     header("Location:login.php");
      exit();
   }