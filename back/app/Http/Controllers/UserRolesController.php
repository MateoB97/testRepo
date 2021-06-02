<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserRoles;


class UserRolesController extends Controller
{
    public function index()
    {
        $index= UserRoles::orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new UserRoles($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = UserRoles::find($id);

        return $model;
    }

    public function update(Request $request, $id)
    {
        $model = UserRoles::find($id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = UserRoles::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }
}
