<?php
namespace App\Http\Controllers;

use App\Models\Orcamento;
use Illuminate\Http\Request;

class OrcamentoController extends Controller
{
    public function create()
    {
        $porte = request('porte', '');
        return view('pages.orcamento', compact('porte'));
    }

    public function store(Request $request)
    {
        // Fallback server-side save (WhatsApp is handled client-side)
        $request->validate([
            'nome'      => 'required|string|max:150',
            'email'     => 'nullable|email|max:200',
            'telefone'  => 'required|string|max:20',
            'porte'     => 'nullable|string|max:60',
            'quantidade'=> 'nullable|integer|min:1',
            'observacoes'=> 'nullable|string|max:1000',
        ]);

        Orcamento::create($request->only(['nome','email','telefone','porte','quantidade','observacoes']));

        return redirect()->route('orcamento')
            ->with('success', 'Orçamento registrado! Nossa equipe entrará em contato.');
    }
}
