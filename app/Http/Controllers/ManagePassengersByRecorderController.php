<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Http\Request;
use Response;

class ManagePassengersByRecorderController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function ajax($action, Request $request)
    {
        switch ($action) {
            case 'editRecorders':
                if (Auth::user()->isAdmin()) {
                    $id = $request->get('id');
                    $field = $request->get('field');
                    $value = $request->get('value');

                    $success = DB::update("UPDATE registrodespacho SET $field = $value, ignore_trigger = TRUE WHERE id_registro = $id");

                    return Response::json([
                        'success' => $success,
                        'value' => $value
                    ]);
                }
                return "Noting to do";
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
