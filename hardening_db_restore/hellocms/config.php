<?php
  $servername = "localhost";
  $username = "root";
  $password = "root";
  $dbname = "ctf";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed!");
  }
?>