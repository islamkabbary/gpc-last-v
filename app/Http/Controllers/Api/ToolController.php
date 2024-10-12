<?php

namespace App\Http\Controllers\Api;

use App\Models\Tool;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;

class ToolController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Tool::get();

        return $this->success($data, 'Tool have been fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->all();

        $data = Tool::create($inputs);

        return $this->success($data, 'Tool has been created successfully');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $inputs = $request->except('_method');
        $data_before = Tool::find($id);
        $data_before->update($inputs);
        $data = Tool::find($id);
        return $this->success($data, 'Tool has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Tool::where('id', $id)->delete();

        return $this->success([], 'Tool has been deleted successfully');
    }
}
