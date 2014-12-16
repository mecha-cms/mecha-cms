<?php

/**
 * ===========================================================
 *  Mecha - Content Management System
 *  Copyright (c) 2014 Taufik Nurrohman <http://mecha-cms.com>
 * ===========================================================
 */

define('DS', DIRECTORY_SEPARATOR);
define('MECHA_VERSION', '1.1.2');
define('ROOT', rtrim(__DIR__, '\\/'));
define('SYSTEM', ROOT . DS . 'system');
define('DECK', ROOT . DS . 'manager');
define('LANGUAGE', ROOT . DS . 'cabinet' . DS . 'languages');
define('ARTICLE', ROOT . DS . 'cabinet' . DS . 'articles');
define('PAGE', ROOT . DS . 'cabinet' . DS . 'pages');
define('RESPONSE', ROOT . DS . 'cabinet' . DS . 'responses');
define('STATE', ROOT . DS . 'cabinet' . DS . 'states');
define('CUSTOM', ROOT . DS . 'cabinet' . DS . 'custom');
define('ASSET', ROOT . DS . 'cabinet' . DS . 'assets');
define('PLUGIN', ROOT . DS . 'cabinet' . DS . 'plugins');
define('SHIELD', ROOT . DS . 'cabinet' . DS . 'shields');
define('CACHE', ROOT . DS . 'cabinet' . DS . 'scraps');

define('SEPARATOR', '===='); // Separator between the page header and page content
define('ES', '>'); // Self closing HTML tag's end character(s)
define('TAB', '  '); // Standard indentation on the page
define('NL', PHP_EOL); // New line character of HTML output
define('O_BEGIN', ""); // Begin HTML output
define('O_END', PHP_EOL); // End HTML output
define('HTML_PARSER', 'Markdown Extra'); // Depends on the type of HTML parser in the `plugins` folder

define('ICON_LIBRARY_PATH', 'maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css');
define('JAVASCRIPT_LIBRARY_PATH', 'cdnjs.cloudflare.com/ajax/libs/zepto/1.1.4/zepto.min.js');

require SYSTEM . DS . 'ignite.php';
require SYSTEM . DS . 'launch.php';