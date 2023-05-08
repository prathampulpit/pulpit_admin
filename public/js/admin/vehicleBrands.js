var app = new Vue({

    el: '#app',
    data: {
        // list
        items: [],
        current_page: 1,
        last_page: null,
        from: null,
        to: null,
        per_page: 100,
        is_ara_lite: 'all',
        total: null,
        order_by: 'id',
        order: 'desc',
        search_text: '',

        // delete
        entity_id: '',
        entity: "VehicleBrands",

        errors: '',
        loaded: false,
        isLoading: false,

        // filter
        filter_role: '',
        filter_department: ''
    },

    mounted() {
        this.get();
    },
    computed: {
        filtering: function() {
            return (this.filter_department || this.filter_role);
        }
    },

    methods: {
        filter() {
            $('#filter').modal('hide');
            this.get();
        },
        resetAllFilter() {
            this.filter_department = '';
            this.filter_role = '';
            $('#filter').modal('hide');
            this.get();
        },
        clearSearch() {
            this.search_text = '';
            this.get();
        },
        search: _.debounce(function() {
            //$('#search_text').prop('disabled', true);
            this.get()
        }, 500),

        get() {
            let vm = this;
            vm.isLoading = true;
            axios.get(user_indexUrlJson, {
                    params: {
                        page: this.current_page,
                        order_by: this.order_by,
                        order: this.order,
                        search: this.search_text,
                        department: this.filter_department,
                        role: this.filter_role,
                        per_page: this.per_page,
                        is_ara_lite: this.is_ara_lite
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
                    //$('#search_text').prop('disabled', false);

                    Vue.nextTick(initListUsers);

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
                .then(function(response) {
                    vm.loaded = true;
                    vm.isLoading = false;
                    Snackbar.show({
                        pos: 'bottom-right',
                        text: 'Status Updated Successfully!',
                        actionText: 'Okay'
                    });
                    vm.get();
                })
                .catch(function(error) {
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
        registerBy(e) {
            this.is_ara_lite = e.target.value;
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
            this.entity_id = id;
            $('#modalDeleteConfirm').modal();
        },

        destroy() {
            let vm = this;
            vm.isLoading = true;
            axios.post(user_deleteUrl + '/' + vm.entity_id)
                .then(function(response) {
                    vm.loaded = true;
                    vm.isLoading = false;

                    vm.entity_id = '';
                    Snackbar.show({
                        pos: 'bottom-right',
                        text: 'User Type Deleted Successfully!',
                        actionText: 'Okay'
                    });
                    vm.get();
                    $('#modalDeleteConfirm').modal('hide');
                });

        }
    }
})