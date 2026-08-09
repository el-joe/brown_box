<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SearchSuggestionRequest;
use App\Models\SearchSuggestion;
use App\Services\SearchSuggestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SearchSuggestionController extends Controller
{
    public function __construct(private readonly SearchSuggestionService $suggestions)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['keyword', 'is_active']);

        $suggestions = $this->suggestions->paginate($filters)->withQueryString();

        return view('admin.search-suggestions.index', [
            'suggestions' => $suggestions,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.search-suggestions.form', ['suggestion' => new SearchSuggestion()]);
    }

    public function store(SearchSuggestionRequest $request): RedirectResponse
    {
        $this->suggestions->create($this->mapData($request));

        return redirect()->route('admin.search-suggestions.index')->with('success', __('Suggestion created successfully.'));
    }

    public function edit(int $id): View
    {
        $suggestion = $this->suggestions->find($id);

        abort_if(! $suggestion, 404);

        return view('admin.search-suggestions.form', ['suggestion' => $suggestion]);
    }

    public function update(SearchSuggestionRequest $request, int $id): RedirectResponse
    {
        $this->suggestions->update($id, $this->mapData($request));

        return redirect()->route('admin.search-suggestions.index')->with('success', __('Suggestion updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->suggestions->delete($id);

        return redirect()->route('admin.search-suggestions.index')->with('success', __('Suggestion deleted successfully.'));
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:search_suggestions,id'],
            'action' => ['required', Rule::in(['enable', 'disable'])],
        ]);

        $this->suggestions->bulkSetActive($data['ids'], $request->string('action')->toString() === 'enable');

        return redirect()->route('admin.search-suggestions.index')->with('success', __('Suggestions updated successfully.'));
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $count = $this->suggestions->importFromCsv($request->file('file')->getRealPath());

        return redirect()->route('admin.search-suggestions.index')->with('success', __(':count keyword(s) imported.', ['count' => $count]));
    }

    private function mapData(SearchSuggestionRequest $request): array
    {
        $data = $request->safe()->only(['keyword', 'sort_order']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
