<?php

namespace App\Http\Controllers;

use App\AnotherClasses\ResponseHandler;
use App\EditRequest;
use App\Http\Requests\EditRequestRequest;
use App\Http\Requests\RequestStatusRequest;
use App\WithdrawalStatus;
use Illuminate\Http\Request;

use JWTAuth;

class AdminRequestController extends Controller
{
    public function index() {
        return view('/vendor/voyager/edit_request');
    }


    public function get_user_requests() {
        $phone_number = JWTAuth::parseToken()->authenticate()->phone_number;

        $requests = EditRequest::orderBy('created_at', 'desc')
            ->where('phone_number', $phone_number)
            ->with('status')
            ->get();

        return ResponseHandler::getJsonResponse(200, "Данные успешно получены", $requests);
    }

    public function get_requests($type = null)
    {

        $query = EditRequest::orderBy('created_at', 'asc')
            ->with('status')
            ->take(45)
            ->with('user');

        if ($type == "in_work")
            $query->where('status_id', WithdrawalStatus::WAITING_FOR_CONFIRMATION);

        $result = $query->get();

        return $result;
    }

    public function change_status(RequestStatusRequest $request) {

        return EditRequest::where('id', $request->request_id)
            ->update(['status_id' => $request->status_id]);
    }

    public function store(EditRequestRequest $request) {

        $phone_number = JWTAuth::parseToken()->authenticate()->phone_number;

        EditRequest::create([
            "phone_number"  => $phone_number,
            "content"       => $request->json_content,
        ]);

        return ResponseHandler::getJsonResponse(200, "данные успешно сохранены");

    }
}
