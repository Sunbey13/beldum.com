var app = new Vue({
    el: '#main',
    data: {
        user: '',
        organs: '',
        tasks: '',
        task: {
            task_id: '',
            date: '',
            task_type: '',
            organ_id: '',
            organ_address: '',
            curator_organ: '',
            curator_organ_tel: '',
            customer_id: '',
            cargo_name: '',
            cargo_l: '',
            cargo_w: '',
            cargo_h: '',
            cargo_weight: '',
            curator_remeza: '',
            notes: ''
        },
        task_lists: '',
        task_list: {
            task_list_id: '',
            date: '',
            car_id: '',
            tasks: [],
            created_by: '',
            route: '',
            driver_name: '',
            driver_surname: '',
            driver_phone: '',
        },
        modal: {
            action: '',
            title: '',
            button: '',
        },
        taskModal: {
            action: '',
            title: '',
            button: '',
        },
        cars: '',
        successMessage: '',
        errorMessage: '',
        emptyTableMessage: '',
        emptyRequestsMessage: '',
        emptyNewTasksMessage: '',
        banks: '',
        poaData: [],
    },
    methods: {
        addTaskList:function() {
          axios.post('./main-action.php', {
              action: 'add',
              task_list_id: app.task_list.task_list_id,
              date: app.task_list.date,
              car_id: app.task_list.car_id,
              tasks: JSON.stringify(app.task_list.tasks),
              route: app.task_list.route,
              driver: app.task_list.driver_surname + " " + app.task_list.driver_name + ", " + app.task_list.driver_phone,
          }).then(function (response) {
             if (!response.data.errorMessage) {
                 app.getTaskLists();
                 app.getNewTasks();
                 app.clearTaskListForm();
                 app.setSuccessMessage(response.data.successMessage);
             } else {
                 app.setErrorMessage(response.data.errorMessage);
             }
          });
        },

        getTaskLists:function() {
            axios.post('./main-action.php', {
                action: 'getLists',
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    if (!response.data.successMessage) {
                        app.task_lists = response.data;
                        app.emptyTableMessage = '';
                    } else {
                        app.task_lists = '';
                        app.emptyTableMessage = response.data.successMessage;
                    }
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        getBankData:function() {
          axios.post('./main-action.php', {
              action: 'getBankData',
          }).then(function (response) {
             if (!response.data.errorMessage) {
                 app.banks = response.data;
             } else {
                 app.setErrorMessage(response.data.errorMessage);
             }
          });
        },

        getTaskList:function(id) {
            axios.post('./main-action.php', {
                action: 'getList',
                task_list_id: id
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    app.modal.title = 'Изменить лист заданий №';
                    app.task_list.task_list_id = response.data.task_list_id;
                    app.task_list.date = response.data.date;
                    app.task_list.car_id = response.data.car_id;
                    app.task_list.route = response.data.route;
                    app.task_list.tasks = response.data.tasks;
                    app.getAllTasks(app.task_list.tasks);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        updateTaskList:function() {
            app.carChanged();
            axios.post('./main-action.php', {
                action: 'update',
                task_list_id: app.task_list.task_list_id,
                date: app.task_list.date,
                car_id: app.task_list.car_id,
                tasks: JSON.stringify(app.task_list.tasks),
                route: app.task_list.route,
                driver: app.task_list.driver_surname + " " + app.task_list.driver_name + ", " + app.task_list.driver_phone,
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    app.getTaskLists();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        deleteTaskList:function(id) {
            axios.post('./main-action.php', {
                action: 'delete',
                task_list_id: id,
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    app.getTaskLists();
                    app.getAllTasks();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        getCars:function() {
          axios.post('./main-action.php', {
              action: 'getCars',
          }).then(function (response) {
              if (!response.data.errorMessage) {
                  app.cars = response.data;
              } else {
                  app.setErrorMessage(response.data.errorMessage);
              }
          });
        },

        getNewTasks:function() {
            axios.post('./main-action.php', {
                action: 'getNewTasks'
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    if (!response.data.successMessage) {
                        app.tasks = response.data;
                        app.emptyNewTasksMessage = '';
                    } else {
                        app.emptyNewTasksMessage = response.data.successMessage;
                        app.tasks = '';
                    }
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        getAllTasks:function(task_ids) {
            axios.post('./main-action.php', {
                action: 'getAllTasks',
                task_ids: JSON.stringify(task_ids),
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    if (!response.data.successMessage) {
                        app.tasks = response.data;
                        for (let i = 0; i < app.tasks.length; i++) {
                            app.poaData.push({bank_data: '', to_date: '', number: '', organ_name: ''});
                        }
                        app.emptyRequestsMessage = '';
                    } else {
                        app.emptyRequestsMessage = response.data.successMessage;
                        app.tasks = '';
                    }
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        getTaskListForEdit:function(id) {
            app.setModalEdit();
            app.carChanged();
            app.getTaskList(id);
            app.getCars();
        },

        getTaskListPOA:function(id) {
            app.getCars();
            app.getTaskList(id);
            app.getBankData();
        },

        completeTaskList:function (id, tasks) {
            axios.post('./main-action.php', {
                action: 'completeTaskList',
                task_list_id: id,
                tasks: JSON.stringify(tasks),
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    app.getTaskLists();
                    app.getAllTasks();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        modalRouter:function() {
            if (app.modal.action == 'add') {
                app.addTaskList();
            } else {
                app.updateTaskList();
            }
        },

        setModalAdd:function() {
            app.tasks = '';
            app.getCars();
            app.getNewTasks();
            app.modal.action = 'add';
            app.modal.title = 'Создать лист заданий №';
            app.modal.button = 'Создать';
        },

        setModalEdit:function() {
            app.tasks = '';
            app.modal.action = 'update';
            app.modal.title = 'Загрузка...';
            app.modal.button = 'Сохранить';
        },

        clearFormData:function() {
            app.cars = '';
            app.tasks = '';
            app.banks = '';
            app.poaData = [];
            app.clearTaskListForm();
            app.getAllTasks();
        },

        clearTaskListForm:function() {
            app.task_list.task_list_id = '';
            app.task_list.date = '';
            app.task_list.car_id = '';
            app.task_list.tasks = [];
            app.task_list.created_by = '';
            app.task_list.route = '';
            app.task_list.driver_name = '';
            app.task_list.driver_surname = '';
            app.task_list.driver_phone = '';
        },

        getTask:function(id) {
            app.setTaskModalEdit();
            axios.post('./main-action.php', {
                action: 'getTask',
                task_id: id
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    app.taskModal.title = "Изменить заявку"
                    app.task.task_id = id;
                    app.task.date = response.data.date;
                    app.task.task_type = response.data.task_type;
                    app.task.organ_id = response.data.organ_id;
                    app.task.organ_address = response.data.organ_address;
                    app.task.curator_organ = response.data.curator_organ;
                    app.task.curator_organ_tel = response.data.curator_organ_tel;
                    app.task.customer_id = response.data.customer_id;
                    app.task.cargo_name = response.data.cargo_name;
                    app.task.cargo_l = response.data.cargo_l;
                    app.task.cargo_w = response.data.cargo_w;
                    app.task.cargo_h = response.data.cargo_h;
                    app.task.cargo_weight = response.data.cargo_weight;
                    app.task.curator_remeza = response.data.curator_remeza;
                    app.task.notes = response.data.notes;
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                    app.taskModal.title = response.data.errorMessage;
                }
            });
        },

        addTask:function() {
            axios.post('./main-action.php', {
                action: 'addTask',
                date: app.task.date,
                task_type: app.task.task_type,
                organ_id: app.task.organ_id,
                organ_address: app.task.organ_address,
                curator_organ: app.task.curator_organ,
                curator_organ_tel: app.task.curator_organ_tel,
                cargo_name: app.task.cargo_name,
                cargo_h: app.task.cargo_h,
                cargo_w: app.task.cargo_w,
                cargo_l: app.task.cargo_l,
                cargo_weight: app.task.cargo_weight,
                curator_remeza: app.task.curator_remeza,
                notes: app.task.notes,
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    app.clearTaskForm();
                    app.getAllTasks();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        deleteTask:function (id) {
            axios.post('./main-action.php', {
                action: 'deleteTask',
                task_id: id,
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    app.clearTaskForm();
                    app.getAllTasks();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        taskModalRouter:function() {
            if (app.taskModal.action == 'add') {
                app.addTask();
            } else {
                app.updateTask();
            }
        },

        setTaskModalAdd:function() {
            app.getOrgans();
            app.taskModal.action = 'add';
            app.taskModal.title = 'Создать заявку';
            app.taskModal.button = 'Создать';
        },

        setTaskModalEdit:function() {
            app.getOrgans();
            app.taskModal.action = 'update';
            app.taskModal.title = 'Загрузка...';
            app.taskModal.button = 'Сохранить';
        },

        getOrgans:function() {
            axios.post('./main-action.php', {
                action: 'getOrgans',
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    app.organs = response.data;
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        updateTask:function () {
            axios.post('./main-action.php', {
                action: 'updateTask',
                task_id: app.task.task_id,
                date: app.task.date,
                task_type: app.task.task_type,
                organ_id: app.task.organ_id,
                organ_address: app.task.organ_address,
                curator_organ: app.task.curator_organ,
                curator_organ_tel: app.task.curator_organ_tel,
                cargo_name: app.task.cargo_name,
                cargo_h: app.task.cargo_h,
                cargo_w: app.task.cargo_w,
                cargo_l: app.task.cargo_l,
                cargo_weight: app.task.cargo_weight,
                curator_remeza: app.task.curator_remeza,
                notes: app.task.notes,
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    app.getAllTasks();
                    app.setSuccessMessage(response.data.successMessage);
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
        },

        clearTaskForm:function() {
            app.task.task_id = '';
            app.task.date = '';
            app.task.task_type = '';
            app.task.organ_id = '';
            app.task.organ_address = '';
            app.task.curator_organ = '';
            app.task.curator_organ_tel = '';
            app.task.customer_id = '';
            app.task.cargo_name = '';
            app.task.cargo_l = '';
            app.task.cargo_w = '';
            app.task.cargo_h = '';
            app.task.cargo_weight = '';
            app.task.curator_remeza = '';
            app.task.notes = '';
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

        addToTaskList:function (id) {
            app.task_list.tasks.push({ id: id });
        },

        deleteFromTaskList:function (id) {
            app.task_list.tasks.splice(app.isInTaskList(id), 1);
        },

        isInTaskList:function (id) {
            return app.task_list.tasks.findIndex(el => el.id == id);
        },

        carChanged:function () {
            if (this.cars && this.task_list.car_id) {
                var carIndex = app.cars.findIndex(el => el.car_id == app.task_list.car_id);
                app.task_list.driver_name = app.cars[carIndex].name;
                app.task_list.driver_surname = app.cars[carIndex].surname;
                app.task_list.driver_phone = app.cars[carIndex].phone;
            }
        },

        getUser:function() {
          axios.post('./main-action.php', {
              action: 'getUser',
          }).then(function (response) {
             if (!response.data.errorMessage) {
                 app.user = response.data;
             } else {
                 app.setErrorMessage(response.data.errorMessage);
             }
          });
        },
    },

    created:function () {
        this.getAllTasks();
        this.getUser();
        this.getTaskLists();
    },
});
