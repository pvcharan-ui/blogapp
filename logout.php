<?php
require __DIR__.'/lib/auth.php';
do_logout();
header('Location: index.php');
