<?php

namespace app\Http\Controllers\Api;

use App\Models\Visit;
use App\Models\Client;
use App\Models\Service;
use App\Models\VisitImage;
use Illuminate\Support\Arr;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Http\Resources\ServiceResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ServiceUpdateRequest;

class ServiceController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Service::with(['visits', 'contracts', 'quotations'])->get();
        return $this->success(ServiceResource::collection($data), 'Service have been fetched successfully');
    }

    public function show($id)
    {
        $service = Service::with(['visits', 'contracts', 'quotations'])->where('id', $id)->first();

        if (!$service) {
            return $this->error('Service not found', 404);
        }
        return $this->success(new ServiceResource($service), 'Service have been fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request)
    {
        try {
            // Start a database transaction to ensure atomicity
            DB::transaction(function () use ($request, &$responses) {
                $servicesData = $request->input('services');
                $responses = [];

                // Iterate through each service data
                foreach ($servicesData as $serviceData) {
                    // Create a new service record
                    $service = Service::create([
                        'type_of_injury' => $serviceData['type_of_injury'],
                        'client_id' => $serviceData['client_id'],
                        'level_injury' => $serviceData['level_injury'],
                        'description' => $serviceData['description']
                    ]);

                    // Increment the client's number of services if client exists
                    $client = Client::find($serviceData['client_id']);
                    if ($client) {
                        $client->increment('number_of_services');
                    }

                    // Iterate through visits associated with the service
                    foreach ($serviceData['visits'] as $visitData) {
                        // Add the service ID to the visit data
                        $visitData['service_id'] = $service->id;

                        // Create a new visit record
                        $visit = Visit::create([
                            'service_id' => $service->id,
                            'unit_id' => $visitData['unit_id'],
                            'team_id' => $visitData['team_id'],
                            'date' => $visitData['date'],
                            'time' => $visitData['time'],
                            'description' => $visitData['description'] ?? null // Default to null if not provided
                        ]);

                        // Attach tools to the visit with quantities and costs
                        foreach ($visitData['tools'] as $tool) {
                            $visit->tools()->attach($tool['tool_id'], [
                                'quantity' => $tool['quantity'],
                                'cost' => $tool['cost'],
                            ]);
                        }

                        // Handle media uploads for images and sketches
                        $this->handleMedia($request, $visit->id, 'images', 'images/visits');
                        $this->handleMedia($request, $visit->id, 'sketch', 'images/visits');
                    }

                    // Prepare the response resource for the created service
                    $responses[] = new ServiceResource($service->load('visits'));
                }
            });

            // Return a success response with created services
            return $this->success(['services' => $responses], 'Services have been created successfully');
        } catch (\Throwable $th) {
            // Log the error and return a failure response
            Log::error($th->getMessage());
            return $this->error('Error creating Services');
        }
    }

    private function handleMedia($request, $visitId, $type, $directory)
    {
        if ($request->file($type)) {
            foreach ($request->file($type) as $file) {
                $fileName = time() . "_{$type}_visit_" . $file->getClientOriginalName();
                $filePath = Storage::disk('public')->putFileAs($directory, $file, $fileName);

                $dataImage = [
                    'visit_id' => $visitId,
                    'type' => $type,
                    'url' => $filePath,
                ];
                VisitImage::create($dataImage);
            }
        }
    }

    public function update(ServiceUpdateRequest $request, $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }
        $dateService = $request->only('client_id', 'type_of_injury', 'level_injury', 'description');
        $service->update($dateService);

        $visitsData = $request->input('visits', []);

        $service->visits()->delete();
        foreach ($visitsData as $visitData) {
            $visit = Visit::create([
                'service_id' => $service->id,
                'unit_id' => $visitData['unit_id'],
                'team_id' => $visitData['team_id'],
                'date' => $visitData['date'],
                'time' => $visitData['time'],
                'description' => $visitData['description'],
            ]);

            foreach ($visitData['tools'] as $toolData) {
                $visit->tools()->attach($toolData['tool_id'], [
                    'quantity' => $toolData['quantity'],
                    'cost' => $toolData['cost'],
                ]);
            }

            $this->handleMedia($request, $visit->id, 'images', 'images/visits');
            $this->handleMedia($request, $visit->id, 'sketch', 'images/visits');
        }

        $date = [
            'service' => new ServiceResource($service->load('visits.tools')),
        ];
        return $this->success($date, 'Service has been updated successfully');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        $service->visits()->delete();
        $service->contracts()->delete();
        $service->quotations()->delete();

        $service->delete();

        return $this->success([], 'Service has been deleted successfully');
    }

    public function getServiceOfClient($clint_id)
    {
        $client = Client::find($clint_id);
        // dd($client->services);
        return $this->success($client->services, 'Service has been updated successfully');
    }
}
