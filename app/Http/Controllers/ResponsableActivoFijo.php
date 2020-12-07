<?php

namespace App\Http\Controllers;

use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Tercero;

class ResponsableActivoFijo extends Controller
{
    public function getData(){
        $responablesActivoFijo = DB::table('responsables_activos_fijos')->whereNull('responsables_activos_fijos.deleted_at')
            ->leftJoin('terceros as tercero', 'responsables_activos_fijos.tercero_id', '=', 'tercero.id')
            ->leftJoin('listas_elementos as elementos','tercero.tipo_documento_id','=','elementos.id')
            ->select(
            'tercero.activo As activo',
            DB::raw("CONCAT(elementos.nombre,' - ',tercero.numero_documento,' - ',tercero.nombre1, ' ', tercero.nombre2, ' ', tercero.apellido1, ' ', tercero.apellido2)AS empleado"),
        );
        return DataTables::of($responablesActivoFijo)->toJson(); 
    }


    public function buscarTercero(Request $request){
         $terceros = Tercero::withoutTrashed()
            ->leftjoin('listas_elementos as elementos', 'terceros.tipo_documento_id', '=', 'elementos.id')
            ->select(
            'elementos.nombre as cedula',   
            'numero_documento',
            'nombre1',
            'nombre2',
            'apellido1',
            'apellido2',
            );  
        //Filtros    
        if ($request->nombre1) {
            $terceros->where('nombre1', 'like', '%'. $request->nombre1 . '%');
        }
        if ($request->nombre2) {
            $terceros->where('nombre2', 'like', '%' .$request->nombre2 . '%');
        }
        if ($request->apellido1) {
            $terceros->where('apellido1', 'like', '%'. $request->apellido1 . '%');
        }
        if ($request->apellido2) {
            $terceros->where('apellido2', 'like', '%' . $request->apellido2 . '%');
        }

        return datatables()->eloquent($terceros)->toJson();
    }
}
