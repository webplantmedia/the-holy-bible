<?php
ini_set('memory_limit','160M');

// Debug
require_once 'debug.php';
// Helper functions
require_once 'functions.php';

if ( isset( $_GET['translation'] ) && ! empty( $_GET['translation'] ) ) {
	$translation = get_translation( strtolower( $_GET['translation'] ) );
}
else {
	$translation = get_translation( 'kjv' );
}

// Create connection
$con = mysqli_connect( "localhost", "root", "jesuschrist", "TEST_bible" );

// Check connection
if ( mysqli_connect_errno() ) {
	echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
}

$r = mysqli_query($con, "SELECT * FROM $bible_book ORDER BY number ASC");
$book = array();
while( $b = mysqli_fetch_array( $r, MYSQLI_ASSOC ) ) {
	$b['filename'] = str_replace( " ", "-", strtolower( $b['fullname'] ) );
	$b['anchor'] = str_replace( " ", "-", strtolower( $b['short'] ) );
	$book[ $b['number'] ] = $b;
}
foreach ( $book as $key => $value ) {
	$prev = $key - 1;
	$next = $key + 1;

	$book[ $key ]['prev_link'] = $book[ $key ]['filename'] . '-intro.html';
	if ( isset( $book[ $prev ] ) ) {
		$book[ $key ]['prev_chap'] = $book[ $prev ]['filename'] . '.html#' . $book[ $prev ]['anchor'] . '-ch' . $book[ $prev ]['chapters'];
		$book[ $key ]['prev_book'] = $book[ $prev ]['filename'] . '-intro.html';
		$book[ $key ]['prev_book_title'] = $book[ $prev ]['fullname'];
	}
	if ( isset( $book[ $next ] ) ) {
		$book[ $key ]['next_link'] = $book[ $next ]['filename'] . '-intro.html';
		$book[ $key ]['next_book'] = $book[ $next ]['filename'] . '-intro.html';
		$book[ $key ]['next_book_title'] = $book[ $next ]['fullname'];
	}
	$book[ $key ]['next_chap'] = $book[ $key ]['filename'] . '.html#' . $book[ $key ]['anchor'] . '-ch1';
}

if ( isset( $_GET['import'] ) ) {
	// pr($book);
	require_once 'texttosql.php';
}
else {
	$header = get_html_header();
	$footer = get_html_footer();
	build_ncx2( $book );
	build_spine_manifest( $book );
	build_html_intro( $book, $header, $footer );
	build_html_toc( $book, $header, $footer );
	build_html_body( $book, $header, $footer, $con );
	// build_html_appendix( $book, $header, $footer, $con );
}

mysqli_close($con);
