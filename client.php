<?php
$localsocket = 'tcp://127.0.0.1:9001';

function colorize($string, $color = null, $bgColor = null)
{
    $color   = sprintf($color, 48);
    $bgColor = null;
    return ($color !== null ? "\x1b[" . $color   . 'm' : '')
    . ($bgColor !== null ? "\x1b[" . $bgColor . 'm' : '')
    . $string
    . "\x1b[22;39m\x1b[0;49m";
}


if (!isset($argv[1])) {
    echo colorize("You must specify \"command\" param:", '0;31')."\n";
    echo colorize('Available command:', '0;43')."\n";
    echo colorize("get-all-users", '0;32'). "\n";
    echo "Вывести на экран ID всех зарегистрированных на WebSocket сервере пользователей\n";
    echo colorize("get-all-user-task=userId", '0;32'). "\n";
    echo "Вывести на экран ID всех зарегистрированных на WebSocket сервере задач одного пользователя\n";
    echo colorize("send-message=all message=\"Текст сообщения\"", '0;32'). "\n";
    echo "Отправить сообщение всем зарегистрированным на WebSocket сервере пользователям, во все задачи\n";
    die();
}

$command = $argv[1];
$command = explode('=', $command);

$params = [];
if (count($argv)>2) {
    for($i=2; $i<count($argv); $i++){
        list($pName, $pVal) = explode('=', $argv[$i]);
        $params[$pName] = $pVal;
    }
}


// соединяемся с локальным tcp-сервером
$instance = stream_socket_client($localsocket);
// отправляем сообщение
fwrite($instance, json_encode(['command' => $command, 'params' => $params])  . "\n");
$response = fread($instance, 10000);
$response = json_decode($response, 1);
print_r($response);