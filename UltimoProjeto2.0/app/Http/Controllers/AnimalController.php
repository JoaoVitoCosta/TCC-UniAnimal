<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnimalRequest;
use App\Models\Animal;
use App\Models\Coleira;
use App\Repositories\AnimalRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AnimalController extends Controller
{
    public function __construct(private AnimalRepositoryInterface $animais)
    {
        // NOTA IMPORTANTE: authorizeResource() foi removido de propósito.
        // Ele depende internamente de $this->middleware(...), um recurso
        // que o Laravel 11+ removeu da classe base Controller (reforma
        // "routing without controllers"). Chamá-lo aqui derruba TODAS as
        // rotas de /animais com "Call to undefined method ::middleware()".
        // A autorização é feita explicitamente em cada método abaixo,
        // via $this->authorize(), que não depende de middleware.
    }

    private function tutorId(): int
    {
        return Auth::user()->tutor->id;
    }

    public function index()
    {
        $this->authorize('viewAny', Animal::class);

        $animais = $this->animais->paginate(
            $this->tutorId(),
            request()->only(['status', 'porte', 'busca']),
            15
        );

        return view('pages.animais.index', compact('animais'));
    }

    public function create()
    {
        $this->authorize('create', Animal::class);

        return view('pages.animais.form', ['animal' => new Animal()]);
    }

    /**
     * Busca a coleira pelo código digitado (criando-a se ainda não existir)
     * e garante que ela não pertence a outro animal. Lança 422 amigável
     * se já estiver vinculada.
     */
    private function localizarColeiraDisponivel(string $codigo, ?int $animalIdAtual = null): Coleira
    {
        $coleira = Coleira::obterOuCriar($codigo);

        $jaEDoProprioAnimal = $animalIdAtual !== null && $coleira->animal_id === $animalIdAtual;

        if (! $coleira->estaDisponivel() && ! $jaEDoProprioAnimal) {
            throw ValidationException::withMessages([
                'codigo_coleira' => 'Este código de coleira já está vinculado a outro animal.',
            ]);
        }

        return $coleira;
    }

    public function store(AnimalRequest $request)
    {
        $this->authorize('create', Animal::class);

        $coleira = $this->localizarColeiraDisponivel($request->validated('codigo_coleira'));

        // Nota: FormRequest::safe()->except(...) já retorna um array PHP puro
        // (verificado na fonte: Illuminate\Support\Traits\InteractsWithData::except()
        // chama ->all() internamente e devolve array). NÃO chamar ->all() de novo aqui.
        $data = $request->safe()->except('codigo_coleira');
        $data['tutor_id'] = $this->tutorId();
        $data['status'] = 'saudavel';

        $animal = $this->animais->create($data);

        $coleira->vincularAoAnimal($animal);

        return redirect()->route('animais.index')
            ->with('success', 'Animal cadastrado com sucesso! A coleira já pode ser usada para login no aplicativo.');
    }

    public function show(Animal $animal)
    {
        $this->authorize('view', $animal);

        $animal->load(['coleira.localizacoes' => fn ($q) => $q->take(30), 'ultimoBatimento', 'ultimaTemperatura']);
        return view('pages.animais.show', compact('animal'));
    }

    public function edit(Animal $animal)
    {
        $this->authorize('update', $animal);

        $animal->load('coleira');
        return view('pages.animais.form', compact('animal'));
    }

    public function update(AnimalRequest $request, Animal $animal)
    {
        $this->authorize('update', $animal);

        $novoCodigo = $request->validated('codigo_coleira');
        $coleiraAtual = $animal->coleira;

        $data = $request->safe()->except('codigo_coleira');
        $this->animais->update($animal, $data);

        // Só mexe na coleira se o código realmente mudou — evita
        // desvincular/revincular a mesma coleira sem necessidade.
        if (! $coleiraAtual || $coleiraAtual->codigo !== $novoCodigo) {
            $novaColeira = $this->localizarColeiraDisponivel($novoCodigo, $animal->id);

            // Libera a coleira antiga de volta para o estoque (disponível),
            // em vez de deixá-la presa a um animal que trocou de dispositivo.
            $coleiraAtual?->forceFill([
                'animal_id' => null,
                'status'    => 'disponivel',
                'ativada_em' => null,
            ])->save();

            $novaColeira->vincularAoAnimal($animal);
        }

        return redirect()->route('animais.show', $animal)
            ->with('success', 'Animal atualizado com sucesso!');
    }

    public function destroy(Animal $animal)
    {
        $this->authorize('delete', $animal);

        $this->animais->delete($animal);

        return redirect()->route('animais.index')
            ->with('success', 'Animal removido com sucesso.');
    }
}
