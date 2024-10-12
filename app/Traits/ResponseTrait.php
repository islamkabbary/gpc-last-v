<?php

namespace App\Traits;

trait ResponseTrait
{
    protected $success = 200;
    protected $error = 500;

    public $response = ['status' => false, 'data' => [], 'message' => '', 'pagination' => null];

    public function success($data = [], $message = null, $pagination = null)
    {
        $this->response['status'] = true;
        $this->response['data'] = $data;
        $this->response['message'] = !is_null($message) ? $message : 'Success';

        // Include pagination data if provided
        if ($pagination) {
            $this->response['pagination'] = $pagination;
        }


        return response()->json($this->response, $this->success);
    }

    public function error($data = [], $message = null)
    {
        $this->response['status'] = false;
        $this->response['data'] = $data;
        $this->response['message'] = !is_null($message) ? $message : 'Error';
        return response()->json($this->response, $this->error);
    }
}
