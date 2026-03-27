<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class NfcAdminController extends Controller
{
    public function index()
    {
        $title = 'Write NFC';

        $users = User::orderBy('id', 'desc')->paginate(15);

        return view('nfc.admin-write', compact('title', 'users'));
    }
}

