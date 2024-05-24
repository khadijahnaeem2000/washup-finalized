<?php


namespace App\Http\Controllers;

use DB;
use DataTables;
// use App\Models\Addon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class Customer_has_retainerController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:retainer-list', ['only' => ['index','show']]);
         $this->middleware('permission:retainer-create', ['only' => ['create','store']]);
         $this->middleware('permission:retainer-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:retainer-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return view('customer_has_retainer.index');
    }

    public function list()
    {
        DB::statement(DB::raw('set @srno=0'));
        $data       = DB::table('customer_has_retainer_days')
                        ->leftjoin('customers', 'customers.id', '=', 'customer_has_retainer_days.customer_id')
                        ->leftjoin('customer_types', 'customer_types.id', '=', 'customers.customer_type_id')
                        ->leftjoin('days', 'days.id', '=', 'customer_has_retainer_days.day_id')
                        ->leftjoin('time_slots', 'time_slots.id', '=', 'customer_has_retainer_days.time_slot_id')
                        ->select(
                                'customers.id',
                                'customers.name as customer_name',
                                'customer_types.name as customer_type',
                                'customers.contact_no',
                                'days.name as day_name',
                                 DB::raw('CONCAT(time_slots.start_time,  "  -  ", time_slots.end_time) as timeslot_name'),
                                'customer_has_retainer_days.note as note',
                                 DB::raw('@srno  := @srno  + 1 AS srno')
                                )
                        ->get();

        return 
            DataTables::of($data)
                ->make(true);

    }

    public function create()
    {}

    public function store(Request $request)
    {}

     public function show($id)
    {}


    public function edit($id)
    {}


    public function update(Request $request, $id)
    {}

    public function destroy(Request $request)
    {}




}
