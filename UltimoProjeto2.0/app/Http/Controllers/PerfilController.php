<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    public function show()
    {
        $tutor = Auth::user()->tutor;

        return view('pages.perfil', compact('tutor'));
    }

    public function update(Request $request)
    {
        $tutor = Auth::user()->tutor;

        $validated = $request->validate([
            'nome'     => ['required', 'string', 'max:150'],
            'telefone' => ['required', 'string', 'max:20'],
            'email'    => ['required', 'email', 'max:200', Rule::unique('tutores', 'email')->ignore($tutor->id)],
            'endereco' => ['nullable', 'string', 'max:255'],
        ], [
            'nome.required'     => 'Informe o seu nome.',
            'telefone.required' => 'Informe o telefone.',
            'email.required'    => 'Informe o e-mail.',
            'email.unique'      => 'Este e-mail já está em uso.',
        ]);

        $tutor->update($validated);

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }
}
