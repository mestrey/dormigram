<?php

namespace Database\Seeders;

use App\Contracts\Repositories\RoleRepositoryContract;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(RoleRepositoryContract $roleRepository)
    {
        $roleRepository->truncate();

        $roleRepository->create(['name' => 'admin']);
    }
}
