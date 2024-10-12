<?php

namespace app\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

use App\Traits\ResponseTrait;

class ScheduleController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Schedule::with('supervisor')->get();

        return $this->success($data,'Schedule have been fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->all();

        $data = Schedule::create($inputs);

        return $this->success($data,'Schedule has been created successfully');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $inputs = $request->except('_method');
        $data_before = Schedule::find($id);
        $data_before->update($inputs);
        $data = Schedule::find($id);
        return $this->success($data, 'Schedule has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Schedule::where('id',$id)->delete();

        return $this->success([],'Schedule has been deleted successfully');
    }
}
