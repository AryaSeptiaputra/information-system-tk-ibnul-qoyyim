<!-- Pagination Controls -->
<div class="admin-pagination-wrapper">
    <!-- Left: Items Per Page Selector -->
    <div class="admin-items-per-page">
        <form method="GET" class="admin-per-page-form">
            <!-- Preserve current filters -->
            @if($search ?? null)
                <input type="hidden" name="search" value="{{ $search }}">
            @endif
            @if($role ?? null)
                <input type="hidden" name="role" value="{{ $role }}">
            @endif
            @if($status ?? null)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            @if($group ?? null)
                <input type="hidden" name="group" value="{{ $group }}">
            @endif
            @if($gender ?? null)
                <input type="hidden" name="gender" value="{{ $gender }}">
            @endif
            @if($contact ?? null)
                <input type="hidden" name="contact" value="{{ $contact }}">
            @endif
            @if($date_from ?? null)
                <input type="hidden" name="date_from" value="{{ $date_from }}">
            @endif
            @if($date_to ?? null)
                <input type="hidden" name="date_to" value="{{ $date_to }}">
            @endif
            @if($payment_type ?? null)
                <input type="hidden" name="payment_type" value="{{ $payment_type }}">
            @endif
            @if($period_mode ?? null)
                <input type="hidden" name="period_mode" value="{{ $period_mode }}">
            @endif
            @if($late ?? null)
                <input type="hidden" name="late" value="{{ $late }}">
            @endif
            @if($month ?? null)
                <input type="hidden" name="month" value="{{ $month }}">
            @endif
            @if($year ?? null)
                <input type="hidden" name="year" value="{{ $year }}">
            @endif
            @if($school_year ?? null)
                <input type="hidden" name="school_year" value="{{ $school_year }}">
            @endif
            @if($id_class ?? null)
                <input type="hidden" name="id_class" value="{{ $id_class }}">
            @endif
            @if($id_payment ?? null)
                <input type="hidden" name="id_payment" value="{{ $id_payment }}">
            @endif
            
            <label for="admin-per-page">Tampilkan:</label>
            <select name="per_page" id="admin-per-page" class="admin-per-page-select">
                <option value="10" @selected($per_page == 10)>10</option>
                <option value="25" @selected($per_page == 25)>25</option>
                <option value="50" @selected($per_page == 50)>50</option>
                <option value="100" @selected($per_page == 100)>100</option>
            </select>
            <span>item per halaman</span>
        </form>
    </div>

    <!-- Center: Info Text -->
    <div class="admin-pagination-info">
        Menampilkan <strong>{{ $items->firstItem() ?? 0 }}</strong> - <strong>{{ $items->lastItem() ?? 0 }}</strong> dari <strong>{{ $items->total() }}</strong> item
    </div>

    <!-- Right: Pagination Links -->
    <div class="admin-pagination-links">
        @if($items->onFirstPage())
            <span class="admin-pagination-btn admin-pagination-btn-disabled">← Sebelumnya</span>
        @else
            <a href="{{ $items->previousPageUrl() }}" class="admin-pagination-btn">← Sebelumnya</a>
        @endif

        <!-- Page Numbers -->
        <div class="admin-pagination-numbers">
            @foreach($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                @if($page == $items->currentPage())
                    <span class="admin-pagination-number admin-pagination-number-active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="admin-pagination-number">{{ $page }}</a>
                @endif
            @endforeach
        </div>

        @if($items->hasMorePages())
            <a href="{{ $items->nextPageUrl() }}" class="admin-pagination-btn">Berikutnya →</a>
        @else
            <span class="admin-pagination-btn admin-pagination-btn-disabled">Berikutnya →</span>
        @endif
    </div>
</div>
