<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PruneUnverifiedRegistrations extends Command
{
    protected $signature = 'registrations:prune-unverified {--dry-run : List what would be deleted without deleting anything}';

    protected $description = 'Delete self-registered accounts that never verified their email within the configured window';

    public function handle(): int
    {
        $days   = (int) Setting::get('reg_unverified_expiry_days', 7);
        $days   = max(1, $days);
        $cutoff = now()->subDays($days);

        $query = User::whereNull('email_verified_at')
            ->where('created_at', '<', $cutoff)
            ->whereIn('role', ['archer', 'coach', 'club_admin']);

        // Safety guard: never delete accounts that predate email-verification
        // enforcement. Legacy users (old no-verification flow) have no
        // email_verified_at but are legitimate active accounts. Only accounts
        // created at/after this timestamp are subject to pruning.
        $since = Setting::get('reg_verification_since');
        if ($since) {
            $query->where('created_at', '>=', $since);
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->info("No prunable unverified registrations older than {$days} day(s).");
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->warn("[dry-run] {$users->count()} account(s) would be pruned (older than {$days} day(s)):");
            foreach ($users as $u) {
                $this->line("  - {$u->email} ({$u->role}, created {$u->created_at->toDateString()})");
            }
            return self::SUCCESS;
        }

        $deleted = 0;
        foreach ($users as $user) {
            DB::transaction(function () use ($user, &$deleted) {
                if ($user->archer) {
                    if ($user->archer->photo) {
                        Storage::disk('public')->delete($user->archer->photo);
                    }
                    $user->archer->delete();
                }
                if ($user->coach) {
                    if ($user->coach->photo) {
                        Storage::disk('public')->delete($user->coach->photo);
                    }
                    $user->coach->delete();
                }

                // A club admin who never verified: remove their still-pending club too
                // (frees the slug/subdomain). Only if it was never approved.
                if ($user->role === 'club_admin' && $user->club && ! $user->club->active) {
                    $club = $user->club;
                    if ($club->logo) {
                        Storage::disk('public')->delete($club->logo);
                    }
                    $club->delete();
                }

                $user->delete();
                $deleted++;
            });
        }

        $this->info("Pruned {$deleted} unverified registration(s) older than {$days} day(s).");
        return self::SUCCESS;
    }
}
