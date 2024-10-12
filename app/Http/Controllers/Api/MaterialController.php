<?php

namespace app\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

use App\Traits\ResponseTrait;

class MaterialController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Material::get();

        return $this->success($data,'Material have been fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->all();

        $data = Material::create($inputs);

        return $this->success($data,'Material has been created successfully');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $inputs = $request->except('_method');
        $data_before = Material::find($id);
        $data_before->update($inputs);
        $data = Material::find($id);
        return $this->success($data, 'Material has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Material::where('id',$id)->delete();

        return $this->success([],'Material has been deleted successfully');
    }
}
