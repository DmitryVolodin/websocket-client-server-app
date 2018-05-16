<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Application/Autoloader.php';

use Workerman\Worker;

use Application\WebSockMessage\TaskManager;

$clients = [];
$tasks = [];

$manager = new TaskManager();

// Создаём Websocket сервер
$ws_worker = new Worker("websocket://websocket:9000");

// 1 процесс
$ws_worker->count = 1;

// Обработчик входящего соединения
$ws_worker->onConnect = function($connection)
{
    echo "New connection " . $connection->id."\n";
};

// Создаём обработчик, который будет выполняться при запуске ws-сервера
$ws_worker->onWorkerStart = function() use (&$manager)
{
    // Создаём локальный tcp-сервер, чтобы отправлять на него сообщения из кода php клиента
    $inner_tcp_worker = new Worker("tcp://127.0.0.1:9001");
    // Создаём обработчик сообщений, который будет срабатывать,
    // когда на локальный tcp-сокет приходит сообщение
    $inner_tcp_worker->onMessage = function($connection, $data) use (&$manager){
        $data = json_decode($data);

        switch($data->command[0]) {
            case 'get-all-users':
            case 'get-all-user-task':
            case 'send-message':
                $result = $manager->execCommand($data->command, $data->params);
                break;
            default:
                $result = [
                    'status'=>'fail',
                    'error'=>'Unsupported method'
                ];
        }

        $connection->send(json_encode($result));
    };
    $inner_tcp_worker->listen();
};

// Обработчик получения сообщения на websocket сервер
$ws_worker->onMessage = function($connection, $data) use (&$manager)
{
    $data = json_decode($data);
    // Регистрируем клиента и его таски
    $manager->register($data->task_id, $data->client_id, $connection);
};

// Обработчик закрытия соединения на websocket сервере
$ws_worker->onClose = function($connection) use ($manager)
{
    // Удаляем из зарегистрированных клиента и его таски (для закрываемого соединения)
    $manager->unregister($connection);
    echo "Connection closed\n";
};

// Запускаем Websocket сервер
Worker::runAll();