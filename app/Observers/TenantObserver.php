<?php
namespace App\Observers;

use App\Models\Tenant;


class TenantObserver
{
        public function updated(Tenant $tenant): void
    {
        //info($this->is_blocked);
        // if ($is_blocked) {
        //     foreach (Transport::where('tenant_id', $tenant->id)->get() as $key => $transport) {
        //         $transport->access = 1;
        //         $transport->save();
        //     }
        // }

    }
}
