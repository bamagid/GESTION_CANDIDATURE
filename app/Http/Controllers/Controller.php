<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="api gestion de candidature",
 *     description="Cet api va permettre de decentraliser la gestion des candidatures des futures apprenants de simplon semnegal",
 *     version="0.0.1"
 * )
 */

class Controller extends BaseController
{
    /**
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="En-tête d’autorisation JWT utilisant le schéma Bearer. Example: \"Authorization: Bearer {token}\""
     * )
     */
    use AuthorizesRequests, ValidatesRequests;
}
