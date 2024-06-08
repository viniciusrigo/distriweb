@extends('adminlte::page')

@section('title', 'DW - Contas a Pagar')

@section('content_header')

@stop
    
@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Nova conta à pagar</h3>
    </div>
    
    <div class="card-body">
        <form class="form-row" action="{{ route('admin.financeiro.contas-a-pagar.store') }}" method="POST">
            @csrf
            <div class="form-group col-md-2" style="padding: 3px;">
                <label for="tipo_conta" style="margin: 0px;">Conta<code>*</code></label>
                <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="tipo_conta" name="tipo_conta">
                    <option value="1">Água</option>
                    <option value="2">Luz</option>
                    <option value="3">Internet</option>
                </select>
            </div>
            <div class="form-group col-md-2" style="padding: 3px;">
                <label for="fornecedor_id" style="margin: 0px;">Fornecedor</label>
                <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="fornecedor_id" name="fornecedor_id">
                    <option value="1">Sanepar</option>
                    <option value="2">Copel</option>
                    <option value="3">Sercomtel</option>
                </select>
            </div>
            <div class="form-group col-md-2" style="padding: 3px;">
                <label for="ativo" style="margin: 0px;">Ativo<code>*</code></label>
                <select style="margin: 0px;" class="custom-select form-control-border border-width-2" id="ativo" name="ativo">
                    <option value="s">Sim</option>
                    <option value="n">Não</option>
                </select>
            </div>
            <div class="d-flex form-group col-md-12">
                <button style="margin-left: auto;" class="btn btn-success" type="submit">Cadastrar</button>
            </div>    
        </form>
    </div>
    
</div>
@stop