Pusher.logToConsole = true;

var pusher = new Pusher("a6a67bfcca58a6561007", {
    cluster: "mt1",
});

var receipientId = $('#receipient_id').val();

var channel = pusher.subscribe('notifications.' + receipientId);
channel.bind("notification.sent", function (data) {
    alert(JSON.stringify(data));
});
