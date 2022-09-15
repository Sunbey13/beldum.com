var app = new Vue({
    el: "#login-form",
    data: {
        login: '',
        password: '',
        actionButton: "Войти",
        successMessage: '',
        errorMessage: '',
    },
    methods: {
        checkLogin: function () {
            if (app.checkForm()) {
                axios.post("../login.php", {
                    login: app.login,
                    password: app.password,
                }).then(function (response) {
                    if (!response.data.error) {
                        app.errorMessage = '';
                        app.successMessage = response.data.successMessage;
                        app.login = '';
                        app.password = '';
                        setTimeout(
                            function() {
                                var role = response.data.role
                                if (role == 1 || role == 2) {
                                    window.location.href = "../bmts/main.php";
                                } else if(role == 3){
                                    window.location.href = "../driver/main.php";
                                } else if (role == 4) {
                                    window.location.href = "../customer/main.php";
                                } else {
                                    window.location.href = "../admin.php";
                                }
                            },
                            2000
                        )
                    } else {
                        app.errorMessage = response.data.errorMessage;
                    }
                });
            }
        },

        checkForm: function () {
            if (app.login == '' && app.password == '') {
                app.errorMessage = "Логин и пароль не могут быть пустыми!";
                return false;
            }
            if (app.login == '') {
                app.errorMessage = "Логин не может быть пустым!";
                return false
            }
            if (app.password == '') {
                app.errorMessage = "Пароль не может быть пустым!";
                return false
            }
            return true;
        },

        keymonitor: function(event) {
            if(event.key == "Enter"){
                app.checkLogin();
            }
        },
    },
});