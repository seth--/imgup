<?php
/*  controller.php
 *
 *  part of phpMMVC 1.5 by Eric Newport aka Kethinov - http://eric.halo43.com
 *
 *  licensed under the Creative Commons Attribution 3.0 United States License.
 *  You are permitted to redistribute and/or modify this work for commercial or
 *  noncommercial purposes provided proper attribution to the original author (me)
 *  is present in the redistribution.
 */

// set your settings here
$_CONTROLLER['DB_PATH'] = __DIR__.'/db.sqlite';// configure this in cron.php too
$_CONTROLLER['MODELS_PATH'] = 'php/models/';    // relative path to the models directory
$_CONTROLLER['DEFAULT_PAGE'] = 'index.php';     // default model file to load if none is explicitly called for
$_CONTROLLER['404_PAGE'] = '404.php';           // model file to use as a custom 404 page
$_CONTROLLER['ENABLE_EXT'] = false;             // accept either domain.ext/model or domain.ext/model.php

// --- below this line, all the magic you don't need to worry about happens ---

// utility controller vars
$_CONTROLLER['BASE_DIR'] = dirname($_SERVER['PHP_SELF']);
if ($_CONTROLLER['BASE_DIR'] == '/') $_CONTROLLER['BASE_DIR'] = '';
$_CONTROLLER['PATH_ARGS'] = explode('/', $_SERVER['QUERY_STRING']);
if ($_CONTROLLER['PATH_ARGS'][0]) {
    // strip get args from model and path args
    $parts = explode('&', $_CONTROLLER['PATH_ARGS'][0]);
    $_CONTROLLER['MODEL'] = $parts[0];
    $last = count($_CONTROLLER['PATH_ARGS']) - 1;
    $parts = explode('&', $_CONTROLLER['PATH_ARGS'][$last]);
    $_CONTROLLER['PATH_ARGS'][$last] = $parts[0];
    unset($last);
    unset($parts);
}
else $_CONTROLLER['MODEL'] = substr($_CONTROLLER['DEFAULT_PAGE'], 0, (strlen($_CONTROLLER['DEFAULT_PAGE']) - 4));

// markup to use for the 404 page if there isn't a custom 404 page defined by you
$_CONTROLLER['404_FALLBACK_MARKUP'] = <<<MARKUP
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL {$_SERVER['REQUEST_URI']} was not found on this server.</p>
<hr>
<address>{$_SERVER['SERVER_SOFTWARE']} at {$_SERVER['SERVER_NAME']} Port {$_SERVER['SERVER_PORT']}</address>
</body></html>
MARKUP;

// strip get args from first path arg (we only want the model name)
$getstart = strpos($_CONTROLLER['PATH_ARGS'][0], '&');
if ($getstart) $_CONTROLLER['PATH_ARGS'][0] = substr($_CONTROLLER['PATH_ARGS'][0], 0, $getstart);
unset($getstart); // i ain't never called malloc without callin' free

// okay, time for the controller logic!

// check to see if the first path arg exists
if ($_CONTROLLER['PATH_ARGS'][0]) {
    
    // extensionless request
    if (file_exists($_CONTROLLER['MODELS_PATH'].$_CONTROLLER['PATH_ARGS'][0].'.php')) {
        require_once($_CONTROLLER['MODELS_PATH'].$_CONTROLLER['PATH_ARGS'][0].'.php');
    }
    
    // nope, didn't find it. let's try again without appending a .php extension
    elseif ($_CONTROLLER['ENABLE_EXT'] && file_exists($_CONTROLLER['MODELS_PATH'].$_CONTROLLER['PATH_ARGS'][0]) && substr($_CONTROLLER['MODELS_PATH'].$_CONTROLLER['PATH_ARGS'][0], strlen($_CONTROLLER['MODELS_PATH'].$_CONTROLLER['PATH_ARGS'][0]) - 4, 4) == '.php') {
        $_CONTROLLER['MODEL'] = substr($_CONTROLLER['PATH_ARGS'][0], 0, (strlen($_CONTROLLER['PATH_ARGS'][0]) - 4));
        require_once($_CONTROLLER['MODELS_PATH'].$_CONTROLLER['PATH_ARGS'][0]);
    }

    // nope, file definitely doesn't exist, throw a 404
    elseif (file_exists($_CONTROLLER['MODELS_PATH'].$_CONTROLLER['404_PAGE'])) {
        require_once($_CONTROLLER['MODELS_PATH'].$_CONTROLLER['404_PAGE']);
    }
    
    // 404 page doesn't exist either, render fallback markup
    else {
        echo $_CONTROLLER['404_FALLBACK_MARKUP'];
    }
}

// there are no path args, so we need to send the user to the default page
elseif (file_exists($_CONTROLLER['MODELS_PATH'].$_CONTROLLER['DEFAULT_PAGE'])) {
    require_once($_CONTROLLER['MODELS_PATH'].$_CONTROLLER['DEFAULT_PAGE']);
}

// cannot find default page either, redirect to 404 page
elseif (file_exists($_CONTROLLER['MODELS_PATH'].$_CONTROLLER['404_PAGE'])) {
    require_once($_CONTROLLER['MODELS_PATH'].$_CONTROLLER['404_PAGE']);
}

// cannot find 404 page either, render fallback markup
else {
    echo $_CONTROLLER['404_FALLBACK_MARKUP'];
}
?>