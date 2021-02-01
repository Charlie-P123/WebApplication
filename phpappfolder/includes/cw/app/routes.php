<?php

require $app_path . 'src/DatabaseWrapper.php'; // make db functions available

require 'routes/homepage.php';
require 'routes/createmessage.php';
require 'routes/downloadmessagedata.php';
require 'routes/sendmessage.php';
require 'routes/viewmessagedata.php';

require 'routes/dbtest.php'; //testing
