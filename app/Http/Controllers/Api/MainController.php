<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class MainController extends Controller
{
    public function get(Request $request)
    {
        if ($request->query('day') || $request->query('day') === "0") {
            $data = Todo::where('uid', Auth::user()->id)
                ->where('day', $request->query('day'))
                ->get();
        } else {
            $data  = Auth::user()->todo;
        }
        if (sizeof($data) > 0) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "berhasil mendapatkan " . sizeof($data) . " data",
                    'data' => $data
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => "belum memiliki item",
                    'data' => null
                ],
                200
            );
        }
    }

    public function getOne(Request $request)
    {
        $id = $request->query('id');
        if (!$id) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "id wajib",
                    'data' => null
                ],
                200
            );
        }

        $todo = Todo::find($id);
        if (!$todo) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "item not found",
                    'data' => null
                ],
                200
            );
        }

        if ($todo->uid != Auth::user()->id) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "this item not yours",
                    'data' => null
                ],
                200
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "success get data",
                'data' => $todo
            ],
            200
        );
    }

    public function store(Request $request)
    {
        if (
            is_null($request->name)
            || is_null($request->url)
            || is_null($request->day)
        ) {
            return response()->json([
                'status' => false,
                'message' => 'name, url, day are required',
                'data' => null
            ], 200);
        }

        if ($request->day > 6 || $request->day < 0) {
            return response()->json([
                'status' => false,
                'message' => 'day must be in 0 until 6',
                'data' => null
            ], 200);
        }

        $todo = Todo::create([
            'name' => $request->name,
            'url' => $request->url,
            'day' => $request->day,
            'uid' => Auth::user()->id
        ]);
        return response()->json(
            [
                'status' => true,
                'message' => 'success create data',
                'data' => $todo
            ],
            200
        );
    }

    public function update(Request $request)
    {
        $id = $request->query('id');
        if (!$id) {
            return response()->json([
                'status' => false,
                'message' => 'id todo is required',
                'data' => null
            ], 200);
        }

        $todo = Todo::find($id);
        if (!$todo) {
            return response()->json([
                'status' => false,
                'message' => 'item is not found',
                'data' => null
            ], 200);
        }

        if ($request->query('name')) {
            $todo->name = $request->query('name');
        }

        if ($request->query('day')) {
            $todo->day = $request->query('day');
        }

        if ($request->query('url')) {
            $todo->url = $request->query('url');
        }

        $todo->save();

        return response()->json(
            [
                'status' => true,
                'message' => 'success updated data',
                'data' => $todo
            ],
            200
        );
    }

    public function drop(Request $request)
    {
        $id = $request->query('id');
        if (!$id) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'id todo is required',
                    'data' => null
                ],
                200
            );
        }

        $todo = Todo::find($id);
        if (!$todo) {
            return response()->json([
                'status' => false,
                'message' => 'item is not found',
                'data' => null
            ], 200);
        }

        if ($todo->user->id != Auth::user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'this item is not yours',
                'data' => null
            ], 200);
        }

        $todo->delete();
        return response()->json([
            'status' => true,
            'message' => 'Success delete item',
            'data' => null
        ], 200);
    }
    public function check(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'you are authenticated',
            'data' => $request->user()
        ], 200);
    }

    public function login(Request $request)
    {
        if (is_null($request->email) || is_null($request->password)) {
            return response()->json([
                'status' => false,
                'message' => 'email or password is required',
                'data' => null
            ], 200);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'user not found',
                'data' => null
            ], 200);
        }

        if (Hash::check($request->password, $user->password)) {
            $user->api_token = Str::random(24);
            $user->save();
            return response()->json([
                'status' => true,
                'message' => 'login successfull',
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Authenticating fail',
                'data' => null
            ], 200);
        }
    }

    public function register(Request $request)
    {
        if (
            is_null($request->email)
            || is_null($request->password)
            || is_null($request->name)
        ) {
            return response()->json([
                'status' => false,
                'message' => 'name, email, password are required',
                'data' => null
            ], 200);
        }

        $u = User::where('email', $request->email)->first();
        if ($u) {
            return response()->json([
                'status' => false,
                'message' => 'email already used',
                'data' => null
            ], 200);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(24)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'login successfull',
            'data' => $user
        ], 200);
    }
}
