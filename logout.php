<?php
   session_start();
   
      unset($_SESSION["staffid"]);
      unset($_SESSION["custid"]);
      unset($_SESSION["agentid"]);
   header("location:login.php");
?>