<?php

namespace App\Actions;

use App\Enums\TrackableStatus;
use Illuminate\Database\Eloquent\Model;

class StatusUpdate
{
    public static function update(Model $model, string $action): void
    {
        if ($action == 'pause') {
            $model->status = TrackableStatus::PAUSED;
        } elseif ($action == 'complete') {
            $model->status = TrackableStatus::COMPLETED;
        } elseif ($action == 'resume') {
            $model->status = TrackableStatus::INPROGRESS;
        } elseif ($action == 'start') {
            $model->status = TrackableStatus::ACTIVE;
        } elseif ($action == 'cancel') {
            $model->status = TrackableStatus::CANCELLED;
        }

        $model->save();
    }
}
