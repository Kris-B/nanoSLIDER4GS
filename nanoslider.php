<?php
/*
Plugin Name: nanoSlider
Description: Portage of the Nivo Slider. Possible image source : one list of URLs or Picasa/Google+ album or Flickr photoset
Version: 1.2.0a
Author: Christophe Brisbois
Author URI: http://www.brisbois.fr/
*/

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");
$nanoSlider_debugmode=false;

# register plugin
register_plugin(
	$thisfile, 						//Plugin id
	'nanoSlider', 					//Plugin name
	'1.2.0a', 						//Plugin version
	'Christophe Brisbois',  		//Plugin author
	'http://www.brisbois.fr/',		//author website
	'<b>Portage of the Nivo Slider.</b><br> Supported image source : one list of URLs or one Picasa/Google+ photo album or one Flickr photoset', //Plugin description
	'pages', 						//page type - on which admin tab to display
	'nanoSlider_show'				//main function (administration)
);

# activate filter 
add_filter('content','nanoSlider'); 
add_action('index-pretemplate','nanoSlider_check'); 
queue_script('jquery', GSFRONT);



class nanoSlider_slider {
	var $_kind='';
    var $_userID='';
    var $_albumName='';
	var $_maxWidth='';
	var $_displayCaption=false;
	var $_theme='default';
	var $_nivoOptions='';
	var $_listImages='';
	var $_listImagesBaseURL='';
	var $_listCaptions='';
	var $_listURLs='';
	var $_forceJQuery=false;
	var $_consistencyError='';

	// check the consistency of the parameters
	public function checkConsistency() {
	
		if( empty($this->_kind) OR ($this->_kind != 'picasa' AND $this->_kind != 'url' AND $this->_kind != 'flickr') ) {
			$this->_consistencyError='Incorrect parameters for nanoSlider. Please define the "kind". Possible values : "url", "picasa", "flickr".'.$this->_kind;
			return false;
		}
		

		if( $this->_kind == 'picasa' AND !empty($this->_userID)  ) {
			return true;
		}

		if( $this->_kind == 'flickr' AND !empty($this->_albumName)  ) {
			return true;
		}

		
		if( $this->_kind == 'url' AND !empty($this->_listImages)  ) {
			return true;
		}

//		if( $this->_stringFound ) {
//			$this->_consistencyError='Incorrect parameters for nanoSlider. Please check the settings in your page.';
//			return false;
//		}
		$this->_consistencyError='Incorrect parameters for nanoSlider. Please check the settings in your page.';
		return false;
		
	}

	
	// build the parameter string to pass to the javascript
	public function jsParams() {
		$s="{";
		switch( $this->_kind ){
			case "picasa":
				if( !empty($this->_userID) ) { $s.="'userID':'".$this->_userID."',"; } 
				if( !empty($this->_albumName) ) { $s.="'albumName':'".$this->_albumName."',"; } 
				break;
			case "flickr":
				if( !empty($this->_albumName) ) { $s.="'albumName':'".$this->_albumName."',"; } 
				break;
			case "url":
				if( !empty($this->_listImages) ) { $s.="'listImages':'".$this->_listImages."',"; } 
				if( !empty($this->_listImagesBaseURL) ) { $s.="'listImagesBaseURL':'".$this->_listImagesBaseURL."',"; } 
				if( !empty($this->_listCaptions) ) { $s.="'listCaptions':'".$this->_listCaptions."',"; } 
				if( !empty($this->_listURLs) ) { $s.="'listURLs':'".$this->_listURLs."',"; } 
				break;
		}
		if( !empty($this->_maxWidth) ) { $s.="'maxWidth':'".$this->_maxWidth."',"; } 
		if( !empty($this->_theme) ) { $s.="'theme':'".$this->_theme."',"; } 
		if( !empty($this->_kind) ) { $s.="'kind':'".$this->_kind."',"; } 
		if( !empty($this->_displayCaption) ) { $s.="'displayCaption':'".$this->_displayCaption."',"; } 
		
		if ( strlen($s) == 1 ) { return ""; }

		$s=substr($s,0,strlen($s)-1)."}";
		return $s;
	}
}

class nanoSlider_parsedContent {
	var $_sliders;
	var $_newContent='';
	
	function __construct($content) {
		$this->_sliders=array();
		$this->_newContent=$content;
		$ok=true;
		do{
			$ok=$this->parseContent();
		} while( $ok );
	}
	
	function parseContent() {
		$p1 = strpos($this->_newContent, '(%nanoslider');
		if ( $p1 === false ){  return false; };
		$p2= strpos($this->_newContent, '%)', $p1+2);
		if ( $p2 === false ){  return false; };

		$n=count($this->_sliders);

		$this->_sliders[$n]=new nanoSlider_slider();
		$tmp=substr($this->_newContent, $p1+12, $p2-$p1-12);
		// replace the settings with the DIV container in the page
		$this->_newContent=substr($this->_newContent,0,$p1)."<div id='nanoSlider".$n."' class='nanoSlider'></div>".substr($this->_newContent,$p2+2);

		$tmp=html_entity_decode($tmp);
		$tmp=str_replace('<p>','',$tmp);
		$tmp=str_replace('</p>','',$tmp);
		$tmp=str_replace('&nbsp;','',$tmp);
		$tmp=str_replace('&amp;','&',$tmp);
		$tmp=str_replace('<br>','',$tmp);
		$tmp=str_replace('<br />','',$tmp);
		$tmp=str_replace(' ','',$tmp);
		$tmp=str_replace('\"','"',$tmp);
		$tmp=str_replace(array("\t", "\n", "\r"),'',$tmp);
		parse_str($tmp,$fields);

		// get parameters value
		if( isset($fields['kind']) ) { $this->_sliders[$n]->_kind=$fields['kind']; }
		if( isset($fields['userID']) ) { $this->_sliders[$n]->_userID=$fields['userID']; }
		if( isset($fields['maxWidth']) ) { $this->_sliders[$n]->_maxWidth=$fields['maxWidth']; }
		if( isset($fields['album']) ) { $this->_sliders[$n]->_albumName=$fields['album']; }
		if( isset($fields['theme']) ) { $this->_sliders[$n]->_theme=$fields['theme']; }
		if( isset($fields['listImages']) ) { $this->_sliders[$n]->_listImages=$fields['listImages']; }
		if( isset($fields['listImagesBaseURL']) ) { $this->_sliders[$n]->_listImagesBaseURL=$fields['listImagesBaseURL']; }
		if( isset($fields['listCaptions']) ) { $this->_sliders[$n]->_listCaptions=$fields['listCaptions']; }
		if( isset($fields['listURLs']) ) { $this->_sliders[$n]->_listURLs=$fields['listURLs']; }
		if( isset($fields['displayCaption']) ) { $this->_sliders[$n]->_displayCaption=$fields['displayCaption']; }
		if( isset($fields['forceJQuery']) ) { 
			if( $fields['forceJQuery'] == 'true' ) { $this->_sliders[$n]->_forceJQuery=$fields['forceJQuery']; }
		}
		
		
		if( isset($fields['nivoOptions']) ) {
			$this->_sliders[$n]->_nivoOptions=$fields['nivoOptions'];
			$this->_sliders[$n]->_nivoOptions=strtolower($this->_sliders[$n]->_nivoOptions);
			// restore the case of the nivoslider options
			$this->_sliders[$n]->_nivoOptions=str_replace('pauseonhover','pauseOnHover',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('randomstart','randomStart',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('boxrows','boxRows',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('animspeed','animSpeed',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('pausetime','pauseTime',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('startslide','startSlide',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('directionnav','directionNav',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('controlnav','controlNav',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('controlnavthumbs','controlNavThumbs',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('manualadvance','manualAdvance',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('nexttext','nextText',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('slicedown','sliceDown',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('slicedownleft','sliceDownLeft',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('sliceup','sliceUp',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('sliceupleft','sliceUpLeft',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('sliceupdown','sliceUpDown',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('sliceupdownleft','sliceUpDownLeft',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('slideinright','slideInRight',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('slideinleft','slideInLeft',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('boxrandom','boxRandom',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('boxrain','boxRain',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('boxrainreverse','boxRainReverse',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('boxraingrow','boxRainGrow',$this->_sliders[$n]->_nivoOptions);
			$this->_sliders[$n]->_nivoOptions=str_replace('boxraingrowreverse','boxRainGrowReverse',$this->_sliders[$n]->_nivoOptions);
		}

		return true;
	}

}


function nanoSlider_test() {
	echo "ok<br>";
}

function nanoSlider_check() {
	global $nanoSlider_debugmode;
    global $data_index;
	//echo 'NANOSLIDER_TEST - getcwd: '.getcwd().'<br>';
	
	if( file_exists(getcwd().'/plugins/nanoslider_debug.on') ) {
		$nanoSlider_debugmode=true;
		nanoSlider_debug('debug mode is on');
	}
	
	if (strpos($data_index->content, '(%nanoslider') === false ){
		nanoSlider_debug('(%nanoslider not detected');
		return false;
	}
	add_action('theme-header','nanoSlider_header');
};

function nanoSlider_header($dcontent='') {
	
    global $data_index;	
	global $SITEURL;
	
	nanoSlider_debug('nanoSlider_header start');
	
	$content='';
	if( $dcontent == '' ) { $content=$data_index->content; }
	else { $content=$dcontent; }
		
	if (strpos($content, '(%nanoslider') === false ){  return false; };

	$mp = new nanoSlider_parsedContent($content);
	nanoSlider_debug('count: '.count($mp->_sliders));
	
    $tmpContent='<meta name="generator" content="GetSimple nanoSLIDER">'."\n";
    $tmpContent.='<link href="'.$SITEURL.'/plugins/nanoslider/nivoslider/nivo-slider.css" rel="stylesheet" type="text/css">'."\n";
	$tmpContent.='<link href="'.$SITEURL.'/plugins/nanoslider/nivoslider/themes/default/default.css" rel="stylesheet" type="text/css" media="screen">'."\n";
	for( $i=0; $i<count($mp->_sliders); $i++ ) {
		if( strlen($mp->_sliders[$i]->_theme) > 0 and $mp->_sliders[$i]->_theme != 'default' ) {
			$tmpContent.='<link href="'.$SITEURL.'/plugins/nanoslider/nivoslider/themes/'.$mp->_sliders[$i]->_theme.'/'.$mp->_sliders[$i]->_theme.'.css" rel="stylesheet" type="text/css" media="screen">'."\n";
		}
	}
	
	for( $i=0; $i<count($mp->_sliders); $i++ ) {
		if( $mp->_sliders[$i]->_forceJQuery ==  true  ) {
			$tmpContent.='<script type="text/javascript" src="'.$SITEURL.'/plugins/nanoslider/js/jquery-1.8.2.min"></script>'."\n";
			break;
		}
	}
    $tmpContent.='<script type="text/javascript" src="'.$SITEURL.'/plugins/nanoslider/nivoslider/jquery.nivo.slider.pack.js"></script>'."\n";
    $tmpContent.='<link href="'.$SITEURL.'/plugins/nanoslider/css/nanoslider.css" rel="stylesheet" type="text/css">'."\n";
    $tmpContent.='<script type="text/javascript" src="'.$SITEURL.'/plugins/nanoslider/js/nanoslider.js"></script>'."\n";
    $tmpContent.='<script>'."\n";
    $tmpContent.='  jQuery(document).ready(function () {'."\n";
	for( $i=0; $i<count($mp->_sliders); $i++ ) {
		$tmpContent.="    nanoSLIDER.Initiate('nanoSlider".$i."',".$mp->_sliders[$i]->jsParams().",'".$mp->_sliders[$i]->_nivoOptions."','".$mp->_sliders[$i]->_theme."',".$i.");"."\n";
	}
    $tmpContent.='  });'."\n";
    $tmpContent.='</script>'."\n";
    
    echo $tmpContent;
	nanoSlider_debug('nanoSlider_header end');
};

function nanoSlider($content) {
	nanoSlider_debug('nanoSlider start');
	
	if (strpos($content, '(%nanoslider') === false ){  return $content; };
	$mp = new nanoSlider_parsedContent($content);
	for( $i=0; $i<count($mp->_sliders); $i++ ) {
		if( $mp->_sliders[$i]->checkConsistency() === false ) {
			return $mp->_sliders[$i]->_consistencyError;
		}
	}

	nanoSlider_debug('nanoSlider end');
	return $mp->_newContent;
}

function nanoSlider_show() {
	#echo '<p>I like to echo "Hello World" in the footers of all themes.</p>';
}

function nanoSlider_debug($content) {
	global $nanoSlider_debugmode;
	
	if( $nanoSlider_debugmode ) {
		echo 'NANOSLIDER_DEBUG: '.$content.'<br>';
	}
}

?>