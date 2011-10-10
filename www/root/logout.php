<?php

require_once('runtime.php');

foreach ($_SESSION as $k=>$v) {
    sess($k, null);
}
header('Location: /');
exit;
