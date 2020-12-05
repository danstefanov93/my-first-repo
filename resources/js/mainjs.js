const axios = require('axios');

$(function(){
    $(document).on('click','#reload', function() {
        axios.get(route('reload-captcha'))
            .then(function (response) {
                $('.captcha span').html(response.data.captcha);
            });
    });
});