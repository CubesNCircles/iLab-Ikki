<?php
	include('vendor/simplehtmldom/simple_html_dom.php');

	// $html = file_get_html( 'http://fr.wikipedia.org/wiki/Pont_de_la_Liberation' );
	$html = file_get_html( 'http://fr.wikipedia.org/wiki/Pont_de_la_Liberation' );
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Wiki</title>
</head>
<body>
	<h1>Contenu (mw-content-ltr)</h1>
	<?php
		foreach ( $html->find('div[class^=mw-content-ltr]') as $b ) {
			$b->table = null;
			echo $b->outertext;
		}
	?>
</body>
</html>
