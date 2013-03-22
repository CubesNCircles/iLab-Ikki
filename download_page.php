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
	echo "<h1>" . $htmlPage->find('td[class="entete"]', 0)->plaintext.'<hr>' . "</h1>";

	foreach ( $htmlPage->find('div[class^=mw-content-ltr]') as $b )
	{
		echo $b->outertext;
	}
?>
<script>
	$('a').on('click', function(e) {
		e.preventDefault();
	});
	// edit links
	$('span.editsection').hide();
	// toc table
	$('table.toc').hide();

	// references & all arround
	$('.references-small').prev().hide();
	$('.references-small').next().hide();
	$('.references-small').next().next().hide();
	$('.references-small').hide();

	$('.references').prev().hide()
	$('.references').hide()
	// $('.references').next().hide()
	// $('.references').next().next().hide()

	//misc stuff
	$('table.noprint').hide();

	// bottom: more useless links
	$('ul.bandeau-portail').prev().prev().hide();
	$('ul.bandeau-portail').prev().hide();
	$('ul.bandeau-portail').hide();
</script>
