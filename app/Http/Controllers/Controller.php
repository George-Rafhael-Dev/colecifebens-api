<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="ColecifeBens API",
 *     version="0.1.0",
 *     description="REST API for a collectibles marketplace"
 * )
 * @OA\Server(
 *     url="http://localhost:8000/api/v1",
 *     description="Local server"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}