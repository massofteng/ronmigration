<?php
require_once 'vendor/autoload.php';
require_once './helpers/helpers.php';

use Dotenv\Dotenv;
use Connection\Database;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$oldDb = new Database($_ENV['DB_DATABASE_OLD']);
$newDb = new Database($_ENV['DB_DATABASE_NEW']);

// ?Run your migrations here