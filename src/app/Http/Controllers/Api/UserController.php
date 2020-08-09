<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $admin;

    public function __construct(Request $request)
    {
        $this->middleware(function ($request, $next) {
            $this->admin = $request->user()->tokenCan('admin');
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($this->admin) {
            $users = User::all();
        } else {
            $users = User::all(['id', 'name']);
        }

        $response = [
            'success' => true,
            'result' => $users,
        ];

        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, User $user)
    {
        if (Auth::id() !== $user->id && !$this->admin) {
            $user = $user->only(['id', 'name']);
        }

        $response = [
            'success' => true,
            'result' => $user,
        ];

        return response()->json($response, 200);
    }

    /**
     * Update the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if (Auth::id() !== $user->id && !$this->admin) {
            $response = [
                'success' => false,
                'message' => 'Unauthorised request. Missing permissions to update resource.',
            ];

            return response()->json($response, 401);
        }

        $user->update($request->all());

        $response = [
            'success' => true,
            'result' => $user,
        ];

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        if (Auth::id() !== $user->id && !$this->admin) {
            $response = [
                'success' => false,
                'message' => 'Unauthorised request. Missing permissions to remove resource.',
            ];

            return response()->json($response, 401);
        }

        $user->delete();

        $response = [
            'success' => true,
            'result' => "User resource:{$user->id} has been deleted",
        ];

        return response()->json($response, 200);
    }

    public static function respondNotFound(string $msg = null)
    {
        $error['code'] = 404;

        if (!!$msg) {
            $error['message'] = $msg;
        }

        $response = [
            'success' => false,
            'error' => $error,
        ];

        return response()->json($response);
    }
}