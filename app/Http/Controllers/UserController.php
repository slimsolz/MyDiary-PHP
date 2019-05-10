<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
  private $request;

  public function __construct(Request $request) {
    $this->request = $request;
  }

  public function signUp() {
    $this->validate($this->request, [
      'name' => 'required|alpha',
      'email' => 'required|email',
      'password' => 'required|alpha_num|min:8'
    ]);

    $foundUser = User::where('email', $this->request->email)->first();
    if ($foundUser) {
      return response()->json([
        'message' => 'User already exists'
      ], 409);
    }

    $newUser = User::create([
      'name' => $this->request->name,
      'email' => $this->request->email,
      'password' => $this->request->password
    ]);

    $token = $this->jwt($newUser);

    return response()->json([
      'message' => 'user successfully created',
      'token' => $token,
      'user' => [
        'name' => $newUser->name,
        'email' => $newUser->email,
      ]
    ], 201);
  }

  public function login() {
    $this->validate($this->request, [
      'email' => 'required|email',
      'password' => 'required|alpha_num|min:8'
    ]);

    $user = [
      'email' => $this->request->email,
      'password' => $this->request->password
    ];

    $foundUser = User::where('email', $user['email'])->first();
    if (!$foundUser) {
      return response()->json([
        'message' => 'Incorrect email/password'
      ], 401);
    }

    $confirmPassword = Hash::check($user['password'], $foundUser->password);

    if($confirmPassword) {
      $token = $this->jwt($foundUser);
      return response()->json([
        'message' => 'user logged in successfully',
        'token' => $token,
        'user' => [
          'email' => $foundUser->email,
        ]
      ]);
    }

    return response()->json([
      'message' => 'Incorrect email/password'
    ], 401);
  }

  public function viewProfile()
  {
    $user_id = $this->request->auth->id;
    return User::findOrFail($user_id);
  }

  public function updateProfile()
  {
    $this->validate($this->request, [
      'firstname' => 'required|alpha',
      'lastname' => 'required|alpha',
      'sex' => 'required|boolean'
    ]);

    $user_id = $this->request->auth->id;
    $user = User::find($user_id);
    $user->firstname  = $this->request->firstname;
    $user->lastname = $this->request->lastname;
    $user->sex = $this->request->sex;

    $user->save();
    return response()->json([
      'message' => 'profile updated successfully',
      'profile' => $user
    ]);
  }

  /**
     * Create a token
     *
     * @param \App\User $user
     * @return string
     */
    protected function jwt(User $user) {
      $payload = [
          'iss' => "jwt",
          'sub' => $user->id,
          'iat' => time(),
          'exp' => time() + 60 * 60 * 24
      ];

      return JWT::encode($payload, env('JWT_SECRET'));
  }
}
