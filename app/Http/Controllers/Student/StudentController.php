<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{

    public function index()
    {
        $users = User::paginate(10);
        return inertia('Students/Index', ['users' => $users]);
    }
    public function show(User $user)
    {
        return inertia('Students/Index', ['user' => $user]);
    }
}
