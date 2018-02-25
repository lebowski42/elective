<?php
/**
 * @author  Martin Schulte <schulte-martin@web.de>
 * @date    30.12.2017
 * @version 1.0
 */




ini_set('display_errors', 'On');
require ("lib/base.php");



$f3 = Base::instance();

// Configuration
$f3->config("elective/config/config.ini");
//Routes
$f3->config("elective/config/routes.ini");
// Locales and Language
$f3->set('LOCALES','elective/dict/');
$f3->set('LANGUAGE','de');



new Session();

$f3->run();

