<?php
// King James Version
// $bible_book = 'bible_books_en';
// $bible_text = 'bible_kjv';
// $bible_images = 'bible_images';

// Authorized King James Version
// $bible_book = 'cpe_book';
// $bible_text = 'cpe_bible';
// $bible_images = 'bible_images';

// Authorized King James Version + Translations
$bible_book = 'cpe_books';
$bible_text = 'cpe_bibles';
$bible_images = 'bible_images';

function get_translation( $key ) {
	$translation = array();

	switch ( $key ) {
		case 'tamil' :
			$translation['title'] = 'The Holy Bible';
			$translation['table_of_contents'] = 'Table of Contents';
			$translation['description'] = 'Tamil Romanised Bible';
			$translation['new_testament'] = "puthiya ea'rpaadu";
			$translation['old_testament'] = "pazhaiya ea'rpaadu";
			$translation['navigation'] = 'Navigation';
			$translation['chapters'] = 'Chapters';
			$translation['path'] = 'translations/tamil/';
			$translation['text_column'] = 'tamil';
			$translation['column_prefix'] = 'tamil_';
			$translation['stylesheet'] = '../../../style.css';
			break;
		default :
			$translation['title'] = 'The Holy Bible';
			$translation['table_of_contents'] = 'Table of Contents';
			$translation['description'] = 'Authorized King James Version';
			$translation['new_testament'] = 'The New Testament';
			$translation['old_testament'] = 'The Old Testament';
			$translation['navigation'] = 'Navigation';
			$translation['chapters'] = 'Chapters';
			$translation['path'] = '';
			$translation['text_column'] = 'text';
			$translation['column_prefix'] = '';
			$translation['stylesheet'] = '../style.css';
	}

	return $translation;

}
function get_html_header() {
	global $translation;

	?>
	<?php ob_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta charset="UTF-8">
			<title><?php echo $translation['title']; ?></title>
			<link rel="stylesheet" type="text/css" href="<?php echo $translation['stylesheet']; ?>" />
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
	global $translation;

	$intro  = '<br />';
	$intro .= '<br />';
	$intro .= '<h1>'.$translation['title'].'</h1>';
	$intro .= '<br />';
	$intro .= '<br />';
	$intro .= '<h3 class="center">'.$translation['description'].'</h3>';
	
	file_put_contents( $translation['path'] . "html/intro.html", $header . $intro . $footer );
}

function build_html_toc( $book, $header, $footer ) {
	global $translation;

	$toc = '';

	$toc .= '<div id="toc">';
	$toc .= '<h2>'.$translation['table_of_contents'].'</h2>';

	$toc .= '<ul>';
	$toc .= '<li><a href="toc.html#the-old-testament">'.$translation['old_testament'].'</a></li>';
	$toc .= '<li><a href="toc.html#the-new-testament">'.$translation['new_testament'].'</a></li>';
	$toc .= '</ul>';

	foreach ( $book as $o ) {
		if ( 1 == $o['number'] ) {
			$toc .= '<h3 id="the-old-testament">'.$translation['old_testament'].'</h3>'."\n";
			$toc .= '<ul>';
		}
		if ( 40 == $o['number'] ) {
			$toc .= '</ul>'."\n";
			$toc .= '<h3 id="the-new-testament">'.$translation['new_testament'].'</h3>'."\n";
			$toc .= '<ul>';
		}

		$toc .= '<li><span class="book-link"><a id="book-'.$o['anchor'].'" href="'.$o['filename'].'-intro.html">'.$o[ $translation['column_prefix'] . 'fullname'].'</a></span><span class="toc-shortname">&nbsp;&nbsp;'.str_replace( ' ', '', $o[ $translation['column_prefix'] . 'short'] ).'</span></li>';
	}

	$toc .= '</ul>'."\n";
	// $toc .= '<h3>Appendix</h3>'."\n";
	// $toc .= '<ul><li><a id="toc-illustrations" href="illustrations.html">Illustrations</a></li></ul>'."\n";

	$toc .= '</div>';

	file_put_contents( $translation['path'] . "html/toc.html", $header . $toc . $footer );
}

function build_html_body( $book, $header, $footer, $con ) {
	global $bible_text;
	global $translation;

	foreach ( $book as $o ) {
		$body = '';
		$intro = '';
		$prev = htmlentities("<<");
		$next = htmlentities(">>");
		$space = "&nbsp;&nbsp;&nbsp;&nbsp;";
		$blank = "&nbsp;";

		if ( 1 == $o['number'] ) {
			$temp = '<br /><br /><h2>'.$translation['old_testament'].'</h2>'."\n";
			file_put_contents( $translation['path'] . "html/old-testament.html", $header . $temp . $footer );
		}
		if ( 40 == $o['number'] ) {
			$temp = '<br /><br /><h2>'.$translation['new_testament'].'</h2>'."\n";
			file_put_contents( $translation['path'] . "html/new-testament.html", $header . $temp . $footer );
		}

		$intro .= '<div>';
		$intro .= '<h3 id="'.$o['anchor'].'">' . $o[ $translation['column_prefix'] . 'title'] . '</h3>'."\n";

		$ch_nav = '';
		$temp = '';
		for ( $ch = 1; $ch <= $o['chapters']; $ch++ ) {
			$r2 = mysqli_query($con, "SELECT * FROM $bible_text WHERE book=".$o['number']." AND chapter=".$ch." ORDER BY verse ASC");

			$temp .= '<div id="'.$o['anchor'].'-ch'.$ch.'">';

			$content_link = 'html/'.$o['filename'].'.html';
			$content_link .= '#'.$o['anchor'].'-ch'.$ch;

			$ch_nav .= '<li>'.$blank.$blank.'<a href="'.$o['filename'].'.html#'.$o['anchor'].'-ch'.$ch.'">'.$ch.'</a>'.$blank.$blank.'</li>';

			$temp .= '<h4 href="'.$o['filename'].'-intro.html" class="title-nav chapter-nav chapter-'.$ch.'">';

			$prevch = $ch - 1;
			$nextch = $ch + 1;

			// Prev Chapter
			if ( $prevch > 0 )
				$temp .= '<span class="left-pos"><a href="'.$o['filename'].'.html#'.$o['anchor'].'-ch'.$prevch.'">'.$prev.'</a>'.$space.'</span>';
			else {
				// Prev Book
				if ( isset( $o['prev_link'] ) )
					$temp .= '<span class="left-pos"><a href="'.$o['prev_link'].'">'.$prev.'</a>'.$space.'</span>';
				else
					$temp .= '<span class="left-pos"><span class="no-link">'.$prev.'</span>'.$space.'</span>';
			}

			// Current Book
			$temp .= '<span class="tn-heading"><a href="'.$o['filename'].'-intro.html">' . $o[ $translation['column_prefix'] . 'fullname'] . '</a>' . $blank . $ch . '</span>'; 

			// Next Chapter
			if ( $nextch <= $o['chapters'] )
				$temp .= '<span class="right-pos">'.$space.'<a href="'.$o['filename'].'.html#'.$o['anchor'].'-ch'.$nextch.'">'.$next.'</a></span>';
			else {
				// Next Book
				if ( isset( $o['next_link'] ) )
					$temp .= '<span class="right-pos">'.$space.'<a href="'.$o['next_link'].'">'.$next.'</a></span>';
				else
					$temp .= '<span class="right-pos">'.$space.'<span class="no-link">'.$next.'</span></span>';
			}

			$temp .= '</h4>'."\n";

			$temp .= '<ul class="verse-nav">'."\n";
			for ( $i = 1; $i <= $r2->num_rows; $i++ ) {
				$temp .= '<li class="verse-link">'.$blank.$blank.'<a href="'.$o['filename'].'.html#'.$o['anchor'].'-ch'.$ch.'-v'.$i.'">'.$i.'</a>'.$blank.$blank.' </li>';
			}
			$temp .= '</ul>'."\n";


			while( $oo = mysqli_fetch_array( $r2, MYSQLI_ASSOC ) ) {
				$temp .= get_subheading( $oo[ $translation['text_column'] ] );
				$temp .= '<p id="'.$o['anchor'].'-ch'.$ch.'-v'.$oo['verse'].'" class="verse">';
					$temp .= '<a class="verse-anchor" href="'.$o['filename'].'.html#'.$o['anchor'].'-ch'.$ch.'">';
							$temp .= '<strong><span class="hide-this">'.str_replace( ' ', '', $o[ $translation['column_prefix'] . 'short'] ) . $oo['chapter'] . '.</span>'.$oo['verse'].'</strong>';
					$temp .= '</a>';
					$temp .= ' ' . format( $oo[ $translation['text_column'] ] );
				$temp .= '</p>'."\n";
				$temp .= get_footer( $oo[ $translation['text_column'] ] );
			}

			$temp .= '</div>';
		}

		$intro .= '<h4 class="center hide-this">'.$translation['navigation'].'</h4>'."\n";
		$intro .= '<p class="title-nav hide-this">';
		// Prev Book
		if ( isset( $o['prev_book'] ) )
			$intro .= '<span class="left-book"><a href="'.$o['prev_book'].'">'. $prev. ' ' . $o['prev_book_title'] . '</a></span><br />';
		// Next Book
		if ( isset( $o['next_book'] ) )
			$intro .= '<span class="right-book"><a href="'.$o['next_book'].'">'.$o['next_book_title'] . ' ' . $next.'</a></span>';
		$intro .= '</p>';
		$intro .= '<p class="title-nav">';

		if ( isset( $o['prev_chap'] ) )
			$intro .= '<span class="left-pos"><a href="'.$o['prev_chap'].'">'.$prev.'</a>'.$space.'</span>';
		else
			$intro .= '<span class="left-pos"><span class="no-link">'.$prev.'</span>'.$space.'</span>';

		$intro .= '<span class="tn-heading"><a href="toc.html">'.$translation['table_of_contents'].'</a></span>';
		// $intro .= '<span class="tn-heading"><a href="toc.html#book-'.$o['anchor'].'">Table of Contents</a></span>';

		if ( isset( $o['next_chap'] ) )
			$intro .= '<span class="right-pos">'.$space.'<a href="'.$o['next_chap'].'">'.$next.'</a></span>';
		else
			$intro .= '<span class="right-pos">'.$space.'<span class="no-link">'.$next.'</span></span>';

		$intro .= '</p>'."\n";
		$intro .= '<h4 class="center">'.$translation['chapters'].'</h4>'."\n";
		$ch_nav = '<ul class="ch-nav">'.$ch_nav.'</ul>';
		$intro .= $ch_nav;

		$intro .= '</div>';
		file_put_contents( $translation['path'] . "html/".$o['filename']."-intro.html", $header . $intro . $footer );

		$body .= $temp;

		file_put_contents( $translation['path'] . "html/".$o['filename'].".html", $header . $body . $footer );
	}
}

function build_spine_manifest( $book ) {
	global $translation;
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

	/* $manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/appendix.html"></item>'."\n";
	$spine .= '<itemref idref="item'.$i.'"/>'."\n";
	$i++;

	$manifest .= '<item id="item'.$i.'" media-type="application/xhtml+xml" href="html/illustrations.html"></item>'."\n";
	$spine .= '<itemref idref="item'.$i.'"/>'."\n"; */

	file_put_contents( $translation['path'] . "log/manifest.log.txt", $manifest );
	file_put_contents( $translation['path'] . "log/spine.log.txt", $spine );
}

function build_html_appendix( $book, $header, $footer, $con ) {
	global $bible_images;
	global $translation;

	$appendix = '<br /><br /><h2>Appendix</h2>';
	file_put_contents( $translation['path'] . "html/appendix.html", $header . $appendix . $footer );

	$pics  = '<br /><br /><h3>Illustrations by Gustave Dore</h3>';
	$pics .= '<p class="center"><a href="toc.html"><small>(Back to Table of Contents)</small></a></p>';
	$pics .= '<mbp:pagebreak />';

	$r = mysqli_query($con, "SELECT * FROM $bible_images ORDER BY sort ASC");
	while( $o = mysqli_fetch_array( $r, MYSQLI_ASSOC ) ) {
		$pics .= '<div class="woodcuts"><img src="../images/'.$o['file'].'" /></div>';
		$pics .= '<mbp:pagebreak />';
	}

	file_put_contents( $translation['path'] . "html/illustrations.html", $header . $pics . $footer );
}

function build_ncx( $book ) {
	global $translation;
	$order = 2; //toc is first
	$ncx = '';

	foreach ( $book as $o ) {
		if ( 1 == $o['number'] ) {
ob_start(); ?>
		<navPoint class="testament" id="old-testament" playOrder="<?php echo $order; ?>">
			<navLabel>
				<text><?php echo $translation['old_testament']; ?></text>
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
				<text><?php echo $translation['new_testament']; ?></text>
			</navLabel>
			<content src="html/new-testament.html"/>
		</navPoint>
<?php $ncx .= ob_get_contents();
			$order++;
		}

ob_start(); ?>
		<navPoint class="book" id="<?php echo $o['anchor'].'-ch1'; ?>" playOrder="<?php echo $order; ?>">
			<navLabel>
				<text><?php echo $o[ $translation['column_prefix'] . 'fullname']; ?></text>
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
					<text><?php echo $o[ $translation['column_prefix'] . 'fullname'].' '.$ch; ?></text>
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
/*
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
 */

	file_put_contents( $translation['path'] . "log/ncx.log.txt", $ncx );
}
function build_ncx2( $book ) {
	global $translation;
	$order = 2; //toc is first
	$ncx = '';

	foreach ( $book as $o ) {
		if ( 1 == $o['number'] ) {
ob_start(); ?>
		<navPoint class="testament" id="old-testament" playOrder="<?php echo $order; ?>">
			<navLabel>
				<text><?php echo $translation['old_testament']; ?></text>
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
				<text><?php echo $translation['new_testament']; ?></text>
			</navLabel>
			<content src="html/new-testament.html"/>
<?php $ncx .= ob_get_contents();
			$order++;
		}

ob_start(); ?>
			<navPoint class="book" id="<?php echo $o['anchor']; ?>" playOrder="<?php echo $order; ?>">
				<navLabel>
					<text><?php echo $o[ $translation['column_prefix'] . 'fullname']; ?></text>
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
						<text><?php echo $o[ $translation['column_prefix'] . 'fullname'].' '.$ch; ?></text>
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
<?php /*
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
 */ ?>
<?php $ncx .= ob_get_contents();

	file_put_contents( $translation['path'] . "log/ncx.log.txt", $ncx );
}

function format( $text ) {
	$text = preg_replace( "#<<(.+?)>>#", "", $text );
	$text = str_replace( "[", "<i>", $text );
	$text = str_replace( "]", "</i>", $text );

	$text = trim( $text );

	return $text;
}
function get_subheading( $text ) {
	$heading = '';

	$text = trim( $text );

	preg_match( "#^<<(.+?)>>#", $text, $matches );

	if ( ! empty( $matches ) && is_array( $matches ) ) {
		if ( isset( $matches[1] ) ) {
			$heading = '<h5>' . format( $matches[1] ) . '</h5>';
		}
	}

	return $heading;
}
function get_footer( $text ) {
	$footer = '';

	$text = trim( $text );

	preg_match( "#<<(.+?)>>$#", $text, $matches );

	if ( ! empty( $matches ) && is_array( $matches ) ) {
		if ( isset( $matches[1] ) ) {
			$footer = $matches[1];
			$footer = preg_replace( "#<<(.+?)>>#", "", $footer );
			$footer = str_replace( "[", "", $footer );
			$footer = str_replace( "]", "", $footer );
			$footer = '<p><b>' . $footer . '</b></p>';
		}
	}

	return $footer;
}
