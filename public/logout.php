<?php

require_once "../app/core/Session.php";

Session::start();

Session::destroy();

header("Location: login.php");

exit;