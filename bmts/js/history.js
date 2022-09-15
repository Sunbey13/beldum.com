var app = new Vue({
    el: '#history',
    data: {
        task_lists: '',
        emptyTableMessage: '',
        successMessage: '',
        errorMessage: ''
    },
    methods: {
        getTaskLists:function() {
            axios.post('./history-action.php', {
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
    },

    created:function() {
      this.getTaskLists();
    },
});