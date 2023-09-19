<?php

session_start();
session_unset();
session_destroy();
$_SESSION['login'] = FALSE;

header('Location: landPage.html');
