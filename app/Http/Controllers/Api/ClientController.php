<?php

namespace app\Http\Controllers\Api;


use App\Models\Unit;
use App\Models\Client;
use App\Models\Contact;

use App\Models\UnitType;
use App\Models\ContactType;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Http\Requests\ClientUpdateRequest;

class ClientController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::with(['contacts', 'units'])->paginate(10);
        return $this->success(
            ClientResource::collection($clients),
            'Clients have been fetched successfully',
            [
                'current_page' => $clients->currentPage(),
                'last_page' => $clients->lastPage(),
                'per_page' => $clients->perPage(),
                'total' => $clients->total(),
                'next_page_url' => $clients->nextPageUrl(),
                'prev_page_url' => $clients->previousPageUrl(),
            ]
        );
    }

    public function show($id)
    {
        $client = Client::with(['contacts', 'units'])->find($id);

        if (!$client) {
            return $this->error('Client not found', 404);
        }

        return $this->success(
            new ClientResource($client),
            'Client retrieved successfully'
        );
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientRequest $request)
    {
        try {
            DB::transaction(function () use ($request, &$client) {
                $client = Client::create($request->only(['name', 'client_id', 'status', 'description']));
                $this->storeContacts($request, $client->id);
                $this->storeUnits($request, $client->id);
            });

            return $this->success(
                new ClientResource($client),
                'Client has been created successfully'
            );
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->error('Error creating client');
        }
    }

    private function storeContacts(ClientRequest $request, $clientId)
    {
        $contactTypes = ContactType::whereIn('name', ['phone', 'email'])->get()->keyBy('name');
        $contacts = [];

        if ($request->has('phone') && isset($contactTypes['phone'])) {
            foreach ($request->phone as $phone) {
                $contacts[] = Contact::create([
                    'value' => $phone,
                    'client_id' => $clientId,
                    'contact_type_id' => $contactTypes['phone']->id,
                ]);
            }
        }

        if ($request->has('email') && isset($contactTypes['email'])) {
            foreach ($request->email as $email) {
                $contacts[] = Contact::create([
                    'value' => $email,
                    'client_id' => $clientId,
                    'contact_type_id' => $contactTypes['email']->id,
                ]);
            }
        }

        return $contacts;
    }

    private function storeUnits(ClientRequest $request, $client_id)
    {
        if (!$request->has('units')) {
            return [];
        }

        $units = [];


        foreach ($request->units as $unit) {
            $unit['client_id'] = $client_id;
            $createdUnit = Unit::create($unit);
            $units[] = $createdUnit;
        }

        return $units;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientUpdateRequest $request, Client $client)
    {
        try {
            DB::transaction(function () use ($request, $client) {
                $client->update($request->only(['name', 'client_id', 'status', 'description']));

                $this->updateContacts($request, $client->id);
                $this->updateUnits($request, $client->id);
            });

            return $this->success(
                new ClientResource($client),
                'Client has been updated successfully'
            );
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->error('Error updating client');
        }
    }

    private function updateContacts($request, $clientId)
    {
        $contactTypes = ContactType::whereIn('name', ['phone', 'email'])->get()->keyBy('name');

        Contact::where('client_id', $clientId)->delete();
        $contacts = [];

        if ($request->has('phone') && isset($contactTypes['phone'])) {
            foreach ($request->phone as $phone) {
                $contacts[] = Contact::create([
                    'value' => $phone,
                    'client_id' => $clientId,
                    'contact_type_id' => $contactTypes['phone']->id,
                ]);
            }
        }

        if ($request->has('email') && isset($contactTypes['email'])) {
            foreach ($request->email as $email) {
                $contacts[] = Contact::create([
                    'value' => $email,
                    'client_id' => $clientId,
                    'contact_type_id' => $contactTypes['email']->id,
                ]);
            }
        }

        return $contacts;
    }

    private function updateUnits($request, $clientId)
    {
        $unitTypes = UnitType::whereIn('name', ['name', 'address', 'lat', 'lang', 'Note'])->get()->keyBy('name');

        Unit::where('client_id', $clientId)->delete();

        $units = [];

        foreach ($request->units as $unit) {
            foreach (['name', 'address', 'lat', 'lang'] as $field) {
                if (isset($unit[$field]) && isset($unitTypes[$field])) {
                    $units[] = Unit::create([
                        'value' => $unit[$field],
                        'unit_type_id' => $unitTypes[$field]->id,
                        'client_id' => $clientId,
                    ]);
                }
            }

            if (isset($unit['note_unit']) && isset($unitTypes['Note'])) {
                $units[] = Unit::create([
                    'value' => $unit['note_unit'],
                    'unit_type_id' => $unitTypes['Note']->id,
                    'client_id' => $clientId,
                ]);
            }
        }

        return $units;
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return $this->error('Client not found', 404);
        }

        DB::transaction(function () use ($client) {
            // Delete related contacts and units
            $client->contacts()->delete();
            $client->units()->delete();

            // Delete the client
            $client->delete();
        });

        // Return success response
        return $this->success([], 'Client deleted successfully');
    }
}
