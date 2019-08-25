<?php
$basePath = dirname(__dir__) . DIRECTORY_SEPARATOR;
//phpinfo();
// die();

require_once $basePath . 'vendor/autoload.php';

$app = App\App::getInstance();
$app->setStartTime();
$app::load();

$app->getRouter($basePath)
    ->get('/', 'Site#index', 'home')
    ->get('/about', 'Site#about', 'about')
    ->get('/mentions', 'Site#mentions', 'mentions')
    ->match('/action', 'Site#action', 'action')

    ->match('/contact', 'Users#contact', 'contact')
    ->match('/subscribe', 'Users#inscription', 'userSubscribe')
    ->match('/login', 'Users#connexion', 'userLogin')
    ->get('/logout', 'Users#deconnectSession', 'userLogout')
    ->get('/identification/verify/[i:id]-[*:token]', 'Users#inscription', 'inscription_Verify')
    ->post('/getClient', 'UserInfos#getClient', 'getClient')

    ->match('/profil', 'UserInfos#profil', 'profil')
    ->match('/profil/[i:id]', 'UserInfos#profil', 'profil_post')
    ->get('/identification/verify/[i:id]-[*:token]', 'Users#inscription', 'login_verify')
    ->match('/resetpwd', 'users#resetpwd', 'resetpwd')

    ->match('/admin', 'admin\Admin#index', 'admin')
    
    ->run();

