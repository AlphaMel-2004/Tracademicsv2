<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Role;
use App\Models\Program;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update Program Head users to have their program_id assigned
        $programHeadRole = Role::where('name', 'Program Head')->first();
        if ($programHeadRole) {
            $programHeads = User::where('role_id', $programHeadRole->id)->get();
            
            foreach ($programHeads as $head) {
                $programCode = str_replace('Program Head ', '', $head->name);
                $program = Program::where('code', $programCode)->first();
                
                if ($program && !$head->program_id) {
                    $head->update(['program_id' => $program->id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set program_id to null for Program Head users
        $programHeadRole = Role::where('name', 'Program Head')->first();
        if ($programHeadRole) {
            User::where('role_id', $programHeadRole->id)->update(['program_id' => null]);
        }
    }
};
