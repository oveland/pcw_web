@if( count($paginator) )
<div style="width: 25%;border-top: 1px solid #505050;" class="m-t-10 p-t-5">
    <small class="clearfix f-s-13">
        <i class="icon-layers"></i>
        <strong>{{ $paginator->total() }}</strong> @lang('registers in total'). @lang('Items') {{ $paginator->firstItem() }} @lang('to') {{ $paginator->lastItem() }}
    </small>
</div>
@endif