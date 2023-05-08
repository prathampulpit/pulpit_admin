var commonFunction = {
    filters: {
        moment: function (date) {
            return moment(date).format(dateFormat);
        },
        capitalize: function (value) {
            if (!value) return ''
            value = value.toString()
            return value.charAt(0).toUpperCase() + value.slice(1)
        }
    }
}


Vue.use(VueLoading);
Vue.component('loading', VueLoading)
Vue.component('select-2', {
    template: '<select class="form-control select-two" :name="name" :multiple="multiple" :tags="tags" :url="url"></select>',
    props: {
        name: '',
        options: {

        },
        value: null,
        multiple: {
            Boolean,
            default: false
        },
        tags: {
            Boolean,
            default: false
        },
        url: '',
    },
    data() {
        return {
            select2data: []
        }
    },
    watch: {
        options() {
            let select = $(this.$el)
            this.formatOptions()
            select.empty().select2({
                placeholder: 'Select',
                data: this.select2data,
                allowClear: true
            })
        }
    },

    mounted() {

        let vm = this
        let select = $(this.$el)
        if (typeof this.url == 'undefined') {
            this.formatOptions()
            select
                .select2({
                    placeholder: 'Select',
                    allowClear: true,
                    data: this.select2data,
                    tags: vm.tags
                })
                .on('change', function () {
                    vm.$emit('input', select.val())
                })
        } else {
            select.select2({
                placeholder: 'Select an option',
                minimumInputLength: 3,
                tags: vm.tags,
                ajax: {
                    url: this.url,
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term
                        }
                        return query;
                    },
                    processResults: function (response) {
                        return {
                            results: response,
                        };
                    }
                }
            })
                .on('change', function () {
                    vm.$emit('input', select.val())
                })
        }
        select.val(this.value).trigger('change')


    },
    methods: {
        formatOptions() {
            this.select2data = []
            //this.select2data.push({ id: '', text: 'Select' })
            for (let key in this.options) {
                if (typeof this.options[key] == 'object') {
                    this.select2data.push({ id: this.options[key].id, text: this.options[key].text })
                } else {
                    this.select2data.push({ id: key, text: this.options[key] })
                }

            }
        }
    },
    destroyed: function () {
        $(this.$el).off().select2('destroy')
    }
})

var toolbar1 = 'undo redo | bold italic underline strikethrough | link | alignleft aligncenter alignright alignjustify | bullist numlist  | removeformat | preview';
var toolbar2 = false;
var plugins = 'lists';
var VueEasyTinyMCE = {

    //declare the props
    props: {
        id: { type: String, default: 'editor' },
        value: { default: '' },
        toolbar1: { default: toolbar1 },
        toolbar2: { default: toolbar2 },
        plugins: { default: plugins },
        other: {
            default: function () {
                return {};
            },
            type: Object
        }
    },

    data: function () {
        return {
            objTinymce: null
        }
    },

    //template: '<div><textarea rows="10" v-bind:value="value"></textarea></div>', //inside a div
    template: '<textarea :id="computedId" :value="value"></textarea>',

    computed: {
        //for multi instance support
        computedId: function () {
            if (this.id === 'editor' || this.id === '' || this.id === null) {
                return 'editor-' + this.guidGenerator(); //put default value on computedId
            } else {
                return this.id;
            }
        }
    },

    mounted: function () {

        var component = this;
        var initialOptions = {
            //target: this.$el.children[0], //(when textarea template is inside a element like a div)
            height: 300,
            menubar: false,
            branding: false,
            target: this.$el,
            toolbar1: this.toolbar1,
            toolbar2: this.toolbar2,
            browser_spellcheck: true,
            plugins: this.plugins,
            init_instance_callback: function (editor) {
                editor.on('Change KeyUp Undo Redo', function (e) {
                    component.updateValue(editor.getContent());
                });
                //editor.setContent(component.value); //use instead :value="value"
                //alert("init");
                component.objTinymce = editor;
            }
        };

        var options = Object.assign({}, initialOptions, this.other);
        tinymce.init(options);
    },

    methods: {
        guidGenerator: function () {
            function s4() {
                return Math.floor((1 + Math.random()) * 0x10000)
                    .toString(16)
                    .substring(1);
            }

            return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
                s4() + '-' + s4() + s4() + s4();
        },
        updateValue: function (value) {
            this.$emit('input', value);
        }
    },

    watch: {
        value: function (newValue, oldValue) {
            // if v-model content change programmability
            if (this.value !== this.objTinymce.getContent())
                this.objTinymce.setContent(this.value);
        }
    }

};


Vue.component('circle-progress-bar', {
    template: '<div class="vue-circular-progress"> <div class="circle"> <svg :width="circleSize" :height="circleSize" class="circle__svg"> <circle :cx="centralP":cy="centralP":r="radius" :style="circleStyle"class="circle__progress circle__progress--path"></circle> <circle :cx="centralP":cy="centralP":r="radius":style="fileStyl"class="circle__progress circle__progress--fill"></circle> </svg> <div class="percent"> <slot> <span class="percent__int">{{ int }}</span> </slot> </div> </div> <slot name="footer"></slot> </div>',
    props: {
        width: {
            type: Number,
            default: 4
        },
        radius: {
            type: Number,
            default: 38
        },
        transitionDuration: {
            type: Number,
            default: 1000
        },
        color: {
            type: String,
            default: "#aaff00"
        },
        value: {
            validator: function (value) {
                // should be a number and less or equal than 100
                return !Number.isNaN(Number(value)) && Number(value) <= 100;
            },
            default: "0"
        }
    },
    data() {
        return {
            offset: "",
            int: 0,
            dec: "00"
        };
    },
    computed: {
        circumference() {
            return this.radius * Math.PI * 2;
        },
        circleStyle() {
            return {
                "stroke-width": this.width,
                stroke: this.color
            };
        },
        fileStyl() {
            return {
                strokeDashoffset: this.offset,
                "--initialStroke": this.circumference,
                "--transitionDuration": `${this.transitionDuration}ms`,
                "stroke-width": this.width,
                stroke: this.color
            };
        },
        circleSize() {
            return (this.radius + this.width) * 2;
        },
        centralP() {
            return this.circleSize / 2;
        }
    },
    methods: {
        increaseNumber(number, className) {
            if (number == 0) {
                return;
            }
            const innerNum = parseInt(
                this.findClosestNumber(this.transitionDuration / 10, number)
            );
            let interval = this.transitionDuration / innerNum;
            let counter = 0;
            const handlerName = `${className}Interval`;
            this[handlerName] = setInterval(() => {
                const bitDiff = number.toString().length - innerNum.toString().length;
                if (bitDiff == 0) {
                    this[className] = counter;
                } else {
                    this[className] = counter * 10 * bitDiff;
                }
                if (counter === innerNum) {
                    // back to origin precision
                    this[className] = number;
                    window.clearInterval(this[handlerName]);
                }
                counter++;
            }, interval);
        },
        findClosestNumber(bound, value) {
            if (value <= bound) {
                return value;
            }
            return this.findClosestNumber(bound, value / 10);
        },
        countNumber(v) {
            this.offset = "";
            this.initTimeoutHandler = setTimeout(() => {
                this.offset = (this.circumference * (100 - v)) / 100;
            }, 100);
            if (this.$slots.default) return;
            let [int, dec] = v.toString().split(".");
            // fallback for NaN
            [int, dec] = [Number(int), Number(dec)];
            this.increaseNumber(int, "int");
            this.increaseNumber(Number.isNaN(dec) ? 0 : dec, "dec");
        },
        clearHandlers() {
            if (this.initTimeoutHandler) {
                clearTimeout(this.initTimeoutHandler);
            }
            if (this.intInterval) {
                clearInterval(this.intInterval);
            }
            if (this.decInterval) {
                clearInterval(this.decInterval);
            }
        }
    },
    watch: {
        value: {
            handler: function (v) {
                const n = Number(v);
                if (Number.isNaN(n) || n == 0) {
                    return;
                }
                this.clearHandlers();
                this.countNumber(v);
            },
            immediate: true
        }
    },
    beforeDestroy() {
        this.clearHandlers();
    }
});