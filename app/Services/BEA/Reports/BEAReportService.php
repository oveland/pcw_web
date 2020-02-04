<?php


namespace App\Services\BEA\Reports;


use Illuminate\Support\Collection;

class BEAReportService
{
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
    public function mergeMarks($marksGroup)
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
    public function mergeDetails($detailGroup)
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
    public function mergeTotals($totalsGroup)
    {
        $totals = collect([]);
        foreach ($totalsGroup->first() as $key => $value) {
            $totals->put($key, $totalsGroup->sum($key));
        }
        return (object)$totals->toArray();
    }
}