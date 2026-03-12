<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permiso;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear permisos
        $permisos = [
            'ver_facturas',
            'crear_factura',
            'editar_factura',
            'eliminar_factura',
            'ver_clientes',
            'crear_cliente',
            'editar_cliente',
            'eliminar_cliente',
            'ver_productos',
            'crear_producto',
            'editar_producto',
            'eliminar_producto',
            'ver_reportes',
            'gestionar_usuarios',
            'ver_todas_empresas',
        ];

        foreach ($permisos as $permiso) {
            Permiso::firstOrCreate(
                ['nombre' => $permiso],
                ['descripcion' => $permiso]
            );
        }

        // SUPER ADMIN
        $superAdmin = Role::firstOrCreate([
            'nombre' => 'super_admin',
            'descripcion' => 'Super administrador del sistema'
        ]);
        $superAdmin->permisos()->sync(Permiso::all());

        // ADMIN EMPRESA
        $adminEmpresa = Role::firstOrCreate([
            'nombre' => 'admin_empresa',
            'descripcion' => 'Administrador de empresa'
        ]);
        $adminEmpresa->permisos()->sync(
            Permiso::whereIn('nombre', [
                'ver_facturas',
                'crear_factura',
                'editar_factura',
                'ver_clientes',
                'crear_cliente',
                'editar_cliente',
                'ver_productos',
                'crear_producto',
                'editar_producto',
                'ver_reportes',
                'gestionar_usuarios',
            ])->pluck('id')
        );

        // USUARIO NORMAL
        $usuarioNormal = Role::firstOrCreate([
            'nombre' => 'usuario_normal',
            'descripcion' => 'Usuario normal'
        ]);
        $usuarioNormal->permisos()->sync(
            Permiso::whereIn('nombre', [
                'ver_facturas',
                'crear_factura',
                'ver_clientes',
                'ver_productos',
            ])->pluck('id')
        );
    }
} 