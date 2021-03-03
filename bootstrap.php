<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

include './src/config/DatabaseConnector.php';

ini_set("allow_url_fopen", true);

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$dbConnection = (new DatabaseConnector())->getConn();
?>