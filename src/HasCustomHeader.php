<?php

namespace Aripdev\Queryable;

use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

trait HasCustomHeader
{
    public function withResponse($request, $response)
    {
        try {

            $headers  = App::make('xheader');

            if (!empty($header = $headers->headers)) {
                foreach ($header as $key => $value) {
                    $response->header($key, $value);
                }
            }
        } catch (\Exception $e) {

            Log::alert($e->getMessage() . " :" . __CLASS__ . ":" . __LINE__);
        }
    }
}
