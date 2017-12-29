<?php
// Beging parse code
$delimiter = "\t";

function parsetoc( $key ) {
	// for talim bible
	$table = array(
		'gen' => array( 'name' => "aathiyaagamam", 'id' => 1 ),
		'exod' => array( 'name' => "yaaththiraagamam", 'id' => 2 ),
		'lev' => array( 'name' => "leaviyaraagamam", 'id' => 3 ),
		'num' => array( 'name' => "e'n'naagamam", 'id' => 4 ),
		'deut' => array( 'name' => "ubaagamam", 'id' => 5 ),
		'josh' => array( 'name' => "yoasuvaa", 'id' => 6 ),
		'judg' => array( 'name' => "niyaayaathibathiga'l", 'id' => 7 ),
		'ruth' => array( 'name' => "rooth", 'id' => 8 ),
		'1sam' => array( 'name' => "1 saamuveal", 'id' => 9 ),
		'2sam' => array( 'name' => "2 saamuveal", 'id' => 10 ),
		'1kings' => array( 'name' => "1 iraajaakka'l", 'id' => 11 ),
		'2kings' => array( 'name' => "2 iraajaakka'l", 'id' => 12 ),
		'1chr' => array( 'name' => "1 naa'laagamam", 'id' => 13 ),
		'2chr' => array( 'name' => "2 naa'laagamam", 'id' => 14 ),
		'ezra' => array( 'name' => "es'raa", 'id' => 15 ),
		'neh' => array( 'name' => "negeamiyaa", 'id' => 16 ),
		'esth' => array( 'name' => "esthar", 'id' => 17 ),
		'job' => array( 'name' => "yoabu", 'id' => 18 ),
		'ps' => array( 'name' => "sanggeetham", 'id' => 19 ),
		'prov' => array( 'name' => "neethimozhiga'l", 'id' => 20 ),
		'eccl' => array( 'name' => "pirasanggi", 'id' => 21 ),
		'song' => array( 'name' => "unnathappaattu", 'id' => 22 ),
		'isa' => array( 'name' => "easaayaa", 'id' => 23 ),
		'jer' => array( 'name' => "ereamiyaa", 'id' => 24 ),
		'lam' => array( 'name' => "pulambal", 'id' => 25 ),
		'ezek' => array( 'name' => "eseakkiyeal", 'id' => 26 ),
		'dan' => array( 'name' => "thaaniyeal", 'id' => 27 ),
		'hos' => array( 'name' => "oasiyaa", 'id' => 28 ),
		'joel' => array( 'name' => "yoaveal", 'id' => 29 ),
		'amos' => array( 'name' => "aamoas", 'id' => 30 ),
		'obad' => array( 'name' => "obathiyaa", 'id' => 31 ),
		'jonah' => array( 'name' => "yoanaa", 'id' => 32 ),
		'mic' => array( 'name' => "meegaa", 'id' => 33 ),
		'nah' => array( 'name' => "naagoom", 'id' => 34 ),
		'hab' => array( 'name' => "aabakook", 'id' => 35 ),
		'zeph' => array( 'name' => "seppaniyaa", 'id' => 36 ),
		'hag' => array( 'name' => "aagaay", 'id' => 37 ),
		'zech' => array( 'name' => "sagariyaa", 'id' => 38 ),
		'mal' => array( 'name' => "malkiyaa", 'id' => 39 ),
		'matt' => array( 'name' => "maththeayu", 'id' => 40 ),
		'mark' => array( 'name' => "maarku", 'id' => 41 ),
		'luke' => array( 'name' => "lookkaa", 'id' => 42 ),
		'john' => array( 'name' => "yoavaan", 'id' => 43 ),
		'acts' => array( 'name' => "appoasthalarudaiya nadapadiga'l", 'id' => 44 ),
		'rom' => array( 'name' => "roamar", 'id' => 45 ),
		'1cor' => array( 'name' => "1 korinthiyar", 'id' => 46 ),
		'2cor' => array( 'name' => "2 korinthiyar", 'id' => 47 ),
		'gal' => array( 'name' => "kalaaththiyar", 'id' => 48 ),
		'eph' => array( 'name' => "ebeasiyar", 'id' => 49 ),
		'phil' => array( 'name' => "pilippiyar", 'id' => 50 ),
		'col' => array( 'name' => "koloaseyar", 'id' => 51 ),
		'1thess' => array( 'name' => "1 thesaloanikkeayar", 'id' => 52 ),
		'2thess' => array( 'name' => "2 thesaloanikkeayar", 'id' => 53 ),
		'1tim' => array( 'name' => "1 theemoaththeayu", 'id' => 54 ),
		'2tim' => array( 'name' => "2 theemoaththeayu", 'id' => 55 ),
		'titus' => array( 'name' => "theeththu", 'id' => 56 ),
		'phlm' => array( 'name' => "pileamoan", 'id' => 57 ),
		'heb' => array( 'name' => "ebireyar", 'id' => 58 ),
		'jas' => array( 'name' => "yaakkoabu", 'id' => 59 ),
		'1pet' => array( 'name' => "1 peathuru", 'id' => 60 ),
		'2pet' => array( 'name' => "2 peathuru", 'id' => 61 ),
		'1john' => array( 'name' => "1 yoavaan", 'id' => 62 ),
		'2john' => array( 'name' => "2 yoavaan", 'id' => 63 ),
		'3john' => array( 'name' => "3 yoavaan", 'id' => 64 ),
		'jude' => array( 'name' => "yoothaa", 'id' => 65 ),
		'rev' => array( 'name' => "ve'lippaduththina viseasham", 'id' => 66 ),
	);

	if ( array_key_exists( $key, $table ) ) {
		return $table[$key]['id'];
	}
	else {
		// should not end here.
		// pr($key);
	}

	return false;
	
}

function parse( $data ) {
	global $con;
	global $bible_text;

	if ( ! isset( $data[0] ) || ! isset( $data[1] ) || ! isset( $data[2] ) || ! isset( $data[3] ) || ! isset( $data[4] ) ) {
		return;
	}

	if ( empty( $data[0] ) || empty( $data[1] ) || empty( $data[2] ) || empty( $data[3] ) || empty( $data[4] ) ) {
		return;
	}
	$key = str_replace( ' ', '', strtolower($data[0]) );

	if ( $book = parsetoc( $key ) ) {
		// pr($id);
		$chapter = intval($data[1]);
		$verse = intval($data[3]);
		$text = trim( $data[4] );

		$sql = 'UPDATE '.$bible_text.' SET tamil="'.mysqli_real_escape_string($con, $text).'" WHERE book="'.$book.'" AND chapter="'.$chapter.'" AND verse="'.$verse.'"';
		if ($con->query($sql) === TRUE) {
			// echo "Record updated successfully";
		} else {
			echo "Error updating record: " . $conn->error;
		}
	}
	else {
		pr('error');
	}

}

$fp = fopen('translations/tamil/tamil-romanised-bible.txt', 'r');

while ( !feof($fp) ) {
    $line = fgets($fp, 2048);

    $data = str_getcsv($line, $delimiter);

    parse($data);
}                              

fclose($fp);
