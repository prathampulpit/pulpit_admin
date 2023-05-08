var app = new Vue({

    el: '#app',
    data: {
        items: [],

        current_page: 1,
        entity_id: null,
        entity: "Category",
        last_page: null,
        from: null,
        to: null,
        per_page: 100,
        total: null,
        company_type: 'all',

        order_by: 'id',
        order: 'desc',
        search_text: '',

        error: '',
        loaded: false,
        isLoading: false,

    },

    mounted() {
        this.get();
    },

    methods: {
        clearSearch() {
            this.search_text = '';
            this.get();
        },
        search: _.debounce(function() {
            this.get()
        }, 500),

        date: function(date) {
            return moment(date).format('D/M/Y');
        },
        get() {
            let vm = this;
            vm.isLoading = true;
            axios.get(indexUrlJson, {
                    params: {
                        page: this.current_page,
                        order_by: this.order_by,
                        order: this.order,
                        search: this.search_text,
                        per_page: this.per_page,
                    }
                })
                .then(function(response) {

                    vm.items = response.data.data;

                    vm.last_page = response.data.last_page;
                    vm.from = response.data.from;
                    vm.to = response.data.to;
                    vm.total = response.data.total;

                    vm.loaded = true;
                    vm.isLoading = false;

                    Vue.nextTick(initList);
                })
                .catch(function(error) {
                    vm.loaded = false;
                    vm.isLoading = false;
                });
        },
        toggleStatus(userId) {
            let vm = this;
            vm.isLoading = true;
            axios.post(toggle_status_url + '/' + userId)
                .then(function (response) {
                    vm.loaded = true;
                    vm.isLoading = false;
                    Snackbar.show({
                        pos: 'bottom-right',
                        text: 'Status Updated Successfully!',
                        actionText: 'Okay'
                    });
                    vm.get();
                })
                .catch(function (error) {
                    vm.loaded = false;
                    vm.isLoading = false;
                    NProgress.done();
                });
        },
        toggleSetDefault(userId) {
            let vm = this;
            vm.isLoading = true;
            axios.post(toggle_setdefault_url + '/' + userId)
                .then(function (response) {
                    vm.loaded = true;
                    vm.isLoading = false;
                    Snackbar.show({
                        pos: 'bottom-right',
                        text: 'Default value set successfully!',
                        actionText: 'Okay'
                    });
                    vm.get();
                })
                .catch(function (error) {
                    vm.loaded = false;
                    vm.isLoading = false;
                    NProgress.done();
                });
        },
        paginate(page) {
            this.current_page = page;
            this.get();
        },
        sort(order_by) {
            this.order_by = order_by;
            this.order = this.order == 'asc' ? 'desc' : 'asc';
            this.get();
        },
        perPage(e) {
            this.per_page = e.target.value;
            this.get();
        },
        
        classSort(column) {
            return {
                'top-arrow': this.order == 'desc' && this.order_by == column,
                'btm-arrow': this.order == 'asc' && this.order_by == column,
                'sort-default': this.order != column
            }

        },
        confirm(id) {
            console.log(id);
            this.entity_id = id;
            $('#modalDeleteConfirm').modal();
        },

        destroy() {
            let vm = this;
            axios.delete(deleteUrl + '/' + vm.entity_id)
                .then(function(response) {
                    vm.get();
                    vm.entity_id = '';
                    Snackbar.show({
                        pos: 'bottom-right',
                        text: vm.entity + ' Deleted Successfully!',
                        actionText: 'Okay'
                    });
                    $('#modalDeleteConfirm').modal('hide');
                });

        }
    }

})