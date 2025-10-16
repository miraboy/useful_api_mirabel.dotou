<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Module;

class ModuleController extends Controller
{
    //activation of the module for user
     public function activation(Request $request, $id)
    {
        $user = Auth::user();
        $module = Module::findOrFail($id);
        
        $current = $user->modules()->where('module_id', $id)->first();
        
        $user->modules()->updateExistingPivot($id, [
            'active' => true
        ]);
        $res = [
            "message"=>"Module activated" 
        ];
        return response()->json($res);
    }

    //desactivation of the module for user
     public function deactivation(Request $request, $id)
    {
        $user = Auth::user();
        $module = Module::findOrFail($id);
        
        $current = $user->modules()->where('module_id', $id)->first();
        
        $user->modules()->updateExistingPivot($id, [
            'active' => false
        ]);
        $res = [
            "message"=>"Module deactivated"
        ];
        return response()->json($res);
    }
}
