<?php

/** This file is part of KCFinder project
  *
  *      @desc Base configuration file
  *   @package KCFinder
  *   @version 2.41
  *    @author Pavel Tzonkov <pavelc@users.sourceforge.net>
  * @copyright 2010, 2011 KCFinder Project
  *   @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
  *   @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
  *      @link http://kcfinder.sunhater.com
  */

// IMPORTANT!!! Do not remove uncommented settings in this file even if
// you are using session configuration.
// See http://kcfinder.sunhater.com/install for setting descriptions
if (preg_match('/^[1-2]?[0-5]?[0-5]\.[1-2]?[0-5]?[0-5]\.[1-2]?[0-5­]?[0-5]\.[1-2]?[0-5]?[0-5]$/', $_SERVER['HTTP_HOST'])) {
  $host = $_SERVER['HTTP_HOST'];
} elseif (strpos($_SERVER['HTTP_HOST'], '.') !== FALSE) {
  $parts = explode('.', $_SERVER['HTTP_HOST']);
  $result = array_pop($parts);
  $host = ".".array_pop($parts) .'.'. $result;
} else {
  $host = ".".$_SERVER['HTTP_HOST'];
}

$path = substr($_SERVER['DOCUMENT_ROOT'],0,strrpos($_SERVER['DOCUMENT_ROOT'],"/"));
ini_set('session.save_path', $path.'/sessions');
ini_set('session.gc_maxlifetime', $_COOKIE['ttl']);
session_set_cookie_params($_COOKIE['ttl'], '/', $host, FALSE);

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the uploadDir
 *
 */
define('ENVIRONMENT', 'development');

if (defined('ENVIRONMENT')){
    switch (ENVIRONMENT)
    {
        case 'development':
            $uploadDir = trim($_SERVER['DOCUMENT_ROOT'], '/') .'/uploads';
        break;
    
        case 'testing':
        case 'production':
            $uploadDir = '/'. trim($_SERVER['DOCUMENT_ROOT'], '/') .'/uploads';
        break;

        default:
            exit('The application environment is not set correctly.');
    }
    
}

$_CONFIG = array(

    'disabled' => false,
    'denyZipDownload' => true,
    'denyUpdateCheck' => true,
    'denyExtensionRename' => true,

    'theme' => "oxygen",

    'uploadURL' => "/uploads",
    'uploadDir' => $uploadDir,

    'dirPerms' => 0755,
    'filePerms' => 0644,

    'access' => array(

        'files' => array(
            'upload' => true,
            'delete' => true,
            'copy' => true,
            'move' => true,
            'rename' => true
        ),

        'dirs' => array(
            'create' => true,
            'delete' => true,
            'rename' => true
        )
    ),

    'deniedExts' => "exe com msi bat php phps phtml php3 php4 cgi pl",

    'types' => array(

        // CKEditor & FCKEditor types
        'files'   =>  "",
        'flash'   =>  "swf",
        'images'  =>  "*img",

        // TinyMCE types
        'file'    =>  "",
        'media'   =>  "swf flv avi mpg mpeg qt mov wmv asf rm",
        'image'   =>  "*img",
    ),

    'filenameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'dirnameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'mime_magic' => "",

    'maxImageWidth' => 0,
    'maxImageHeight' => 0,

    'thumbWidth' => 120,
    'thumbHeight' => 120,

    'thumbsDir' => "thumbs",

    'jpegQuality' => 90,

    'cookieDomain' => "",
    'cookiePath' => "",
    'cookiePrefix' => 'KCFINDER_',

    // THE FOLLOWING SETTINGS CANNOT BE OVERRIDED WITH SESSION CONFIGURATION

    '_check4htaccess' => false,
    //'_tinyMCEPath' => "/tiny_mce",

    '_sessionVar' => &$_SESSION['KCFINDER'],
    //'_sessionLifetime' => 30,
    //'_sessionDir' => "/full/directory/path",

    //'_sessionDomain' => ".mysite.com",
    //'_sessionPath' => "/my/path",
);

?>