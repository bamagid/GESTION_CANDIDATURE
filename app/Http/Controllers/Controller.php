<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
/**
 *     security={
 *         {"BearerAuth": {}}
 *     },
 *     @OA\Info(
 *         title="API Simplon Sénégal",
 *         description="Cet API permet de gérer les candidatures que Simplon Sénégal reçoit pour leurs différentes formations",
 *         version="1.0.0"
 *     ),
 *     @OA\SecurityScheme(
 *         securityScheme="BearerAuth",
 *         type="http",
 *         scheme="bearer",
 *         bearerFormat="JWT"
 *     ),
 *     consumes={"application/json"},
 *     consumes={"multipart/form-data""}
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
