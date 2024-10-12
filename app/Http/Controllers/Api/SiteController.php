<?php

namespace app\Http\Controllers\Api;


use App\Models\Site;
use App\Models\Unit;
use App\Models\SiteNote;
use App\Models\SiteValue;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;

class SiteController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Unit::get();

        return $this->success(UnitResource::collection($data),'Site have been fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $inputs = $request->except('notes','values');


    //     $data = Site::create($inputs);

    //     if (isset($request->notes)) {
    //         foreach ($request->notes as $key => $note) {
    //             SiteNote::create([
    //                 'site_id' => $data->id,
    //                 'note' => $note,
    //             ]);
    //         }
    //     }

    //     if (isset($request->values)) {
    //         foreach ($request->values as $key => $value) {
    //             SiteValue::create([
    //                 'site_id' => $data->id,
    //                 'value' => $value['value'],
    //                 'key_value' => $value['key_value'],
    //             ]);
    //         }
    //     }
    //     $dataLoad = Site::with(['values','notes'])->find($data->id);

    //     return $this->success($dataLoad,'Site has been created successfully');
    // }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $inputs = $request->except('_method');
        $data_before = Site::find($id);
        $data_before->update($inputs);
        if (isset($request->notes)) {
            SiteNote::where('service_id',$id)->delete();
            foreach ($request->notes as $key => $note) {
                SiteNote::create([
                    'site_id' => $data_before->id,
                    'note' => $note,
                ]);
            }
        }

        if (isset($request->values)) {
            SiteValue::where('service_id',$id)->delete();
            foreach ($request->values as $key => $value) {
                SiteValue::create([
                    'site_id' => $data_before->id,
                    'value' => $value['value'],
                    'key_value' => $value['key_value'],
                ]);
            }
        }
        $data = Site::find($id);
        return $this->success($data, 'Site has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Site::where('id',$id)->delete();

        return $this->success([],'Site has been deleted successfully');
    }
}
