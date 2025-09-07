<?php

namespace Tests\Feature;

use Tests\TestCase;
use Livewire\Livewire;
use App\Http\Livewire\Patients;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PatientsQuickAssociateTest extends TestCase
{
    use RefreshDatabase;

    public function test_quick_associate_attaches_user_to_existing_patient()
    {
        // create a patient without a user
        $patient = Patient::factory()->create(['user_id' => null]);
        $user = User::factory()->create();

        Livewire::test(Patients::class)
            ->call('openAssociate', $patient->id)
            ->set('associateUserId', $user->id)
            ->call('associateSave');

        $this->assertDatabaseHas('patients', ['id' => $patient->id, 'user_id' => $user->id]);
    }
}
