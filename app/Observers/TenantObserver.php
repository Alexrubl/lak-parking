<?php

namespace App\Observers;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use App\Models\Journal;

class TenantObserver
{
    /**
     * Handle the Transport "created" event.
     */
    public function created(Tenant $tenant): void
    {
        if (Auth::check()) {
            $journal = new Journal;
            $journal->text = 'Создание арендатора '. $tenant->name;
            $journal->user_id = Auth::user()->id;
            $journal->save();
        }
    }

    /**
     * Handle the Transport "updated" event.
     */
    public function updated(Tenant $tenant): void
    {
        if (Auth::check()) {
            $journal = new Journal;
            $journal->text = 'Обновление арендатора '. $tenant->name;
            $journal->user_id = Auth::user()->id;
            $journal->save();
        }
    }

    /**
     * Handle the Transport "deleted" event.
     */
    public function deleted(Tenant $tenant): void
    {
        if (Auth::check()) {
            $journal = new Journal;
            $journal->text = 'Удаление арендатора '. $tenant->name;
            $journal->user_id = Auth::user()->id;
            $journal->save();
        }
    }

    /**
     * Handle the Transport "restored" event.
     */
    public function restored(Tenant $tenant): void
    {
        if (Auth::check()) {
            $journal = new Journal;
            $journal->text = 'Восстановление с корзины арендатора '. $tenant->name;
            $journal->user_id = Auth::user()->id;
            $journal->save();
        }
    }

    /**
     * Handle the Transport "force deleted" event.
     */
    public function forceDeleted(Tenant $tenant): void
    {
        info('forcedelete');
        if (Auth::check()) {
            $journal = new Journal;
            $journal->text = 'Полное удаление арендатора '. $tenant->name;
            $journal->user_id = Auth::user()->id;
            $journal->save();
        }
        $tenant->forceDeleteQuietly();
    }
}
