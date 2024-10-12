<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use App\Models\Contract;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContractRequest;

class ContractController extends Controller
{
    use ResponseTrait;

    public function getContractAndQuotation()
    {
        try {
            $contract = Setting::whereIn('key', ['contract', 'quotation'])->get();
            return $this->success($contract, 'contract have been fetched successfully');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    public function addContractToForClient(ContractRequest $request)
    {
        try {
            $data = $request->only('client_id', 'service_id', 'contract');
            $data['created_by'] = auth()->user()->id;
            $oldContract = Contract::where('client_id', $request->client_id)->first();
            if ($oldContract) {
                $oldContract->contract = $request->contract;
                $oldContract->save();
                $contract = $oldContract;
            } else {
                $contract = Contract::create($data);
            }
            return $this->success($contract, 'contract have been created successfully');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    public function approval($contract_id)
    {
        try {
            $contract = Contract::find($contract_id);
            $contract->approved_by = auth()->user()->id;
            $contract->status = "Approved";
            $contract->save();
            return $this->success($contract, 'contract Approved successfully');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
