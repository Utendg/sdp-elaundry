<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceItemController extends Controller
{
    /** Show the price list. */
    public function index(): View
    {
        return view('admin.service-items.index', [
            'items' => ServiceItem::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    /** Add a new priced item. */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        ServiceItem::create($data);

        return back()->with('status', 'Item added to the price list.');
    }

    /** Update an item's name, service or price. */
    public function update(Request $request, ServiceItem $serviceItem): RedirectResponse
    {
        $data = $this->validated($request);
        $serviceItem->update($data);

        return back()->with('status', 'Item updated.');
    }

    /** Toggle whether the item is offered. */
    public function toggle(ServiceItem $serviceItem): RedirectResponse
    {
        $serviceItem->update(['is_active' => ! $serviceItem->is_active]);

        return back()->with('status', 'Item availability updated.');
    }

    /** Remove an item from the price list. */
    public function destroy(ServiceItem $serviceItem): RedirectResponse
    {
        $serviceItem->delete();

        return back()->with('status', 'Item removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'service' => ['required', Rule::in(['wash', 'iron', 'wash_iron', 'dry_clean'])],
            'unit_price' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
