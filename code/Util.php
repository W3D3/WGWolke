<?php

class Util {

    /*
    * Create a new guid
    */
    public static function newGuid() {
        if (function_exists("com_create_guid") == true) {
            return trim(com_create_guid(), "{}");
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535));
    }

    /*
    * Create a new empty guid
    */
    public static function newEmptyGuid() {
        return "00000000-0000-0000-0000-000000000000";
    }
}