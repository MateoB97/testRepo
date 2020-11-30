<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
   public function index()
    {
        $users = User::all();
        return response()->json(
            [
                'status' => 'success',
                'users' => $users->toArray()
            ], 200);
    }

    public function show(Request $request, $id)
    {
        $user = User::find($id);
        return response()->json(
            [
                'status' => 'success',
                'user' => $user->toArray()
            ], 200);
    }

    public function update(Request $request, $id)
    {
        $model = User::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function cambiarPass(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $val = false;

        if (Hash::check($request->old_pass, $user->password)) {
            $user->password = Hash::make($request->new_pass);
            $val = $user->save();
        } else {
            return 'Error: La contraseña anterior es incorrecta.';
        }

        if ($val) {
            return 'done';
        } else {
            return 'Error: no se pudo modificar la contraseña.';
        }
    }

    public function reiniciarPassword($id)
    {
         $model = User::find($id);
         
         $model->password = Hash::make($model->name);
         $validate = $model->save();

         $return = $validate ? 'true' : 'false';

         return $return;
    }
}
