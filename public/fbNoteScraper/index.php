<!DOCTYPE html>

<html>
    <head>
		<style>
			#FBContent ._4lmk._2vxa._5s6c {
				color: rgb(200, 60, 20); 
				text-shadow: 3px 3px 3px #141414; 
				font-family: 'Lobster', cursive; 
				margin-top: 20px; 
				margin-bottom: 10px; 
				font-size: 24px;
			}
			
			#FBContent ._2yud { display: none; }
			
			#FBContent ._5bdz { background-repeat: no-repeat; }
			#FBContent pre {background:rgba(0,0,0,0.5); color:#ddd; border: none; font-style: italic; }
		</style>
    </head>
    
    <body>
		<div id="FBContent">
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
		</div>
    </body>
</html>
