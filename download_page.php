<?php
	include('vendor/simplehtmldom/simple_html_dom.php');

	// Get cURL resource
	$curl = curl_init();
	// Set some options - we are passing in a useragent too here
	curl_setopt_array($curl, array(
	    CURLOPT_RETURNTRANSFER => 1,
	    CURLOPT_URL => $_POST['url'],
	    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
	));
	// Send the request & save response to $resp
	$page = curl_exec($curl);
	$html = new simple_html_dom();
	$htmlPage = $html->load($page);
	// Close request to clear up some resources
	curl_close($curl);
?>
<h1>Contenu (mw-content-ltr)</h1>
<?php
	foreach ( $htmlPage->find('div[class^=mw-content-ltr]') as $b ) {
		$b->table = null;
		echo $b->outertext;
	}

?>
