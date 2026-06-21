<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoStoreRequest;
use App\Http\Requests\ProductoUpdateRequest;
use App\Models\Categoria;
use App\Models\Chaqueta;
use App\Models\Material;
use App\Models\Talla;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $productos = $this->productosPorEstado('Activo', $request->query('buscar'));

        return view('admin.productos.index', compact('productos'));
    }

    public function inactivos(Request $request)
    {
        $productos = $this->productosPorEstado('Inactivo', $request->query('buscar'));

        return view('admin.productos.inactivos', compact('productos'));
    }

    public function reactivar(Chaqueta $producto)
    {
        if (Schema::hasColumn('chaqueta', 'estado')) {
            $producto->update(['estado' => 'Activo']);
        }

        return redirect()
            ->route('admin.productos.inactivos')
            ->with('success', 'Producto reactivado correctamente.');
    }

    protected function productosPorEstado(string $estado, ?string $buscar = null)
    {
        $productos = collect([]);

        if (Schema::hasTable('chaqueta')) {
            $select = [
                'c.id_chaqueta',
                'c.modelo_chaqueta as nombre',
                'cat.tipo_categoria as categoria',
                'c.precio',
            ];

            if (Schema::hasColumn('chaqueta', 'imagen')) {
                $select[] = 'c.imagen';
            }

            $productos = DB::table('chaqueta as c')
                ->leftJoin('categoria as cat', 'cat.id_categoria', '=', 'c.categoria_id_categoria')
                ->select($select)
                ->when(Schema::hasColumn('chaqueta', 'estado'), fn ($query) => $query->where('c.estado', $estado))
                ->when(filled($buscar), function ($query) use ($buscar) {
                    $buscar = '%' . trim($buscar) . '%';
                    $query->where(function ($query) use ($buscar) {
                        $query->where('c.id_chaqueta', 'like', $buscar)
                            ->orWhere('c.modelo_chaqueta', 'like', $buscar)
                            ->orWhere('cat.tipo_categoria', 'like', $buscar)
                            ->orWhere('c.precio', 'like', $buscar);
                    });
                })
                ->orderByDesc('c.id_chaqueta')
                ->get()
                ->map(function ($producto) {
                    $materiales = collect([]);
                    $tallas = collect([]);

                    if (Schema::hasTable('chaqueta_has_materiales') && Schema::hasTable('materiales')) {
                        $materiales = DB::table('chaqueta_has_materiales as cm')
                            ->join('materiales as m', 'm.id_materiales', '=', 'cm.materiales_id_materiales')
                            ->where('cm.chaqueta_id_chaqueta', $producto->id_chaqueta)
                            ->select('m.id_materiales as id', 'm.material as nombre', 'm.cantidad')
                            ->get();
                    }

                    if (Schema::hasTable('chaqueta_has_talla') && Schema::hasTable('talla')) {
                        $tallas = DB::table('chaqueta_has_talla as ct')
                            ->join('talla as t', 't.id_talla', '=', 'ct.talla_id_talla')
                            ->where('ct.chaqueta_id_chaqueta', $producto->id_chaqueta)
                            ->orderBy('t.orden')
                            ->pluck('t.talla')
                            ->toArray();
                    } elseif (Schema::hasTable('stock') && Schema::hasColumn('stock', 'talla')) {
                        $tallas = DB::table('stock')
                            ->where('chaqueta_id_chaqueta', $producto->id_chaqueta)
                            ->whereNotNull('talla')
                            ->distinct()
                            ->orderBy('talla')
                            ->pluck('talla')
                            ->toArray();
                    }

                    $stockTotal = 0;
                    if (Schema::hasTable('stock')) {
                        $stockQuery = DB::table('stock')->where('chaqueta_id_chaqueta', $producto->id_chaqueta);
                        $stockTotal = Schema::hasColumn('stock', 'cantidad')
                            ? $stockQuery->sum('cantidad')
                            : $stockQuery->count();
                    }

                    return (object) [
                        'id' => $producto->id_chaqueta,
                        'nombre' => $producto->nombre,
                        'categoria' => $producto->categoria ?? 'Sin categoría',
                        'descripcion' => 'Producto registrado en la base de datos.',
                        'precio' => (float) $producto->precio,
                        'imagen' => property_exists($producto, 'imagen') ? $producto->imagen : null,
                        'tallas' => $tallas,
                        'materiales' => $materiales,
                        'stock_total' => (int) $stockTotal,
                    ];
                });
        }

        return $this->paginarColeccion($productos);
    }

    protected function paginarColeccion($items, int $perPage = 10): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $collection = collect($items)->values();

        return (new LengthAwarePaginator(
            $collection->forPage($page, $perPage)->values(),
            $collection->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        ))->withQueryString();
    }

    public function create()
    {
        $categorias = Categoria::query()->where('estado_categoria', 1)->orderBy('tipo_categoria')->pluck('tipo_categoria', 'id_categoria');
        $materiales = Material::query()
            ->when(Schema::hasColumn('materiales', 'estado'), fn ($query) => $query->where('estado', 'Activo'))
            ->orderBy('material')
            ->get();
        $tallas = Schema::hasTable('talla')
            ? Talla::query()->orderBy(Schema::hasColumn('talla', 'orden') ? 'orden' : 'id_talla')->get()
            : collect([]);

        return view('admin.productos.create', compact('categorias', 'materiales', 'tallas'));
    }

    public function store(ProductoStoreRequest $request)
    {
        $data = $request->validated();
        $categoria = $this->resolveCategoria($data);

        $producto = Chaqueta::create([
            'id_chaqueta' => $data['id_chaqueta'] ?? null,
            'modelo_chaqueta' => $data['nombre'],
            'precio' => $data['precio'],
            'categoria_id_categoria' => $categoria?->id_categoria,
        ]);

        if ($request->hasFile('imagen') && Schema::hasColumn('chaqueta', 'imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            $producto->update(['imagen' => $path]);
        }

        if (! empty($data['materiales'])) {
            $producto->materiales()->sync($data['materiales']);
        }

        // Guardar cantidades en la tabla stock por cada talla
        $tallasConCantidad = [];
        if (Schema::hasTable('stock') && ! empty($data['cantidades'])) {
            foreach ($data['cantidades'] as $tallaId => $cantidad) {
                $tallaIdInt = (int) $tallaId;
                if ($cantidad > 0) {
                    DB::table('stock')->updateOrInsert(
                        [
                            'chaqueta_id_chaqueta' => $producto->id_chaqueta,
                            'talla_id_talla' => $tallaIdInt,
                        ],
                        [
                            'cantidad' => $cantidad,
                        ]
                    );
                    $tallasConCantidad[] = $tallaIdInt;
                } else {
                    // Si la cantidad es 0, asegurarnos de eliminar registro si existía
                    DB::table('stock')->where('chaqueta_id_chaqueta', $producto->id_chaqueta)
                        ->where('talla_id_talla', $tallaIdInt)->delete();
                }
            }

            // Sincronizar relación de tallas del producto según las cantidades > 0
            if (Schema::hasTable('chaqueta_has_talla') && Schema::hasTable('talla')) {
                $producto->tallas()->sync($tallasConCantidad);
            }
        }

        return redirect()->route('admin.productos')->with('success', 'Producto creado correctamente.');
    }

    public function show(Chaqueta $producto)
    {
        $producto->load('categoria', 'materiales');

        return view('admin.productos.show', compact('producto'));
    }

    public function edit($producto)
    {
        if (! $producto instanceof Chaqueta) {
            $productoId = $producto;
            $producto = Chaqueta::find($productoId) ?? new Chaqueta();
            $producto->forceFill(['id_chaqueta' => $productoId]);
        }

        $producto->load('categoria', 'materiales');
        if (Schema::hasTable('chaqueta_has_talla') && Schema::hasTable('talla')) {
            $producto->load('tallas');
        }
        $categorias = Categoria::query()->where('estado_categoria', 1)->orderBy('tipo_categoria')->pluck('tipo_categoria', 'id_categoria');
        $materiales = Material::query()
            ->when(Schema::hasColumn('materiales', 'estado'), fn ($query) => $query->where('estado', 'Activo'))
            ->orderBy('material')
            ->get();
        $tallas = Schema::hasTable('talla')
            ? Talla::query()->orderBy(Schema::hasColumn('talla', 'orden') ? 'orden' : 'id_talla')->get()
            : collect([]);

        return view('admin.productos.edit', compact('producto', 'categorias', 'materiales', 'tallas'));
    }

    public function update(ProductoUpdateRequest $request, Chaqueta $producto)
    {
        $data = $request->validated();
        $categoria = $this->resolveCategoria($data);

        $oldId = $producto->id_chaqueta;

        // If ID change requested, update PK and related FK references in other tables
        if (! empty($data['id_chaqueta']) && (int) $data['id_chaqueta'] !== (int) $oldId) {
            $newId = (int) $data['id_chaqueta'];


            DB::transaction(function () use ($oldId, $newId) {
                // Ensure target id doesn't already exist
                $exists = DB::table('chaqueta')->where('id_chaqueta', $newId)->exists();
                if ($exists) {
                    throw new \Exception("El id_chaqueta {$newId} ya existe.");
                }

                // Update parent PK first — FK constraints with ON UPDATE CASCADE will propagate to child tables
                DB::table('chaqueta')->where('id_chaqueta', $oldId)->update(['id_chaqueta' => $newId]);
            });

            $producto->id_chaqueta = $newId;
        }

        $producto->fill([
            'modelo_chaqueta' => $data['nombre'],
            'precio' => $data['precio'],
            'categoria_id_categoria' => $categoria?->id_categoria,
        ]);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }

            $producto->imagen = $request->file('imagen')->store('productos', 'public');
        }

        $producto->save();

        $producto->materiales()->sync($data['materiales'] ?? []);

        // Actualizar cantidades en la tabla stock por cada talla
        $tallasConCantidad = [];
        if (Schema::hasTable('stock') && isset($data['cantidades'])) {
            foreach ($data['cantidades'] as $tallaId => $cantidad) {
                $tallaIdInt = (int) $tallaId;
                if ($cantidad > 0) {
                    DB::table('stock')->updateOrInsert(
                        [
                            'chaqueta_id_chaqueta' => $producto->id_chaqueta,
                            'talla_id_talla' => $tallaIdInt,
                        ],
                        [
                            'cantidad' => $cantidad,
                        ]
                    );
                    $tallasConCantidad[] = $tallaIdInt;
                } else {
                    DB::table('stock')->where('chaqueta_id_chaqueta', $producto->id_chaqueta)
                        ->where('talla_id_talla', $tallaIdInt)->delete();
                }
            }

            // Sincronizar relación de tallas del producto según las cantidades > 0
            if (Schema::hasTable('chaqueta_has_talla') && Schema::hasTable('talla')) {
                $producto->tallas()->sync($tallasConCantidad);
            }
        }

        return redirect()->route('admin.productos')->with('success', 'Producto actualizado correctamente.');
    }

    protected function resolveCategoria(array $data)
    {
        if (! empty($data['categoria_id'])) {
            return Categoria::find($data['categoria_id']);
        }

        if (! empty($data['categoria_nueva'])) {
            return Categoria::firstOrCreate([
                'tipo_categoria' => $data['categoria_nueva'],
            ], [
                'estado_categoria' => 1,
            ]);
        }

        if (! empty($data['categoria'])) {
            return Categoria::firstOrCreate([
                'tipo_categoria' => $data['categoria'],
            ], [
                'estado_categoria' => 1,
            ]);
        }

        return null;
    }

    public function destroy(Chaqueta $producto)
    {
        if (Schema::hasColumn('chaqueta', 'estado')) {
            $producto->update(['estado' => 'Inactivo']);
        }

        return redirect()->route('admin.productos')->with('success', 'Producto inactivado correctamente.');
    }
}
