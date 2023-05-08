<!--
<div class="d-flex my-3 justify-content-start">
    Showing records @{{ from }} to @{{ to }} of @{{ total }}
</div>
-->
<!-- <div class="pagination d-flex justify-content-end">
    <a class="paginate_button previous" :class="current_page == 1 ? 'disabled': ''"
        @click.prevent="paginate(current_page-1)">
        <i class="material-icons">arrow_right_alt</i>
    </a>
        <a class="paginate_button" :class="1==current_page ? 'current' : ''" @click.prevent="paginate(1)" v-if="current_page > 3">1</a>
        <a class="paginate_button" :class="1==current_page ? 'current' : ''" @click.prevent="paginate(1)" v-if="current_page > 4"><span>...</span></a>
        <template v-for="page in last_page" v-if="page >= current_page - 2 && page <= current_page + 2">
            <a class="paginate_button" :class="page==current_page ? 'current' : ''"@click.prevent="paginate(page)" v-if="page == current_page">@{{ page }}</a>
            <a class="paginate_button" :class="page==current_page ? 'current' : ''"@click.prevent="paginate(page)" v-else>@{{ page }}</a>
        </template>
        <span v-if="current_page < last_page - 3">...</span>
        <a class="paginate_button" :class="last_page==current_page ? 'current' : ''"@click.prevent="paginate(last_page)" v-if="current_page < last_page - 2">@{{ last_page }}</a>
        
    <a class="paginate_button next" :class="current_page == last_page ? 'disabled': ''"
        @click.prevent="paginate(current_page+1)">
        <i class="material-icons">arrow_right_alt</i>
    </a>
</div> -->
<!-- <div class="d-flex my-3 justify-content-start">
    
</div> -->

<div class="col-sm-12 col-md-5">
    <div class="dataTables_info" id="example1_info" role="status" aria-live="polite">Showing records @{{ from }} to @{{ to }} of @{{ total }}</div>
</div>

<div class="col-sm-12 col-md-7" v-if="total > per_page">
    <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate">
    <ul class="pagination" style="float: right;">
        <!-- <li class="paginate_button page-item previous disabled" id="example2_previous">
            <a class="paginate_button previous page-link" :class="current_page == 1 ? 'disabled': ''" @click.prevent="paginate(current_page-1)" aria-controls="example2" data-dt-idx="0" tabindex="0" class="page-link">Previous</a>
        </li>
        
        <li class="paginate_button page-item active">
            <a :class="1==current_page ? 'current' : ''" @click.prevent="paginate(1)" v-if="current_page > 3" aria-controls="example2" data-dt-idx="1" tabindex="0" class="page-link">1</a>
        </li>
        <li class="paginate_button page-item ">
            <a :class="1==current_page ? 'current' : ''" @click.prevent="paginate(1)" v-if="current_page > 4" aria-controls="example2" data-dt-idx="2" tabindex="0" class="page-link"><span>...</span></a>
        </li>

        <li class="paginate_button page-item next page-link" id="example2_next"><a class="paginate_button next" :class="current_page == last_page ? 'disabled': ''"
            @click.prevent="paginate(current_page+1)" aria-controls="example2" data-dt-idx="7" tabindex="0" class="page-link">Next</a>
        </li> -->

        <a v-if="current_page != 1" class="paginate_button previous page-link" :class="current_page == 1 ? 'disabled': ''" @click.prevent="paginate(current_page-1)" data-dt-idx="0" tabindex="0">
        Previous
        </a>

        <a v-else class="paginate_button previous page-link" :class="current_page == 1 ? 'disabled': ''" data-dt-idx="0" tabindex="0">
        Previous
        </a>

        <a class="paginate_button page-link" :class="1==current_page ? 'current' : ''" @click.prevent="paginate(1)" v-if="current_page > 3" data-dt-idx="1" tabindex="0">1</a>
        <a class="paginate_button page-link" :class="1==current_page ? 'current' : ''" @click.prevent="paginate(1)" v-if="current_page > 4" data-dt-idx="1" tabindex="0"><span>...</span></a>

        <template v-for="page in last_page" v-if="page >= current_page - 2 && page <= current_page + 2">
            <a class="paginate_button page-link" :class="page==current_page ? 'current' : ''"@click.prevent="paginate(page)" v-if="page == current_page">@{{ page }}</a>
            <a class="paginate_button page-link" :class="page==current_page ? 'current' : ''"@click.prevent="paginate(page)" v-else>@{{ page }}</a>
        </template>
        
        <span v-if="current_page < last_page - 3">...</span>
        <a class="paginate_button page-link" :class="last_page==current_page ? 'current' : ''"@click.prevent="paginate(last_page)" v-if="current_page < last_page - 2">@{{ last_page }}..</a>
        
        <a v-if="current_page != last_page" class="paginate_button next page-item next page-link" :class="current_page == last_page ? 'disabled': ''"
            @click.prevent="paginate(current_page+1)" data-dt-idx="20" tabindex="0" >
            Next
        </a>

        <a v-else class="paginate_button next page-item next page-link" :class="current_page == last_page ? 'disabled': ''" data-dt-idx="20" tabindex="0" >
            Next
        </a>
    </ul>

    </div>
</div>