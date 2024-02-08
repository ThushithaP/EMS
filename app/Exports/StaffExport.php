<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class StaffExport implements FromView {
    use Exportable;

    public $staff;
    public function __construct($staff) {
        $this->staff = $staff;
    }

    public function view(): View {
        return view('exports.staff', [
            'staff' => $this->staff
        ]);
    }
}
