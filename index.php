<?php
ini_set('memory_limit','160M');

// Debug
require_once 'debug.php';

// Create connection
$con = mysqli_connect( "localhost", "root", "jesuschrist", "TEST_bible" );

// Check connection
if ( mysqli_connect_errno() ) {
	echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
}

$toc = '';
$header = '';
$body = '';
$intro = '';
$footer = '';
$pics = '';

$i = 1;
$order = 2; //toc is first
$manifest = '';
$spine = '';
$ncx = '';
$ncx_chapters = '';

$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/intro.html"></item>'."\n";
$spine .= '<itemref idref="item'.$i.'"/>'."\n";
$i++;
$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/toc.html"></item>'."\n";
$spine .= '<itemref idref="item'.$i.'"/>'."\n";
$i++;

$html = '';

?>
<?php ob_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>The Holy Bible</title>
		<link rel="stylesheet" type="text/css" href="../style.css">
	</head>
	<body>
<?php $header = ob_get_contents(); ?>
<?php $footer = '</body></html>'; ?>

<?php
$intro .= '<br />';
$intro .= '<br />';
$intro .= '<h1>The Holy Bible</h1>';
$intro .= '<br />';
$intro .= '<br />';
$intro .= '<h3 class="center">Authorized King James Version<h3>';
// $intro .= '<mbp:pagebreak />';

file_put_contents( "html/intro.html", $header . $intro . $footer );


$toc .= '<h2>Table of Contents</h2>';
$r = mysqli_query($con, "SELECT * FROM bible_books_en ORDER BY number ASC");
while( $o = mysqli_fetch_array( $r, MYSQLI_ASSOC ) ) {
	$o['anchor'] = str_replace( " ", "-", strtolower( $o['fullname'] ) );
	$body = '';

	if ( 1 == $o['number'] ) {
		$toc .= '<h3>The Old Testament</h3>';
		$toc .= '<ul class="toc">';

ob_start(); ?>
		<navPoint class="testament" id="old-testament" playOrder="<?php echo $order; ?>">
			<navLabel>
				<text>The Old Testament</text>
			</navLabel>
			<content src="html/old-testament.html"/>
		</navPoint>
<?php $ncx .= ob_get_contents();
		$order++;

		$temp = '<br /><br /><h2>The Old Testament</h2>';
		// $temp .= '<mbp:pagebreak />';
		file_put_contents( "html/old-testament.html", $header . $temp . $footer );
		$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/old-testament.html"></item>'."\n";
		$spine .= '<itemref idref="item'.$i.'"/>'."\n";
		$i++;
	}
	if ( 40 == $o['number'] ) {
		$toc .= '</ul>';
		$toc .= '<h3>The New Testament</h3>';
		$toc .= '<ul class="toc">';

ob_start(); ?>
		</navPoint>
		<navPoint class="testament" id="new-testament" playOrder="<?php echo $order; ?>">
			<navLabel>
				<text>The New Testament</text>
			</navLabel>
			<content src="html/new-testament.html"/>
		</navPoint>
<?php $ncx .= ob_get_contents();
		$order++;

		$temp = '<br /><br /><h2>The New Testament</h2>';
		// $temp .= '<mbp:pagebreak />';
		file_put_contents( "html/new-testament.html", $header . $temp . $footer );
		$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/new-testament.html"></item>'."\n";
		$spine .= '<itemref idref="item'.$i.'"/>'."\n";
		$i++;
	}

ob_start(); ?>
		<navPoint class="book" id="<?php echo $o['anchor'].'-ch1'; ?>" playOrder="<?php echo $order; ?>">
			<navLabel>
				<text><?php echo $o['fullname']; ?></text>
			</navLabel>
			<content src="html/<?php echo $o['anchor'].'.html'; ?>"/>
<?php $ncx .= ob_get_contents();
$order++;

	$toc .= '<li><a href="'.$o['anchor'].'.html">'.$o['fullname'].'</a><span class="toc-shortname">'.str_replace( ' ', '', $o['short'] ).'</span></li>';

	$body .= '<a class="book-anchor" name="'.$o['anchor'].'"></a>';
	$body .= '<h3>' . $o['title'] . '</h3>';

	$ncx_chapters = '';
	for ( $ch = 1; $ch <= $o['chapters']; $ch++ ) {
		$content_link = 'html/'.$o['anchor'].'.html';
		if ( $ch > 1 ) {
			$content_link .= '#'.$o['anchor'].'-ch'.$ch;
		}

		if ( 1 != $o['chapters'] ) {
			$body .= '<a class="chapter-anchor" name="'.$o['anchor'].'-ch'.$ch.'"></a>';
			$body .= '<h4>' . $o['fullname'] . ' ' . $ch . '</h4>'; 
ob_start(); ?>
			<navPoint class="chapter" id="<?php echo $o['anchor'].'-ch'.$ch; ?>" playOrder="<?php echo $order; ?>">
				<navLabel>
					<text><?php echo $o['fullname'].' '.$ch; ?></text>
				</navLabel>
				<content src="<?php echo $content_link; ?>"/>
			</navPoint>
<?php $ncx_chapters .= ob_get_contents();
$order++;
		}

		$r2 = mysqli_query($con, "SELECT * FROM bible_kjv WHERE book=".$o['number']." AND chapter=".$ch." ORDER BY verse ASC");
		while( $oo = mysqli_fetch_array( $r2, MYSQLI_ASSOC ) ) {
			$body .= '<p class="verse"><a class="verse-anchor" name="'.$o['anchor'].'-ch'.$ch.'-v'.$oo['verse'].'"><strong><span class="hide-this">'.str_replace( ' ', '', $o['short'] ).$oo['chapter'].'.</span>'.$oo['verse'].'</strong></a> ' . $oo['text'] . '</p>';
		}
	}

	$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/'.$o['anchor'].'.html"></item>'."\n";
	$spine .= '<itemref idref="item'.$i.'"/>'."\n";
	$i++;

	// $body .= '<mbp:pagebreak />';
	file_put_contents( "html/".$o['anchor'].".html", $header . $body . $footer );

ob_start(); ?>
<?php echo $ncx_chapters; ?>
		</navPoint>
<?php $ncx .= ob_get_contents();

}

ob_start(); ?>
		<navPoint class="appendix" id="appendix" playOrder="<?php echo $order++; ?>">
			<navLabel>
				<text>Appendix</text>
			</navLabel>
			<content src="html/appendix.html"/>
			<navPoint class="illustrations" id="illustrations" playOrder="<?php echo $order; ?>">
				<navLabel>
					<text>Illustrations</text>
				</navLabel>
				<content src="html/illustrations.html"/>
			</navPoint>
		</navPoint>
<?php $ncx .= ob_get_contents();

$toc .= '</ul>';
$toc .= '<h3>Appendix</h3>';
$toc .= '<ul class="toc"><li><a href="illustrations.html">Illustrations</a></li></ul>';
// $toc .= '<mbp:pagebreak />';

file_put_contents( "html/toc.html", $header . $toc . $footer );

$appendix = '<br /><br /><h2>Appendix</h2>';
file_put_contents( "html/appendix.html", $header . $appendix . $footer );

$pics .= '<br /><br /><h3>Illustrations by Gustave Dore</h3>';
$pics .= '<mbp:pagebreak />';

$r = mysqli_query($con, "SELECT * FROM bible_images ORDER BY sort ASC");
while( $o = mysqli_fetch_array( $r, MYSQLI_ASSOC ) ) {
	$pics .= '<div class="woodcuts"><img src="../images/'.$o['file'].'" /></div>';
	$pics .= '<mbp:pagebreak />';
}

$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/appendix.html"></item>'."\n";
$spine .= '<itemref idref="item'.$i.'"/>'."\n";
$i++;
$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/illustrations.html"></item>'."\n";
$spine .= '<itemref idref="item'.$i.'"/>'."\n";

file_put_contents( "html/illustrations.html", $header . $pics . $footer );

mysqli_close($con);

file_put_contents( "log/manifest.log.txt", $manifest );
file_put_contents( "log/spine.log.txt", $spine );
file_put_contents( "log/ncx.log.txt", $ncx );
