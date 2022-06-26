<?php


namespace App\Services\LM\Reports;


use App\Exports\LM\DailyReportExport;
use App\Models\LM\Mark;
use App\Models\LM\Turn;
use App\Models\Company\Company;
use Auth;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use function collect;

class LMReportService
{
    protected $dbId;

    public function __construct($dbId = 1)
    {
        $this->dbId = $dbId ?? 1;
    }

    /**
     * @param Company $company
     * @param $date
     * @return object
     */
    public function buildConsolidatedDailyReport(Company $company, $date)
    {
        $vehicles = $company->vehicles;

        $vehicleTurns = Turn::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->where('db_id', $this->dbId)
            ->get();

        $marks = Mark::enabled()
            ->where('db_id', $this->dbId)
            ->whereIn('turn_id', $vehicleTurns->pluck('id'))
            ->where('liquidated', true)
            //->where('taken', false)
            ->whereDate('date', $date)
            ->with(['turn.vehicle', 'turn.route', 'turn.vehicle', 'turn.driver', 'trajectory', 'liquidation'])
            ->orderBy('initial_time')
            //->limit(5)
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
            $path = "LM/$file->fileName";
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
     * @param Collection $liquidations
     * @return Collection
     */
    public function buildMainReport($liquidations)
    {
        if ($liquidations->isEmpty()) {
            return collect([
                'empty' => true
            ]);
        }

        $marksGroup = $liquidations->pluck('marks', 'id');
        $totalsGroup = $liquidations->pluck('totals', 'id');
        $detailGroup = $liquidations->pluck('liquidation', 'id');

        $marksGroup = $marksGroup->map(function ($marks, $liquidationId) use ($liquidations) {
            $marks = collect($marks);
            $totalMarks = $marks->count();
            $liquidation = collect($liquidations)->where('id', $liquidationId)->first();
            return $marks->map(function ($mark, $index) use ($totalMarks, $liquidation) {
                $pendingBalance = 0;
                $realTaken = 0;
                if ($totalMarks == $index + 1) {
                    $pendingBalance = $liquidation->liquidation->pendingBalance;
                    $realTaken = $liquidation->liquidation->realTaken;
                }
                $mark->pendingBalance = $pendingBalance;
                $mark->realTaken = $realTaken;
                return $mark;
            });
        });

        $totalsGroup = $totalsGroup->map(function ($totals, $liquidationId) use ($liquidations) {
            $totals = collect($totals);

            $liquidation = collect($liquidations)->where('id', $liquidationId)->first();
            $pendingBalance = $liquidation->liquidation->pendingBalance;
            $realTaken = $liquidation->liquidation->realTaken;

            $totals->put('pendingBalance', $pendingBalance);
            $totals->put('realTaken', $realTaken);

            return $totals;
        });

        $totals = $this->mergeTotals($totalsGroup);
        $totals->pendingBalance = $totals->balance - $totals->realTaken;

        return collect([
            'marks' => $this->mergeMarks($marksGroup),
            'totals' => $totals,
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

        return $marks->sortBy('dateTime')->values();
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