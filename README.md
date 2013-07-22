Nekoimg
=======

A CakePHP Plugin to display kitten photo from Flickr

Usage
-----
There are 2 ways to install this plugin.

###Download zip
Download zip and unpack the file.  
Create Nekoimg directory in your APP/Plugin. Then move unpacked files to APP/Plugin/Nekoimg.

###Install as a submodule

    git submodule add git@github.com:sigesaba/Nekoimg.git app/Plugin/Nekoimg
    
###Setting
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
$licenseInfo includes photo's owner name and photo title.
    
    <?php echo $imgNeko;?>
    <div>
    <?php echo $licenseInfo;?>
    </div>
