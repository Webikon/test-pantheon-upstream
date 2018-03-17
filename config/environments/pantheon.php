<?php
if (isset($_ENV['PANTHEON_ENVIRONMENT'])):
    /** MySQL hostname; on Pantheon this includes a specific port number. */
    putenv( "DB_HOST=" . $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT'] );

    /** A couple extra tweaks to help things run well on Pantheon. **/
    if (isset($_SERVER['HTTP_HOST'])) {
        // HTTP is still the default scheme for now. 
        $scheme = 'http';
        // If we have detected that the end use is HTTPS, make sure we pass that
        // through here, so <img> tags and the like don't generate mixed-mode
        // content warnings.
        if (isset($_SERVER['HTTP_USER_AGENT_HTTPS']) && $_SERVER['HTTP_USER_AGENT_HTTPS'] == 'ON') {
            $scheme = 'https';
        }
        putenv( "WP_HOME=" . $scheme . '://' . $_SERVER['HTTP_HOST'] );
        putenv( "WP_SITEURL=" . $scheme . '://' . $_SERVER['HTTP_HOST'] . '/wp' );
    }
    // Don't show deprecations; useful under PHP 5.5
    error_reporting(E_ALL ^ E_DEPRECATED);
    // Force the use of a safe temp directory when in a container
    if ( defined( 'PANTHEON_BINDING' ) ):
        define( 'WP_TEMP_DIR', sprintf( '/srv/bindings/%s/tmp', PANTHEON_BINDING ) );
    endif;

    if ( 'dev' == $_ENV['PANTHEON_ENVIRONMENT'] ) :
        define('SAVEQUERIES', true);
        define('WP_DEBUG', true);
        define('SCRIPT_DEBUG', true);
    endif;

    if ( in_array( $_ENV['PANTHEON_ENVIRONMENT'], array( 'test', 'live' ) ) && ! defined( 'DISALLOW_FILE_MODS' ) ) :
        ini_set('display_errors', 0);
        define('WP_DEBUG_DISPLAY', false);
        define('SCRIPT_DEBUG', false);
        // FS writes aren't permitted in test or live, so we should let WordPress know to disable relevant UI
        define('DISALLOW_FILE_MODS', true);
    endif;
endif;

