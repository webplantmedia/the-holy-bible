<?php
ini_set('memory_limit','160M');

// Debug
require_once 'debug.php';
// Helper functions
require_once 'functions.php';

// Create connection
$con = mysqli_connect( "localhost", "root", "jesuschrist", "TEST_bible" );

// Check connection
if ( mysqli_connect_errno() ) {
	echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
}

// Beging parse code
$delimiter = "\t";

function parse( $data ) {
	pr($data);
}

echo "Hello";

$fp = fopen('translations/tamil-romanised-bible.txt', 'r');

while ( !feof($fp) ) {
    $line = fgets($fp, 2048);

    $data = str_getcsv($line, $delimiter);

    parse($data);
}                              

fclose($fp);
