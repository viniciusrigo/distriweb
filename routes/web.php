<?php

use App\Http\Controllers\Admin\BancoController;
use App\Http\Controllers\Admin\CaixaController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\ComandaController;
use App\Http\Controllers\Admin\EstoqueController;
use App\Http\Controllers\Admin\FidelidadeController;
use App\Http\Controllers\Admin\FornecedorController;
use App\Http\Controllers\Admin\PagarContaController;
use App\Http\Controllers\Admin\GlobalConfigController;
use App\Http\Controllers\Admin\MovimentacoesFinanceiraController;
use App\Http\Controllers\Admin\PDV2Controller;
use App\Http\Controllers\Admin\PDVController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\UsuariosController;
use App\Http\Controllers\Admin\VendaManualController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginClienteController;
use App\Http\Controllers\Admin\VendaController;
use App\Http\Controllers\LojaController;
use App\Models\Produto;
use App\Models\VariaveisProduto;
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
        $produtos_desconto = VariaveisProduto::where('promocao', 's')->where('pontos', 0)
        ->join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome", "variaveis_produtos.*")
        ->orderBy('nome', 'asc')->get()->toArray();

        $produtos_fidelidade = VariaveisProduto::where('promocao', "!=", 's')->where('pontos', ">", 0)
        ->join("produtos", "variaveis_produtos.produto_id", "=", "produtos.id")
        ->select("produtos.nome", "variaveis_produtos.*")
        ->orderBy('nome', 'asc')->get()->toArray();
        
        $produtos = Produto::where("ativo", "s")
        ->select("id", "nome")
        ->orderBy('nome', 'asc')->get();
        for($x = 0; $x < count($produtos); $x++){
            $variavel = VariaveisProduto::where("produto_id", $produtos[$x]->id)->orderBy("preco")->first(["preco", "id"]);
            $produtos[$x]->preco = $variavel->preco;
            $produtos[$x]->variavel_produto_id = $variavel->id;

        }

        return view('welcome', compact('produtos_desconto', 'produtos_fidelidade', 'produtos'));
    } 
})->name("welcome");

Route::get('/confirmar-entrega', [LojaController::class, 'index_confirmar_entrega'])->name('index-confirmar-entrega');
Route::get('/confirmar-entrega/pedido/{pedido_id?}/{codigo?}', [LojaController::class, 'confirmar_entrega'])->name('confirmar-entrega');

Route::get('/login-cliente', [LoginClienteController::class, 'index'])->name('logincliente');
Route::post('/login-cliente', [LoginClienteController::class, 'auth'])->name('logincliente.auth');
Route::get('/logout-cliente', [LoginClienteController::class, 'destroy'])->name('logincliente.destroy');
Route::get('/novo-cliente', [LoginClienteController::class, 'index_register'])->name('novo.cliente');
Route::post('/cliente-register', [LoginClienteController::class, 'cliente_register'])->name('novo.cliente.register');

Route::get('/loja', [LojaController::class, 'index'])->name('loja.index');
Route::get('/loja/carrinho', [LojaController::class, 'carrinho'])->name('loja.carrinho');
Route::post('/loja/add-carrinho', [LojaController::class, 'add_carrinho'])->name('loja.add-carrinho');
Route::post('/loja/remover-carrinho', [LojaController::class, 'remover_carrinho'])->name('loja.remover-carrinho');
Route::post('/loja/verifica-carrinho', [LojaController::class, 'verifica_carrinho'])->name('loja.verifica-carrinho');
Route::post('/loja/concluir-pedido', [LojaController::class, 'concluir_pedido'])->name('loja.concluir-pedido');
Route::post('/loja/produtos-ajax', [LojaController::class, 'produtos_ajax'])->name('loja.produtos.ajax');
Route::post('/loja/cancelar-pedido', [LojaController::class, 'cancelar_pedido'])->name('loja.produtos.cancelar-pedido');

Route::get('/admin', [AdminController::class, 'index'])->middleware('check.client')->name('admin');
Route::post('/admin/dados', [AdminController::class, 'get_dados'])->middleware('check.client')->name('admin.dados');
Route::post('/admin/indicadores-ajax', [AdminController::class, 'indicadores_ajax'])->middleware('check.client')->name('admin.indicadores-ajax');

Route::prefix('/admin')->group(function () {

    Route::get('/usuarios', [UsuariosController::class, 'page_index'])->name('admin.usuarios.index')->middleware('can:acesso_usuarios');
    Route::get('/usuarios/editar/{id}', [UsuariosController::class, 'page_edit'])->name('admin.usuarios.edit')->middleware('can:acesso_usuarios');
    Route::post('/usuarios/update', [UsuariosController::class, 'update'])->name('admin.usuarios.update')->middleware('can:acesso_usuarios');

    Route::get('/configuracao-global', [GlobalConfigController::class, 'page_index'])->name('admin.configuracao-global.index')->middleware('can:acesso_config_global');
    Route::post('/configuracao-global', [GlobalConfigController::class, 'store'])->name('admin.configuracao-global.store')->middleware('can:acesso_config_global');
    Route::post('/configuracao-global/nova-forma-pagamento', [GlobalConfigController::class, 'new_payment_method'])->name('admin.configuracao-global.nova-forma-pagamento')->middleware('can:acesso_config_global');
    Route::post('/configuracao-global/novo-banco', [GlobalConfigController::class, 'new_bank'])->name('admin.configuracao-global.novo-banco')->middleware('can:acesso_config_global');
    Route::post('/configuracao-global/novo-tipo-conta', [GlobalConfigController::class, 'new_account_type'])->name('admin.configuracao-global.novo-tipo-conta')->middleware('can:acesso_config_global');
    Route::post('/configuracao-global/info-empresa', [GlobalConfigController::class, 'info_company'])->name('admin.configuracao-global.info-empresa')->middleware('can:acesso_config_global');
    Route::put('/configuracao-global/update-info-empresa', [GlobalConfigController::class, 'update_info_company'])->name('admin.configuracao-global.update-info-empresa')->middleware('can:acesso_config_global');
    Route::put('/configuracao-global/update-taxa', [GlobalConfigController::class, 'update_taxa'])->name('admin.configuracao-global.update-taxa')->middleware('can:acesso_config_global');
    Route::put('/configuracao-global/update-zona', [GlobalConfigController::class, 'update_zona'])->name('admin.configuracao-global.update-zona')->middleware('can:acesso_config_global');

    Route::get('/clientes', [ClienteController::class, 'page_index'])->name('admin.clientes.index')->middleware('can:acesso_clientes');

    Route::get('/venda-manual', [VendaManualController::class, 'page_index'])->name('admin.venda-manual.index')->middleware('can:acesso_venda_manual');
    Route::get('/venda-manual/create', [VendaManualController::class, 'page_create'])->name('admin.venda-manual.create')->middleware('can:acesso_venda_manual');
    Route::post('/venda-manual/delete', [VendaManualController::class, 'delete'])->name('admin.venda-manual.delete')->middleware('can:acesso_venda_manual');
    Route::get('/venda-manual/venda/{id}', [VendaManualController::class, 'page_sale'])->name('admin.venda-manual.venda')->middleware('can:acesso_venda_manual');
    Route::post('/venda-manual/venda/add-produto', [VendaManualController::class, 'add_product'])->name('admin.venda-manual.add-produto')->middleware('can:acesso_venda_manual');
    Route::post('/venda-manual/venda/remove-produto', [VendaManualController::class, 'remove_product'])->name('admin.venda-manual.remove-produto')->middleware('can:acesso_venda_manual');
    Route::post('/venda-manual/venda/adicional', [VendaManualController::class, 'additional'])->name('admin.venda-manual.adicional')->middleware('can:acesso_venda_manual');
    Route::post('/venda-manual/venda/finalizar', [VendaManualController::class, 'finish'])->name('admin.venda-manual.finalizar')->middleware('can:acesso_venda_manual');
    Route::get('/venda-manual/venda/imprimir/{venda_id}/{forma_pagamento_id}/{zona_id?}/{endereco?}/{dinheiro?}/{troco?}', [VendaManualController::class, 'imprimir'])->name('admin.venda-manual.imprimir')->middleware('can:acesso_venda_manual');

    Route::get('/pdv', [PDVController::class, 'page_index'])->name('admin.pdv.index')->middleware('can:acesso_pdv');
    Route::get('/pdv/create', [PDVController::class, 'page_create'])->name('admin.pdv.create')->can('acesso_pdv');
    Route::post('/pdv/delete', [PDVController::class, 'delete'])->name('admin.pdv.delete')->middleware('can:acesso_pdv');
    Route::get('/pdv/venda/{id}', [PDVController::class, 'page_sale'])->name('admin.pdv.venda')->middleware('can:acesso_pdv');
    Route::post('/pdv/venda/add-produto', [PDVController::class, 'add_product'])->name('admin.pdv.add-produto')->middleware('can:acesso_pdv');
    Route::post('/pdv/venda/remove-produto', [PDVController::class, 'remove_product'])->name('admin.pdv.remove-produto')->middleware('can:acesso_pdv');
    Route::post('/pdv/venda/concluir-venda', [PDVController::class, 'finish_sale'])->name('admin.pdv.concluir-venda')->middleware('can:acesso_pdv');
    Route::get('/pdv/venda/imprimir/{id}', [PDVController::class, 'imprimir_venda'])->name('admin.pdv.imprimir-venda')->middleware('can:acesso_pdv');
    Route::post('/pdv/venda/adicional', [PDVController::class, 'additional_sale'])->name('admin.pdv.adicional-venda')->middleware('can:acesso_pdv');

    Route::get('/pdv2', [PDV2Controller::class, 'page_index'])->name('admin.pdv2.index')->middleware('can:acesso_pdv2');
    Route::get('/pdv2/create', [PDV2Controller::class, 'page_create'])->name('admin.pdv2.create')->middleware('can:acesso_pdv2');
    Route::post('/pdv2/delete', [PDV2Controller::class, 'delete'])->name('admin.pdv2.delete')->middleware('can:acesso_pdv');
    Route::get('/pdv2/venda/{id}', [PDV2Controller::class, 'page_sale'])->name('admin.pdv2.venda')->middleware('can:acesso_pdv2');
    Route::post('/pdv2/venda/add-produto', [PDV2Controller::class, 'add_product'])->name('admin.pdv2.add-produto')->middleware('can:acesso_pdv2');
    Route::post('/pdv2/venda/remove-produto', [PDV2Controller::class, 'remove_product'])->name('admin.pdv2.remove-produto')->middleware('can:acesso_pdv2');
    Route::post('/pdv2/venda/concluir-venda', [PDV2Controller::class, 'finish_sale'])->name('admin.pdv2.concluir-venda')->middleware('can:acesso_pdv2');
    Route::get('/pdv2/venda/imprimir/{id}', [PDV2Controller::class, 'imprimir_venda'])->name('admin.pdv2.imprimir-venda')->middleware('can:acesso_pdv2');
    Route::post('/pdv2/venda/adicional', [PDV2Controller::class, 'additional_sale'])->name('admin.pdv2.adicional-venda')->middleware('can:acesso_pdv2');

    Route::get('/comandas', [ComandaController::class, 'page_index'])->name('admin.comandas.index')->middleware('can:acesso_comandas');
    Route::post('/comandas/store', [ComandaController::class, 'store'])->name('admin.comandas.store')->middleware('can:acesso_comandas');
    Route::post('/comandas/closed', [ComandaController::class, 'closed'])->name('admin.comandas.closed')->middleware('can:acesso_comandas');
    Route::get('/comandas/delete/{id}', [ComandaController::class, 'destroy'])->name('admin.comandas.destroy')->middleware('can:acesso_comandas');
    Route::post('/comandas/add-produto', [ComandaController::class, 'add_product'])->name('admin.comandas.add-produto')->middleware('can:acesso_comandas');
    Route::post('/comandas/remove-produto', [ComandaController::class, 'remove_product'])->name('admin.comandas.remove-produto')->middleware('can:acesso_comandas');
    Route::get('/comandas/imprimir/{id}', [ComandaController::class, 'imprimir'])->name('admin.comandas.imprimir')->middleware('can:acesso_comandas');
    Route::post('/comandas/novo-pedido', [ComandaController::class, 'new_request'])->name('admin.comandas.novo-pedido')->middleware('can:acesso_comandas');

    Route::get('/pedidos', [PedidoController::class, 'page_index'])->name('admin.pedidos.index')->middleware('can:acesso_pedidos');
    Route::get('/pedidos/imprimir/{id}', [PedidoController::class, 'imprimir_pedido'])->name('admin.pedidos.imprimir')->middleware('can:acesso_pedidos');
    Route::post('/pedidos/change-status', [PedidoController::class, 'status'])->name('admin.pedidos.change-status')->middleware('can:acesso_pedidos');
    Route::post('/pedidos/rejeitar', [PedidoController::class, 'reject'])->name('admin.pedidos.rejeitar')->middleware('can:acesso_pedidos');

    Route::get('/fornecedores', [FornecedorController::class, 'page_index'])->name('admin.fornecedores.index')->middleware('can:acesso_fornecedores');
    Route::post('/fornecedores/novo', [FornecedorController::class, 'new'])->name('admin.fornecedores.novo')->middleware('can:acesso_fornecedores');

    Route::get('/vendas', [VendaController::class, 'page_index'])->name('admin.vendas.index')->middleware('can:acesso_vendas');
    Route::post('/vendas/consulta-produtos', [VendaController::class, 'consult_products_ajax'])->name('admin.vendas.consulta-produtos')->middleware('can:acesso_vendas');
    Route::get('/vendas/detalhe/{id}', [VendaController::class, 'detail_sale'])->name('admin.vendas.detalhe.index')->middleware('can:acesso_vendas');

    Route::get('/caixa', [CaixaController::class, 'page_index'])->name('admin.caixa.index')->middleware('can:acesso_caixa');
    Route::post('/caixa/abrir', [CaixaController::class, 'open'])->name('admin.caixa.open')->middleware('can:acesso_caixa');
    Route::post('/caixa/fechar', [CaixaController::class, 'close'])->name('admin.caixa.close')->middleware('can:acesso_caixa');

    Route::get('/bancos', [BancoController::class, 'page_index'])->name('admin.banco.index')->middleware('can:acesso_bancos');
    Route::post('/bancos/mov-extra', [BancoController::class, 'mov_extra'])->name('admin.banco.mov-extra')->middleware('can:acesso_bancos');

    Route::get('/fidelidade', [FidelidadeController::class, 'page_index'])->name('admin.fidelidade.index')->middleware('can:acesso_fidelidade');
    Route::post('/fidelidade/remover', [FidelidadeController::class, 'remove'])->name('admin.fidelidade.remover')->middleware('can:acesso_fidelidade');
    Route::post('/fidelidade/adicionar', [FidelidadeController::class, 'add'])->name('admin.fidelidade.adicionar')->middleware('can:acesso_fidelidade');

    Route::delete('/estoque/produtos/delete/{id}', [EstoqueController::class, 'destroy'])->name('admin.estoque.produtos.destroy')->middleware('can:acesso_estoque');
    Route::put('/estoque/produtos/update', [EstoqueController::class, 'update'])->name('admin.estoque.produtos.update')->middleware('can:acesso_estoque');
    Route::put('/estoque/produtos/update-variavel', [EstoqueController::class, 'update_variable'])->name('admin.estoque.produtos.update-variavel')->middleware('can:acesso_estoque');
    Route::get('/estoque/produtos/create', [EstoqueController::class, 'page_create'])->name('admin.estoque.produtos.page-create')->middleware('can:acesso_estoque');
    Route::get('/estoque/produtos/create-combo', [EstoqueController::class, 'page_create_combo'])->name('admin.estoque.produtos.page-create-combo')->middleware('can:acesso_estoque');
    Route::get('/estoque/produtos/editar/{id}', [EstoqueController::class, 'page_edit'])->name('admin.estoque.produtos.page-edit')->middleware('can:acesso_estoque');
    Route::get('/estoque/produtos/', [EstoqueController::class, 'page_index'])->name('admin.estoque.produtos.page-index')->middleware('can:acesso_estoque');
    Route::post('/estoque/produtos/', [EstoqueController::class, 'store'])->name('admin.estoque.produtos.store')->middleware('can:acesso_estoque');
    Route::post('/estoque/produtos/combo', [EstoqueController::class, 'store_combo'])->name('admin.estoque.produtos.store-combo')->middleware('can:acesso_estoque');
    Route::post('/estoque/produtos/variavel-promocao/', [EstoqueController::class, 'variable_promotion'])->name('admin.estoque.produtos.variavel-promocao')->middleware('can:acesso_estoque');
    Route::post('/estoque/produtos/variavel-status/', [EstoqueController::class, 'variable_status'])->name('admin.estoque.produtos.variavel-status')->middleware('can:acesso_estoque');

    Route::get('/estoque/manifestos', [EstoqueController::class, 'page_manifest'])->name('admin.estoque.manifesto.index')->middleware('can:acesso_estoque');
    Route::post('/estoque/manifestos/novo', [EstoqueController::class, 'new_manifest'])->name('admin.estoque.manifesto.novo')->middleware('can:acesso_estoque');

    Route::get('/estoque/lotes', [EstoqueController::class, 'page_batch'])->name('admin.estoque.lote.index')->middleware('can:acesso_estoque');
    Route::post('/estoque/lotes/novo', [EstoqueController::class, 'new_batch'])->name('admin.estoque.lote.novo')->middleware('can:acesso_estoque');

    Route::get('/financeiro/contas-a-pagar', [PagarContaController::class, 'page_index'])->name('admin.financeiro.contas-a-pagar.index')->middleware('can:acesso_financeiro');
    Route::post('/financeiro/contas-a-pagar', [PagarContaController::class, 'store'])->name('admin.financeiro.contas-a-pagar.store')->middleware('can:acesso_financeiro');
    Route::delete('/financeiro/contas-a-pagar/delete/{id}', [PagarContaController::class, 'destroy'])->name('admin.financeiro.contas-a-pagar.destroy')->middleware('can:acesso_financeiro');
    Route::get('/financeiro/contas-a-pagar/pagar/{id}', [PagarContaController::class, 'pay'])->name('admin.financeiro.contas-a-pagar.pagar')->middleware('can:acesso_financeiro');
    Route::get('/financeiro/movimentacoes', [MovimentacoesFinanceiraController::class, 'page_index'])->name('admin.financeiro.movimentacoes.index')->middleware('can:acesso_financeiro');
})->middleware('check.client');