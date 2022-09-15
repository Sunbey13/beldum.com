var app = new Vue({
    el: '#drivers',
    data: {
        drivers: '',
        driver: {
            id: '',
            name: '',
            surname: '',
            lastName: '',
            phone: '',
            passport_series_number: '',
            passport_issued_by: '',
            passport_issue_date: '',
            licenses: '',
            dateOfBirth: '',
            contract_number: '',
            contract_date: '',
            ie_name: '',
            bank_iban: '',
            bank_bik: '',
            initials: '',
        },
        modal: {
            action: '',
            title: '',
            button: '',
        },
        successMessage: '',
        errorMessage: '',
        emptyTableMessage: '',
    },
    methods: {
        getDrivers:function() {
            axios.post('./drivers-action.php', {
                action: 'get',
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    if (!response.data.successMessage) {
                        app.drivers = response.data;
                        app.emptyTableMessage = '';
                    } else {
                        app.drivers = '';
                        app.emptyTableMessage = response.data.successMessage;
                    }
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        getDriver:function(id) {
                app.setModalEdit();
                axios.post('./drivers-action.php', {
                    action: 'getDriver',
                    id: id
                }).then(function(response) {
                    if (!response.data.errorMessage) {
                        app.modal.title = 'Изменить водителя';
                        app.driver.id = id;
                        app.driver.name = response.data.name;
                        app.driver.surname = response.data.surname;
                        app.driver.lastName = response.data.last_name;
                        app.driver.phone = response.data.phone;
                        app.driver.licenses = response.data.licenses;
                        app.driver.dateOfBirth = response.data.date_of_birth;
                        app.driver.passport_series_number = response.data.passport_series_number;
                        app.driver.passport_issued_by = response.data.passport_issued_by;
                        app.driver.passport_issue_date = response.data.passport_issue_date;
                        app.driver.contract_number = response.data.contract_number;
                        app.driver.contract_date = response.data.contract_date;
                        app.driver.ie_name = response.data.ie_name;
                        app.driver.bank_iban = response.data.bank_iban;
                        app.driver.bank_bik = response.data.bank_bik;
                    } else {
                        app.setErrorMessage(response.data.errorMessage);
                        app.modal.title = response.data.errorMessage;
                    }
                });
        },

        addDriver:function() {
            if (app.driver.name != '' && app.driver.surname != '' && app.driver.lastName != '') {
                app.driver.initials = this.getInitials(app.driver.surname + ' ' + app.driver.name + ' ' + app.driver.lastName)
            } else {
                app.driver.initials = this.getInitials(app.driver.surname + ' ' + app.driver.name)
            }
            axios.post('./drivers-action.php', {
                action: 'add',
                name: app.driver.name,
                surname: app.driver.surname,
                last_name: app.driver.lastName,
                phone: app.driver.phone,
                licenses: app.driver.licenses,
                date_of_birth: app.driver.dateOfBirth,
                passport_series_number: app.driver.passport_series_number,
                passport_issue_date: app.driver.passport_issue_date,
                passport_issued_by: app.driver.passport_issued_by,
                contract_number: app.driver.contract_number,
                contract_date: app.driver.contract_date,
                ie_name: app.driver.ie_name,
                bank_iban: app.driver.bank_iban,
                bank_bik: app.driver.bank_bik,
                initials: app.driver.initials,
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    app.clearFormData();
                    app.getDrivers();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        updateDriver:function() {
            if (app.driver.name != '' && app.driver.surname != '' && app.driver.lastName != '') {
                app.driver.initials = this.getInitials(app.driver.surname + ' ' + app.driver.name + ' ' + app.driver.lastName)
            } else {
                app.driver.initials = this.getInitials(app.driver.surname + ' ' + app.driver.name)
            }
            axios.post('./drivers-action.php', {
                action: 'update',
                driver_id: app.driver.id,
                name: app.driver.name,
                surname: app.driver.surname,
                last_name: app.driver.lastName,
                phone: app.driver.phone,
                passport_series_number: app.driver.passport_series_number,
                passport_issue_date: app.driver.passport_issue_date,
                passport_issued_by: app.driver.passport_issued_by,
                licenses: app.driver.licenses,
                date_of_birth: app.driver.dateOfBirth,
                contract_number: app.driver.contract_number,
                contract_date: app.driver.contract_date,
                ie_name: app.driver.ie_name,
                bank_iban: app.driver.bank_iban,
                bank_bik: app.driver.bank_bik,
                initials: app.driver.initials,
            }).then(function(response){
                if (!response.data.errorMessage) {
                    app.getDrivers();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        deleteDriver: function(driver_id) {
            axios.post('./drivers-action.php', {
                action: 'delete',
                driver_id: driver_id,
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    app.clearFormData();
                    app.getDrivers();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        modalRouter:function() {
            if (app.modal.action == 'add') {
                app.addDriver();
            } else {
                app.updateDriver();
            }
        },

        setModalAdd:function() {
            app.modal.action = 'add';
            app.modal.title = 'Добавить нового водителя';
            app.modal.button = 'Добавить';
        },

        setModalEdit:function() {
            app.modal.action = 'update';
            app.modal.title = 'Загрузка...';
            app.modal.button = 'Сохранить';
        },

        clearFormData:function() {
            app.driver.id = '';
            app.driver.name = '';
            app.driver.surname = '';
            app.driver.lastName = '';
            app.driver.phone = '';
            app.driver.passport_issue_date = '';
            app.driver.passport_issued_by = '';
            app.driver.passport_series_number = '';
            app.driver.licenses = '';
            app.driver.dateOfBirth = '';
            app.driver.contract_number = '';
            app.driver.contract_date = '';
            app.driver.ie_name = '';
            app.driver.bank_iban = '';
            app.driver.bank_bik = '';
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

    created:function() {
        this.getDrivers();
    },
});