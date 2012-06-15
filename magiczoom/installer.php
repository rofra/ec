<?php
    /**
        MagicToolbox installer
    */
    
    ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);

    require_once(dirname(__FILE__) . '/magictoolbox.installer.core.class.php');
    require_once(dirname(__FILE__) . '/magictoolbox.installer.magento.class.php');

    $modInstaller = new MagicToolboxmagentoModuleInstallerClass();
    $uninstall = false;
    $upgrade = false;
    if(isset($_GET['mode']) && trim($_GET['mode']) == 'uninstall') {
        $uninstall = true;
    }
    if(isset($_GET['mode']) && trim($_GET['mode']) == 'upgrade') {
        $upgrade = true;
    }
    
    if(!$modInstaller->run($uninstall, $upgrade)) {
        echo '[error]';
        echo $modInstaller->getErrors();
        $modInstaller->restore();
    } else {
        echo '[done]';
        $modInstaller->setBackups();
        echo $modInstaller->getErrors();
    }
