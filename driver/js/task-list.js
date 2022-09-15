let params = new URLSearchParams(location.search);

var app = new Vue({
    el: '#task-list',
    data: {
        successMessage: '',
        errorMessage: '',
        task_list: '',
    },

    methods: {
        getList:function() {
          axios.post('./task-list-action.php', {
              action: 'getList',
              task_list_id: params.get('id'),
          })  .then(function (response) {
             if (!response.data.errorMessage) {
                 app.task_list = response.data;
             } else {
                 app.setErrorMessage(response.data.errorMessage);
             }
          });
        },

        setAsDone:function(id) {
            axios.post('./task-list-action.php', {
                action: 'setAsDone',
                task_id: id
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    app.getList();
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            })
        },

        returnToWork:function(id) {
            axios.post('./task-list-action.php', {
                action: 'returnToWork',
                task_id: id
            }).then(function(response) {
                if (!response.data.errorMessage) {
                    app.getList();
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            })
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
      this.getList();
    },
});