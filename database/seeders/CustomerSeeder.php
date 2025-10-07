<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar 50 clientes aleatórios
        Customer::factory(50)->create();

        // Criar alguns clientes específicos para teste
        Customer::create([
            'name' => 'João Silva',
            'email' => 'joao.silva@example.com',
            'phone' => '(11) 99999-9999',
            'address' => 'Rua das Flores, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01234-567',
            'country' => 'BR',
            'status' => 'active',
            'notes' => 'Cliente VIP - desconto especial',
        ]);

        Customer::create([
            'name' => 'Maria Santos',
            'email' => 'maria.santos@example.com',
            'phone' => '(21) 88888-8888',
            'address' => 'Av. Copacabana, 456',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'zip_code' => '22000-000',
            'country' => 'BR',
            'status' => 'active',
        ]);

        Customer::create([
            'name' => 'Pedro Costa',
            'email' => 'pedro.costa@example.com',
            'phone' => '(31) 77777-7777',
            'address' => 'Rua da Liberdade, 789',
            'city' => 'Belo Horizonte',
            'state' => 'MG',
            'zip_code' => '30000-000',
            'country' => 'BR',
            'status' => 'inactive',
            'notes' => 'Cliente inativo há 6 meses',
        ]);
    }
}
