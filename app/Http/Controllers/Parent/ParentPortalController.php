<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Support\ParentPortalData;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ParentPortalController extends Controller
{
    public function __construct(
        private readonly ParentPortalData $portalData
    ) {
    }

    public function dashboard(Request $request)
    {
        return view('parent.parentdashboard', $this->buildViewData($request));
    }

    public function children(Request $request)
    {
        return view('parent.children', $this->buildViewData($request));
    }

    public function attendance(Request $request)
    {
        return view('parent.attendance', $this->buildViewData($request));
    }

    public function results(Request $request)
    {
        return view('parent.results', $this->buildViewData($request));
    }

    public function courses(Request $request)
    {
        return view('parent.courses', $this->buildViewData($request));
    }

    public function notices(Request $request)
    {
        return view('parent.notices', $this->buildViewData($request));
    }

    public function communication(Request $request)
    {
        return view('parent.communication', $this->buildViewData($request));
    }

    public function events(Request $request)
    {
        return view('parent.events', $this->buildViewData($request));
    }

    public function print(Request $request)
    {
        return view('parent.print', $this->buildViewData($request));
    }

    private function buildViewData(Request $request): array
    {
        $requestedChildId = $request->integer('child');

        $data = $this->portalData->build($request->user(), [
            'selected_child_id' => $requestedChildId,
            'section' => $request->query('section'),
        ]);

        $selectedChild = $this->resolveSelectedChild($data['children'], $requestedChildId);

        return array_merge($data, [
            'selectedChild' => $selectedChild,
            'selectedChildId' => $selectedChild['id'] ?? null,
        ]);
    }

    private function resolveSelectedChild(Collection $children, ?int $selectedChildId): ?array
    {
        if ($children->isEmpty()) {
            return null;
        }

        if ($selectedChildId) {
            $selected = $children->firstWhere('id', $selectedChildId);

            if ($selected) {
                return $selected;
            }
        }

        return $children->first();
    }
}
