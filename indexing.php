<?php

require_once "app/init.php";
require_once "./simple_html_dom/simple_html_dom.php";





class Scrap {    
	public $scrapUrl = '';

	public $urlParts = '';

	public $scrappedUrls = [];

	public $maximumSize = 0;

	public function __construct($url){
		$this->scrapUrl = $url;
		$this->urlParts = explode('.', $this->scrapUrl);
		$this->urlParts[] = 'www';
	}

	public function runScrapper(){
	    $this->scrapping($this->scrapUrl);
	    // $this->scrapping('https://www.w3schools.com/html/default.asp');
	}

	public function validate($url){
		$handle = curl_init($url);
		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		if($httpCode == 404 ) {
			return false;
		}else{
			return true;
		}
		curl_close($handle);
	} 
 
	public function scrapping($url){		
		if($this->maximumSize == 100) return;
		$this->maximumSize++;

		if(!$this->validate($url)) return;

	    $scraper = @file_get_html($url);
	    if($scraper === false) return;
	    $a = $scraper->find('a');

	    $title = $scraper->find('title')[0]->innertext;

	    $body = strip_tags($scraper->find('body')[0]);
	    $body = str_replace(' ', '', $body);		
	    $body = preg_replace('/\s/', '', $body);
	    $body = preg_replace('/\s+/', ' ', $body);
	    $body = trim(preg_replace('/\s+/', ' ', $body));
	    // var_dump($title);
	    // exit;
	   	$indexed = $GLOBALS['es']->index([
			'index' => 'pages',
			'type' => 'page',
			'body' => [
				'title' => $title,
				'url' => $url,
				'text' => $body,
			]
		]);

		// if($indexed){
		// 	// print_r($indexed);
		// } 
	    
	    foreach ($a as $key => $value) {
	    	if($value->href == 'javascript:void(0)' || $value->href == 'javascript:void(0);'){
	    		continue;
	    	}

	    	$withBase = false;

	    	foreach($this->urlParts as $part){
	    		// if(strpos($value->href, "." . $part) !== false ||
	    		//  strpos($value->href, $part . ".") !== false || 
	    		//  strpos($value->href, 'http:') !== false || 
	    		//  strpos($value->href, 'https:') !== false){
	    		if(strpos($value->href, $part) !== false){ 	
	    			$withBase = true;
	    		}
	    	}
	    		
	    	if (!$withBase) {
				$currentUrl = $this->scrapUrl;
		   		$currentUrl .= ($value->href{0} == '/') ? '' : '/'; 
		   		$currentUrl .= $value->href; 
		    }else{
		    	$currentUrl = $value->href;
		    }	
		    
		    str_replace('//', '/', $currentUrl);
		    str_replace(':/', '//', $currentUrl);

	      	if(in_array($currentUrl, $this->scrappedUrls) || $currentUrl == $this->scrapUrl ){
	      		continue;
			}
	       	
	       	$this->scrappedUrls[] = $currentUrl;
	       	$this->scrapping($currentUrl);
	        // var_dump($currentUrl);
	    }

	}

}

if(!empty($_POST)) {
	if(isset($_POST['url']) && !empty($_POST['url'])){
		$url = $_POST['url'];

		echo "<pre>";
		$scr = new Scrap($url);
		$scr->runScrapper();
		// var_dump($scr->scrappedUrls);	
		// exit;
	}
}

header('Location: ./search.php');
exit;





