<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Objective;
use App\Models\Trackable;
use Inertia\Inertia;
use Inertia\Response;

class ObjectiveController extends Controller
{
    /*
     * A list of all objectives
     */
    public function index(): Response
    {
        $obj = Trackable::query()
            ->where('trackable_type', '=', Objective::class)
            ->with(['trackable', 'category', 'user', 'visibility'])
            ->latest()
            ->paginate(12);
        return Inertia::render('Objectives/Index', [
            'objectives' => $obj
        ]);
    }

}
