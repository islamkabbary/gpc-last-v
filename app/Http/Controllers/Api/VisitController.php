<?php

namespace app\Http\Controllers\Api;


use App\Helper\UploadFile;
use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitImage;
use App\Models\VisitSite;
use App\Models\VisitTool;
use App\Models\VisitToolUser;
use App\Models\VisitMaterial;
use Illuminate\Http\Request;

use App\Traits\ResponseTrait;

class VisitController extends Controller
{
    use ResponseTrait;
    use UploadFile;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Visit::with(['service', 'team','materials.material','tools.tool','tools.users.user','sites.site','images'])->get();

        return $this->success($data, 'Visit have been fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->except('sites', 'materials','tools');

        $inputs['status'] = 'Pending';

        $data = Visit::create($inputs);

        if (isset($request->materials)) {
            foreach ($request->materials as $key => $material) {
                $mate = Material::find($material['id']);
                VisitMaterial::create([
                    'visit_id' => $data->id,
                    'material_id' => $material['id'],
                    'value' => $material['value'],
                    'price' => $material['value'] * $mate->price,
                ]);
            }
        }

        if (isset($request->sites)) {
            foreach ($request->sites as $key => $site) {
                VisitSite::create([
                    'visit_id' => $data->id,
                    'site_id' => $site,
                ]);
            }
        }

        if (isset($request->tools)) {
            foreach ($request->tools as $key => $tool) {
                $visit_tool = VisitTool::create([
                    'visit_id' => $data->id,
                    'tool_id' => $tool,
                ]);

                $users = User::where('team_id', $data->team_id)->get();
                foreach ($users as $key => $user) {
                    VisitToolUser::create([
                        'user_id' => $user->id,
                        'visit_tool_id' => $visit_tool->id,
                        'status'=> 0,
                    ]);
                }
            }
        }

        if (isset($request->images)) {

            foreach ($request->images as $key => $image) {


                $file = $this->uploadFile($image, '/visit/images', 'yes', "-visit-$data->id");

                VisitImage::create([
                    'visit_id'=> $data->id,
                    'image'=> $file,
                ]);
            }

           

        }
        $dataLoad = Visit::with(['service', 'team'])->find($data->id);

        return $this->success($dataLoad, 'Visit has been created successfully');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $inputs = $request->except('_method','sites', 'materials','tools');
        $data_before = Visit::find($id);
        $data_before->update($inputs);
        if (isset($request->materials)) {
            VisitMaterial::where('visit_id', $id)->delete();
            foreach ($request->materials as $key => $material) {
                $mate = Material::find($material['id']);
                VisitMaterial::create([
                    'visit_id' => $data_before->id,
                    'material_id' => $material['id'],
                    'value' => $material['value'],
                    'price' => $material['value'] * $mate->price,
                ]);
            }
        }

        if (isset($request->sites)) {
            VisitSite::where('visit_id', $id)->delete();
            foreach ($request->sites as $key => $site) {
                VisitSite::create([
                    'visit_id' => $data_before->id,
                    'site_id' => $site,
                ]);
            }
        }

        if (isset($request->tools)) {
            foreach ($request->tools as $key => $tool) {
                VisitTool::where('visit_id', $id)->delete();
                $visit_tool = VisitTool::create([
                    'visit_id' => $data_before->id,
                    'tool_id' => $tool,
                ]);

                $users = User::where('team_id', $data_before->team_id)->get();
                foreach ($users as $key => $user) {
                    VisitToolUser::create([
                        'user_id' => $user->id,
                        'visit_tool_id' => $visit_tool->id,
                        'status'=> 0,
                    ]);
                }
            }
        }
       
        $data = Visit::with(['service', 'team'])->find($id);
        return $this->success($data, 'Visit has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Visit::where('id', $id)->delete();

        return $this->success([], 'Visit has been deleted successfully');
    }
}
