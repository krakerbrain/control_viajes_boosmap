<?php
session_start();
session_destroy();
header("location:../login/index.php");
// header("location:http://biowork.tech/login/index.php");
?>