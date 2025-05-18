@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
                    <h2 class="text-2xl font-bold mb-6">Your Saved Medications</h2>

                    {{-- Flash Messages --}}
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('message'))
                        <div class="mb-4 p-4 bg-blue-100 text-blue-700 rounded">
                            {{ session('message') }}
                        </div>
                    @endif

                    {{-- Error Messages --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Medication Table --}}
                    @if ($userDrugs->isEmpty())
                        <p class="text-gray-600">You haven't added any drugs yet.</p>
                    @else
                        <table class="w-full table-auto border border-gray-300 rounded shadow-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-4 py-2 text-left">RxCUI</th>
                                    <th class="border px-4 py-2 text-left">Name</th>
                                    <th class="border px-4 py-2 text-left">Base Names</th>
                                    <th class="border px-4 py-2 text-left">Dose Form</th>
                                    <th class="border px-4 py-2 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($userDrugs as $drug)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-4 py-2">{{ $drug['rxcui'] }}</td>
                                        <td class="border px-4 py-2">{{ $drug['name'] }}</td>
                                        <td class="border px-4 py-2">
                                            @if (!empty($drug['baseNames']) && is_array($drug['baseNames']))
                                                {{ implode(', ', $drug['baseNames']) }}
                                            @else
                                                <em>N/A</em>
                                            @endif
                                        </td>
                                        <td class="border px-4 py-2">
                                            @if (!empty($drug['doseForms']) && is_array($drug['doseForms']))
                                                {{ implode(', ', $drug['doseForms']) }}
                                            @else
                                                <em>N/A</em>
                                            @endif
                                        </td>
                                        <td class="border px-4 py-2">
                                            <form action="{{ route('drugs.destroy', $drug['rxcui']) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this drug?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 hover:underline">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('drugs.create') }}" class="text-blue-600 hover:underline font-medium">
                            ➕ Add another drug
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
