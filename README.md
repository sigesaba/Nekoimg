Nekoimg
=======

A CakePHP Plugin to display kitten photo from Flickr

Usage
-----

###Before the installation...
Sign up Flickr if you haven't already.  
http://www.flickr.com/signup  

Get an API Key here.   
http://www.flickr.com/services/

###Download

####Download zip
Download zip and unpack the file.  
Create Nekoimg directory in your APP/Plugin. Then move unpacked files to APP/Plugin/Nekoimg.

####Install as a submodule

    git submodule add git@github.com:sigesaba/Nekoimg.git app/Plugin/Nekoimg
    
###Set up
####Place your API Key
Place your Flickr API Key in APP/Plugin/Nekoimg/Controller/Component/NekoimgComponent.php

    protected $api_key = YOUR_FLICKR_API_KEY;


####Load the plugin
Load the plugin in APP/Config/bootstrap.php.

    CakePlugin::loadAll();

or 

    CakePlugin::load('Nekoimg'); 
    
####Configuring components in Controller

    public $components = array(
  			'Nekoimg.Nekoimg',
  		);

####Call getNekoImg() method in a Contrller Action

    $this->Nekoimg->getNekoImg();
    
####Display in a View file
$imgNeko includes <img> tag.   
$creditInfo includes photo's owner name and photo title.
    
    <?php echo $imgNeko;?>
    <div>
    <?php echo $creditInfo;?>
    </div>

