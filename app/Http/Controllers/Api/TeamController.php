<?php

namespace app\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

use App\Traits\ResponseTrait;

class TeamController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Team::with('leader')->get();

        return $this->success($data,'Team have been fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->all();

        $data = Team::create($inputs);

        return $this->success($data,'Team has been created successfully');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $inputs = $request->except('_method');
        $data_before = Team::find($id);
        $data_before->update($inputs);
        $data = Team::find($id);
        return $this->success($data, 'Team has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Team::where('id',$id)->delete();

        return $this->success([],'Team has been deleted successfully');
    }
}
