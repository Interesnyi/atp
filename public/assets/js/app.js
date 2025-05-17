$(document).ready(function() {
    /* Авторизация */
    $(document).on('submit', '#loginForm', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append("sendLoginForm", true);

        $.ajax({
            type: "POST",
            url: "/login",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                var res = jQuery.parseJSON(response);
                if(res.status == 422) {
                    $('#errorMessageLogin').removeClass('d-none');
                    $('#errorMessageLogin').text(res.message);
                } else if(res.status == 200) {
                    $('#errorMessageLogin').addClass('d-none');
                    $('#loginModal').modal('hide');
                    $('#loginForm')[0].reset();

                    alertify.set('notifier','position', 'top-right');
                    alertify.success(res.message);
                    
                    setTimeout(function(){
                        window.location.reload();
                    }, 2000);
                } else if(res.status == 500) {
                    alert(res.message);
                }
            }
        });
    });

    /* Регистрация */
    $(document).on('submit', '#registerForm', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append("sendRegisterForm", true);

        $.ajax({
            type: "POST",
            url: "/register",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                var res = jQuery.parseJSON(response);
                if(res.status == 422) {
                    $('#errorMessageRegister').removeClass('d-none');
                    $('#errorMessageRegister').text(res.message);
                } else if(res.status == 200) {
                    $('#errorMessageRegister').addClass('d-none');
                    $('#registerModal').modal('hide');
                    $('#registerForm')[0].reset();

                    alertify.set('notifier','position', 'top-right');
                    alertify.success(res.message);
                } else if(res.status == 500) {
                    alert(res.message);
                }
            }
        });
    });
}); 