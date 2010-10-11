<?php
// If you remove this. You might die.
define('FRAPI_CACHE_ADAPTER', 'apc');

// Use the constant CUSTOM_MODEL to access the custom model directory
// IE: require CUSTOM_MODEL . DIRECTORY_SEPARATOR . 'ModelName.php';
// Or add an autolaoder if you are brave.

// Frapi comes with Armchair by default. You can use it or decide to remove it.
// You can find armchair at: git://github.com/till/armchair.git
//require CUSTOM_MODEL . DIRECTORY_SEPARATOR . 'ArmChair' . DIRECTORY_SEPARATOR . 'ArmChair.php';

// Change this to define where you have web2project installed
define('W2P_INSTALL_DIR', '/var/www/html/web2project.local');

// The w2p include directory
define('W2P_INCLUDE_DIR', DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR);

// Bootstrap w2p
require W2P_INSTALL_DIR . DIRECTORY_SEPARATOR . 'base.php';
require W2P_BASE_DIR . W2P_INCLUDE_DIR . 'config.php';
require W2P_BASE_DIR . W2P_INCLUDE_DIR . 'main_functions.php';
require W2P_BASE_DIR . W2P_INCLUDE_DIR . 'db_adodb.php';
