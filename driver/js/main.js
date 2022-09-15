var app = new Vue({
    el: '#driver-main',
    data: {
        task_lists: '',
        emptyMessage: '',
        successMessage: '',
        errorMessage: '',
    },
    methods: {
        getTaskLists:function() {
            axios.post('./main-action.php', {
                action: 'getLists'
            }).then(function (response) {
                if (!response.data.errorMessage) {
                    if (!response.data.successMessage) {
                        app.task_lists = response.data;
                        app.emptyMessage = '';
                    } else {
                        app.task_lists = '';
                        app.emptyMessage = response.data.successMessage;
                    }
                } else {
                    app.setErrorMessage(response.data.errorMessage);
                }
            });
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
        this.getTaskLists();
    },
});