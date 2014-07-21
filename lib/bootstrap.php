<?php

namespace Base;

// Turn on error reporting
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

// Initialize app's web path
Path::initDir(getcwd());

// Initialize app's base URI
Path::initURI();

// Init the App to read configuration
App::init();

// Route the URI to correct controller and execute it
App::execute(new Request(isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/'));