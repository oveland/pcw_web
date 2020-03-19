<?php


namespace App\Services\BEA\Reports;


use App\Exports\BEA\DailyReportExport;
use App\Models\BEA\Mark;
use App\Models\BEA\Turn;
use App\Models\Company\Company;
use Exception;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BEAReportService
{
    /**
     * @param Company $company
     * @param $date
     * @return object
     */
    public function buildConsolidatedDailyReport(Company $company, $date)
    {
        $vehicles = $company->vehicles;

        $vehicleTurns = Turn::whereIn('vehicle_id', $vehicles->pluck('id'))->get();

        $marks = Mark::enabled()
            ->whereIn('turn_id', $vehicleTurns->pluck('id'))
            ->where('liquidated', true)
            //->where('taken', false)
            ->whereDate('date', $date)
            ->with(['turn.vehicle', 'turn.route', 'turn.vehicle', 'turn.driver', 'trajectory', 'liquidation'])
            ->orderBy('initial_time')
            ->get();

        $data = collect([]);
        foreach ($marks as $index => $mark) {
            $liquidation = $mark->liquidation;
            $liquidationDetails = $liquidation->liquidation;
            $liquidationTurns = collect($liquidationDetails->byTurns);
            $liquidationTurn = (object)$liquidationTurns->where('markId', $mark->id)->first();

            $data->push((object)[
                'mark' => $mark->getAPIFields(),
                'liquidationDetails' => $liquidationDetails,
                'liquidationTurn' => $liquidationTurn,
                'liquidation' => $liquidation->toArray(),
            ]);
        }

        return (object)[
            'date' => $date,
            'company' => $company,
            'data' => $data
        ];
    }

    /**
     * @param $report
     * @param bool $store
     * @return Response|BinaryFileResponse
     * @throws Exception
     */
    public function exportConsolidatedDailyReport($report, $store = false)
    {
        $file = new DailyReportExport($report);

        if ($store) {
            $path = "BEA/$file->fileName";
            Excel::store($file, $path);
            return collect()->push($path);
        }

        return $file->download();
    }

    /**
     * @param Collection $liquidations
     * @return Collection
     */
    public function buildDailyReport($liquidations)
    {
        if ($liquidations->isEmpty()) {
            return collect([
                'empty' => true
            ]);
        }

        $marksGroup = $liquidations->pluck('marks');
        $totalsGroup = $liquidations->pluck('totals');
        $detailGroup = $liquidations->pluck('liquidation');

        return collect([
            'marks' => $this->mergeMarks($marksGroup),
            'totals' => $this->mergeTotals($totalsGroup),
            'details' => $this->mergeDetails($detailGroup),
            'liquidations' => $liquidations->values()
        ]);
    }

    /**
     * @param Collection $marksGroup
     * @return object
     */
    private function mergeMarks($marksGroup)
    {
        $marks = collect([]);

        foreach ($marksGroup as $marksList) {
            foreach ($marksList as $mark) {
                $marks->push($mark);
            }
        }

        return $marks->sortBy('number')->values();
    }

    /**
     * @param Collection $detailGroup
     * @return object
     */
    private function mergeDetails($detailGroup)
    {
        $details = collect([]);

        foreach ($detailGroup->first() as $key => $detailList) {
            if (is_array($detailList)) {
                $data = collect([]);
                $keyList = $detailGroup->pluck($key);
                foreach ($keyList as $list) {
                    foreach ($list as $detail) {
                        $data->push($detail);
                    }
                }

                $details->put($key, $data);
            }
        }

        return $details;
    }

    /**
     * @param Collection $totalsGroup
     * @return object
     */
    private function mergeTotals($totalsGroup)
    {
        $totals = collect([]);
        foreach ($totalsGroup->first() as $key => $value) {
            $totals->put($key, $totalsGroup->sum($key));
        }
        return (object)$totals->toArray();
    }
}