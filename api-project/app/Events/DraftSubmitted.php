<?php

namespace App\Events;

use App\Models\Draft;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DraftSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(public Draft $draft) {}
}
