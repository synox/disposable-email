<?php

# development mode:
error_reporting(E_ALL);
R::setup('sqlite:../data/db.sqlite' ); 

# enable for production use:
//error_reporting(0);
// R::setup('mysql:host=localhost;dbname=mailbox',
//        'myuser','p4ssw0rd');
// R::freeze( TRUE );

# get hostname from request if possible - or define your own
$serverName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
define('DOMAIN', str_replace('www.', '', $serverName));

// URI-Redirector Prefix (leave empty for direct links)
define('URI_REDIRECT_PREFIX', "https://dubgo.com/r?"); 

// date_default_timezone_set('Europe/Paris');
