<?php
function get_html_header() {
	?>
	<?php ob_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<title>The Holy Bible</title>
			<link rel="stylesheet" type="text/css" href="../style.css">
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
	$intro .= '<h3 class="center">Authorized King James Version<h3>';
	
	file_put_contents( "html/intro.html", $header . $intro . $footer );
}

function build_html_toc( $book, $header, $footer ) {
	$toc = '<h2>Table of Contents</h2>';

	foreach ( $book as $o ) {
		if ( 1 == $o['number'] ) {
			$toc .= '<h3>The Old Testament</h3>';
			$toc .= '<ul class="toc">';
		}
		if ( 40 == $o['number'] ) {
			$toc .= '</ul>';
			$toc .= '<h3>The New Testament</h3>';
			$toc .= '<ul class="toc">';
		}

		$toc .= '<li><a name="toc-'.$o['anchor'].'" href="'.$o['anchor'].'.html">'.$o['fullname'].'</a><span class="toc-shortname">'.str_replace( ' ', '', $o['short'] ).'</span></li>';
	}

	$toc .= '</ul>';
	$toc .= '<h3>Appendix</h3>';
	$toc .= '<ul class="toc"><li><a name="toc-illustrations" href="illustrations.html">Illustrations</a></li></ul>';

	file_put_contents( "html/toc.html", $header . $toc . $footer );
}

function build_html_body( $book, $header, $footer, $con ) {
	foreach ( $book as $o ) {
		$body = '';
		$prev = "&larr;";
		$next = "&rarr;";

		if ( 1 == $o['number'] ) {
			$temp = '<br /><br /><h2>The Old Testament</h2>';
			file_put_contents( "html/old-testament.html", $header . $temp . $footer );
		}
		if ( 40 == $o['number'] ) {
			$temp = '<br /><br /><h2>The New Testament</h2>';
			file_put_contents( "html/new-testament.html", $header . $temp . $footer );
		}

		$body .= '<a class="book-anchor" name="'.$o['anchor'].'"></a>';
		$body .= '<h3>' . $o['title'] . '</h3>';
		$body .= '<ul class="title-nav">';

		if ( isset( $o['prev_chap'] ) )
			$body .= '<li class="left-pos"><a href="'.$o['prev_chap'].'">'.$prev.'</a></li>';
		else
			$body .= '<li class="left-pos"><span>'.$prev.'</span></li>';

		$body .= '<li><a href="toc.html#toc-'.$o['anchor'].'">Table of Contents</a></li>';

		if ( isset( $o['next_chap'] ) )
			$body .= '<li class="right-pos"><a href="'.$o['next_chap'].'">'.$next.'</a></li>';
		else
			$body .= '<li class="right-pos"><span>'.$next.'</span></li>';

		$body .= '</ul>';
		$body .= '<p class="center"><strong>Chapters</strong></p>';

		$ch_nav = '';
		$temp = '';
		for ( $ch = 1; $ch <= $o['chapters']; $ch++ ) {
			$content_link = 'html/'.$o['anchor'].'.html';
			$content_link .= '#'.$o['anchor'].'-ch'.$ch;

			$ch_nav .= '<li><a href="'.$o['anchor'].'.html#'.$o['anchor'].'-ch'.$ch.'">'.$ch.'</a></li>';

			$temp .= '<a class="chapter-anchor" name="'.$o['anchor'].'-ch'.$ch.'"></a>';
			$temp .= '<ul class="title-nav">';

			// Prev Chapter
			$prevch = $ch - 1;
			$nextch = $ch + 1;
			if ( $prevch > 0 )
				$temp .= '<li class="left-pos"><a href="'.$o['anchor'].'.html#'.$o['anchor'].'-ch'.$prevch.'">'.$prev.'</a></li>';
			else {
				// Prev Book
				if ( isset( $o['prev_link'] ) )
					$temp .= '<li class="left-pos"><a href="'.$o['prev_link'].'">'.$prev.'</a></li>';
				else
					$temp .= '<li class="left-pos"><span>'.$prev.'</span></li>';
			}

			// Current Book
			$temp .= '<li><strong><a href="'.$o['anchor'].'.html">' . $o['fullname'] . '</a> ' . $ch . '</strong></li>'; 

			// Next Chapter
			if ( $nextch <= $o['chapters'] )
				$temp .= '<li class="right-pos"><a href="'.$o['anchor'].'.html#'.$o['anchor'].'-ch'.$nextch.'">'.$next.'</a></li>';
			else {
				// Next Book
				if ( isset( $o['next_link'] ) )
					$temp .= '<li class="right-pos"><a href="'.$o['next_link'].'">'.$next.'</a></li>';
				else
					$temp .= '<li class="right-pos"><span>'.$next.'</span></li>';
			}

			$temp .= '</ul>';

			$r2 = mysqli_query($con, "SELECT * FROM bible_kjv WHERE book=".$o['number']." AND chapter=".$ch." ORDER BY verse ASC");
			while( $oo = mysqli_fetch_array( $r2, MYSQLI_ASSOC ) ) {
				$temp .= '<p class="verse"><a class="verse-anchor" name="'.$o['anchor'].'-ch'.$ch.'-v'.$oo['verse'].'"><strong><span class="hide-this">'.str_replace( ' ', '', $o['short'] ).$oo['chapter'].'.</span>'.$oo['verse'].'</strong></a> ' . $oo['text'] . '</p>';
			}
		}
		$ch_nav = '<ul class="ch-nav">'.$ch_nav.'</ul>';
		$body .= $ch_nav;
		$body .= '<mbp:pagebreak />';
		$body .= $temp;

		// $body .= '<mbp:pagebreak />';
		file_put_contents( "html/".$o['anchor'].".html", $header . $body . $footer );
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

		$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/'.$o['anchor'].'.html"></item>'."\n";
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
			<content src="html/<?php echo $o['anchor'].'.html'; ?>"/>
<?php $ncx .= ob_get_contents();
$order++;

		$ncx_chapters = '';
		for ( $ch = 1; $ch <= $o['chapters']; $ch++ ) {
			$content_link = 'html/'.$o['anchor'].'.html';
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
				<content src="html/<?php echo $o['anchor'].'.html'; ?>"/>
<?php $ncx .= ob_get_contents();
$order++;

		$ncx_chapters = '';
		for ( $ch = 1; $ch <= $o['chapters']; $ch++ ) {
			$content_link = 'html/'.$o['anchor'].'.html';
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
