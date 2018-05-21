<?php

// 本地仓库路径
$local = '/mnt/www/junan.viirose.com';

// 安全验证字符串，为空则不验证
$token = 'vlQUTCw7EguCqY5F';


// 如果启用验证，并且验证失败，返回错误
$httpToken = isset($_SERVER['HTTP_X_GITLAB_TOKEN']) ? $_SERVER['HTTP_X_GITLAB_TOKEN'] : '';
if ($token && $httpToken != $token) {
    header('HTTP/1.1 403 Permission Denied');
    die('Permission denied.');
}

// 如果仓库目录不存在，返回错误
if (!is_dir($local)) {
    header('HTTP/1.1 500 Internal Server Error');
    die('Local directory is missing');
}

//如果请求体内容为空，返回错误
$payload = file_get_contents('php://input');
if (!$payload) {
    header('HTTP/1.1 400 Bad Request');
    die('HTTP HEADER or POST is missing.');
}

echo shell_exec("cd {$local} && git pull 2>&1");
die("done " . date('Y-m-d H:i:s', time()));