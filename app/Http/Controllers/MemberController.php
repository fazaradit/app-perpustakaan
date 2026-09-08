<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    private array $members = [
        [
            'id' => 1,
            'nama' => 'Fieza Rausyan Al Ghifari',
            'nim' => '3125600001',
            'email' => 'fieza.ghifari@example.com',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Jl. Merdeka No. 10, Jakarta',
            'status' => 'aktif',
        ],
        [
            'id' => 2,
            'nama' => 'Reyvan Andhika',
            'nim' => '3125600025',
            'email' => 'reyvan.andhika@example.com',
            'nomor_telepon' => '082345678901',
            'alamat' => 'Jl. Sudirman No. 45, Bandung',
            'status' => 'aktif',
        ],
        [
            'id' => 3,
            'nama' => 'Radityarma Naufal Fazani',
            'nim' => '3125600008',
            'email' => 'radityarma.naufal@example.com',
            'nomor_telepon' => '083456789012',
            'alamat' => 'Jl. Diponegoro No. 8, Surabaya',
            'status' => 'nonaktif',
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = $this->members;

        return view('members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $validated = $request->validated();

        return redirect()->route('members.index')
            ->with('success', "Anggota \"{$validated['nama']}\" berhasil ditambahkan (data dummy, belum tersimpan ke database).");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return "MemberController@show, id: {$id}";
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return "MemberController@edit, id: {$id}";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return "MemberController@update, id: {$id}";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return "MemberController@destroy, id: {$id}";
    }
}
