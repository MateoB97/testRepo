<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LotPesoPlanta;

class LotPesoPlantaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = LotPesoPlanta::pesosTotalesLote();
        return $list;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        //verifico que no se halla borrado ninguno en el nuevo guardado, si se borro alguno, se elimina
        $existentes = LotPesoPlanta::where('lote_id',$request->lote_id)->get();

        foreach ($existentes as $existente) {

            $flag_existe = 0;

            foreach ($request->productos as $item) {
               if ($existente->id == $item['id']) {
                   $flag_existe = 1;
               }
            }

            if ($flag_existe != 1) {
                $existente->delete();
            }
        }


        foreach ($request->productos as $item) {

            $validate = strrpos($item['id'], "nuevo");

            if ( $validate !== false) {
                $newItem = new  LotPesoPlanta($item);
                $newItem->lote_id = $request->lote_id;
                $newItem->save();
            }
           
        }
        
        return 'done';

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function GetData($idlote)
    {
        $data = LotPesoPlanta::where('lote_id','=',$idlote)->get();
        foreach ($data as $item) {
            $item->peso = number_format($item->peso, 3, '.', '');
        }
        return $data;
    }

    public function DeleteByLote($idlote)
    {
        $data = LotPesoPlanta::where('lote_id','=',$idlote)->get();
        
        foreach ($data as $item) {
            $item->forceDelete();
        }

        return 'done';
    }

}
