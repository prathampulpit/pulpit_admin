<template id="vueModal">
	<div class="modal" v-bind:class="side" :id="id">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@{{ title }}</h4>
                    <button type="button" class="close" @click="closePreviewModal"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
					<slot></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
Vue.component('vue-modal', {
    template: '#vueModal',
    props: {
        id : '',
		side : '',

		title : ''
    },
    methods: {
        closePreviewModal() {
            $('#'+ this.id +'').modal('hide');
        }
    }
});
</script>