<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LoginClienteController extends Controller
{

    public function index(){
        if (Auth::check()) {
            return redirect()->intended("loja"); 
        } else {
            return view("site/cliente/login/index");
        }  
    }

    public function auth(Request $request){

        $request->validate([
            "cpf" => "required",
            "password" => "required"
        ]);

        $authenticado = Auth::attempt(['cpf' => $request->input('cpf'), 'password' => $request->input('password'), 'cliente' => 's']);

        if($authenticado){
            $request->session()->regenerate();

            $request->session()->regenerateToken();

            return redirect()->intended("loja");
        } else {
            return redirect()->back()->with("error","CPF ou Senha Inválido");
        }

    }

    public function destroy(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route("welcome");
    }

    public function loja(){
        if (Auth::check()) {
            return view("site/cliente/loja/index");
        } else {
            return redirect()->route("logincliente");
        }
        
    }

    public function index_register(Request $request){
        return view("site/cliente/login/novo-cliente");
    }

    public function cliente_register(Request $request){
        $user = $request->except('_token');
        $user['password'] = bcrypt($request->input('password'));
        $user['cliente'] = "s";
        $user['pontos'] = 0;
        $user['celular'] = $request->input('celular');
        $user['logradouro'] = $request->input('logradouro').", ".$request->input('numero');
        
        $user = User::create($user);

        Auth::login($user);

        return redirect()->route('loja.index');
    }

}
