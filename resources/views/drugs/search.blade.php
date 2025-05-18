@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                <h2 class="text-2xl font-bold mb-6">🔍 Drug Search</h2>

                {{-- Flash/Error Message --}}
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Search Form --}}
                <form action="{{ route('drug.search.results') }}" method="GET" class="mb-6">
                    <div>
                        <label for="drug_name" class="block text-sm font-medium text-gray-700">Enter Drug Name</label>
                        <input type="text" name="drug_name" id="drug_name"
                            class="mt-1 w-full border border-gray-300 rounded-md shadow-sm p-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            value="{{ old('drug_name', $drug_name ?? '') }}" required>
                    </div>
                    <button type="submit"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700">
                        Search
                    </button>
                </form>

                {{-- Search Results --}}
                @if (isset($searched) && collect($results)->isEmpty())
                    <div class="p-4 bg-yellow-100 text-yellow-800 rounded">
                        No drugs found for <strong>{{ $drug_name }}</strong>.
                    </div>
                @elseif (isset($results) && collect($results)->isNotEmpty())
                    <h3 class="text-xl font-semibold mb-4">Search Results</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left border border-gray-300 rounded shadow-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-4 py-2">RxCUI</th>
                                    <th class="border px-4 py-2">Name</th>
                                    <th class="border px-4 py-2">Base Names</th>
                                    <th class="border px-4 py-2">Dose Form</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @foreach ($results as $drug)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-4 py-2">{{ $drug['rxcui'] }}</td>
                                        <td class="border px-4 py-2">{{ $drug['name'] }}</td>
                                        <td class="border px-4 py-2">{{ implode(', ', $drug['base_names']) ?: 'N/A' }}</td>
                                        <td class="border px-4 py-2">{{ $drug['dose_form_group'] ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
