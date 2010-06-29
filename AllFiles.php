<?php

// Use the constant CUSTOM_MODEL to access the custom model directory
// IE: require CUSTOM_MODEL . DIRECTORY_SEPARATOR . 'ModelName.php';
// Or add an autolaoder if you are brave.

// Frapi comes with Armchair by default. You can use it or decide to remove it.
// You can find armchair at: git://github.com/till/armchair.git
require CUSTOM_MODEL . DIRECTORY_SEPARATOR . 'ArmChair' . DIRECTORY_SEPARATOR . 'ArmChair.php';

// Change this to define where you have web2project installed
define('W2P_INSTALL_DIR', '/var/www/html/web2project');

// Bootstrap w2p
require W2P_INSTALL_DIR . DIRECTORY_SEPARATOR . 'base.php';
require_once W2P_BASE_DIR . '/includes/config.php';
require_once W2P_BASE_DIR . '/includes/main_functions.php';
require_once W2P_BASE_DIR . '/includes/db_adodb.php';