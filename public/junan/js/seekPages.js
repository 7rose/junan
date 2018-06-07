function init(key) {
    var get_url = '/filter/' + key;
    $.get(
        get_url,
        post_data, function(json) {
            for (var i = 0; i < json.length; i++) {
                console.log(json[i]['customer_name'];
            }
        }
    );
}