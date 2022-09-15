var app = new Vue({
    el: '#cars',
    data: {
        cars: '',
        car: {
            id: '',
            driverID: '',
            mark: '',
            model: '',
            number: '',
            carcassW: '',
            carcassH: '',
            carcassL: '',
            carcassWeight: '',
        },
        drivers: '',
        modal: {
            action: '',
            title: '',
            button: '',
        },
        successMessage: '',
        errorMessage: '',
        emptyTableMessage: ''
    },
    methods: {
        getCars:function() {
            axios.post('./cars-action.php', {
                action: 'getCars',
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    if (!response.data.successMessage) {
                        app.cars = response.data;
                        app.emptyTableMessage = '';
                    } else {
                        app.cars = '';
                        app.emptyTableMessage = response.data.successMessage;
                    }
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        getCar:function(id) {
            app.setModalEdit();
            axios.post('./cars-action.php', {
                action: 'getCar',
                id: id
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    app.modal.title = 'Изменить авто'
                    app.car.id = id;
                    app.car.driverID = response.data.driver_id;
                    app.car.mark = response.data.mark;
                    app.car.model = response.data.model;
                    app.car.number = response.data.number;
                    app.car.carcassW = response.data.carcass_w;
                    app.car.carcassH = response.data.carcass_h;
                    app.car.carcassL = response.data.carcass_l;
                    app.car.carcassWeight = response.data.carcass_weight;
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                    app.modal.title = response.data.errorMessage;
                }
            });
        },

        updateCar:function() {
            axios.post('./cars-action.php', {
                action: 'update',
                id: app.car.id,
                driverID: app.car.driverID,
                mark: app.car.mark,
                model: app.car.model,
                number: app.car.number,
                carcassW: app.car.carcassW,
                carcassH: app.car.carcassH,
                carcassL: app.car.carcassL,
                carcassWeight: app.car.carcassWeight,
            }).then(function(response){
                if (!response.data.errorMessage) {
                    app.getCars();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        getDrivers:function() {
            axios.post('./cars-action.php', {
                action: 'getDrivers'
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    app.drivers = response.data;
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        addCar:function() {
            axios.post('./cars-action.php', {
                action: 'add',
                driverID: app.car.driverID,
                mark: app.car.mark,
                model: app.car.model,
                number: app.car.number,
                carcassW: app.car.carcassW,
                carcassH: app.car.carcassH,
                carcassL: app.car.carcassL,
                carcassWeight: app.car.carcassWeight,
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    app.clearFormData();
                    app.getCars();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        deleteCar:function(id) {
            axios.post('./cars-action.php', {
                action: 'delete',
                id: id,
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    app.clearFormData();
                    app.getCars();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        modalRouter:function() {
            if (app.modal.action == 'add') {
                app.addCar();
            } else {
                app.updateCar();
            }
        },

        setModalAdd:function() {
            app.getDrivers();
            app.modal.action = 'add';
            app.modal.title = 'Добавить новое авто';
            app.modal.button = 'Добавить';

        },

        setModalEdit:function() {
            app.getDrivers();
            app.modal.action = 'update';
            app.modal.title = 'Загрузка...';
            app.modal.button = 'Сохранить';
        },

        clearFormData:function() {
            app.car.id = '';
            app.drivers = '';
            app.car.driverID = '';
            app.car.mark = '';
            app.car.model = '';
            app.car.number = '';
            app.car.carcassW = '';
            app.car.carcassH = '';
            app.car.carcassL = '';
            app.car.carcassWeight = '';
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
    },

    created:function() {
        this.getCars();
        this.getDrivers();
    },
});