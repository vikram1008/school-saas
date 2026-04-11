<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\FeeService;
use Illuminate\Console\Command;

class GenerateFeeDemands extends Command
{
    protected $signature   = 'fees:generate-demands';
    protected $description = 'Generate monthly fee demands for all active students in all tenant DBs.';

    public function handle(FeeService $feeService): void
    {
        $this->info('Generating fee demands — ' . now()->toDateString());

        $tenants = Tenant::where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            $this->line("Processing: {$tenant->school_name}");
            try {
                tenancy()->initialize($tenant);
                $result = $feeService->generateMonthlyDemands();
                $this->info("  → Generated: {$result['generated']} | Skipped: {$result['skipped']}");
                tenancy()->end();
            } catch (\Exception $e) {
                tenancy()->end();
                $this->error("  → Failed: {$e->getMessage()}");
            }
        }

        $this->info('Fee demand generation complete.');
    }
}