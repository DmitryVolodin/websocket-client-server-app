<?php
namespace Application\WebSockMessage;

class TaskManager
{
    public $taskMap = [];
    public $connectionMap = [];

    public function register($taskId, $clientId, $connection)
    {
        if (!array_key_exists($clientId, $this->taskMap)) {
            $this->taskMap[$clientId] = [];
        }

        if (!array_key_exists($taskId, $this->taskMap[$clientId])) {
            $this->taskMap[$clientId][$taskId] = [];
        }

        $this->taskMap[$clientId][$taskId][$connection->id] = $connection;
        $this->connectionMap[$connection->id] = $clientId . '_' . $taskId;
    }

    public function getTasks($clientId = null)
    {
        if ($clientId !== null) {
            if (!array_key_exists($clientId, $this->taskMap)) {
                throw new \Exception('Client does not register');
            }

            return $this->taskMap[$clientId];
        } else {
            return $this->taskMap;
        }
    }

    public function unregister($connection)
    {
        if (!array_key_exists($connection->id, $this->connectionMap)) {
            return;
        }

        $connKey =  $this->connectionMap[$connection->id];
        list($clientId, $taskId) = explode('_', $connKey);

        unset($this->taskMap[$clientId][$taskId][$connection->id]);
        unset($this->connectionMap[$connection->id]);

        if (count($this->taskMap[$clientId][$taskId]) == 0) {
            unset($this->taskMap[$clientId][$taskId]);
        }
        if (count($this->taskMap[$clientId]) == 0) {
            unset($this->taskMap[$clientId]);
        }
    }

    public function execCommand($command, $params)
    {
        switch($command[0]) {
            case 'get-all-users':
                return [
                    'status'=>'success',
                    'result'=>array_keys($this->taskMap)
                ];
            break;
            case 'get-all-user-task':
                $clientId = $command[1];
                if (array_key_exists($clientId, $this->taskMap)) {
                    return [
                        'status'=>'success',
                        'result'=>array_keys($this->taskMap[$clientId])
                    ];
                } else {
                    return [
                        'status'=>'fail',
                        'message'=>'Client does not register'
                    ];
                }
                break;
            case 'send-message':
                $message = $params->message;
                if($command[1]!='all') {
                    return [
                        'status' => 'fail',
                        'message' => 'Usupported ' . $command[0] . ' param value'
                    ];
                }
                // рассылаем "broadcast" сообщение
                foreach ($this->taskMap as $clientTasks) {
                    foreach ($clientTasks as $taskConnections) {
                        foreach ($taskConnections as $webConnection) {
                            $webConnection->send(json_encode(['action' => 'get-message', 'message' => $message]));
                        }
                    }
                }
                return [
                    'status'=>'success',
                    'result'=> 'The message was successfully sent to ' . count($this->taskMap) . ' client(s)'
                ];

                break;
        }
    }
}