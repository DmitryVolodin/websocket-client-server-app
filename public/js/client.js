$(function() {


    var alertB = function (idElem) {
        this.idElem = idElem;

    }

    alertB.prototype.show = function (message) {
        $('#' + this.idElem).html(message);
        $('#' + this.idElem).css('display', 'block');

    };

    var alertPrimary = new alertB('alertPrimary');

    $('#' + alertPrimary.idElem).alert();


    var socket = new WebSocket('ws://websocket:9000');

    // Выводим сообщение при открытии WebSocket-соединения.
    socket.onopen = function(event) {
        var data = {'client_id': client_id, 'task_id': task_id};
        dataJson = JSON.stringify(data);

        socket.send(dataJson); // Отправка данных на сервер.

        alertPrimary.show('WebSocket is connected');
    };

    socket.onmessage = function(event) {
        var dataJson = event.data;
        try {
            data = JSON.parse(dataJson);
        }catch(e) {
            return;
        }
        if (data.action == 'get-message') {
            $('#exampleModal').modal('show');
            $('#exampleModal .modal-body').html(data.message);
        }
    };

});





