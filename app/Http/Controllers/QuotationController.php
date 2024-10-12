<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\QuotationRequest;

class QuotationController extends Controller
{
    use ResponseTrait;

    public function addQuotationToForClient(QuotationRequest $request)
    {
        try {
            $data = $request->only('client_id','service_id','quotation');
            $data['created_by'] = auth()->user()->id;
            $oldQuotation = Quotation::where('client_id', $request->client_id)->first();
            if ($oldQuotation) {
                $oldQuotation->quotation = $request->quotation;
                $oldQuotation->save();
                $quotation = $oldQuotation;
            } else {
                $quotation = Quotation::create($data);
            }
            return $this->success($quotation, 'Quotation have been created successfully');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
