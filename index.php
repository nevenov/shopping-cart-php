<?php
session_start();

$session = &$_SESSION;

include('include.php');

include('layout/header.php');

new Router($_GET, $_POST, $session);

include('layout/footer.php');