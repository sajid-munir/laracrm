<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Copies admins, agents, leads, notes and activity logs from the old plain-PHP
 * CRM database (the "legacy" connection) into this app's database.
 *
 * IDs are kept, so links between leads, agents and notes stay the same.
 * Password hashes are copied as they are; Laravel's bcrypt check accepts them,
 * so everyone signs in with their existing password. The old database is only read.
 */
#[Signature('crm:import {--force : Skip the confirmation prompt}')]
#[Description('Replace this app\'s CRM data with the data from the old CRM database')]
class ImportLegacyCrm extends Command
{
    /**
     * Tables in foreign-key order, with the columns copied from each.
     *
     * @var array<string, list<string>>
     */
    private const TABLES = [
        'users' => ['id', 'name', 'email', 'password', 'status', 'created_at'],
        'sales_agents' => ['id', 'employee_name', 'phone', 'email', 'password', 'status', 'created_at'],
        'leads' => ['id', 'lead_title', 'customer_name', 'phone', 'email', 'message', 'assigned_agent_id', 'status', 'is_new', 'created_at', 'updated_at'],
        'lead_notes' => ['id', 'lead_id', 'added_by', 'admin_id', 'agent_id', 'note', 'created_at'],
        'lead_activity_logs' => ['id', 'lead_id', 'action', 'old_value', 'new_value', 'performed_by', 'admin_id', 'agent_id', 'created_at'],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $legacy = DB::connection('legacy');
        $legacyName = $legacy->getDatabaseName();

        $this->info("Source: {$legacyName}   Target: ".DB::connection()->getDatabaseName());

        if (! $this->option('force') && ! $this->confirm('This deletes all users, agents, leads and notes in the target database and copies them from the source. Continue?')) {
            return self::FAILURE;
        }

        Schema::withoutForeignKeyConstraints(function () use ($legacy): void {
            DB::transaction(function () use ($legacy): void {
                foreach (array_reverse(array_keys(self::TABLES)) as $table) {
                    DB::table($table)->delete();
                }

                DB::table('password_resets')->delete();

                foreach (self::TABLES as $table => $columns) {
                    $copied = 0;

                    $legacy->table($table)->select($columns)->orderBy('id')->chunk(500, function ($rows) use ($table, &$copied): void {
                        DB::table($table)->insert($rows->map(fn ($row): array => (array) $row)->all());
                        $copied += $rows->count();
                    });

                    $this->line(str_pad($table, 20).$copied.' rows');
                }
            });
        });

        $this->info('Import finished.');

        return self::SUCCESS;
    }
}
