<!-- resources/views/vendor/pagination/custom.blade.php -->
@if ($paginator->hasPages())
    <div class="mt-6 flex flex-col md:flex-row justify-between items-center">
        <!-- Showing X to Y of Z entries -->
        <div class="text-sm text-gray-600 mb-4 md:mb-0">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} entries
        </div>

        <!-- Pagination Links -->
        <div class="flex justify-center items-center space-x-2">
            <!-- First Page -->
            <a href="{{ $paginator->url(1) }}"
               class="px-3 py-1 rounded-md border {{ $paginator->onFirstPage() ? 'text-gray-400 border-gray-300 cursor-not-allowed' : 'border-gray-300 hover:bg-gray-100' }}"
               {{ $paginator->onFirstPage() ? 'disabled' : '' }}>
                «
            </a>

            <!-- Previous Page -->
            <a href="{{ $paginator->previousPageUrl() }}"
               class="px-3 py-1 rounded-md border {{ $paginator->onFirstPage() ? 'text-gray-400 border-gray-300 cursor-not-allowed' : 'border-gray-300 hover:bg-gray-100' }}"
               {{ $paginator->onFirstPage() ? 'disabled' : '' }}>
                ‹
            </a>

            <!-- Page Numbers -->
            <div class="flex space-x-1">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="w-8 h-8 flex items-center justify-center">...</span>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <a href="{{ $url }}"
                               class="w-8 h-8 flex items-center justify-center rounded-md {{ $paginator->currentPage() == $page ? 'bg-red-600 text-white' : 'hover:bg-gray-100 border border-gray-300' }}">
                                {{ $page }}
                            </a>
                        @endforeach
                    @endif
                @endforeach
            </div>

            <!-- Next Page -->
            <a href="{{ $paginator->nextPageUrl() }}"
               class="px-3 py-1 rounded-md border {{ $paginator->hasMorePages() ? 'border-gray-300 hover:bg-gray-100' : 'text-gray-400 border-gray-300 cursor-not-allowed' }}"
               {{ $paginator->hasMorePages() ? '' : 'disabled' }}>
                ›
            </a>

            <!-- Last Page -->
            <a href="{{ $paginator->url($paginator->lastPage()) }}"
               class="px-3 py-1 rounded-md border {{ $paginator->currentPage() == $paginator->lastPage() ? 'text-gray-400 border-gray-300 cursor-not-allowed' : 'border-gray-300 hover:bg-gray-100' }}"
               {{ $paginator->currentPage() == $paginator->lastPage() ? 'disabled' : '' }}>
                »
            </a>
        </div>
    </div>
@endif
