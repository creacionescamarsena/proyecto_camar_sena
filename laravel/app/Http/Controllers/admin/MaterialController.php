<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $materiales = $this->materialesPorEstado('Activo', $request->query('buscar'));
        $materialesStockBajo = $materiales->filter(fn ($material) => (int) ($material->cantidad ?? 0) < 10)->count();

        return view('admin.materiales.index', compact('materiales', 'materialesStockBajo'));
    }

    public function inactivos(Request $request)
    {
        $materiales = $this->materialesPorEstado('Inactivo', $request->query('buscar'));

        return view('admin.materiales.inactivos', compact('materiales'));
    }

    public function reactivar($material)
    {
        if (Schema::hasColumn('materiales', 'estado')) {
            DB::table('materiales')->where('id_materiales', $material)->update(['estado' => 'Activo']);
        }

        return redirect()->route('admin.materiales.inactivos')->with('success', 'Material reactivado correctamente.');
    }

    public function create()
    {
        return view('admin.materiales.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $proveedorId = $this->resolveProveedor($data['proveedor'] ?? null);

        if (Schema::hasTable('materiales')) {
            $insertData = [
                'material' => $data['nombre'],
                'proveedor_material_cod_proveedor' => $proveedorId,
                'precio' => $data['precio'],
                'cantidad' => $data['cantidad'],
            ];

            if (! empty($data['id_materiales'])) {
                $insertData['id_materiales'] = (int) $data['id_materiales'];
            }

            DB::table('materiales')->insert($insertData);
        }

        return redirect()->route('admin.materiales.index')->with('success', 'Material creado correctamente.');
    }

    public function edit($id)
    {
        $material = $this->findMaterial($id) ?? (object) [
            'id' => (int) $id,
            'nombre' => '',
            'proveedor' => '',
            'precio' => 0,
            'cantidad' => 0,
        ];

        return view('admin.materiales.edit', compact('material'));
    }

    public function update(Request $request, $material)
    {
        $data = $this->validatedData($request, $material);
        $proveedorId = $this->resolveProveedor($data['proveedor'] ?? null);

        $material = $this->updatePrimaryKeyIfNeeded((int) $material, $data);

        DB::table('materiales')->where('id_materiales', $material)->update([
            'material' => $data['nombre'],
            'proveedor_material_cod_proveedor' => $proveedorId,
            'precio' => $data['precio'],
            'cantidad' => $data['cantidad'],
        ]);

        return redirect()->route('admin.materiales.index')->with('success', 'Material actualizado correctamente.');
    }

    public function destroy($material)
    {
        if (Schema::hasColumn('materiales', 'estado')) {
            DB::table('materiales')->where('id_materiales', $material)->update(['estado' => 'Inactivo']);
        } else {
            DB::table('materiales')->where('id_materiales', $material)->delete();
        }

        return redirect()->route('admin.materiales.index')->with('success', 'Material inactivado correctamente.');
    }

    private function materialesPorEstado(string $estado, ?string $buscar = null)
    {
        if (! Schema::hasTable('materiales')) {
            return collect([]);
        }

        $query = DB::table('materiales as m')
            ->select('m.id_materiales as id', 'm.material as nombre', 'm.precio', 'm.cantidad')
            ->when(Schema::hasColumn('materiales', 'estado'), fn ($query) => $query->where('m.estado', $estado))
            ->orderByDesc('m.id_materiales');

        if (Schema::hasTable('proveedor_material')) {
            $query->leftJoin('proveedor_material as pm', 'pm.cod_proveedor', '=', 'm.proveedor_material_cod_proveedor')
                ->addSelect('pm.proveedor_material as proveedor');
        }

        if (filled($buscar)) {
            $like = '%' . trim($buscar) . '%';
            $query->where(function ($query) use ($like) {
                $query->where('m.id_materiales', 'like', $like)
                    ->orWhere('m.material', 'like', $like)
                    ->orWhere('m.precio', 'like', $like)
                    ->orWhere('m.cantidad', 'like', $like);

                if (Schema::hasTable('proveedor_material')) {
                    $query->orWhere('pm.proveedor_material', 'like', $like);
                }
            });
        }

        return $query->get();
    }

    private function findMaterial($id): ?object
    {
        if (! Schema::hasTable('materiales')) {
            return null;
        }

        $query = DB::table('materiales as m')
            ->where('m.id_materiales', $id)
            ->select('m.id_materiales as id', 'm.material as nombre', 'm.precio', 'm.cantidad');

        if (Schema::hasTable('proveedor_material')) {
            $query->leftJoin('proveedor_material as pm', 'pm.cod_proveedor', '=', 'm.proveedor_material_cod_proveedor')
                ->addSelect('pm.proveedor_material as proveedor');
        } else {
            $query->addSelect(DB::raw('NULL as proveedor'));
        }

        return $query->first();
    }

    private function validatedData(Request $request, $material = null): array
    {
        return $request->validate([
            'id_materiales' => [
                'nullable',
                'integer',
                $material
                    ? Rule::unique('materiales', 'id_materiales')->ignore($material, 'id_materiales')
                    : 'unique:materiales,id_materiales',
            ],
            'nombre' => ['required', 'string', 'max:100'],
            'proveedor' => ['nullable', 'string', 'max:100'],
            'precio' => ['required', 'numeric', 'min:0'],
            'cantidad' => ['required', 'integer', 'min:0'],
        ]);
    }

    private function resolveProveedor(?string $proveedor): ?int
    {
        if (! filled($proveedor) || ! Schema::hasTable('proveedor_material')) {
            return null;
        }

        $proveedorId = DB::table('proveedor_material')->where('proveedor_material', $proveedor)->value('cod_proveedor');
        if ($proveedorId) {
            return (int) $proveedorId;
        }

        return (int) DB::table('proveedor_material')->insertGetId([
            'proveedor_material' => $proveedor,
        ]);
    }

    private function updatePrimaryKeyIfNeeded(int $oldId, array $data): int
    {
        if (empty($data['id_materiales']) || (int) $data['id_materiales'] === $oldId) {
            return $oldId;
        }

        $newId = (int) $data['id_materiales'];
        DB::transaction(function () use ($oldId, $newId) {
            if (Schema::hasTable('chaqueta_has_materiales')) {
                DB::table('chaqueta_has_materiales')
                    ->where('materiales_id_materiales', $oldId)
                    ->update(['materiales_id_materiales' => $newId]);
            }

            DB::table('materiales')->where('id_materiales', $oldId)->update(['id_materiales' => $newId]);
        });

        return $newId;
    }
}
