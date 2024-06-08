<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EstoqueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "nome" => $this->faker->unique()->word,
            "categoria_id" => 1,
            "quantidade" => $this->faker->randomNumber,
            "sku" => $this->faker->randomNumber,
            "pontos"  => $this->faker->randomNumber,
            "preco" => $this->faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 50),
            "preco_custo" => $this->faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 50),
            "preco_promocao"  => $this->faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 50),
            "cfop"  => $this->faker->numberBetween($min = 4000, $max = 5000),
            "ncm"  => $this->faker->numberBetween($min = 20000000, $max = 30000000),
            "codigo_barras" => $this->faker->numberBetween($min = 1, $max = 9000),
            "cst_csosn" => $this->faker->numberBetween($min = 1, $max = 9000),
            "cst_pis" => $this->faker->numberBetween($min = 1, $max = 9000),
            "cst_cofins" => $this->faker->numberBetween($min = 1, $max = 9000),
            "cst_ipi" => $this->faker->numberBetween($min = 1, $max = 9000),
            "perc_icms" => $this->faker->numberBetween($min = 1, $max = 9000),
            "perc_pis" => $this->faker->numberBetween($min = 1, $max = 9000),
            "perc_cofins" => $this->faker->numberBetween($min = 1, $max = 9000),
            "perc_ipi" => $this->faker->numberBetween($min = 1, $max = 9000),
            "promocao" => 'n',
            "ativo" => 's',
            "ult_compra" => date("Y-m-d", time()),
            "data_cadastro" => date("Y-m-d", time())
        ];
    }
}
