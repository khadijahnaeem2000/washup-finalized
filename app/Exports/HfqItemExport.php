<?php

namespace App\Exports;

use DB;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HfqItemExport implements FromCollection,  WithHeadings
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return ($this->data);
    }

    public function headings(): array
    {
        return [
            'Packing date',
            'Order#',
            'HFQ Order#',
            'Customer name',
            'Item',
            'Qty',
            'Wash house'
        ];
    }
}
