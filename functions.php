<?php
function get_html_header() {
	?>
	<?php ob_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<title>The Holy Bible</title>
			<link rel="stylesheet" type="text/css" href="../style.css" />
		</head>
		<body>
	<?php $header = ob_get_contents(); ?>
	<?php
	return $header;
}

function get_html_footer() {
	$footer = '</body></html>';

	return $footer;
}

function build_html_intro( $book, $header, $footer ) {
	$intro  = '<br />';
	$intro .= '<br />';
	$intro .= '<h1>The Holy Bible</h1>';
	$intro .= '<br />';
	$intro .= '<br />';
	$intro .= '<h3 class="center">Authorized King James Version</h3>';
	
	file_put_contents( "html/intro.html", $header . $intro . $footer );
}

function build_html_toc( $book, $header, $footer ) {
	$toc = '';

	$toc .= '<div id="toc">';
	$toc .= '<h2>Table of Contents</h2>';

	foreach ( $book as $o ) {
		if ( 1 == $o['number'] ) {
			$toc .= '<h3>The Old Testament</h3>'."\n";
			$toc .= '<ul>';
		}
		if ( 40 == $o['number'] ) {
			$toc .= '</ul>'."\n";
			$toc .= '<h3>The New Testament</h3>'."\n";
			$toc .= '<ul>';
		}

		$toc .= '<li><span class="book-link"><a id="book-'.$o['anchor'].'" href="'.$o['filename'].'-intro.html">'.$o['fullname'].'</a></span><span class="toc-shortname">&nbsp;&nbsp;'.str_replace( ' ', '', $o['short'] ).'</span></li>';
	}

	$toc .= '</ul>'."\n";
	$toc .= '<h3>Appendix</h3>'."\n";
	$toc .= '<ul><li><a id="toc-illustrations" href="illustrations.html">Illustrations</a></li></ul>'."\n";

	$toc .= '</div>';

	file_put_contents( "html/toc.html", $header . $toc . $footer );
}

function build_html_body( $book, $header, $footer, $con ) {
	foreach ( $book as $o ) {
		$body = '';
		$intro = '';
		$prev = "&larr;";
		$next = "&rarr;";
		$space = "&nbsp;&nbsp;&nbsp;&nbsp;";
		$blank = "&nbsp;";

		if ( 1 == $o['number'] ) {
			$temp = '<br /><br /><h2>The Old Testament</h2>'."\n";
			file_put_contents( "html/old-testament.html", $header . $temp . $footer );
		}
		if ( 40 == $o['number'] ) {
			$temp = '<br /><br /><h2>The New Testament</h2>'."\n";
			file_put_contents( "html/new-testament.html", $header . $temp . $footer );
		}

		$intro .= '<div>';
		$intro .= '<h3 id="'.$o['anchor'].'">' . $o['title'] . '</h3>'."\n";

		$ch_nav = '';
		$temp = '';
		for ( $ch = 1; $ch <= $o['chapters']; $ch++ ) {
			$r2 = mysqli_query($con, "SELECT * FROM bible_kjv WHERE book=".$o['number']." AND chapter=".$ch." ORDER BY verse ASC");

			$temp .= '<div id="'.$o['anchor'].'-ch'.$ch.'"><div>';

			$content_link = 'html/'.$o['filename'].'.html';
			$content_link .= '#'.$o['anchor'].'-ch'.$ch;

			$ch_nav .= '<li>'.$blank.$blank.'<a href="'.$o['filename'].'.html#'.$o['anchor'].'-ch'.$ch.'">'.$ch.'</a>'.$blank.$blank.'</li>';

			$temp .= '<h4 href="'.$o['filename'].'-intro.html" class="title-nav chapter-nav chapter-'.$ch.'">';

			$prevch = $ch - 1;
			$nextch = $ch + 1;

			/* // Prev Chapter
			if ( $prevch > 0 )
				$temp .= '<span class="left-pos"><a href="'.$o['filename'].'.html#'.$o['anchor'].'-ch'.$prevch.'">'.$prev.'</a>'.$space.'</span>';
			else {
				// Prev Book
				if ( isset( $o['prev_link'] ) )
					$temp .= '<span class="left-pos"><a href="'.$o['prev_link'].'">'.$prev.'</a>'.$space.'</span>';
				else
					$temp .= '<span class="left-pos"><span class="no-link">'.$prev.'</span>'.$space.'</span>';
			} */

			// Current Book
			$temp .= '<span class="tn-heading"><a href="'.$o['filename'].'-intro.html">' . $o['fullname'] . '</a> ' . $ch . '</span>'; 

			/* // Next Chapter
			if ( $nextch <= $o['chapters'] )
				$temp .= '<span class="right-pos">'.$space.'<a href="'.$o['filename'].'.html#'.$o['anchor'].'-ch'.$nextch.'">'.$next.'</a></span>';
			else {
				// Next Book
				if ( isset( $o['next_link'] ) )
					$temp .= '<span class="right-pos">'.$space.'<a href="'.$o['next_link'].'">'.$next.'</a></span>';
				else
					$temp .= '<span class="right-pos">'.$space.'<span class="no-link">'.$next.'</span></span>';
			} */

			$temp .= '</h4>'."\n";

			$temp .= '<ul class="verse-nav">'."\n";
			for ( $i = 1; $i <= $r2->num_rows; $i++ ) {
				$temp .= '<li class="verse-link">'.$space.'<a href="'.$o['filename'].'.html#'.$o['anchor'].'-ch'.$ch.'-v'.$i.'">'.$i.'</a>'.$space.'</li>';
			}
			$temp .= '</ul>'."\n";

			while( $oo = mysqli_fetch_array( $r2, MYSQLI_ASSOC ) ) {
				$temp .= '<p id="'.$o['anchor'].'-ch'.$ch.'-v'.$oo['verse'].'" class="verse">';
					$temp .= '<a class="verse-anchor" href="'.$o['filename'].'.html#'.$o['anchor'].'-ch'.$ch.'">';
							$temp .= '<strong><span class="hide-this">'.str_replace( ' ', '', $o['short'] ) . $oo['chapter'] . '.</span>'.$oo['verse'].'</strong>';
					$temp .= '</a>';
					$temp .= $oo['text'];
				$temp .= '</p>'."\n";
			}

			$temp .= '</div>';
		}

		$intro .= '<h4 class="center hide-this">Navigation</h4>'."\n";
		$intro .= '<p class="title-nav hide-this">';
		// Prev Book
		if ( isset( $o['prev_book'] ) )
			$intro .= '<span class="left-book"><a href="'.$o['prev_book'].'">'. "&laquo;". ' ' . $o['prev_book_title'] . '</a></span><br />';
		// Next Book
		if ( isset( $o['next_book'] ) )
			$intro .= '<span class="right-book"><a href="'.$o['next_book'].'">'.$o['next_book_title'] . ' ' . "&raquo;".'</a></span>';
		$intro .= '</p>';
		$intro .= '<p class="title-nav">';

		// if ( isset( $o['prev_chap'] ) )
			// $intro .= '<span class="left-pos"><a href="'.$o['prev_chap'].'">'.$prev.'</a>'.$space.'</span>';
		// else
			// $intro .= '<span class="left-pos"><span class="no-link">'.$prev.'</span>'.$space.'</span>';

		$intro .= '<span class="tn-heading"><a href="toc.html#book-'.$o['anchor'].'">Table of Contents</a></span>';

		// if ( isset( $o['next_chap'] ) )
			// $intro .= '<span class="right-pos">'.$space.'<a href="'.$o['next_chap'].'">'.$next.'</a></span>';
		// else
			// $intro .= '<span class="right-pos">'.$space.'<span class="no-link">'.$next.'</span></span>';

		$intro .= '</p>'."\n";
		$intro .= '<h4 class="center">Chapters</h4>'."\n";
		$ch_nav = '<ul class="ch-nav">'.$ch_nav.'</ul>';
		$intro .= $ch_nav;

		$intro .= '</div>';
		file_put_contents( "html/".$o['filename']."-intro.html", $header . $intro . $footer );

		$body .= $temp;

		file_put_contents( "html/".$o['filename'].".html", $header . $body . $footer );
	}
}

function build_spine_manifest( $book ) {
	$spine = '';
	$manifest = '';
	$i = 1;

	$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/intro.html"></item>'."\n";
	$spine .= '<itemref idref="item'.$i.'"/>'."\n";
	$i++;
	$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/toc.html"></item>'."\n";
	$spine .= '<itemref idref="item'.$i.'"/>'."\n";
	$i++;

	foreach ( $book as $o ) {
		if ( 1 == $o['number'] ) {
			$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/old-testament.html"></item>'."\n";
			$spine .= '<itemref idref="item'.$i.'"/>'."\n";
			$i++;
		}
		if ( 40 == $o['number'] ) {
			$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/new-testament.html"></item>'."\n";
			$spine .= '<itemref idref="item'.$i.'"/>'."\n";
			$i++;
		}

		$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/'.$o['filename'].'-intro.html"></item>'."\n";
		$spine .= '<itemref idref="item'.$i.'"/>'."\n";
		$i++;

		$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/'.$o['filename'].'.html"></item>'."\n";
		$spine .= '<itemref idref="item'.$i.'"/>'."\n";
		$i++;
	}

	$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/appendix.html"></item>'."\n";
	$spine .= '<itemref idref="item'.$i.'"/>'."\n";
	$i++;

	$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/illustrations.html"></item>'."\n";
	$spine .= '<itemref idref="item'.$i.'"/>'."\n";

	file_put_contents( "log/manifest.log.txt", $manifest );
	file_put_contents( "log/spine.log.txt", $spine );
}

function build_html_appendix( $book, $header, $footer, $con ) {
	$appendix = '<br /><br /><h2>Appendix</h2>';
	file_put_contents( "html/appendix.html", $header . $appendix . $footer );

	$pics  = '<br /><br /><h3>Illustrations by Gustave Dore</h3>';
	$pics .= '<p class="center"><a href="toc.html"><small>(Back to Table of Contents)</small></a></p>';
	$pics .= '<mbp:pagebreak />';

	$r = mysqli_query($con, "SELECT * FROM bible_images ORDER BY sort ASC");
	while( $o = mysqli_fetch_array( $r, MYSQLI_ASSOC ) ) {
		$pics .= '<div class="woodcuts"><img src="../images/'.$o['file'].'" /></div>';
		$pics .= '<mbp:pagebreak />';
	}

	file_put_contents( "html/illustrations.html", $header . $pics . $footer );
}

function build_ncx( $book ) {
	$order = 2; //toc is first
	$ncx = '';

	foreach ( $book as $o ) {
		if ( 1 == $o['number'] ) {
ob_start(); ?>
		<navPoint class="testament" id="old-testament" playOrder="<?php echo $order; ?>">
			<navLabel>
				<text>The Old Testament</text>
			</navLabel>
			<content src="html/old-testament.html"/>
		</navPoint>
<?php $ncx .= ob_get_contents();
			$order++;
		}
		if ( 40 == $o['number'] ) {
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
		}

ob_start(); ?>
		<navPoint class="book" id="<?php echo $o['anchor'].'-ch1'; ?>" playOrder="<?php echo $order; ?>">
			<navLabel>
				<text><?php echo $o['fullname']; ?></text>
			</navLabel>
			<content src="html/<?php echo $o['filename'].'-intro.html'; ?>"/>
<?php $ncx .= ob_get_contents();
$order++;

		$ncx_chapters = '';
		for ( $ch = 1; $ch <= $o['chapters']; $ch++ ) {
			$content_link = 'html/'.$o['filename'].'.html';
			$content_link .= '#'.$o['anchor'].'-ch'.$ch;

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

	file_put_contents( "log/ncx.log.txt", $ncx );
}
function build_ncx2( $book ) {
	$order = 2; //toc is first
	$ncx = '';

	foreach ( $book as $o ) {
		if ( 1 == $o['number'] ) {
ob_start(); ?>
		<navPoint class="testament" id="old-testament" playOrder="<?php echo $order; ?>">
			<navLabel>
				<text>The Old Testament</text>
			</navLabel>
			<content src="html/old-testament.html"/>
<?php $ncx .= ob_get_contents();
			$order++;
		}
		if ( 40 == $o['number'] ) {
ob_start(); ?>
		</navPoint>
		<navPoint class="testament" id="new-testament" playOrder="<?php echo $order; ?>">
			<navLabel>
				<text>The New Testament</text>
			</navLabel>
			<content src="html/new-testament.html"/>
<?php $ncx .= ob_get_contents();
			$order++;
		}

ob_start(); ?>
			<navPoint class="book" id="<?php echo $o['anchor']; ?>" playOrder="<?php echo $order; ?>">
				<navLabel>
					<text><?php echo $o['fullname']; ?></text>
				</navLabel>
				<content src="html/<?php echo $o['filename'].'-intro.html'; ?>"/>
<?php $ncx .= ob_get_contents();
$order++;

		$ncx_chapters = '';
		for ( $ch = 1; $ch <= $o['chapters']; $ch++ ) {
			$content_link = 'html/'.$o['filename'].'.html';
			$content_link .= '#'.$o['anchor'].'-ch'.$ch;

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
ob_start(); ?>
<?php echo $ncx_chapters; ?>
			</navPoint>
<?php $ncx .= ob_get_contents();

	}

ob_start(); ?>
		</navPoint>
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

	file_put_contents( "log/ncx.log.txt", $ncx );
}
