<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TutorStudentSeeder extends Seeder
{
   public function run(): void
   {
       DB::beginTransaction();
       
       try {
           $studentRole = Role::firstOrCreate([
               'name' => 'student'
           ], [
               'display_name' => 'Étudiant',
               'description' => 'Rôle étudiant',
               'is_active' => true
           ]);

           $tutorRole = Role::firstOrCreate([
               'name' => 'tutor'
           ], [
               'display_name' => 'Tuteur',
               'description' => 'Rôle tuteur',
               'is_active' => true
           ]);

           // Créer un étudiant test
           $student = User::create([
               'first_name' => 'Test',
               'last_name' => 'Student',
               'email' => 'student@test.com',
               'password' => Hash::make('password'),
               'phone' => '+212600000001',
               'address' => '123 Rue Principale, Casablanca',
               'is_active' => true,
               'email_verified_at' => now()
           ]);

           UserRole::create([
               'user_id' => $student->id,
               'role_id' => $studentRole->id
           ]);

           // Créer un tuteur test
           $tutor = User::create([
               'first_name' => 'Test',
               'last_name' => 'Tutor',
               'email' => 'tutor@test.com',
               'password' => Hash::make('password'),
               'phone' => '+212600000002',
               'address' => '123 Rue Principale, Casablanca',
               'is_active' => true,
               'email_verified_at' => now()
           ]);

           UserRole::create([
               'user_id' => $tutor->id,
               'role_id' => $tutorRole->id
           ]);

           DB::commit();

           $this->command->info('✅ Test users created!');
           $this->command->info('👨‍🎓 Student: student@test.com / password');
           $this->command->info('👨‍🏫 Tutor: tutor@test.com / password');
           
       } catch (\Exception $e) {
           DB::rollback();
           throw $e;
       }
   }
}