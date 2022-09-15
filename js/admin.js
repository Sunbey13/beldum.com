var app = new Vue({
    el: '#admin',
    data: {
        users: '',
        drivers: '',
        driver: '',
        successMessage: '',
        errorMessage: '',
        emptyTableMessage: '',
        user: {
            id: '',
            login: '',
            name: '',
            surname: '',
            last_name: '',
            initials: '',
            role: '',
            password1: '',
            password2: '',
            driver_id: '',
        },
        modal: {
            action: '',
            title: '',
            subtitle1: '',
            subtitle2: '',
            button: '',
        },
    },
    methods: {
        getUsers:function(){
            axios.post('admin-action.php', {
                action: 'get'
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    if (!response.data.successMessage) {
                        app.users = response.data;
                        app.emptyTableMessage = '';
                    } else {
                        app.users = '';
                        app.emptyTableMessage = response.data.successMessage;
                    }
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        getUser:function (id) {
            app.setModalEdit();
            axios.post('admin-action.php', {
                action: 'getUser',
                id: id
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    app.modal.title = 'Изменить пользователя'
                    app.user.id = response.data.user_id;
                    app.user.login = response.data.login;
                    app.user.name = response.data.name;
                    app.user.surname = response.data.surname;
                    app.user.last_name = response.data.last_name;
                    app.user.role = response.data.role;
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                    app.modal.title = response.data.errorMessage;
                }
            });
        },

        addUser:function() {
            if (app.user.role == 3) {
                app.user.driver_id = app.driver.driver_id;
                app.user.name = app.driver.name;
                app.user.surname = app.driver.surname;
                app.user.last_name = app.driver.last_name;
            }
            if (app.user.role != 4) {
                if (app.user.name != '' && app.user.surname != '' && app.user.last_name != '') {
                    app.user.initials = this.getInitials(app.user.surname + ' ' + app.user.name + ' ' + app.user.last_name)
                } else {
                    app.user.initials = this.getInitials(app.user.surname + ' ' + app.user.name)
                }
            }
            if (app.user.password1 != '' && app.user.password1 == app.user.password2) {
                axios.post('admin-action.php', {
                    action: 'add',
                    login: app.user.login,
                    password: app.user.password1,
                    name: app.user.name,
                    surname: app.user.surname,
                    last_name: app.user.last_name,
                    role: app.user.role,
                    initials: app.user.initials,
                    driver_id: app.user.driver_id,
                }).then(function(response) {
                    if (!response.data.errorMessage) {
                        app.clearFormData();
                        app.getUsers();
                        app.setSuccessMessage(response.data.successMessage);
                    } else {
                        app.setErrorMessage(response.data.errorMessage);
                    }
                });
            } else {
                app.setErrorMessage('Введенные пароли не совпадают!');
            }
        },

        updateUser:function() {
            if (app.user.role == 3) {
                app.user.driver_id = app.driver.driver_id;
                app.user.name = app.driver.name;
                app.user.surname = app.driver.surname;
                app.user.last_name = app.driver.last_name;
            } else {
                app.user.driver_id = '';
            }
            if (app.user.role == 4) {
                app.users.surname = '';
                app.users.last_name = '';
            } else {
                if (app.user.name != '' && app.user.surname != '' && app.user.last_name != '') {
                    app.user.initials = this.getInitials(app.user.surname + ' ' + app.user.name + ' ' + app.user.last_name)
                } else {
                    app.user.initials = this.getInitials(app.user.surname + ' ' + app.user.name)
                }
            }
            axios.post('admin-action.php', {
                action: 'update',
                id: app.user.id,
                login: app.user.login,
                name: app.user.name,
                surname: app.user.surname,
                last_name: app.user.last_name,
                initials: app.user.initials,
                role: app.user.role,
                driver_id: app.user.driver_id,
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    app.getUsers();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        deleteUser: function(id) {
            axios.post('admin-action.php', {
                action: 'delete',
                id: id,
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    app.clearFormData();
                    app.getUsers();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        getDrivers:function() {
            axios.post('admin-action.php', {
               action: 'getDrivers'
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    app.drivers = response.data;
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        modalRouter:function() {
            if (app.modal.action == 'add') {
                app.addUser();
            } else {
                app.updateUser();
            }
        },

        setModalAdd:function() {
            app.getDrivers();
            app.modal.action = 'add';
            app.modal.title = 'Добавить нового пользователя';
            app.modal.subtitle1 = 'Данные нового пользователя';
            app.modal.subtitle2 = 'Данные нового аккаунта';
            app.modal.button = 'Добавить';
        },

        setModalEdit:function() {
            app.getDrivers();
            app.modal.action = 'update';
            app.modal.title = 'Загрузка...';
            app.modal.subtitle1 = 'Данные пользователя';
            app.modal.subtitle2 = 'Логин аккаунта';
            app.modal.button = 'Сохранить';
        },

        clearFormData:function() {
            app.user.id = '';
            app.user.login = '';
            app.user.name = '';
            app.user.surname = '';
            app.user.last_name = '';
            app.user.initials = '';
            app.user.role = '';
            app.user.password1 = '';
            app.user.password2 = '';
            app.user.driver_id = '';
            app.driver = '';
        },

        setErrorMessage: function(msg) {
            app.errorMessage = msg;
            setTimeout(function () {
                app.errorMessage = '';
            }, 2000);
        },

        setSuccessMessage: function(msg) {
            app.successMessage = msg;
            setTimeout(function () {
                app.successMessage = '';
            }, 2000);
        },

        getInitials: function(str) {
            return str.split(/\s+/).map((w,i) => i ? w.substring(0,1).toUpperCase() + '.' : w).join(' ');
        }
    },

    created:function () {
        this.getUsers();
        this.getDrivers();
    },
});