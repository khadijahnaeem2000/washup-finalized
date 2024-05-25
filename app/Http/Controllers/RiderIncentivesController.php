<?php

namespace App\Http\Controllers;
use DB;
use DataTables;
use Exception;
use App\Models\Rider_incentives;
use App\Models\Rider;
use Illuminate\Http\Request;

class RiderIncentivesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('rider_incentives.index');
    }


    public function list(){
         DB::statement(DB::raw('set @srno=0'));
        
         $data       = DB::table('rider_incentives')->orderBy('rider_incentives.name')
                        ->select(
                                'rider_incentives.id',
                                'rider_incentives.name',
                                'rider_incentives.pickup_rate',
                                'rider_incentives.pickdrop_rate',
                                'rider_incentives.kilometer',
                                'rider_incentives.status',
                                'rider_incentives.default_rider',
                                'rider_incentives.drop_rate'
                                //  DB::raw('@srno  := @srno  + 1 AS srno')
                                )

                        ->get();

         return 
            DataTables::of($data)
                ->addColumn('action',function($data){
                    return '
                    <div class="btn-group btn-group">
                        <a class="btn btn-secondary btn-sm" href="rider_incentives/'.$data->id.'">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="rider_incentives/'.$data->id.'/edit" id="'.$data->id.'">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. route('rider_incentives_delete') .'" data-id="'.$data->id.'">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['','action'])
                ->addIndexColumn()
                ->make(true);
       }


         public function updateStatus(Request $request)
    {
       
      // Find the service by its ID
       $rider_incentives = Rider_incentives::find($request->id);
      // Check if the incen$rider_incentives is found
      if ($rider_incentives) {
        // Update the status of the incen$rider_incentives
        $rider_incentives->status = $request->status;
        $rider_incentives->save(); // Save the changes to the database

     

        // Return a JSON response indicating success
        return response()->json(['success' => true]);
      }   else {
        // Return a JSON response indicating failure with a message
        return response()->json(['success' => false, 'message' => 'Service not found.']);
      }
    }
    
 public function updateDefault(Request $request)
{
    // Find the rider incentive by ID
    $riderIncentive = Rider_incentives::find($request->id);
    
    if ($riderIncentive) {
        // Set the rider incentive ID to all riders
        $riders = Rider::all();
        foreach ($riders as $rider) {
            $rider->rider_incentives = $riderIncentive->id;
            $rider->save();
        }
        
        // Update default_rider in riderincentive table to 1 for the specified rider incentive
        $riderIncentive->default_rider = 1;
        $riderIncentive->save();
        
        // Set default_rider to 0 for all other rider incentives
        Rider_incentives::where('id', '!=', $riderIncentive->id)->update(['default_rider' => 0]);
       
        // Return a JSON response indicating success
        return response()->json(['success' => true]);
    } else {
        // Return a JSON response indicating failure with a message
        return response()->json(['success' => false, 'message' => 'Rider incentive not found.']);
    }
}



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rider_incentives.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
          $data           =Rider_incentives::create($request->all());
          $data->save();
          return redirect()->route('rider_incentives.index')
                            ->with('success',''.$request['name']. ' added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rider_incentives  $rider_incentives
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         $data               = DB::table('rider_incentives')
                                ->where('rider_incentives.id', $id)
                                ->first();
             return view('rider_incentives.show',
                    compact('data')
                    );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rider_incentives  $rider_incentives
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data                   = Rider_incentives::findOrFail($id);  
        return 
            view('rider_incentives.edit',
            compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rider_incentives  $rider_incentives
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data               = Rider_incentives::find($id);
        $upd             = $data->update($request->all());
         return redirect()->route('rider_incentives.index')
                ->with('success',''.$request['name']. ' updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rider_incentives  $rider_incentives
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id         =   $request->ids;
        $customer   =   Rider_incentives::find($id);
        $customer->delete();
       return response()->json(['success' => $customer->name . " Rider Incentive deleted successfully."]);

    }
}
