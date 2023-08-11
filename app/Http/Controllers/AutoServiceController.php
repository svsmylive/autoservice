<?php

namespace App\Http\Controllers;

use App\Domain\Autoservice\Actions\GetAutoserviceAction;

class AutoServiceController
{
    public function get(GetAutoserviceAction $action): void
    {
        $action->execute();
    }
}
