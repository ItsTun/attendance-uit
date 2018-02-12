@if ($paginator->lastPage() > 1)
<nav class="my-4">
    <ul class="pagination pagination-circle pg-blue mb-0 justify-content-center">
        <li class="page-item {{ ($paginator->currentPage() == 1) ? ' disabled' : '' }}">
            <a class="page-link" aria-label="Previous" href="{{ $paginator->url(1) }}">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
            </a>
        </li>
        @php $start = 1; if($paginator->currentPage() > 2) $start = $paginator->currentPage(); @endphp
        @for ($i = $start; $i <= $start + 2; $i++)
            <li class="page-item {{ ($paginator->currentPage() == $i) ? ' active' : '' }}" style="margin-left: 5px;">
                <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
            </li>
        @endfor
        <li class="page-item {{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}" style="margin-left: 5px;">
            <a class="page-link" aria-label="Next" href="{{ $paginator->url($paginator->currentPage()+1) }}">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
            </a>
        </li>
    </ul>
</nav>
@endif