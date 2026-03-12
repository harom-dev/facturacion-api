<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\Empresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmpresaController extends Controller
{
    /**
     * Registrar una nueva empresa en esta API.
     * El POS llama a esto una sola vez durante el setup.
     * Devuelve el token que el POS debe guardar para autenticar sus requests.
     */
    public function registro(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:255',
            'ruc'       => 'required|string|size:11|unique:empresas,ruc',
            'direccion' => 'nullable|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
        ]);

        $empresa = Empresa::create([
            'nombre'    => $validated['nombre'],
            'ruc'       => $validated['ruc'],
            'direccion' => $validated['direccion'] ?? null,
            'telefono'  => $validated['telefono'] ?? null,
            'email'     => $validated['email'] ?? null,
            'activa'    => true,
        ]);

        $apiToken = ApiToken::create([
            'empresa_id'  => $empresa->id,
            'token'       => Str::random(64),
            'descripcion' => 'Token principal - ' . $empresa->nombre,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Empresa registrada correctamente',
            'data'    => [
                'empresa' => [
                    'id'      => $empresa->id,
                    'nombre'  => $empresa->nombre,
                    'ruc'     => $empresa->ruc,
                ],
                'api_token' => $apiToken->token,
                'nota'      => 'Guarda este token en tu POS. Se usará en cada request como: Authorization: Bearer {token}',
            ],
        ], 201);
    }

    /**
     * Ver información de la empresa autenticada.
     */
    public function info(Request $request): JsonResponse
    {
        $empresa = $request->attributes->get('empresa');

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'                  => $empresa->id,
                'nombre'              => $empresa->nombre,
                'ruc'                 => $empresa->ruc,
                'direccion'           => $empresa->direccion,
                'telefono'            => $empresa->telefono,
                'email'               => $empresa->email,
                'activa'              => $empresa->activa,
                'tiene_certificado'   => $empresa->tieneCertificado(),
            ],
        ]);
    }

    /**
     * Subir o actualizar el certificado digital de la empresa (.pfx / .p12).
     * El POS lo carga desde el archivo que le da SUNAT.
     */
    public function subirCertificado(Request $request): JsonResponse
    {
        $request->validate([
            'certificado' => 'required|file|max:2048',
            'password'    => 'required|string',
        ]);

        $empresa = $request->attributes->get('empresa');

        $contenido = base64_encode(
            file_get_contents($request->file('certificado')->getRealPath())
        );

        $empresa->update([
            'certificado_pfx'      => $contenido,
            'certificado_password' => $request->password,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Certificado actualizado correctamente',
        ]);
    }

    /**
     * Generar un token adicional (por ejemplo, para una segunda integración).
     */
    public function generarToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'descripcion' => 'required|string|max:100',
        ]);

        $empresa = $request->attributes->get('empresa');

        $apiToken = ApiToken::create([
            'empresa_id'  => $empresa->id,
            'token'       => Str::random(64),
            'descripcion' => $validated['descripcion'],
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Token generado correctamente',
            'data'    => [
                'api_token'   => $apiToken->token,
                'descripcion' => $apiToken->descripcion,
            ],
        ], 201);
    }
}
