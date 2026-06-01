<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        return view('clientes.index');
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        return view('clientes.show');
    }

    public function edit(string $id)
    {
        return view('clientes.edit');
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}