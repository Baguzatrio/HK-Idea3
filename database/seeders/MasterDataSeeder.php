<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Divisi;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat Divisi
        $divisiIT = Divisi::updateOrCreate(
            ['kode' => 'IT'],
            ['nama' => 'Information Technology', 'no_urut' => 1]
        );

        $divisiHR = Divisi::updateOrCreate(
            ['kode' => 'HR'],
            ['nama' => 'Human Resources', 'no_urut' => 2]
        );

        $divisiAkun = Divisi::updateOrCreate(
            ['kode' => 'AKUN'],
            ['nama' => 'Akuntansi & Keuangan', 'no_urut' => 3]
        );

        // 2. Buat Permission (Juga sebagai Report Dashboard)
        $perm1 = Permission::updateOrCreate(
            ['name' => 'view-report-it'],
            [
                'guard_name'     => 'web',
                'divisi_id'      => $divisiIT->id,
                'judul_report'   => 'Laporan Infrastruktur IT',
                'nama_report'    => 'Infra IT',
                'link_dashboard' => 'https://example.com/dashboard/it-infra',
            ]
        );

        $perm2 = Permission::updateOrCreate(
            ['name' => 'view-report-hr'],
            [
                'guard_name'     => 'web',
                'divisi_id'      => $divisiHR->id,
                'judul_report'   => 'Laporan Kinerja Karyawan',
                'nama_report'    => 'Kinerja HR',
                'link_dashboard' => 'https://example.com/dashboard/hr-kinerja',
            ]
        );

        // 3. Buat Role & Assign Permissions
        $superAdminRole = Role::updateOrCreate(
            ['name' => 'super_admin'],
            ['guard_name' => 'web']
        );
        $superAdminRole->syncPermissions([$perm1, $perm2]);

        $managerRole = Role::updateOrCreate(
            ['name' => 'manager'],
            ['guard_name' => 'web']
        );
        $managerRole->syncPermissions([$perm1]);

        // 4. Buat Akun Super Admin & Manager
        $superAdminUser = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password'),
                'divisi_id' => $divisiIT->id,
            ]
        );
        if (!$superAdminUser->hasRole('super_admin')) {
            $superAdminUser->assignRole($superAdminRole);
        }

        $managerUser = User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name'      => 'Manager IT',
                'password'  => Hash::make('password'),
                'divisi_id' => $divisiIT->id,
            ]
        );
        if (!$managerUser->hasRole('manager')) {
            $managerUser->assignRole($managerRole);
        }

        $this->command->info('Seeding Master Data Berhasil! User admin@example.com siap digunakan dengan password: password');
    }
}
