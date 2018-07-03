<?php

$path = "/mnt/www/junan.viirose.com";
$key = 'vlQUTCw7EguCqY5F';

$github_signature = @$_SERVER['HTTP_X_HUB_SIGNATURE'];
$payload = file_get_contents('php://input');

$arr = explode('=', $github_signature);
$algo = $arr[0];
$signature = $arr[1];

$payload_hash = hash_hmac($algo, $payload, $key);
if($payload_hash == $signature){
    shell_exec('cd '.$path);
    shell_exec('/usr/bin/git pull');
    shell_exec('composer install --optimize-autoloader');
    shell_exec('php artisan config:cache');
    shell_exec('php artisan route:cache');
    shell_exec('chown -R nginx:nginx'.$path);
    return 200;
}else{
   return 'invalid key!'; 
} 
?>
