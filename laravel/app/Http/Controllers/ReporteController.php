<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Chaqueta;
use App\Models\Material;
use App\Models\TipoDocumento;
use App\Models\Usuario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReporteController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        return view('admin.reportes.index', [
            'totales' => [
                'usuarios' => Schema::hasTable('usuario') ? Usuario::count() : 0,
                'productos' => Schema::hasTable('chaqueta') ? Chaqueta::count() : 0,
                'materiales' => Schema::hasTable('materiales') ? Material::count() : 0,
                'ventas' => Schema::hasTable('facturacion') ? (float) DB::table('facturacion')->sum('total') : 0,
                'envios' => Schema::hasTable('envio') ? DB::table('envio')->count() : 0,
            ],
        ]);
    }

    public function inventario(Request $request)
    {
        $this->authorizeAdmin();

        $productos = $this->inventarioData($request);

        return view('admin.reportes.inventario', [
            'productos' => $this->paginarColeccion($productos, 15),
            'categorias' => $this->categorias(),
            'resumen' => $this->resumenInventario($productos),
        ]);
    }

    public function inventarioPdf(Request $request)
    {
        $this->authorizeAdmin();

        $productos = $this->inventarioData($request);

        return $this->pdf('admin.reportes.pdf.inventario', [
            'titulo' => 'Reporte de inventario',
            'productos' => $productos,
            'resumen' => $this->resumenInventario($productos),
            'filtros' => $this->activeFilters($request),
        ], 'reporte-inventario.pdf', 'landscape');
    }

    public function usuarios(Request $request)
    {
        $this->authorizeAdmin();

        $usuarios = $this->usuariosData($request, true);

        return view('admin.reportes.usuarios', [
            'usuarios' => $usuarios,
            'roles' => $this->roles(),
            'tiposDocumento' => Schema::hasTable('tipo_documento') ? TipoDocumento::orderBy('tipo')->get() : collect(),
        ]);
    }

    public function usuariosPdf(Request $request)
    {
        $this->authorizeAdmin();

        return $this->pdf('admin.reportes.pdf.usuarios', [
            'titulo' => 'Reporte de usuarios',
            'usuarios' => $this->usuariosData($request, false),
            'filtros' => $this->activeFilters($request),
        ], 'reporte-usuarios.pdf', 'landscape');
    }

    public function materiales(Request $request)
    {
        $this->authorizeAdmin();

        $materiales = $this->materialesData($request, true);

        return view('admin.reportes.materiales', [
            'materiales' => $materiales,
            'proveedores' => $this->proveedores(),
        ]);
    }

    public function materialesPdf(Request $request)
    {
        $this->authorizeAdmin();

        return $this->pdf('admin.reportes.pdf.materiales', [
            'titulo' => 'Reporte de materiales',
            'materiales' => $this->materialesData($request, false),
            'filtros' => $this->activeFilters($request),
        ], 'reporte-materiales.pdf');
    }

    public function ventas(Request $request)
    {
        $this->authorizeAdmin();

        $ventas = $this->ventasData($request, true);

        return view('admin.reportes.ventas', [
            'ventas' => $ventas,
            'totalVentas' => $ventas->getCollection()->sum('total'),
            'estadosEnvio' => $this->estadosEnvio(),
        ]);
    }

    public function ventasPdf(Request $request)
    {
        $this->authorizeAdmin();

        $ventas = $this->ventasData($request, false);

        return $this->pdf('admin.reportes.pdf.ventas', [
            'titulo' => 'Reporte de ventas',
            'ventas' => $ventas,
            'totalVentas' => $ventas->sum('total'),
            'filtros' => $this->activeFilters($request),
        ], 'reporte-ventas.pdf', 'landscape');
    }

    public function envios(Request $request)
    {
        $this->authorizeAdmin();

        return view('admin.reportes.envios', [
            'envios' => $this->enviosData($request, true),
            'estadosEnvio' => $this->estadosEnvio(),
        ]);
    }

    public function enviosPdf(Request $request)
    {
        $this->authorizeAdmin();

        return $this->pdf('admin.reportes.pdf.envios', [
            'titulo' => 'Reporte de envios',
            'envios' => $this->enviosData($request, false),
            'filtros' => $this->activeFilters($request),
        ], 'reporte-envios.pdf', 'landscape');
    }

    private function inventarioData(Request $request): Collection
    {
        if (! Schema::hasTable('chaqueta')) {
            return collect();
        }

        $productos = Chaqueta::with(['categoria', 'stock.talla'])
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $query->where('modelo_chaqueta', 'like', '%' . $request->buscar . '%');
            })
            ->when($request->filled('categoria_id'), function ($query) use ($request) {
                $query->where('categoria_id_categoria', $request->categoria_id);
            })
            ->when($request->filled('estado') && Schema::hasColumn('chaqueta', 'estado'), function ($query) use ($request) {
                $query->where('estado', $request->estado);
            })
            ->orderBy('modelo_chaqueta')
            ->get()
            ->map(function (Chaqueta $producto) {
                $stockTotal = $producto->stock->sum(fn ($stock) => (int) $stock->cantidad);

                return (object) [
                    'id' => $producto->id_chaqueta,
                    'modelo' => $producto->modelo_chaqueta,
                    'categoria' => $producto->categoria?->tipo_categoria ?? 'Sin categoria',
                    'precio' => (float) $producto->precio,
                    'estado' => $producto->estado ?? 'Activo',
                    'stock_total' => $stockTotal,
                    'estado_stock' => $stockTotal === 0 ? 'Agotado' : ($stockTotal < 10 ? 'Bajo' : 'Disponible'),
                    'tallas' => $producto->stock
                        ->sortBy(fn ($stock) => $stock->talla?->orden ?? 99)
                        ->map(fn ($stock) => ($stock->talla?->talla ?? 'N/A') . ': ' . (int) $stock->cantidad)
                        ->implode(', '),
                ];
            });

        return $productos
            ->when($request->filled('stock_estado'), fn ($items) => $items->where('estado_stock', $request->stock_estado))
            ->when($request->filled('stock_min'), fn ($items) => $items->filter(fn ($item) => $item->stock_total >= (int) $request->stock_min))
            ->when($request->filled('stock_max'), fn ($items) => $items->filter(fn ($item) => $item->stock_total <= (int) $request->stock_max))
            ->values();
    }

    private function usuariosData(Request $request, bool $paginate)
    {
        if (! Schema::hasTable('usuario')) {
            return $paginate ? $this->emptyPaginator() : collect();
        }

        $query = Usuario::with('tipo_documento')
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $buscar = '%' . $request->buscar . '%';
                $query->where(function ($query) use ($buscar) {
                    $query->where('nombres', 'like', $buscar)
                        ->orWhere('apellidos', 'like', $buscar)
                        ->orWhere('correo', 'like', $buscar)
                        ->orWhere('id_usuario', 'like', $buscar);
                });
            })
            ->when($request->filled('rol'), fn ($query) => $query->where('rol', $request->rol))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->estado))
            ->when($request->filled('tipo_documento_id'), fn ($query) => $query->where('tipo_documento_id', $request->tipo_documento_id))
            ->orderBy('nombres')
            ->orderBy('apellidos');

        return $paginate ? $query->paginate(15)->withQueryString() : $query->get();
    }

    private function materialesData(Request $request, bool $paginate)
    {
        if (! Schema::hasTable('materiales')) {
            return $paginate ? $this->emptyPaginator() : collect();
        }

        $select = [
            'm.id_materiales',
            'm.material',
            Schema::hasColumn('materiales', 'precio') ? 'm.precio' : DB::raw('0 as precio'),
            Schema::hasColumn('materiales', 'cantidad') ? 'm.cantidad' : DB::raw('0 as cantidad'),
            Schema::hasColumn('materiales', 'estado') ? 'm.estado' : DB::raw("'Activo' as estado"),
        ];

        $query = DB::table('materiales as m');

        if (Schema::hasTable('proveedor_material')) {
            $query->leftJoin('proveedor_material as pm', 'pm.cod_proveedor', '=', 'm.proveedor_material_cod_proveedor');
            $select[] = 'pm.proveedor_material as proveedor';
        } else {
            $select[] = DB::raw('NULL as proveedor');
        }

        $query->select($select)
            ->when($request->filled('buscar'), fn ($query) => $query->where('m.material', 'like', '%' . $request->buscar . '%'))
            ->when($request->filled('estado') && Schema::hasColumn('materiales', 'estado'), fn ($query) => $query->where('m.estado', $request->estado))
            ->when($request->filled('proveedor') && Schema::hasTable('proveedor_material'), fn ($query) => $query->where('pm.proveedor_material', $request->proveedor))
            ->when($request->filled('cantidad_min') && Schema::hasColumn('materiales', 'cantidad'), fn ($query) => $query->where('m.cantidad', '>=', (int) $request->cantidad_min))
            ->when($request->filled('cantidad_max') && Schema::hasColumn('materiales', 'cantidad'), fn ($query) => $query->where('m.cantidad', '<=', (int) $request->cantidad_max))
            ->orderBy('m.material');

        return $paginate ? $query->paginate(15)->withQueryString() : $query->get();
    }

    private function ventasData(Request $request, bool $paginate)
    {
        if (! Schema::hasTable('facturacion')) {
            return $paginate ? $this->emptyPaginator() : collect();
        }

        $query = DB::table('facturacion as f')
            ->leftJoin('usuario as u', 'u.id_usuario', '=', 'f.cliente_usuario_id_usuario')
            ->leftJoin('envio as e', 'e.facturacion_id_facturacion', '=', 'f.id_facturacion')
            ->select(
                'f.id_facturacion',
                'f.fecha',
                'f.total',
                'u.nombres',
                'u.apellidos',
                'u.correo',
                'e.tipo_envio'
            )
            ->when($request->filled('desde'), fn ($query) => $query->whereDate('f.fecha', '>=', $request->desde))
            ->when($request->filled('hasta'), fn ($query) => $query->whereDate('f.fecha', '<=', $request->hasta))
            ->when($request->filled('estado_envio'), fn ($query) => $query->where('e.tipo_envio', $request->estado_envio))
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $buscar = '%' . $request->buscar . '%';
                $query->where(function ($query) use ($buscar) {
                    $query->where('u.nombres', 'like', $buscar)
                        ->orWhere('u.apellidos', 'like', $buscar)
                        ->orWhere('u.correo', 'like', $buscar)
                        ->orWhere('f.id_facturacion', 'like', $buscar);
                });
            })
            ->orderByDesc('f.fecha');

        return $paginate ? $query->paginate(15)->withQueryString() : $query->get();
    }

    private function enviosData(Request $request, bool $paginate)
    {
        if (! Schema::hasTable('envio')) {
            return $paginate ? $this->emptyPaginator() : collect();
        }

        $query = DB::table('envio as e')
            ->leftJoin('facturacion as f', 'f.id_facturacion', '=', 'e.facturacion_id_facturacion')
            ->leftJoin('usuario as u', 'u.id_usuario', '=', 'f.cliente_usuario_id_usuario')
            ->leftJoin('direccion as d', 'd.id_direccion', '=', 'e.direccion_id_direccion')
            ->select(
                'e.id_envio',
                'e.facturacion_id_facturacion',
                'e.tipo_envio',
                'e.empresa_transportadora',
                'f.fecha',
                'f.total',
                'u.nombres',
                'u.apellidos',
                'u.correo',
                'd.direccion',
                'd.ciudad'
            )
            ->when($request->filled('estado_envio'), fn ($query) => $query->where('e.tipo_envio', $request->estado_envio))
            ->when($request->filled('transportadora'), fn ($query) => $query->where('e.empresa_transportadora', 'like', '%' . $request->transportadora . '%'))
            ->when($request->filled('desde'), fn ($query) => $query->whereDate('f.fecha', '>=', $request->desde))
            ->when($request->filled('hasta'), fn ($query) => $query->whereDate('f.fecha', '<=', $request->hasta))
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $buscar = '%' . $request->buscar . '%';
                $query->where(function ($query) use ($buscar) {
                    $query->where('u.nombres', 'like', $buscar)
                        ->orWhere('u.apellidos', 'like', $buscar)
                        ->orWhere('u.correo', 'like', $buscar)
                        ->orWhere('e.id_envio', 'like', $buscar)
                        ->orWhere('e.facturacion_id_facturacion', 'like', $buscar);
                });
            })
            ->orderByDesc('e.id_envio');

        return $paginate ? $query->paginate(15)->withQueryString() : $query->get();
    }

    private function pdf(string $view, array $data, string $filename, string $orientation = 'portrait')
    {
        return Pdf::loadView($view, $data)
            ->setPaper('letter', $orientation)
            ->download($filename);
    }

    private function emptyPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, 15, 1, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }

    private function paginarColeccion(Collection $items, int $perPage = 15): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $items->values();

        return (new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        ))->withQueryString();
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->rol === 'Admin', 403);
    }

    private function activeFilters(Request $request): array
    {
        return collect($request->query())
            ->filter(fn ($value) => filled($value) && $value !== 'page')
            ->all();
    }

    private function resumenInventario(Collection $productos): array
    {
        return [
            'total' => $productos->count(),
            'disponibles' => $productos->where('estado_stock', 'Disponible')->count(),
            'bajos' => $productos->where('estado_stock', 'Bajo')->count(),
            'agotados' => $productos->where('estado_stock', 'Agotado')->count(),
            'stock_total' => $productos->sum('stock_total'),
        ];
    }

    private function categorias(): Collection
    {
        return Schema::hasTable('categoria') ? Categoria::orderBy('tipo_categoria')->get() : collect();
    }

    private function proveedores(): Collection
    {
        return Schema::hasTable('proveedor_material')
            ? DB::table('proveedor_material')->whereNotNull('proveedor_material')->orderBy('proveedor_material')->pluck('proveedor_material')
            : collect();
    }

    private function roles(): Collection
    {
        return Schema::hasTable('usuario') ? Usuario::query()->distinct()->orderBy('rol')->pluck('rol')->filter()->values() : collect();
    }

    private function estadosEnvio(): Collection
    {
        return Schema::hasTable('envio') ? DB::table('envio')->distinct()->orderBy('tipo_envio')->pluck('tipo_envio')->filter()->values() : collect();
    }
}
