<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard');
    }

    public function barang(): View
    {
        return view('admin.barang');
    }

    public function riwayat(): View
    {
        return view('admin.riwayat');
    }

    public function laporan(): View
    {
        return view('admin.laporan');
    }

    public function pengeluaran(): View
    {
        return view('admin.pengeluaran');
    }

    public function pengaturan(): View
    {
        return view('admin.pengaturan');
    }

    public function manajemenAkun(): View
    {
        return view('admin.manajemen-akun');
    }

    public function kategori(): View
    {
        return view('admin.kategori');
    }
}
