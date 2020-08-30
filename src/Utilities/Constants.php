<?php
namespace App\Utilities;

class Constants {
    public const UPLOAD_DIRECTORY = "/file_uploads/";
    public const MAX_FILE_SIZE = "5G";
    public const MINIMUM_FREE_SPACE = 1073741824; // 1GB in bytes
    public const FILE_LIFETIME_SECONDS = 12 * 60 * 60; // 12 hours
}
