<?php
namespace App\Contracts;

use Illuminate\Http\Request;

interface StorableInterface
{
    public function store(Request $request);
}
