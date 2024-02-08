<?php

namespace App\Exports;

use App\Models\Leave;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LeaveExport implements FromView {
    public $leaves;

    public function __construct($leaves) {
        $this->leaves = $leaves;
    }
    public function view(): View {
        return view('exports.leave', [
            'leaves' => $this->leaves
        ]);
    }
}
