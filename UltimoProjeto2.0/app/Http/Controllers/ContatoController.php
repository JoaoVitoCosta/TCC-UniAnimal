<?php
namespace App\Http\Controllers;

use App\Models\Contato;
use Illuminate\Http\Request;

class ContatoController extends Controller
{
    public function index()
    {
        return view('pages.contactos');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'     => ['required', 'string', 'min:2', 'max:100'],
            'email'    => ['required', 'email', 'max:200'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'assunto'  => ['nullable', 'string', 'max:150'],
            'mensagem' => ['required', 'string', 'min:10', 'max:3000'],
        ], [
            'nome.required'     => 'O nome é obrigatório.',
            'email.required'    => 'O e-mail é obrigatório.',
            'email.email'       => 'Informe um e-mail válido.',
            'mensagem.required' => 'A mensagem é obrigatória.',
            'mensagem.min'      => 'A mensagem deve ter ao menos 10 caracteres.',
        ]);

        Contato::create($validated);

        return back()->with('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
    }
}
