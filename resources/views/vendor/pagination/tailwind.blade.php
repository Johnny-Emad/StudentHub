@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation"
         class="flex items-center justify-between gap-4">

        {{-- Summary --}}
        <p class="hidden sm:block text-xs text-gray-500 whitespace-nowrap">
            Showing
            <span class="font-semibold text-gray-700">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-semibold text-gray-700">{{ $paginator->lastItem() }}</span>
            of
            <span class="font-semibold text-gray-700">{{ $paginator->total() }}</span>
            results
        </p>

        <div class="flex items-center gap-1 ml-auto">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="Previous">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-md text-gray-300 cursor-not-allowed"
                          aria-hidden="true">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                        </svg>
                    </span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous"
                   class="inline-flex items-center justify-center w-8 h-8 rounded-md text-gray-500 border border-gray-200 bg-white hover:bg-gray-50 hover:text-gray-700 hover:border-gray-300 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </a>
            @endif

            {{-- Page numbers --}}
            <div class="hidden sm:flex items-center gap-1">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span aria-disabled="true">
                            <span class="inline-flex items-center justify-center w-8 h-8 text-xs text-gray-400 select-none">{{ $element }}</span>
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-md text-sm font-semibold bg-indigo-700 text-white shadow-sm cursor-default">
                                        {{ $page }}
                                    </span>
                                </span>
                            @else
                                <a href="{{ $url }}" aria-label="Go to page {{ $page }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-md text-sm font-medium text-gray-600 border border-gray-200 bg-white hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Mobile: current / total --}}
            <span class="sm:hidden inline-flex items-center px-3 h-8 rounded-md text-xs font-medium text-gray-600 bg-gray-100 ring-1 ring-gray-200">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next"
                   class="inline-flex items-center justify-center w-8 h-8 rounded-md text-gray-500 border border-gray-200 bg-white hover:bg-gray-50 hover:text-gray-700 hover:border-gray-300 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="Next">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-md text-gray-300 cursor-not-allowed"
                          aria-hidden="true">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                        </svg>
                    </span>
                </span>
            @endif
        </div>
    </nav>
@endif
