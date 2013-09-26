nanoSLIDER4GS for GetSimple
===========

Image slider plugin for GetSimple CMS.

This plugin is a portage of the NIVO SLIDER to GetSimple CMS. It provides a very easy method to implement quickly a slider on a web page.

Key features
------------
- plugin for GetSimple CMS
- multiple sliders on the same site/page
- responsive
- no HTML knowledge required
- support all the Nivo Slider options (credits: Dev7Studios)
- Possible image sources :
  * Picasa/Google+ account
  * Flickr account
  * list of images (url) (e.g. stored on one GetSimple site)


Demonstration
-------------

[Go to the demonstration site](http://www.nanoslider4gs.brisbois.fr/)



Usage with Picasa/Google+
-----

To display a slider, insert a code like this one in a GetSimple page :

``` HTML
  (%nanoslider kind=picasa&userID=PicasaUserID&album=PicasaAlbum%)
```

Replace:
- PicasaUserID with your Picasa/Google+ user ID.
- PicasaAlbum with the Picasa/Google+ album ID where the images are stored.

Note: syntax is case sensitive.

Example:

``` HTML
  (%nanoslider kind=picasa&userID=cbrisbois@gmail.com&album=5856259539659194001%)
```

Usage with Flickr
-----

To display a slider, insert a code like this one in a GetSimple page :

``` HTML
  (%nanoslider kind=flickr&album=FlickrAlbumID%)
```

Replace:
- FlickrAlbumID with the Flickr photoset ID where the images are stored (can be found in the URL when the photoset is opened in Flickr).

Note: syntax is case sensitive.

Example:

``` HTML
  (%nanoslider kind=picasa&userID=cbrisbois@gmail.com&album=5856259539659194001%)
```


Usage with a list of images
-----

To display a slider, insert a code like this one in a GetSimple page :

``` HTML
  (%nanoslider kind=url&listImagesBaseURL=baseURL&listImages=image1|image2|...|imageN%)
```

Replace:
- baseURL with the URL where the images are stored
- listImages with the filenames of the images (separated by ‘|’)

Note: syntax is case sensitive.

Example:

``` HTML
  (%nanoslider
    kind=url
    &listImagesBaseURL=http://nanoslider.googlecode.com/files/
    &listImages=nanoslider_sample1.jpg|nanoslider_sample2.jpg|nanoslider_sample3.jpg|nanoslider_sample4.jpg
  %)
```





Installation
-----
* The latest release is available for download on the GetSimple Homepage: [Go to](http://get-simple.info/extend/plugin/nanogallery/637/)
* Download the zip file.
* Extract the content of the zip file into the GetSimple ```plugins``` directory.


Syntax and options
------------------
Arguments are separated by ```&```. Syntax is case sensitive. Important : best results are obtained with images having the same size.
Following arguments are supported 

### General arguments
* ```theme``` : name of the theme ```default``` ```dark``` ```light``` ```nano``` (optional)
* ```nivoOptions``` : Options of the Nivo Slider
  * See Nivo Slider homepage for all the jQuery plugin options:
  * http://dev7studios.com/nivo-slider/#/documentation
* ```forceJQuery``` : ```true``` / ```false``` - force load jQuery


### Picasa/Google+ specific arguments
* ```userID``` : user ID of the Picasa/Google+ account (mandatory)
* ```kind``` : ```picasa``` - set the storage type (mandatory)
* ```album``` : album ID - to display only images stored in the specified album  (mandatory)
* ```displayCaption``` : ```true``` / ```false``` - display or not the title of the images (optional)

### Flickr specific arguments
* ```kind``` : ```flickr``` - set the storage type (mandatory)
* ```album``` : photoset ID - to display only images stored in the specified photoset (mandatory)
* ```displayCaption``` : ```true``` / ```false``` - display or not the title of the images (optional)


### List of images specific arguments
* ```kind``` : ```url``` - set the storage type (mandatory)
* ```listImagesBaseURL``` : Base URL where the images are stored
* ```listImages``` : List of the image filenames (separated by ```|```)
* ```listCaptions``` : List of the captions to display over images (separated by ```|```)
   Use underscore for one space between words.



### Picasa/Google+ example:

```
(%nanoslider
  kind=picasa
  &userID=cbrisbois@gmail.com
  &album=5856259539659194001
  &displayCaption=false
  &maxWidth=1000px
  &nivoOptions={"effect":"fold","pauseOnHover":false,"randomStart":true}
  &theme=nano
%)
```

### Flickr example:

```
(%nanoslider
  kind=flickr
  &album=72157594299597591
  &displayCaption=false
  &maxWidth=800px
  &nivoOptions={"effect":"fold","pauseOnHover":false,"randomStart":true}
  &theme=nano
%)
```

### List of images example:


```
(%nanoslider
  kind=url
  &maxWidth=1000px
  &listImagesBaseURL=http://nanoslider.googlecode.com/files/
  &listImages=nanoslider_sample1.jpg|nanoslider_sample2.jpg|nanoslider_sample3.jpg|nanoslider_sample4.jpg
  &nivoOptions={"effect":"random","pauseOnHover":false,"randomStart":true,"pauseTime":4000}
  &theme=dark
%)
```



Debug mode
----------

To enable the debug mode:

* go to the GetSimple ```plugin``` directory
* rename the file ```nanoslider_debug.off``` to ```nanoslider_debug.on```

To disable the debug mode:

* go to the GetSimple ```plugin``` directory
* rename the file ```nanoslider_debug.on``` to ```nanoslider_debug.off```


Requirements
------------
* GetSimple CMS version 3.1 or superior
* Javascript must be enabled

Third party tools
-----------------
* jQuery
* Nivo Slider, credits: Dev7Studios


[![githalytics.com alpha](https://cruel-carlota.pagodabox.com/de295d45496c01bb871078aac2bcfcac "githalytics.com")](http://githalytics.com/Kris-B/nanoGALLERY)

