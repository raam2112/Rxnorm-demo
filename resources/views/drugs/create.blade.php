@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                <h2 class="text-2xl font-semibold mb-6">➕ Add Medication</h2>

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('drugs.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="rxcui" class="block text-sm font-medium text-gray-700">RXCUI</label>
                        <input
                            type="text"
                            name="rxcui"
                            id="rxcui"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            value="{{ old('rxcui') }}"
                            required
                        >
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Add Medication
                        </button>
                        <a href="{{ route('drugs.index') }}" class="ml-4 text-gray-600 hover:underline">
                            ← Back to list
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
