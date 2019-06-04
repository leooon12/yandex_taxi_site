<?php

namespace App\Http\Controllers;

use App\EditRequest;
use App\Http\Requests\EditRequestRequest;
use App\Http\Requests\RequestStatusRequest;
use App\WithdrawalStatus;
use Illuminate\Http\Request;

class AdminRequestController extends Controller
{
    public function index() {
        return view('/vendor/voyager/edit_request');
    }


    public function get_requests($type = null)
    {

        $query = EditRequest::orderBy('created_at', 'asc')
            ->with('status')
            ->with('user');

        if ($type == "in_work")
            $query->where('status_id', WithdrawalStatus::INWORK);

        $result = $query->get();

        return $result;
    }

    public function change_status(RequestStatusRequest $request) {

        return EditRequest::where('id', $request->request_id)
            ->update(['status_id' => $request->status_id]);
    }

    public function store(EditRequestRequest $request) {
        return EditRequest::create([
            "phone_number"  => $request->phone_number,
            "content"       => $request->json_content,
        ]);
    }
}
