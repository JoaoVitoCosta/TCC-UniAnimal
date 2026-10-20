<?php
namespace App\Http\Controllers;

use App\Models\Depoimento;
use Illuminate\Http\Request;

class DepoimentoController extends Controller
{
    public function index()
    {
        $depoimentos = Depoimento::aprovados()->latest()->get();
        return view('pages.depoimentos', compact('depoimentos'));
    }

    public function create()
    {
        return view('pages.novo-depoimento');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'       => ['required', 'string', 'min:2', 'max:100'],
            'email'      => ['required', 'email', 'max:200'],
            'telefone'   => ['nullable', 'string', 'max:20'],
            'depoimento' => ['required', 'string', 'min:20', 'max:2000'],
            'estrelas'   => ['nullable', 'integer', 'min:1', 'max:5'],
        ], [
            'nome.required'       => 'O nome é obrigatório.',
            'email.required'      => 'O e-mail é obrigatório.',
            'email.email'         => 'Informe um e-mail válido.',
            'depoimento.required' => 'O depoimento é obrigatório.',
            'depoimento.min'      => 'O depoimento deve ter ao menos 20 caracteres.',
        ]);

        $validated['estrelas'] = $validated['estrelas'] ?? 5;

        Depoimento::create($validated);

        return redirect()->route('depoimentos')
            ->with('success', 'Obrigado pelo seu depoimento! Ele foi publicado com sucesso.');
    }
}
