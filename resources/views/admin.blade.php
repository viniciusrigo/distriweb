@extends('adminlte::page')

@section('title', 'DW - Dashboard')

@section('css')
    <style>
        ::-webkit-scrollbar{
            width: 7px;
        }
        ::-webkit-scrollbar-thumb{
            border-radius: 30px;
            background-color: #cccccc;
        }
        ::-webkit-scrollbar-thumb:hover{
            border-radius: 30px;
            background-color: #a6a6a6;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-center row mb-1">
        {{-- INDICADORES --}}
        @can('acesso_financeiro')
        <div class="d-flex justify-content-center row col-11 p-2">
            <select id="filtros" class="form-control col-2">
                <option value="hoje">Hoje</option>
                <option value="semana">Últimos 7 dias</option>
                <option value="mes" selected>Mês Atual</option>
                <option value="trimestre">Trimestre</option>
                <option value="ano">Ano Atual</option>
            </select>
            <div class="d-flex align-items-center ml-3">
                <input type="checkbox" id="check_especifico" onclick="especifico()">
                <span class="ml-1">Dia Específico</span>
            </div>
            <div style="display: none;" class="ml-1" id="form_especifico">
                <input id="dia-especifico" class="form-control" type="date" name="especifico">
                <button id="buscar-especifico" class="btn btn-info" type="button">Buscar</button>
            </div>
            <div class="d-flex align-items-center ml-3">
                <input type="checkbox" id="check_periodo" onclick="periodo()">
                <span class="ml-1">Período</span>
            </div>
            <div style="display: none;" id="form_periodo" class="ml-1">
                <input id="dia-inicial" class="form-control" type="date" name="inicial">
                <input id="dia-final" class="form-control" type="date" name="final">
                <button id="buscar-periodo" class="btn btn-info" type="button">Buscar</button>
            </div>
        </div>
        <div class="d-flex justify-content-center row col-11">
            <div class="info-box shadow-none col-md-2 col-sm-6 col-12 m-1">
                <span style="background-color: #d3d1ff; color: #6f68fe" class="info-box-icon"><i class="fas fa fa-info"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Vendas</span>
                    <span id="vendas" style="font-size: 25px;" class="info-box-number">{{ $vendas }}</span>
                </div>           
            </div>
            <div class="info-box shadow-none col-md-2 col-sm-6 col-12 m-1">
                <span style="background-color: #a3dcff; color: #178ed7" class="info-box-icon"><i class="fa-solid fa-arrow-trend-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Faturamento</span>
                    <span id="faturamento" style="font-size: 25px;" class="info-box-number">R${{ $faturamento }}</span>
                </div>           
            </div>
            <div class="info-box shadow-none col-md-2 col-sm-6 col-12 m-1">
                <span style="background-color: #ffdbad; color: #e28914" class="info-box-icon"><i class="fa-solid fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Lucro Bruto</span>
                    <span id="lucro-bruto" style="font-size: 25px;" class="info-box-number">R${{ $lucro }}</span>
                </div>           
            </div>
            <div class="info-box shadow-none col-md-2 col-sm-6 col-12 m-1">
                <span style="background-color: #ffaeae; color: #e21414" class="info-box-icon"><i class="fa-solid fa-arrow-trend-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Despesas</span>
                    <span id="despesas" style="font-size: 25px;" class="info-box-number">R${{ $despesas }}</span>
                </div>           
            </div>
            <div class="info-box shadow-none col-md-2 col-sm-6 col-12 m-1">
                <span style="background-color: #ccffb3; color: #49b812" class="info-box-icon"><i class="fa-solid fa-sack-dollar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Lucro Líquido</span>
                    <span id="lucro-liquido" style="font-size: 25px;" class="info-box-number">R${{ $lucro - $despesas }}</span>
                </div>           
            </div>
        </div>
        @endcan
        {{-- TABELA --}}
        <div class="d-flex justify-content-center row col-12">
            <div class="col-md-3 m-1">
                <div style="height: 350px; overflow:auto;" class="card table-responsive p-0">  
                    <div class="card-header">
                        <strong>20 Mais Vendidos PDV's</strong>
                    </div>      
                    <div class="card-body p-2">    
                        <div class="row">
                            <div class="col-12">
                                <table id="tabela_estoque" class="table table-sm table-hover table-striped compact">
                                    <thead class="bg-white" style="position: sticky; top: 0;">
                                        <tr>
                                            <th>Produto</th>
                                            <th>Vendidos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($mais_vendidos_pdv)
                                            @foreach ($mais_vendidos_pdv as $produto)    
                                                <tr class="tb-tr-bd">
                                                    <td  style="text-align:left">{{ $produto["nome"] }} {{ $produto["variavel_nome"] }}</td>
                                                    <td  style="text-align:left">{{ $produto["total"] }}</td>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>      
                    </div>
                </div>
            </div>
            <div class="col-md-3 m-1">
                <div style="height: 350px; overflow:auto;" class="card table-responsive p-0">  
                    <div class="card-header">
                        <strong>20 Mais Vendidos Comandas</strong>
                    </div>      
                    <div class="card-body p-2">    
                        <div class="row">
                            <div class="col-12">
                                <table id="tabela_estoque" class="table table-sm table-hover table-striped compact">
                                    <thead class="bg-white" style="position: sticky; top: 0;">
                                        <tr>
                                            <th>Produto</th>
                                            <th>Vendidos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($mais_vendidos_comandas)
                                            @foreach ($mais_vendidos_comandas as $produto)    
                                                <tr class="tb-tr-bd">
                                                    <td  style="text-align:left">{{ $produto["nome"] }} {{ $produto["variavel_nome"] }}</td>
                                                    <td  style="text-align:left">{{ $produto["total"] }}</td>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>      
                    </div>
                </div>
            </div>
            <div class="col-md-3 m-1">
                <div style="height: 350px; overflow:auto;" class="card table-responsive p-0">  
                    <div class="card-header">
                        <strong>20 Mais Vendidos Online</strong>
                    </div>      
                    <div class="card-body p-2">    
                        <div class="row">
                            <div class="col-12">
                                <table id="tabela_estoque" class="table table-sm table-hover table-striped compact">
                                    <thead class="bg-white" style="position: sticky; top: 0;">
                                        <tr>
                                            <th>Produto</th>
                                            <th>Vendidos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($mais_vendidos_pedidos)
                                            @foreach ($mais_vendidos_pedidos as $produto)    
                                                <tr class="tb-tr-bd">
                                                    <td  style="text-align:left">{{ $produto["nome"] }} {{ $produto["variavel_nome"] }}</td>
                                                    <td  style="text-align:left">{{ $produto["total"] }}</td>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>      
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-center row col-12">
            <div class="col-md-3 m-1">
                <div style="height: 350px; overflow:auto;" class="card table-responsive p-0">  
                    <div class="card-header">
                        <strong>Produtos Lucro Maior</strong>
                    </div>      
                    <div class="card-body p-2">    
                        <div class="row">
                            <div class="col-12">
                                <table id="tabela_estoque" class="table table-sm table-hover table-striped compact">
                                    <thead class="bg-white" style="position: sticky; top: 0;">
                                        <tr>
                                            <th>Produto</th>
                                            <th>Estoque</th>
                                            <th>Lucro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($produtos_lucro)
                                            @foreach ($produtos_lucro as $produto)    
                                                <tr class="tb-tr-bd">
                                                    <td  style="text-align:left">{{ $produto["nome"] }} {{ $produto["variavel_nome"] }}</td>
                                                    <td  style="text-align:left">{{ $produto["variavel_quantidade"] }}</td>
                                                    <td  style="text-align:left">R${{ $produto["lucro"] }}</td>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>      
                    </div>
                </div>
            </div>
            <div class="col-md-3 m-1">
                <div style="height: 350px; overflow:auto;" class="card table-responsive p-0">  
                    <div class="card-header">
                        <strong style="color: red">Vencimento Próximo</strong>
                    </div>      
                    <div class="card-body p-2">    
                        <div class="row">
                            <div class="col-12">
                                <table id="tabela_estoque" class="table table-sm table-hover table-striped compact">
                                    <thead class="bg-white" style="position: sticky; top: 0;">
                                        <tr>
                                            <th>Produto</th>
                                            <th>Estoque</th>
                                            <th>Validade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($produtos_vencimento)
                                            @foreach ($produtos_vencimento as $produto)    
                                                <tr class="tb-tr-bd">
                                                    <td style="text-align:left">{{ $produto["nome"] }} {{ $produto["variavel_nome"] }}</td>
                                                    <td style="text-align:left">{{ $produto["variavel_quantidade"] }}</td>
                                                    <td style="text-align:left; color: red">{{ date("d/m/Y", strtotime($produto["validade"])) }}</td>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>      
                    </div>
                </div>
            </div>
            <div class="col-md-3 m-1">
                <div style="height: 350px; overflow:auto;" class="card table-responsive p-0">  
                    <div class="card-header">
                        <strong style="color: red">Estoque Baixo</strong>
                    </div>      
                    <div class="card-body p-2">    
                        <div class="row">
                            <div class="col-12">
                                <table id="tabela_estoque" class="table table-sm table-hover table-striped compact">
                                    <thead class="bg-white" style="position: sticky; top: 0;">
                                        <tr>
                                            <th>Produto</th>
                                            <th>Estoque</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($produtos_estoque_baixo)
                                            @foreach ($produtos_estoque_baixo as $produto)    
                                                <tr class="tb-tr-bd">
                                                    <td style="text-align:left">{{ $produto["nome"] }} {{ $produto["variavel_nome"] }}</td>
                                                    <td style="text-align:left; color: red;">{{ $produto["variavel_quantidade"] }}</td>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>      
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center col-12">
            <div class="col-md-3 m-1">
                <div style="height: 350px; overflow:auto;" class="card table-responsive p-0">  
                    <div class="card-header">
                        <strong>Produtos Parados</strong>
                    </div>      
                    <div class="card-body p-2">    
                        <div class="row">
                            <div class="col-12">
                                <table id="tabela_estoque" class="table table-sm table-hover table-striped compact">
                                    <thead class="bg-white" style="position: sticky; top: 0;">
                                        <tr>
                                            <th>Produto</th>
                                            <th>Estoque</th>
                                            <th>Ult Compra</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($produtos_parados)
                                            @foreach ($produtos_parados as $produto)    
                                                <tr class="tb-tr-bd">
                                                    <td  style="text-align:left">{{ $produto["nome"] }} {{ $produto["variavel_nome"] }}</td>
                                                    <td  style="text-align:left">{{ $produto["variavel_quantidade"] }}</td>
                                                    <td  style="text-align:left">{{ $produto["ult_compra"] == null ? "Nunca Vendido" : date("d/m/Y", strtotime($produto["ult_compra"])) }}</td>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>      
                    </div>
                </div>
            </div>
        </div>
        {{-- FIM TABELA --}}
        <div class="col-md-8 m-1">
            <canvas id="myChart"></canvas>
        </div>
        
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('myChart');

        function getDados(){
            $.ajax({
                url: "/admin/dados",
                method: 'post',
                success: function(response){
                    // console.log(response.length)
                    // console.log(response)
                    // console.log(response[0])
                    // console.log(response[0].mes)
                    // console.log("R$"+response[0].faturamento)
                    var labels = []
                    var data = []
                    var meses = ["Null", "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"]
                    for(x = 0; x < response.length; x++){
                        labels.push(meses[response[x].mes])
                        data.push(response[x].faturamento)
                    }
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Faturamento Anual (R$)',
                                data: data,
                                backgroundColor: [
                                'rgba(255, 99, 132, 0.2)'
                                ],
                                borderColor: [
                                'rgb(255, 99, 132)'
                                ],
                                borderWidth: 1
                            }]
                         }
                    });

                    // new Chart(ctx, {
                    //     type: 'bar',
                    //     data: {
                    //         labels: labels,
                    //         datasets: [{
                    //             label: '# of Votes',
                    //             data: data,
                    //             borderWidth: 1
                    //         }]
                    //     },
                    //     options: {
                    //         scales: {
                    //         y: {
                    //             beginAtZero: true
                    //         }
                    //         }
                    //     }
                    // });

                    // new Chart(ctx, {
                    //     type: 'line',
                    //     data: {
                    //         labels: labels,
                    //         datasets: [{
                    //             label: 'Faturamento (R$)',
                    //             data: data,
                    //             fill: false,
                    //             borderColor: 'rgb(75, 192, 192)',
                    //             tension: 0.1
                    //         }]
                    //     },
                    // });
                }
            });
        }

        $(document).ready(function(){
            var _token = $('meta[name="_token"]').attr('content');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': _token
                }
            });

            getDados()

            $("select").on("change", function(){
                let valor = $("select option:selected").val()
                $.ajax({
                    url: "/admin/indicadores-ajax",
                    method: 'post',
                    data: {
                        'data': valor
                    },
                    success: function(response){
                        $("#vendas").html("")
                        $("#faturamento").html("")
                        $("#lucro-bruto").html("")
                        $("#despesas").html("")
                        $("#lucro-liquido").html("")
                        $("#vendas").html(response.vendas)
                        $("#faturamento").html("R$"+response.faturamento)
                        $("#lucro-bruto").html("R$"+response.lucro)
                        $("#despesas").html("R$"+response.despesas)
                        $("#lucro-liquido").html("R$"+ (response.lucro - response.despesas))
                    }
                });
            })

            $("#buscar-especifico").on("click", function(){
                var dia = $("#dia-especifico").val()
                $.ajax({
                    url: "/admin/indicadores-ajax",
                    method: 'post',
                    data: {
                        'data': 'especifico',
                        'dia': dia
                    },
                    success: function(response){
                        $("#vendas").html("")
                        $("#faturamento").html("")
                        $("#lucro-bruto").html("")
                        $("#despesas").html("")
                        $("#lucro-liquido").html("")
                        $("#vendas").html(response.vendas)
                        $("#faturamento").html("R$"+response.faturamento)
                        $("#lucro-bruto").html("R$"+response.lucro)
                        $("#despesas").html("R$"+response.despesas)
                        $("#lucro-liquido").html("R$"+ (response.lucro - response.despesas))
                    }
                });
            })

            $("#buscar-periodo").on("click", function(){
                var dia_inicial = $("#dia-inicial").val()
                var dia_final = $("#dia-final").val()
                alert(dia_inicial)
                alert(dia_final)
                $.ajax({
                    url: "/admin/indicadores-ajax",
                    method: 'post',
                    data: {
                        'data': 'periodo',
                        'dia_inicial': dia_inicial,
                        'dia_final': dia_final
                    },
                    success: function(response){
                        console.log(response)
                        console.log(response.despesas)
                        $("#vendas").html("")
                        $("#faturamento").html("")
                        $("#lucro-bruto").html("")
                        $("#despesas").html("")
                        $("#lucro-liquido").html("")
                        $("#vendas").html(response.vendas)
                        $("#faturamento").html("R$"+response.faturamento)
                        $("#lucro-bruto").html("R$"+response.lucro)
                        $("#despesas").html("R$"+response.despesas)
                        $("#lucro-liquido").html("R$"+ (response.lucro - response.despesas))
                    }
                });
            })



        })

        function periodo(){
            let status = document.getElementById("form_periodo").style.display
            if(status == 'none'){
                document.getElementById("form_periodo").style.display = 'flex'
            } else {
                document.getElementById("form_periodo").style.display = 'none'
            }
        }

        function especifico(){
            let status = document.getElementById("form_especifico").style.display
            if(status == 'none'){
                document.getElementById("form_especifico").style.display = 'flex'
            } else {
                document.getElementById("form_especifico").style.display = 'none'
            }
        }        
    </script>
@endsection
