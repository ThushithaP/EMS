<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DepartmentExport implements FromView {
    use Exportable;
    public $department;
    public function __construct($department) {
        $this->department = $department;
        
    }

    public function view(): View {
        return view('exports.departments', [
            // 'departments' => Department::all()
            'departments' => $this->department
        ]);
    }
    
}
