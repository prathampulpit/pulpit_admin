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
        user_type: '5',
        total: null,
        order_by: 'id',
        order: 'desc',
        search_text: '',
        picker12Lang: 'en',
        start_date: '',
        state_id: 'all',
        plan_id: '',
        city_id: '',

        // delete
        entity_id: '',
        entity: "driver",

        errors: '',
        loaded: false,
        isLoading: false,

        // filter
        filter_role: '',
        filter_department: '',
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

        master_search() {
            this.get();
        },
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
            this.start_date = '';
            this.user_type = 'all';
            this.state_id = 'all';
            this.plan_id = '';
            this.city_id = '';
            this.get();
        },
        search: _.debounce(function() {
            //$('#search_text').prop('disabled', true);
            this.current_page = 1;
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
                        start_date: this.start_date,
                        user_type: this.user_type,
                        state_id: this.state_id,
                        plan_id: this.plan_id,
                        city_id: this.city_id
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
        formatDate(date) {
            let tanggal = moment(date, 'YYYY-MM-DD HH:mm:ss').format('DD MMM YYYY HH:mm:ss');
            if (tanggal != 'Invalid date') {
                return tanggal;
            } else {
                return '';
            }
        },
        capitalize(input) {
            console.log("INPUT:" + input);
            if(input == null || input == ''){
                return '';
            }else{
                let input1 = input.toLowerCase();
                let words = input1.split(' ');  
                let CapitalizedWords = [];  
                words.forEach(element => {  
                    if(element[0] == null){
                        return input;
                    }
                    CapitalizedWords.push(element[0].toUpperCase() + element.slice(1, element.length));  
                });  
                return CapitalizedWords.join(' ');
            }
        },
        allcharcapitalize(input) {
            if(input == null || input == ''){
                return '';
            }else{
                return input.toUpperCase();
            }
        },

        destroy() {
            let vm = this;
            axios.delete(user_deleteUrl + '/' + vm.entity_id)
                .then(function(response) {
                    vm.get();
                    vm.entity_id = '';
                    Snackbar.show({
                        pos: 'bottom-right',
                        text: 'User Deleted Successfully!',
                        actionText: 'Okay'
                    });
                    $('#modalDeleteConfirm').modal('hide');
                });

        }
    }
})