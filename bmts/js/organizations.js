var app = new Vue({
    el: '#organization',
    data: {
        organs: '',
        organ: {
            id: '',
            spec: '',
            organ_name: '',
            organ_address: [{ region: '', city: '', country: 'Республика Беларусь', street: '', postcode: '' }],
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
        getOrgans:function() {
            axios.post('./organizations-action.php', {
                action: 'get',
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    if (!response.data.successMessage) {
                        app.organs = response.data;
                        app.emptyTableMessage = '';
                    } else {
                        app.organs = '';
                        app.emptyTableMessage = response.data.successMessage;
                    }
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        getOrgan:function(id) {
            app.setModalEdit();
            axios.post('./organizations-action.php', {
                action: 'getOrgan',
                id: id,
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    app.modal.title = "Изменить данные организации"
                    app.organ.id = id;
                    app.organ.organ_name = response.data.organ_name;
                    app.organ.spec = response.data.spec;
                    app.organ.organ_address = response.data.organ_address;
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                    app.modal.title = response.data.errorMessage;
                }
            });
        },

        updateOrgan:function() {
            app.checkAddress();
            axios.post('./organizations-action.php', {
                action: 'update',
                id: app.organ.id,
                spec: app.organ.spec,
                organ_name: app.organ.organ_name,
                organ_address: JSON.stringify(app.organ.organ_address),
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    app.getOrgans();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        addOrgan:function() {
            app.checkAddress();
            axios.post('./organizations-action.php', {
                action: 'add',
                spec: app.organ.spec,
                organ_name: app.organ.organ_name,
                organ_address: JSON.stringify(app.organ.organ_address),
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    app.clearFormData();
                    app.getOrgans();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        deleteOrgan: function(id) {
            axios.post('./organizations-action.php', {
                action: 'delete',
                id: id,
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    app.clearFormData();
                    app.getOrgans();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        clearFormData:function() {
            app.organ.id = '';
            app.organ.spec = '';
            app.organ.organ_name = '';
            app.organ.organ_address = [{ region: '', city: '', country: 'Республика Беларусь', street: '', postcode: '' }];
        },

        modalRouter:function() {
            if (app.modal.action == 'add') {
                app.addOrgan();
            } else {
                app.updateOrgan();
            }
        },

        setModalAdd:function() {
            app.modal.action = 'add';
            app.modal.title = 'Добавить новую организацию';
            app.modal.button = 'Добавить';
        },

        setModalEdit:function() {
            app.modal.action = 'update';
            app.modal.title = 'Загрузка...';
            app.modal.button = 'Сохранить';
        },

        setErrorMessage:function(msg) {
            app.errorMessage = msg;
            setTimeout(function () {
                app.errorMessage = '';
            }, 2000);
        },

        setSuccessMessage:function(msg) {
            app.successMessage = msg;
            setTimeout(function () {
                app.successMessage = '';
            }, 2000);
        },

        addAddressInput:function() {
            if (app.organ.organ_address.length < 5) {
                app.organ.organ_address.push({ region: '', city: '', country: 'Республика Беларусь', street: '', postcode: '' });
            } else {
                app.setErrorMessage("Достигнуто максимальное количество адресов для организации");
            }
        },

        deleteAddress:function(index) {
            app.organ.organ_address.splice(index, 1);
        },

        checkAddress:function() {
            for (let i = 0; i < app.organ.organ_address.length; i++) {
                if (app.organ.organ_address[i].region == 'г. Минск') {
                    app.organ.organ_address[i].city = '';
                }
            }
        },
    },

    created:function() {
        this.getOrgans();
    }
});