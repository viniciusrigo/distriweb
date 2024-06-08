<?php

use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\ComandaController;
use App\Http\Controllers\Admin\EstoqueController;
use App\Http\Controllers\Admin\PagarContaController;
use App\Http\Controllers\Admin\GlobalConfigController;
use App\Http\Controllers\Admin\MovimentacoesFinanceiraController;
use App\Http\Controllers\Admin\UsuariosController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginClienteController;
use App\Http\Controllers\Vendedor\VendaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Auth::routes();

Route::get('/', function () {
    $usuario = Auth::user();
    if (isset($usuario)) {
        if ($usuario->cliente == "s"){
            return redirect()->route("loja.index");
        } else {
            return redirect()->route("admin");
        }
        
    } else {
        return view('welcome');
    } 
})->name("welcome");



Route::get('/login-cliente', [LoginClienteController::class, 'index'])->name('logincliente');
Route::post('/login-cliente', [LoginClienteController::class, 'auth'])->name('logincliente.auth');
Route::get('/logout-cliente', [LoginClienteController::class, 'destroy'])->name('logincliente.destroy');
Route::get('/novo-cliente', [LoginClienteController::class, 'index_register'])->name('novo.cliente');
Route::post('/cliente-register', [LoginClienteController::class, 'cliente_register'])->name('novo.cliente.register');

Route::get('/loja', [LoginClienteController::class, 'loja'])->name('loja.index');



Route::get('/admin', [AdminController::class, 'index'])->middleware('check.client')->name('admin');

Route::prefix('/admin')->group(function () {

    Route::get('/usuarios', [UsuariosController::class, 'index'])->name('admin.usuarios.index');
    Route::get('/usuarios/editar/{id}', [UsuariosController::class, 'edit'])->name('admin.usuarios.edit');
    Route::post('/usuarios/update', [UsuariosController::class, 'update'])->name('admin.usuarios.update');

    Route::get('/configuracao-global', [GlobalConfigController::class, 'index'])->name('admin.configuracao-global.index');

    Route::get('/clientes', [ClienteController::class, 'index'])->name('admin.clientes.index')->middleware('can:acesso_clientes');

    Route::get('/comandas', [ComandaController::class, 'index'])->name('admin.comandas.index')->middleware('can:acesso_comandas');
    Route::post('/comandas/store', [ComandaController::class, 'store'])->name('admin.comandas.store')->middleware('can:acesso_comandas');
    Route::post('/comandas/closed', [ComandaController::class, 'closed'])->name('admin.comandas.closed')->middleware('can:acesso_comandas');
    Route::get('/comandas/delete/{id}', [ComandaController::class, 'destroy'])->name('admin.comandas.destroy')->middleware('can:acesso_comandas');
    Route::post('/comandas/add-produto', [ComandaController::class, 'add_produto'])->name('admin.comandas.add-produto')->middleware('can:acesso_comandas');
    Route::post('/comandas/remove-produto', [ComandaController::class, 'remove_produto'])->name('admin.comandas.remove-produto')->middleware('can:acesso_comandas');

    Route::delete('/estoque/produtos/delete/{id}', [EstoqueController::class, 'destroy'])->name('admin.estoque.produtos.destroy')->middleware('can:acesso_estoque');
    Route::put('/estoque/produtos/', [EstoqueController::class, 'update'])->name('admin.estoque.produtos.update')->middleware('can:acesso_estoque');
    Route::get('/estoque/produtos/create', [EstoqueController::class, 'create'])->name('admin.estoque.produtos.create')->middleware('can:acesso_estoque');
    Route::get('/estoque/produtos/editar/{id}', [EstoqueController::class, 'edit'])->name('admin.estoque.produtos.edit')->middleware('can:acesso_estoque');
    Route::get('/estoque/produtos/', [EstoqueController::class, 'index'])->name('admin.estoque.produtos.index')->middleware('can:acesso_estoque');
    Route::post('/estoque/produtos/', [EstoqueController::class, 'store'])->name('admin.estoque.produtos.store')->middleware('can:acesso_estoque');
    Route::post('/estoque/produtos/atualizar-promocao/', [EstoqueController::class, 'atualizar_promocao'])->name('admin.estoque.produtos.atualizar-promocao')->middleware('can:acesso_estoque');
    Route::post('/estoque/produtos/atualizar-ativo/', [EstoqueController::class, 'atualizar_ativo'])->name('admin.estoque.produtos.atualizar-ativo')->middleware('can:acesso_estoque');

    Route::post('/estoque/novo-lote', [EstoqueController::class, 'novo_lote'])->name('admin.estoque.novo-lote');

    Route::get('/financeiro/contas-a-pagar', [PagarContaController::class, 'index'])->name('admin.financeiro.contas-a-pagar.index')->middleware('can:acesso_financeiro');
    Route::post('/financeiro/contas-a-pagar', [PagarContaController::class, 'store'])->name('admin.financeiro.contas-a-pagar.store')->middleware('can:acesso_financeiro');
    Route::delete('/financeiro/contas-a-pagar/delete/{id}', [PagarContaController::class, 'destroy'])->name('admin.financeiro.contas-a-pagar.destroy')->middleware('can:acesso_financeiro');
    Route::get('/financeiro/contas-a-pagar/pagar/{id}', [PagarContaController::class, 'pagar'])->name('admin.financeiro.contas-a-pagar.pagar')->middleware('can:acesso_financeiro');
    Route::get('/financeiro/movimentacoes', [MovimentacoesFinanceiraController::class, 'index'])->name('admin.financeiro.movimentacoes.index')->middleware('can:acesso_financeiro');
})->middleware('check.client');



Route::prefix('/vendedor/pdv')->group(function () {
    Route::get('/', [VendaController::class, 'index'])->name('vendedor.PDV.index')->middleware('can:acesso_pdv');
    Route::get('create', [VendaController::class, 'create'])->name('vendedor.PDV.create')->middleware('can:acesso_pdv');
    Route::get('venda/{id}', [VendaController::class, 'venda'])->name('vendedor.PDV.venda')->middleware('can:acesso_pdv');
    Route::post('venda/add-produto', [VendaController::class, 'add_produto'])->name('vendedor.PDV.add-produto')->middleware('can:acesso_pdv');
    Route::post('venda/remove-produto', [VendaController::class, 'remove_produto'])->name('vendedor.PDV.remove-produto')->middleware('can:acesso_pdv');
    Route::post('venda/concluir-venda', [VendaController::class, 'concluir_venda'])->name('vendedor.PDV.concluir-venda')->middleware('can:acesso_pdv');
});

