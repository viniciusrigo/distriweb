<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\PermissionUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UsuariosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $users = User::all();

        return view("site/admin/usuarios/index",compact("users"));
    }

    public function edit(string|int $id){
        $user = User::find($id);
        $permissions = Permission::all()->select("id", "nome")->toArray();
        $permission_user = DB::table("permission_user")->where("user_id", $user->id)->get()->toArray();
        $acessos = [];
        for ($i=0; $i < count($permission_user); $i++) { 
            array_push( $acessos, $permission_user[$i]->permission_id);
        }

        return view("site/admin/usuarios/update",compact("user", "permissions", "permission_user", "acessos"));
    }

    public function update(Request $request){

        $results = [];
        foreach($request->all() as $key => $value){
            array_push($results, $value);
        }

        $qtd = count($results);

        DB::table("permission_user")->where("user_id", $request->usuario)->delete();
        for ($i= 2; $i < $qtd; $i++){
            $p_u = new PermissionUser;
            $p_u['user_id'] = $request->input('usuario');
            $p_u['permission_id'] = $results[$i];
            $p_u->save();
        }

        $success = "Permissões atualizadas com sucesso";
        session()->flash("success", $success);
        return redirect()->back();
    }
}
