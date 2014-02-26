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

$r = mysqli_query($con, "SELECT * FROM bible_books_en ORDER BY number ASC");
$book = array();
while( $b = mysqli_fetch_array( $r, MYSQLI_ASSOC ) ) {
	$b['anchor'] = str_replace( " ", "-", strtolower( $b['fullname'] ) );
	$book[ $b['number'] ] = $b;
}
foreach ( $book as $key => $value ) {
	$prev = $key - 1;
	$next = $key + 1;
	if ( isset( $book[ $prev ] ) ) {
		$book[ $key ]['prev_link'] = $book[ $prev ]['anchor'] . '.html';
	}
	if ( isset( $book[ $next ] ) ) {
		$book[ $key ]['next_link'] = $book[ $next ]['anchor'] . '.html';
	}
}

$header = get_html_header();
$footer = get_html_footer();
build_ncx2( $book );
build_spine_manifest( $book );
build_html_intro( $book, $header, $footer );
build_html_toc( $book, $header, $footer );
build_html_body( $book, $header, $footer, $con );
build_html_appendix( $book, $header, $footer, $con );

mysqli_close($con);
