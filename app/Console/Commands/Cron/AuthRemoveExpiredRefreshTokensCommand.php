<?php

namespace App\Console\Commands\Cron;

use App\Contracts\Repositories\AuthAccessRepositoryContract;
use App\Contracts\Services\AuthAccessServiceContract;
use Illuminate\Console\Command;

class AuthRemoveExpiredRefreshTokensCommand extends Command
{
    protected $signature = 'auth:cleanup-refresh';
    protected $description = 'Remove expired refresh tokens';

    public function handle(
        AuthAccessRepositoryContract $authAccessRepository,
        AuthAccessServiceContract $authAccessService,
    ) {
        $countRemoved = 0;

        $authAccessRepository->getAuthAccessesByChunks(2, function ($authAccesses) use ($authAccessService, &$countRemoved) {
            foreach ($authAccesses as $authAccess) {
                try {
                    $authAccessService->isValidRefreshToken($authAccess->getRefreshToken());
                } catch (\Exception $e) {
                    $this->warn('Exception with refresh token ' . $authAccess->getId() . ': ' . $e->getMessage());
                    $authAccess->delete();
                    $this->info('Removed refresh token ' . $authAccess->getId());
                    $countRemoved++;
                }
            }
        });

        $this->info('Removed ' . $countRemoved . ' refresh tokens');
    }
}
