<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnimalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'    => ['required', 'string', 'max:100'],
            'especie' => ['required', 'string', 'max:60'],
            'raca'    => ['nullable', 'string', 'max:100'],
            'porte'   => ['required', 'in:pequeno,medio,grande'],
            'idade'   => ['nullable', 'integer', 'min:0', 'max:50'],
            'peso'    => ['nullable', 'numeric', 'min:0.1', 'max:999'],

            // Trava de estoque desativada por enquanto: código inexistente
            // vira uma coleira nova (ver Coleira::obterOuCriar). A checagem
            // de "já vinculada a outro animal" continua em AnimalController.
            'codigo_coleira' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'    => 'Informe o nome do animal.',
            'especie.required' => 'Informe a espécie.',
            'porte.required'   => 'Selecione o porte.',
            'porte.in'         => 'Porte inválido (pequeno, medio ou grande).',
            'peso.min'         => 'O peso deve ser maior que 0.',
            'idade.min'        => 'A idade não pode ser negativa.',

            'codigo_coleira.required' => 'Informe o código da coleira.',
        ];
    }
}
