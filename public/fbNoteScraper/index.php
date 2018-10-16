
<?php
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 'On'); 
	require_once '../../vendor/autoload.php';
	use Sunra\PhpSimple\HtmlDomParser;
	define('MAX_FILE_SIZE', 100000000);
	//This section scrapes our notes page and dumps the info here.
	$opts = array(
	  'http'=>array(
		'header'=>"User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36; Content-Type: text/html; charset=iso-8859-1; Accept-Language: en-gb, en;q=0.8"
	  )
	);
	$context = stream_context_create($opts);
	$noteUrl = "https://www.facebook.com/notes/oxford-parkour/upcoming-training/1786670351562542";
	$fbNotePageStr = file_get_contents($noteUrl, false, $context);
	$fbNotePageStrUTF8 = mb_convert_encoding($fbNotePageStr, 'HTML-ENTITIES', "UTF-8");
	$fbNoteHtml = HtmlDomParser::str_get_html($fbNotePageStrUTF8);
	$externalUrls = $fbNoteHtml->find('#content a[href^="https://l.facebook.com/l.php?u="], #content a[href^="http://l.facebook.com/l.php?u="]');
	
	foreach ($externalUrls as $extUrl){
		$origUrl = $extUrl->href;
		$noFbUrl = str_replace("https://l.facebook.com/l.php?u=","",$origUrl);
		$noFbUrl = str_replace("http://l.facebook.com/l.php?u=","",$noFbUrl);
		$noExtraParams = substr($noFbUrl, 0, strrpos($noFbUrl, '&amp;h='));
		$unEscapedUrl = urldecode($noExtraParams);
		$extUrl->href = $unEscapedUrl;
	}
	
	$internalUrls = $fbNoteHtml->find('#content a[href^="/"]');
	foreach ($internalUrls as $internalUrl){
		$origUrl = $internalUrl->href;
		$withfbUrl = "http://www.facebook.com".$origUrl;
		$unEscapedUrl = urldecode($withfbUrl);                                                                    
		$internalUrl->href = $unEscapedUrl;
	}
	
	$noteBody = $fbNoteHtml->find('#content');
	echo $noteBody[0];
?>