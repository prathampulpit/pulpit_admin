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
                    @if(Route::currentRouteName() == 'admin.jobs.show')
                    <p>{{ @trans('delete_modal.delete_applicant') }}</p>
                    @else
                    <p>{{ @trans('delete_modal.confirmation') }}?</p>
                    @endif
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-cncl" data-dismiss="modal">{{ @trans('delete_modal.cancel') }}</button>
                @if(Route::currentRouteName() == 'admin.jobs.show')
                <button type="submit" class="btn btn-danger" @click="destroy">{{ @trans('delete_modal.confirm') }}</button>
                @else
                <button type="submit" class="btn btn-danger" @click="destroy">{{ @trans('delete_modal.delete') }}</button>
                @endif
            </div>

        </div>
    </div>
</div>