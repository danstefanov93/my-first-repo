$(function(){
    document.on('click','#reload', function() {
        axios.get(route('reload-captcha'))
            .then(function (response) {
                console.log(response);
                $('.captcha span').html(response.data.captcha);
            });
    });
});