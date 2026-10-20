<?php
namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Depoimento;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    public function home()
    {
        $depoimentos = Depoimento::aprovados()->latest()->take(3)->get();
        $produtos = $this->produtosPorPorte();
        return view('pages.home', compact('depoimentos', 'produtos'));
    }

    /**
     * Preços vêm sempre da tabela produtos (uma única fonte), indexados pelo
     * porte — assim a home e a página Produtos nunca mostram valores diferentes.
     */
    private function produtosPorPorte()
    {
        return Produto::ativos()->get()->keyBy('porte');
    }

    public function sobre()    { return view('pages.sobre'); }
    public function diferenciais() { return view('pages.diferenciais'); }

    public function produtos()
    {
        $produtos = $this->produtosPorPorte();
        return view('pages.produtos', compact('produtos'));
    }

    public function oferta() { return view('pages.oferta'); }
}
