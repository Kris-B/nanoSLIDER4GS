// Plugin Name: nanoSlider
// Description: Portage of the Nivo Slider. Possible image source : one list of URLs or Picasa/Google+ album or Flickr photoset
// Version: 1.1.0
// Author: Christophe Brisbois
// Author URI: http://www.brisbois.fr/

var nanoSLIDER = {

	user: "",
	settingsObj: null,
	containerSliderWrapper : null,
	containerSlider : null,
	tW : 100,
	tH : 100,
	itemInfo : new Array(),
	
	
	Initiate : function(elementID, params, nivoOptions, theme, instance) {
		if( params.maxWidth !== undefined ) { jQuery('#'+elementID).css('max-width',params.maxWidth); }
		nanoSLIDER.containerSliderWrapper =jQuery('<div class="slider-wrapper theme-'+theme+'"></div>').appendTo('#'+elementID);

		sliderID='slider'+instance;
		
		nanoSLIDER.containerSlider =jQuery('<div id="'+sliderID+'" class="nivoSlider"></div>').appendTo(nanoSLIDER.containerSliderWrapper);

		var nOpt;
		if( nivoOptions.length > 0 ) {
			try {
				nOpt = jQuery.parseJSON(nivoOptions);
			} catch (err) {
				var txt="There is an error in the NIVO OPTIONS section : please check the settings.\n\n";
				txt+="Error description: " + err.message + "\n\n";
				txt+="Defined options:\n\n "+nivoOptions+"\n\n";
				alert(txt);
			}
		}

		switch(params.kind) {
			case "url":
				var items=params.listImages.split('|');
				var captions='';
				if( params.listCaptions !== undefined ) {
					captions=params.listCaptions.replace('_',' ').split('|');
				}
				for (var i = 0; i < items.length; i++) {
					if( i < captions.length ) {
						jQuery('<img src="'+params.listImagesBaseURL+items[i]+'" alt="" title="'+captions[i]+'"></img>').appendTo('#'+sliderID);
					}
					else {
						jQuery('<img src="'+params.listImagesBaseURL+items[i]+'" alt=""></img>').appendTo('#'+sliderID);
					}
				}
				jQuery('#'+sliderID).nivoSlider(nOpt);
				break;
			case "picasa":
				nanoSLIDER.GetPicasaImages(params.userID, params.albumName, sliderID, nOpt, params.displayCaption);
				break;
			case "flickr":
				nanoSLIDER.GetFlickrImages(params.albumName, sliderID, nOpt, params.displayCaption);
				break;
		}
	
	},

	
	GetPicasaImages : function(userID, albumName, sliderID, nOpt, displayCaption) {
		var url = 'http://picasaweb.google.com/data/feed/api/user/'+userID+'/albumid/'+albumName+'?alt=json&kind=photo&imgmax=d';
		jQuery.ajaxSetup({ cache: false });
		jQuery.support.cors = true;
		url = url + "&callback=?";
		jQuery.getJSON(url, function(data) {
			
			jQuery.each(data.feed.entry, function(i,data){
				//Get the title  (=filename)
				itemTitle = data.media$group.media$title.$t;
				//Get the URL of the thumbnail
				//itemThumbURL = data.media$group.media$thumbnail[0].url;
				//Get the ID 
				itemID = data.id.$t;
				itemID = itemID.split('/')[9].split('?')[0];
				//Get the description
				itemDescription = data.media$group.media$description.$t;
				imgUrl=data.media$group.media$content[0].url;
				var capt="";
				if( displayCaption == true ) { capt=itemDescription; }
				var nImg=jQuery('<img src="'+imgUrl+'" alt="" title="'+capt+'"></img>').appendTo('#'+sliderID);
			});
			jQuery('#'+sliderID).nivoSlider(nOpt);
		})
		//.success(function() { alert("second success"); 	})
		.error( function(XMLHttpRequest, textStatus, errorThrown) {
			alert("Cannot retrieve Picasa/Google+ images (URL:"+url+") : " + XMLHttpRequest.responseText); 
		});
		//.complete(function() { alert("complete"); });

	},

	GetFlickrImages : function( albumName, sliderID, nOpt, displayCaption) {
		var g_flickrApiKey="2f0e634b471fdb47446abcb9c5afebdc";
		var url = "http://api.flickr.com/services/rest/?&method=flickr.photosets.getPhotos&api_key=" + g_flickrApiKey + "&photoset_id="+albumName+"&extras=description,views,url_s,url_o,url_m&format=json&jsoncallback=?";
		jQuery.ajaxSetup({ cache: false });
		jQuery.support.cors = true;
		url = url + "&callback=?";
		jQuery.getJSON(url, function(data) {
			
			jQuery.each(data.photoset.photo, function(i,data){
				//Get the title  (=filename)
				itemTitle = data.title;
				//Get the URL of the thumbnail
				//itemThumbURL = data.media$group.media$thumbnail[0].url;
				//Get the ID 
				itemID = data.id;
				//Get the description
				itemDescription = data.description._content;
				imgUrl=data.url_o;
				var capt="";
				if( displayCaption == true ) { capt=itemDescription; }
				var nImg=jQuery('<img src="'+imgUrl+'" alt="" title="'+capt+'"></img>').appendTo('#'+sliderID);
			});
			jQuery('#'+sliderID).nivoSlider(nOpt);
		})
		//.success(function() { alert("second success"); 	})
		.error( function(XMLHttpRequest, textStatus, errorThrown) {
			alert("Cannot retrieve Flickr images (URL:"+url+") : " + XMLHttpRequest.responseText); 
		});
		//.complete(function() { alert("complete"); });

	}

	

}