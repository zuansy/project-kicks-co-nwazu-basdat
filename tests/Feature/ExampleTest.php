<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_panel_admin_mengarahkan_tamu_ke_login(): void
    {
        $this->get('/barang')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_halaman_login_dan_daftar_bisa_dibuka(): void
    {
        $this->get('/login')->assertStatus(200);
        $this->get('/daftar')->assertStatus(200);
    }
}
