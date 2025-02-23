<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users');
    }

    public function getUsers(Request $request)
    {
        $users = User::all();

        return DataTables::of($users)
            ->addColumn('action', function ($row) {
                $editIcon = '<a href="' . route('user.edit', $row->id) . '" class="action-icon btn-edit" title="Edit User"><i class="fa-solid fa-pen-to-square"></i></a>';
                $deleteIcon = '<a href="' . route('user.delete', $row->id) . '" class="action-icon btn-delete" title="Hapus User"><i class="fa-solid fa-trash"></i></a>';
                $switchIcon = '<a href="' . route('user.switch', $row->id) . '" class="action-icon btn-switch" title="Switch Account"><i class="fa-solid fa-user"></i></a>';
                return '<div class="flex justify-center gap-2">' . $editIcon . $deleteIcon . $switchIcon . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
