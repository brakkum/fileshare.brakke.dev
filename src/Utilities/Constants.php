<?php

define("UPLOAD_DIRECTORY", "/file_uploads/");
define("MAX_FILE_SIZE", "512m");
define("MINIMUM_FREE_SPACE", 21474836480); // bytes
//define("FILE_LIFETIME_SECONDS", 12 * 60 * 60); // 12 hours
define("FILE_LIFETIME_SECONDS", 600); // 10 minutes
define("URL", $_SERVER["SERVER_NAME"]);
