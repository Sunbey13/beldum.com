var app = new Vue({
    el: '#settings',
    data: {
        errorMessage: '',
        successMessage: '',
        noPermissionDataMessage: '',
        emptyBankDataMessage: '',
        poa: '',
        poa_model: {
            poa_eng: '',
            poa_acc: '',
            bank_data: [],
        },
        permission: '',
        permission_model: {
            permission_number: '',
            permission_date: '',
        },
        modal: {
            action: '',
            title: '',
            button: '',
        }
    },
    methods: {
        getPermissionData:function () {
            axios.post('./settings-action.php', {
                action: 'getListPerm',
            }).then(function (response) {
                if (response.data.errorMessage) {
                    app.setErrorMessage(response.data.errorMessage);
                } else {
                    if (!response.data.noPermissionDataMessage) {
                        app.noPermissionDataMessage = '';
                        app.permission = response.data;
                    } else {
                        app.permission = '';
                        app.noPermissionDataMessage = response.data.noPermissionDataMessage;
                    }
                }
            });
        },

        updatePermission:function() {
            axios.post('./settings-action.php', {
                action: 'updatePermission',
                permission_date: app.permission_model.permission_date,
                permission_number: app.permission_model.permission_number,
            }).then(function (response) {
               if (!response.data.errorMessage) {
                   app.getPermissionData();
                   app.setSuccessMessage(response.data.successMessage);
               } else {
                   app.setErrorMessage(response.data.errorMessage);
               }
            });
        },

        getPoaSettings:function () {
            axios.post('./settings-action.php', {
                action: 'getPoaSettings',
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    app.poa = response.data;
                    if (app.poa.bank_data == null) {
                        app.poa.bank_data = [];
                    }
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        updatePoa:function() {
            axios.post('./settings-action.php', {
                action: 'updatePoa',
                poa_eng: app.poa_model.poa_eng,
                poa_acc: app.poa_model.poa_acc,
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    app.getPoaSettings();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        saveBankData:function() {
          axios.post('./settings-action.php', {
              action: 'saveBankData',
              bank_data: JSON.stringify(app.poa_model.bank_data),
          }).then(function (response) {
              if (!response.data.errorMessage) {
                  app.getPoaSettings();
                  app.setSuccessMessage(response.data.successMessage);
              } else {
                  app.setErrorMessage(response.data.errorMessage);
              }
          });
        },

        modalRouter:function() {
            if (app.modal.action == 'permission') {
                app.updatePermission()
            } else if(app.modal.action == 'poa') {
                app.updatePoa();
            } else {
                app.saveBankData()
            }
        },

        addBankDataInput:function() {
            app.poa_model.bank_data.push({
                bank_iban: '',
                bank_bic: '',
                bank_name: '',
                post_code: '',
                bank_address: '',
            });
        },

        deleteBankData:function(index) {
            app.poa_model.bank_data.splice(index, 1);
        },

        setModalPermission:function() {
            app.modal.action = 'permission';
            app.modal.title = 'Изменить данные доверенности';
            app.modal.button = 'Сохранить';
            app.permission_model.permission_date = app.permission.permission_date;
            app.permission_model.permission_number = app.permission.permission_number;
        },

        setModalPoa:function() {
            app.modal.action = 'poa';
            app.modal.title = 'Изменить настройки экспорта';
            app.modal.button = 'Сохранить';
            app.poa_model.poa_acc = app.poa.poa_acc;
            app.poa_model.poa_eng = app.poa.poa_eng;
        },

        setModalBankData:function() {
            app.modal.action = 'bank';
            app.modal.title = 'Изменить банковские реквизиты';
            app.modal.button = 'Сохранить';
            app.poa_model.bank_data = [...app.poa.bank_data];
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

        clearModal:function() {
            setTimeout(function () {
                app.modal.action = '';
                app.modal.button = '';
                app.modal.title = '';
                app.permission_model.permission_date = '';
                app.permission_model.permission_number = '';
                app.poa_model.poa_acc = '';
                app.poa_model.poa_eng = '';
                app.poa_model.bank_data = [];
            }, 150)
        },
    },

    created:function() {
        this.getPermissionData();
        this.getPoaSettings();
    },
});