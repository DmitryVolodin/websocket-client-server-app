
# Задача

Требуется разработать WebSocket сервер, и два клиента для него.
Сервер должен быть написан на PHP.
1-ый Клиент на JavaScript.
2-ой Клиент на PHP.

Описание:
1-ый клиент необходимо встроить в html страницу.
При запуске 1-ого клиента на WebSocket сервере нужно зарегистрировать два параметра: client_id и task_id.
client_id и task_id могут быть статичными переменными на html странице клиента.

Например:
В файле client1.html
<script>
var client_id = 10;
var task_id = 15;
</script>

В файле client2.html
<script>
var client_id = 11;
var task_id = 16;
</script>

2-ой клиент должен выполняться через консоль и принимать команды client.php <command>.
client.php get-all-users -> вывести на экран ID всех зарегистрированных на WebSocket сервере пользователей.
client.php get-all-user-task=userId -> вывести на эран ID всех зарегистрированных на WebSocket сервере задач одного пользователя.
client.php send-message=all message="Текст сообщения" -> отправить сообщение всем зарегистрированным на WebSocket сервере пользователям, во все задачи.
client.php send-message=userId message="Текст сообщения" -> отправить сообщение одному зарегистрированному на WebSocket сервере пользователю, во все задачи.
client.php send-message=userId task=taskId message="Текст сообщения" -> отправить сообщение одному зарегистрированному на WebSocket сервере пользователю, в одну задачу.

1-ый клиент должен выводить все сообщения в модальном окне, например Bootstrap.
