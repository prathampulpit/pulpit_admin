<div class="modal" tabindex="-1" role="dialog" id="modalDeleteConfirm">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ @trans('delete_modal.confirm') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete?
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-cncl" data-dismiss="modal">{{ @trans('delete_modal.cancel') }}</button>
                <button type="submit" class="btn btn-danger" @click="destroy">{{ @trans('delete_modal.confirm') }}</button>
            </div>

        </div>
    </div>
</div>