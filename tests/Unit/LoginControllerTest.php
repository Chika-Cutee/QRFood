<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Auth\LoginController;

class LoginControllerTest extends TestCase
{
    public function test_TC01_input_email_valid()
    {
        $controller = new LoginController();
        $hasil = $controller->tentukanFieldLogin("chika@gmail.com");
        $this->assertEquals("email", $hasil);
    }

    public function test_TC02_input_username()
    {
        $controller = new LoginController();
        $hasil = $controller->tentukanFieldLogin("chika_cute");
        $this->assertEquals("name", $hasil);
    }

    public function test_TC03_input_username_angka()
    {
        $controller = new LoginController();
        $hasil = $controller->tentukanFieldLogin("admin123");
        $this->assertEquals("name", $hasil);
    }

    public function test_TC04_input_email_tidak_valid()
    {
        $controller = new LoginController();
        // Karena domain email salah, ini dianggap sebagai username
        $hasil = $controller->tentukanFieldLogin("chika@domain_salah"); 
        $this->assertEquals("name", $hasil);
    }

    public function test_TC05_input_kosong_error()
    {
        $controller = new LoginController();
        $hasil = $controller->tentukanFieldLogin("");
        $this->assertEquals("Error", $hasil);
    }
}