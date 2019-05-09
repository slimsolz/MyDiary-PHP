<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;

class UserControllerTest extends TestCase
{
  use DatabaseMigrations;

  private $user = [
    'name' => 'testuser',
    'email' => 'testuser@gmail.com',
    'password' => 'testpassword123'
  ];

  protected $token;

  /**
   * test signup.
   *
   * @test
   */
  public function should_successfully_create_a_new_user()
  {
    $response = $this->post('/api/v1/auth/signup', $this->user);
    $response->seeStatusCode(201)
      ->seeJsonStructure([
        'message',
        'token',
        'user' => [
          'name', 'email'
        ]
      ]);
  }

  /**
   * test signup.
   *
   * @test
   */
  public function should_fail_if_email_already_exists()
  {
    User::create($this->user);
    $response = $this->post('/api/v1/auth/signup', $this->user);
    $response->seeStatusCode(409)
      ->seeJsonEquals(['message' => 'User already exists']);
  }

  /**
   * test signup.
   *
   * @test
   */
  public function should_fail_if_email_is_invalid()
  {
    $response = $this->post('/api/v1/auth/signup', [
      'name' => 'usertest',
      'email' => 'wrongemail',
      'password' => 'password1234'
    ]);
    $response->seeStatusCode(422)
      ->seeJsonEquals(['email' => ['The email must be a valid email address.']]);
  }

  /**
   * test signup.
   *
   * @test
   */
  public function should_fail_if_name_is_missing()
  {
    $response = $this->post('/api/v1/auth/signup', [
      'email' => 'wrongemail@gmail.com',
      'password' => 'password1234'
    ]);
    $response->seeStatusCode(422)
      ->seeJsonEquals(['name' => ['The name field is required.']]);
  }

  /**
   * test signup.
   *
   * @test
   */
  public function should_fail_if_name_is_invalid()
  {
    $response = $this->post('/api/v1/auth/signup', [
      'name' => 1234,
      'email' => 'wrongemail@gmail.com',
      'password' => 'password1234'
    ]);
    $response->seeStatusCode(422)
      ->seeJsonEquals(['name' => ['The name may only contain letters.']]);
  }

  /**
   * test signup.
   *
   * @test
   */
  public function should_fail_if_password_is_missing()
  {
    $response = $this->post('/api/v1/auth/signup', [
      'name' => 'testname',
      'email' => 'wrongemail@gmail.com',
      'password' => ''
    ]);
    $response->seeStatusCode(422)
      ->seeJsonEquals(['password' => ['The password field is required.']]);
  }

  /**
   * test signup.
   *
   * @test
   */
  public function should_fail_if_password_is_less_than_6_characters()
  {
    $response = $this->post('/api/v1/auth/signup', [
      'name' => 'testname',
      'email' => 'wrongemail@gmail.com',
      'password' => '12345'
    ]);
    $response->seeStatusCode(422)
      ->seeJsonEquals(['password' => ['The password must be at least 8 characters.']]);
  }

  /**
   * test login.
   *
   * @test
   */
  public function should_successfully_log_a_user_in()
  {
    User::create($this->user);
    $response = $this->post('/api/v1/auth/signin', $this->user);
    $response->seeStatusCode(200)
      ->seeJsonStructure(['message']);
  }

  /**
   * test login.
   *
   * @test
   */
  public function should_fail_if_email_is_wrong()
  {
    User::create($this->user);
    $response = $this->post('/api/v1/auth/signin', [
      'email' => 'wrongemail@gmail.com',
      'password' => $this->user['password']
    ]);
    $response->seeStatusCode(401)
    ->seeJsonEquals(['message' => 'Incorrect email/password']);
  }

  /**
   * test login.
   *
   * @test
   */
  public function should_fail_if_password_is_wrong()
  {
    User::create($this->user);
    $response = $this->post('/api/v1/auth/signin', [
      'email' => $this->user['email'],
      'password' => 'testpassword'
    ]);
    $response->seeStatusCode(401)
    ->seeJsonEquals(['message' => 'Incorrect email/password']);
  }

  /**
   * test profile.
   *
   * @test
   */
  public function should_successfully_get_users_profile()
  {
    User::create($this->user);
    $response = $this->get('/api/v1/profile',
    [
      'HTTP_Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJqd3QiLCJzdWIiOjEsImlhdCI6MTU1NzM2MjUzOCwiZXhwIjoxNTg4ODk4NTM4fQ.Cy27Qhg_G9UxHMHU3YzZ574pDGGwtoSMkxsGr93eHyw'
    ]);
    $response->seeStatusCode(200)
      ->seeJsonEquals([
        'id' => 1,
        'name' => 'testuser',
        'email' => 'testuser@gmail.com',
        'firstname' => null,
        'lastname' => null,
        'sex' => null
      ]);
  }

  /**
   * test profile.
   *
   * @test
   */
  public function should_successfully_update_users_profile()
  {
    User::create($this->user);
    $response = $this->put('/api/v1/profile',
    [
      'name'=>$this->user['name'],
      'email'=>$this->user['email'],
      'firstname' => 'first',
      'lastname' => 'last',
      'sex' => false,
      'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJqd3QiLCJzdWIiOjEsImlhdCI6MTU1NzM2MjUzOCwiZXhwIjoxNTg4ODk4NTM4fQ.Cy27Qhg_G9UxHMHU3YzZ574pDGGwtoSMkxsGr93eHyw'
    ]);
    $response->seeStatusCode(200)
      ->seeJsonEquals([
        'message' => 'profile updated successfully',
        'profile' => [
          'id' => 1,
          'name' => 'testuser',
          'email' => 'testuser@gmail.com',
          'firstname' => 'first',
          'lastname' => 'last',
          'sex' => false
        ]
      ]);
  }
}
