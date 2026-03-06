<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl h2 font-bold text-gray-800">My CVs</h3>
                <a href="{{ route('cv.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    + Create New CV
                </a>
            </div>

            @if($cvs->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-10 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No CVs yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new template.</p>
                        <div class="mt-6">
                            <a href="{{ route('cv.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Create New CV
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($cvs as $cv)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 hover:shadow-md transition">
                            <!-- Placeholder thumbnail if no template thumbnail exist -->
                            <div class="h-48 bg-gray-100 border-b relative">
                                @if($cv->template && $cv->template->thumbnail)
                                    <img src="{{ $cv->template->thumbnail }}" alt="{{ $cv->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                
                                @if($cv->is_draft)
                                    <span class="absolute top-2 right-2 bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-semibold">Draft</span>
                                @endif
                            </div>
                            <div class="p-6">
                                <h4 class="font-bold text-lg text-gray-900 truncate" title="{{ $cv->title }}">{{ $cv->title }}</h4>
                                <p class="text-sm text-gray-500 mb-4 mt-1">Updated {{ $cv->updated_at->diffForHumans() }}</p>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('cv.edit', $cv) }}" class="flex-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium text-center transition">Edit</a>
                                    
                                    <form action="{{ route('cv.destroy', $cv) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this CV?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-white border border-red-300 text-red-600 hover:bg-red-50 px-3 py-2 rounded-md text-sm font-medium transition" title="Delete">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
