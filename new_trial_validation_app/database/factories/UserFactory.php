<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password_hash being used by the factory.
     */
    protected static ?string $passwordHash;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password_hash' => static::$passwordHash ??= Hash::make('password'),
            'role' => 'Staff',
            'department' => 'Staff',
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the model should have a given role.
     */
    public function role(string $role, ?string $department = null): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $role,
            'department' => $department ?? $role,
        ]);
    }
}
