<?php

namespace App\Http\Controllers\Reporting;

use App\Actions\Reporting\AggregateOutcomes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reporting\AggregateReportRequest;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\Group;

#[Group('Reporting')]
class ReportingController extends Controller
{
    public function aggregate(AggregateReportRequest $request, AggregateOutcomes $action): JsonResponse
    {
        $report = $action($request->user(), $request->validated());

        return response()->json(['data' => $report]);
    }

    /**
     * Export the same aggregate report as a downloadable JSON file.
     *
     * NOTE: PDF rendering is intentionally not implemented yet.
     * To enable: `composer require barryvdh/laravel-dompdf` then return Pdf::loadView(...)->download(...).
     */
    public function export(AggregateReportRequest $request, AggregateOutcomes $action): JsonResponse
    {
        $report = $action($request->user(), $request->validated());

        $filename = 'report-'.now()->format('Y-m-d_His').'.json';

        return response()
            ->json(['data' => $report], 200, [], JSON_PRETTY_PRINT)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
