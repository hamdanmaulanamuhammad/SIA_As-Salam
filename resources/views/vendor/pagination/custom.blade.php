@if ($paginator->hasPages())
    <div class="mt-6 flex flex-col md:flex-row justify-between items-center">
        <div class="text-sm text-gray-600 mb-4 md:mb-0">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} entries
        </div>

        <div class="flex justify-center items-center space-x-2">
            <a href="{{ $paginator->url(1) }}"
               class="px-3 py-1 rounded-md border {{ $paginator->onFirstPage() ? 'text-gray-400 border-gray-300 cursor-not-allowed' : 'border-blue-300 hover:bg-blue-50 text-blue-600' }}"
               {{ $paginator->onFirstPage() ? 'disabled' : '' }}>
                «
            </a>

            <a href="{{ $paginator->previousPageUrl() }}"
               class="px-3 py-1 rounded-md border {{ $paginator->onFirstPage() ? 'text-gray-400 border-gray-300 cursor-not-allowed' : 'border-blue-300 hover:bg-blue-50 text-blue-600' }}"
               {{ $paginator->onFirstPage() ? 'disabled' : '' }}>
                ‹
            </a>

            <div class="flex space-x-1">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="w-8 h-8 flex items-center justify-center text-gray-500">...</span>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <a href="{{ $url }}"
                               class="w-8 h-8 flex items-center justify-center rounded-md {{ $paginator->currentPage() == $page ? 'bg-blue-600 text-white' : 'hover:bg-blue-50 border border-blue-300 text-blue-600' }}">
                                {{ $page }}
                            </a>
                        @endforeach
                    @endif
                @endforeach
            </div>

            <a href="{{ $paginator->nextPageUrl() }}"
               class="px-3 py-1 rounded-md border {{ $paginator->hasMorePages() ? 'border-blue-300 hover:bg-blue-50 text-blue-600' : 'text-gray-400 border-gray-300 cursor-not-allowed' }}"
               {{ $paginator->hasMorePages() ? '' : 'disabled' }}>
                ›
            </a>

            <a href="{{ $paginator->url($paginator->lastPage()) }}"
               class="px-3 py-1 rounded-md border {{ $paginator->currentPage() == $paginator->lastPage() ? 'text-gray-400 border-gray-300 cursor-not-allowed' : 'border-blue-300 hover:bg-blue-50 text-blue-600' }}"
               {{ $paginator->currentPage() == $paginator->lastPage() ? 'disabled' : '' }}>
                »
            </a>
        </div>
    </div>
@endif
