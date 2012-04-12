<?php

/*
 * This file is part of HTMLPurifier Bundle.
 * (c) 2012 Maxime Dizerens
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Purifier {

    /**
     * @var  HTMLPurifier  singleton instance of the HTML Purifier object
     */
    protected static $singleton;

    /**
     * Returns the singleton instance of HTML Purifier. If no instance has
     * been created, a new instance will be created.
     *
     *     $purifier = Purifier::instance();
     *
     * @return  HTMLPurifier
     */
    public static function instance()
    {
        if ( ! Purifier::$singleton)
        {
            if ( ! class_exists('HTMLPurifier_Config', false))
            {
                if (Config::get('purifier.preload'))
                {
                    // Load the all of HTML Purifier right now.
                    // This increases performance with a slight hit to memory usage.
                    // require path('vendor').'HTMLPurifier/HTMLPurifier.includes.php';
                    require 'library/HTMLPurifier.includes.php';
                }

                // Load the HTML Purifier auto loader
                // require path('vendor').'HTMLPurifier/HTMLPurifier.auto.php';
                require 'library/HTMLPurifier.auto.php';
            }

            // Create a new configuration object
            $config = HTMLPurifier_Config::createDefault();

            if ( ! Config::get('purifier.finalize'))
            {
                // Allow configuration to be modified
                $config->autoFinalize = false;
            }

            // Use the same character set as Laravel
            $config->set('Core.Encoding', Config::get('application.encoding'));

            if (is_array($settings = Config::get('purifier.settings')))
            {
                // Load the settings
                $config->loadArray($settings);
            }

            // Configure additional options
            $config = Purifier::configure($config);

            // Create the purifier instance
            Purifier::$singleton = new HTMLPurifier($config);
        }

        return Purifier::$singleton;
    }

    /**
     * Modifies the configuration before creating a HTML Purifier instance.
     *
     * [!!] You must create an extension and overload this method to use it.
     *
     * @param   HTMLPurifier_Config  configuration object
     * @return  HTMLPurifier_Config
     */
    public static function configure(HTMLPurifier_Config $config)
    {
        return $config;
    }

    /**
     * Removes broken HTML and XSS from text using [HTMLPurifier](http://htmlpurifier.org/).
     *
     *     $text = Purifier::clean($dirty_html);
     *
     * The original content is returned with all broken HTML and XSS removed.
     *
     * @param   mixed   text to clean, or an array to clean recursively
     * @return  mixed
     */
    public static function clean($dirty)
    {
        if (is_array($dirty))
        {
            foreach ($dirty as $key => $value)
            {
                // Recursively clean arrays
                $clean[$key] = Purifier::clean($value);
            }
        }
        else
        {
            // Load HTML Purifier
            $purifier = Purifier::instance();

            // Clean the HTML and return it
            $clean = $purifier->purify($dirty);
        }

        return $clean;
    }
}