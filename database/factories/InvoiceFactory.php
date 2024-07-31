<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome_fantasia' => $this->faker->company,
            'email' => $this->faker->companyEmail,
            'cnpj' => $this->faker->numerify('##############'),
            'codigo_barra' => $this->faker->regexify('[0-9]{5}\.[0-9]{5} [0-9]{5}\.[0-9]{5} [0-9]{5}\.[0-9]{5} [0-9] [0-9]{12} [0-9]{2}'),
            'valor' => $this->faker->randomFloat(2, 100, 1000),
            'data_vencimento' => $this->faker->date('d/m/y'),
            'juros' => $this->faker->randomElement(['1%', '2%', '3%']),
        ];
    }
}
