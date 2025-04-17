<x-app-layout title="Download P9 Form">
    <div class="container py-5">
        <h2 class="fw-bold text-dark mb-4">Download P9 Form</h2>
        <form action="{{ route('myaccount.p9', ['business' => $business->slug]) }}" method="GET">
            <div class="mb-3">
                <label for="year" class="form-label">Select Year</label>
                <select name="year" id="year" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Download P9</button>
        </form>
    </div>
</x-app-layout>