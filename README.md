## HTMLPurifier Bundle for Laravel

You must autoload the bundle in bundles.php:

    return array(
        'purifier' => array('auto'=>true)
    );

Then, just apply the clean method on the data you want to clean:

    $new_data = Purifier::clean($dirty_data);
    
Credits:
    http://htmlpurifier.org/
    https://github.com/shadowhand