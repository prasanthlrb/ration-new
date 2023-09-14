<?php

namespace App\Exceptions;

use App\Helpers\CResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class InstallationException extends Exception
{
    public $step;

    /**
     * @param $step
     */
    public function __construct($step)
    {
        $this->step = $step;
    }


    public function render(): JsonResponse
    {
        return response()->json([
            'fallback' => $this->step,
        ], CResponse::$INSTALLATION_FALLBACK);
    }
}