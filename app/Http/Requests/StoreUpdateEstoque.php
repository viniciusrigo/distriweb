<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateEstoque extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $rules = [
            'id' => '',
            'nome' => '',
            'codigo_barras' => 'unique:produtos',
            'preco' => '',
            'preco_custo' => '',
            'preco_promocao' => '',
            'quantidade' => '',
            'categoria_id' => '',
            'sku' => '',
            'pontos' => '',
            'cfop' => '',
            'ncm' => '',
            'cst_csosn' => '',
            'cst_pis' => '',
            'cst_cofins' => '',
            'cst_ipi' => '',
            'perc_icms' => '',
            'perc_pis' => '',
            'perc_cofins' => '',
            'perc_ipi' => '',
            'promocao' => '',
            'ativo' => '',
            'ult_compra' => '',
            'data_cadastro' => ''
        ];

        if ($this->method() === 'PUT') {
            $rules['codigo_barras'] = [
                "unique:produtos,codigo_barras,{$this->codigo_barras},codigo_barras"
            ];
        }

        return $rules;
    }
}
