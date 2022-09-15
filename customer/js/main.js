var app = new Vue({
   el: '#main-customer',
   data: {
      tasks: '',
      organs: '',
      task: {
         task_id: '',
         date: '',
         task_type: '',
         organ_id: '',
         organ_address: '',
         curator_organ: '',
         curator_organ_tel: '',
         cargo_name: '',
         cargo_w: '',
         cargo_h: '',
         cargo_l: '',
         cargo_weight: '',
         curator_remeza: '',
         notes: '',
      },
      modal: {
         action: '',
         title: '',
         button: '',
      },
      successMessage: '',
      errorMessage: '',
      emptyRequestsMessage: '',
   } ,
   methods: {

      getTasks:function() {
        axios.post('./main-action.php', {
           action: 'getTasks',
        }).then(function (response) {
           if (!response.data.errorMessage) {
              if (!response.data.successMessage) {
                 app.tasks = response.data;
                 app.emptyRequestsMessage = '';
              } else {
                 app.tasks = '';
                 app.emptyRequestsMessage = response.data.successMessage;
              }
           } else {
              app.setErrorMessage(response.data.errorMessage);
           }
        });
      },

      getTask:function(task_id) {
         app.setModalEdit();
         axios.post('./main-action.php', {
            action: 'getTask',
            task_id: task_id,
         }).then(function (response) {
            if (!response.data.errorMessage) {
               app.taskModal.title = "Изменить заявку"
               app.task.task_id = task_id;
               app.task.date = response.data.date;
               app.task.task_type = response.data.task_type;
               app.task.organ_id = response.data.organ_id;
               app.task.organ_address = response.data.organ_address;
               app.task.curator_organ = response.data.curator_organ;
               app.task.curator_organ_tel = response.data.curator_organ_tel;
               app.task.cargo_name = response.data.cargo_name;
               app.task.cargo_w = response.data.cargo_w;
               app.task.cargo_h = response.data.cargo_h;
               app.task.cargo_l = response.data.cargo_l;
               app.task.cargo_weight = response.data.cargo_weight;
               app.task.curator_remeza = response.data.curator_remeza;
               app.task.notes = response.data.notes;
            } else {
               app.setErrorMessage(response.data.errorMessage);
               app.modal.title = response.data.errorMessage;
            }
         })
      },

      addTask:function() {
         axios.post('./main-action.php', {
            action: 'add',
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
               app.clearFormData();
               app.getTasks();
               app.setSuccessMessage(response.data.successMessage);
            } else {
               app.setErrorMessage(response.data.errorMessage);
            }
         });
      },

      updateTask:function () {
        axios.post('./main-action.php', {
           action: 'update',
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
              app.getTasks();
              app.setSuccessMessage(response.data.successMessage);
           } else {
              app.setErrorMessage(response.data.errorMessage);
           }
        });
      },

      deleteTask:function (id) {
         axios.post('./main-action.php', {
            action: 'delete',
            task_id: id,
         }).then(function (response) {
            if (!response.data.errorMessage) {
               app.clearFormData();
               app.getTasks();
               app.setSuccessMessage(response.data.successMessage);
            } else {
               app.setErrorMessage(response.data.errorMessage);
            }
         });
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

      modalRouter:function() {
         if (app.modal.action == 'add') {
            app.addTask();
         } else {
            app.updateTask();
         }
      },

      setModalAdd:function() {
         app.getOrgans();
         app.modal.action = 'add';
         app.modal.title = 'Создать заявку';
         app.modal.button = 'Создать';
      },

      setModalEdit:function() {
         app.getOrgans();
         app.modal.action = 'update';
         app.modal.title = 'Загрузка...';
         app.modal.button = 'Сохранить';
      },

      clearFormData:function() {
         app.task.task_id = '';
         app.task.date = '';
         app.task.task_type = '';
         app.task.organ_id = '';
         app.task.organ_address = '';
         app.task.curator_organ = '';
         app.task.curator_organ_tel = '';
         app.task.cargo_name = '';
         app.task.cargo_w = '';
         app.task.cargo_h = '';
         app.task.cargo_l = '';
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
   },

   created:function() {
      this.getTasks();
   },
});