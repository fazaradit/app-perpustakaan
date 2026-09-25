@if ($paginator->hasPages())
    <div style="display: flex; align-items: center; gap: 6px; margin-top: 16px; flex-wrap: wrap;">

        {{-- Tombol Previous --}}
        @if ($paginator->onFirstPage())
            <span style="padding: 5px 12px; border: 1px solid #d1d5db; border-radius: 4px; color: #9ca3af; font-size: 14px;">&laquo; Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               style="padding: 5px 12px; border: 1px solid #d1d5db; border-radius: 4px; color: #2563eb; text-decoration: none; font-size: 14px;">
                &laquo; Sebelumnya
            </a>
        @endif

        {{-- Nomor Halaman --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="padding: 5px 10px; font-size: 14px; color: #6b7280;">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="padding: 5px 12px; border: 1px solid #2563eb; border-radius: 4px; background: #2563eb; color: #fff; font-size: 14px;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           style="padding: 5px 12px; border: 1px solid #d1d5db; border-radius: 4px; color: #2563eb; text-decoration: none; font-size: 14px;">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Tombol Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               style="padding: 5px 12px; border: 1px solid #d1d5db; border-radius: 4px; color: #2563eb; text-decoration: none; font-size: 14px;">
                Berikutnya &raquo;
            </a>
        @else
            <span style="padding: 5px 12px; border: 1px solid #d1d5db; border-radius: 4px; color: #9ca3af; font-size: 14px;">Berikutnya &raquo;</span>
        @endif

        <span style="font-size: 13px; color: #6b7280; margin-left: 8px;">
            Menampilkan {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
        </span>
    </div>
@endif
