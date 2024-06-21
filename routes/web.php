<?php

use App\Http\Controllers\Admin\BancoController;
use App\Http\Controllers\Admin\CaixaController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\ComandaController;
use App\Http\Controllers\Admin\EstoqueController;
use App\Http\Controllers\Admin\PagarContaController;
use App\Http\Controllers\Admin\GlobalConfigController;
use App\Http\Controllers\Admin\MovimentacoesFinanceiraController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\UsuariosController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginClienteController;
use App\Http\Controllers\Admin\VendaController;
use App\Http\Controllers\LojaController;
use App\Models\Produto;
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
        $produtos = Produto::orderBy('nome', 'asc')->get();
        return view('welcome', compact('produtos'));
    } 
})->name("welcome");



Route::get('/login-cliente', [LoginClienteController::class, 'index'])->name('logincliente');
Route::post('/login-cliente', [LoginClienteController::class, 'auth'])->name('logincliente.auth');
Route::get('/logout-cliente', [LoginClienteController::class, 'destroy'])->name('logincliente.destroy');
Route::get('/novo-cliente', [LoginClienteController::class, 'index_register'])->name('novo.cliente');
Route::post('/cliente-register', [LoginClienteController::class, 'cliente_register'])->name('novo.cliente.register');

Route::get('/loja/carrinho', [LojaController::class, 'carrinho'])->name('loja.carrinho');
Route::post('/loja/add-carrinho', [LojaController::class, 'add_carrinho'])->name('loja.add-carrinho');
Route::post('/loja/remover-carrinho', [LojaController::class, 'remover_carrinho'])->name('loja.remover-carrinho');
Route::post('/loja/verifica-carrinho', [LojaController::class, 'verifica_carrinho'])->name('loja.verifica-carrinho');
Route::post('/loja/concluir-pedido', [LojaController::class, 'concluir_pedido'])->name('loja.concluir-pedido');
Route::get('/loja/{busca?}', [LojaController::class, 'index'])->name('loja.index');
Route::post('/loja/produtos-ajax', [LojaController::class, 'produtos_ajax'])->name('loja.produtos.ajax');




Route::get('/admin', [AdminController::class, 'index'])->middleware('check.client')->name('admin');

Route::prefix('/admin')->group(function () {

    Route::get('/usuarios', [UsuariosController::class, 'index'])->name('admin.usuarios.index')->middleware('can:acesso_usuarios');
    Route::get('/usuarios/editar/{id}', [UsuariosController::class, 'edit'])->name('admin.usuarios.edit')->middleware('can:acesso_usuarios');
    Route::post('/usuarios/update', [UsuariosController::class, 'update'])->name('admin.usuarios.update')->middleware('can:acesso_usuarios');

    Route::get('/configuracao-global', [GlobalConfigController::class, 'index'])->name('admin.configuracao-global.index')->middleware('can:acesso_config_global');
    Route::post('/configuracao-global', [GlobalConfigController::class, 'store'])->name('admin.configuracao-global.store')->middleware('can:acesso_config_global');
    Route::post('/configuracao-global/nova-forma-pagamento', [GlobalConfigController::class, 'nova_forma_pagamento'])->name('admin.configuracao-global.nova-forma-pagamento')->middleware('can:acesso_config_global');
    Route::post('/configuracao-global/novo-banco', [GlobalConfigController::class, 'novo_banco'])->name('admin.configuracao-global.novo-banco')->middleware('can:acesso_config_global');
    Route::post('/configuracao-global/novo-tipo-conta', [GlobalConfigController::class, 'novo_tipo_conta'])->name('admin.configuracao-global.novo-tipo-conta')->middleware('can:acesso_config_global');

    Route::get('/clientes', [ClienteController::class, 'index'])->name('admin.clientes.index')->middleware('can:acesso_clientes');

    Route::get('/pdv', [VendaController::class, 'index'])->name('admin.pdv.index')->middleware('can:acesso_pdv');
    Route::get('/pdv/create', [VendaController::class, 'create'])->name('vendedor.pdv.create')->middleware('can:acesso_pdv');
    Route::post('/pdv/delete', [VendaController::class, 'delete'])->name('vendedor.pdv.delete')->middleware('can:acesso_pdv');
    Route::get('/pdv/venda/{id}', [VendaController::class, 'venda'])->name('vendedor.pdv.venda')->middleware('can:acesso_pdv');
    Route::post('/pdv/venda/add-produto', [VendaController::class, 'add_produto'])->name('vendedor.pdv.add-produto')->middleware('can:acesso_pdv');
    Route::post('/pdv/venda/remove-produto', [VendaController::class, 'remove_produto'])->name('vendedor.pdv.remove-produto')->middleware('can:acesso_pdv');
    Route::post('/pdv/venda/concluir-venda', [VendaController::class, 'concluir_venda'])->name('vendedor.pdv.concluir-venda')->middleware('can:acesso_pdv');

    Route::get('/comandas', [ComandaController::class, 'index'])->name('admin.comandas.index')->middleware('can:acesso_comandas');
    Route::post('/comandas/store', [ComandaController::class, 'store'])->name('admin.comandas.store')->middleware('can:acesso_comandas');
    Route::post('/comandas/closed', [ComandaController::class, 'closed'])->name('admin.comandas.closed')->middleware('can:acesso_comandas');
    Route::get('/comandas/delete/{id}', [ComandaController::class, 'destroy'])->name('admin.comandas.destroy')->middleware('can:acesso_comandas');
    Route::post('/comandas/add-produto', [ComandaController::class, 'add_produto'])->name('admin.comandas.add-produto')->middleware('can:acesso_comandas');
    Route::post('/comandas/remove-produto', [ComandaController::class, 'remove_produto'])->name('admin.comandas.remove-produto')->middleware('can:acesso_comandas');
    Route::get('/comandas/imprimir/{id}', [ComandaController::class, 'imprimir'])->name('admin.comandas.imprimir')->middleware('can:acesso_comandas');

    Route::get('/pedidos', [PedidoController::class, 'index'])->name('admin.pedidos.index')->middleware('can:acesso_pedidos');
    Route::get('/pedidos/imprimir/{id}', [PedidoController::class, 'imprimir_pedido'])->name('admin.pedidos.imprimir')->middleware('can:acesso_pedidos');
    Route::post('/pedidos/change-status', [PedidoController::class, 'status'])->name('admin.pedidos.change-status')->middleware('can:acesso_pedidos');
    Route::post('/pedidos/rejeitar', [PedidoController::class, 'rejeitar'])->name('admin.pedidos.rejeitar')->middleware('can:acesso_pedidos');

    Route::get('/vendas', [VendaController::class, 'index_vendas'])->name('admin.vendas.index')->middleware('can:acesso_vendas');
    Route::post('/vendas/consulta-produtos', [VendaController::class, 'consulta_produtos_ajax'])->name('admin.vendas.consulta-produtos')->middleware('can:acesso_vendas');
    Route::get('/vendas/detalhe/{id}', [VendaController::class, 'detalhe_venda'])->name('admin.vendas.detalhe.index')->middleware('can:acesso_vendas');

    Route::get('/caixa', [CaixaController::class, 'index'])->name('admin.caixa.index')->middleware('can:acesso_caixa');
    Route::post('/caixa/abrir', [CaixaController::class, 'open'])->name('admin.caixa.open')->middleware('can:acesso_caixa');
    Route::post('/caixa/fechar', [CaixaController::class, 'close'])->name('admin.caixa.close')->middleware('can:acesso_caixa');

    Route::get('/bancos', [BancoController::class, 'index'])->name('admin.banco.index')->middleware('can:acesso_bancos');

    Route::delete('/estoque/produtos/delete/{id}', [EstoqueController::class, 'destroy'])->name('admin.estoque.produtos.destroy')->middleware('can:acesso_estoque');
    Route::put('/estoque/produtos/', [EstoqueController::class, 'update'])->name('admin.estoque.produtos.update')->middleware('can:acesso_estoque');
    Route::get('/estoque/produtos/create', [EstoqueController::class, 'create'])->name('admin.estoque.produtos.create')->middleware('can:acesso_estoque');
    Route::get('/estoque/produtos/editar/{id}', [EstoqueController::class, 'edit'])->name('admin.estoque.produtos.edit')->middleware('can:acesso_estoque');
    Route::get('/estoque/produtos/', [EstoqueController::class, 'index'])->name('admin.estoque.produtos.index')->middleware('can:acesso_estoque');
    Route::post('/estoque/produtos/', [EstoqueController::class, 'store'])->name('admin.estoque.produtos.store')->middleware('can:acesso_estoque');
    Route::post('/estoque/produtos/atualizar-promocao/', [EstoqueController::class, 'atualizar_promocao'])->name('admin.estoque.produtos.atualizar-promocao')->middleware('can:acesso_estoque');
    Route::post('/estoque/produtos/atualizar-ativo/', [EstoqueController::class, 'atualizar_ativo'])->name('admin.estoque.produtos.atualizar-ativo')->middleware('can:acesso_estoque');

    Route::get('/estoque/lotes', [EstoqueController::class, 'index_lote'])->name('admin.estoque.lote.index')->middleware('can:acesso_estoque');
    Route::post('/estoque/lotes/novo', [EstoqueController::class, 'novo_lote'])->name('admin.estoque.lote.novo')->middleware('can:acesso_estoque');

    Route::get('/financeiro/contas-a-pagar', [PagarContaController::class, 'index'])->name('admin.financeiro.contas-a-pagar.index')->middleware('can:acesso_financeiro');
    Route::post('/financeiro/contas-a-pagar', [PagarContaController::class, 'store'])->name('admin.financeiro.contas-a-pagar.store')->middleware('can:acesso_financeiro');
    Route::delete('/financeiro/contas-a-pagar/delete/{id}', [PagarContaController::class, 'destroy'])->name('admin.financeiro.contas-a-pagar.destroy')->middleware('can:acesso_financeiro');
    Route::get('/financeiro/contas-a-pagar/pagar/{id}', [PagarContaController::class, 'pagar'])->name('admin.financeiro.contas-a-pagar.pagar')->middleware('can:acesso_financeiro');
    Route::get('/financeiro/movimentacoes', [MovimentacoesFinanceiraController::class, 'index'])->name('admin.financeiro.movimentacoes.index')->middleware('can:acesso_financeiro');
})->middleware('check.client');

