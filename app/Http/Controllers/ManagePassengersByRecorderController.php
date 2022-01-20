<?php

namespace App\Http\Controllers;

use App\Models\Routes\DispatchRegister;
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
            case 'editField':
            case 'editRecorders':
                $user = Auth::user();

                if ($user->canEditRecorders()) {
                    $id = $request->get('id');
                    $field = __($request->get('field'));
                    $value = $request->get('value');
                    $observation = $request->get('obs');

                    $success = DB::update("UPDATE registrodespacho SET $field = $value, edit_user_id = $user->id, edited_info = edited_info || 'User $user->id > $field = $value, ', ignore_trigger = TRUE WHERE id_registro = $id");

                    if($success) {
                        $dr = DispatchRegister::find($id);

                        $drObs = $dr->getObservation($field);
                        $drObs->value = $value;
                        $drObs->observation = $observation;

                        $success = $drObs->save();
                    }

                    return Response::json([
                        'success' => $success,
                        'value' => $value
                    ]);
                }
                return "Nothing to do";
                break;
            case 'cancelTurn':
                $user = Auth::user();

                if ($user->canEditRecorders()) {
                    $id = $request->get('id');

                    $success = DB::update("UPDATE registrodespacho SET observaciones = 'No terminó. Cancelado NE', edit_user_id = $user->id, edited_info = edited_info || 'User $user->id > Cancela turno desde NE, ', ignore_trigger = TRUE WHERE id_registro = $id");

                    return Response::json([
                        'success' => $success,
                    ]);
                }
                return "Nothing to do";
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
