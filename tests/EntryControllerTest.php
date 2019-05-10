<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Carbon\Carbon as carbon;
use App\Entry;
use App\User;

class EntryControllerTest extends TestCase
{
  use DatabaseMigrations;

  private $user = [
    'name' => 'testuser',
    'email' => 'testuser@gmail.com',
    'password' => 'testpassword123'
  ];

  private $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJqd3QiLCJzdWIiOjEsImlhdCI6MTU1NzM2MjUzOCwiZXhwIjoxNTg4ODk4NTM4fQ.Cy27Qhg_G9UxHMHU3YzZ574pDGGwtoSMkxsGr93eHyw';

  /**
   * test entry.
   *
   * @test
   */
  public function should_successfully_create_new_entry()
  {
    User::create($this->user);
    $response = $this->post('/api/v1/entries',
      [
        'title' => 'test title',
        'category' => 'test category',
        'image' => 'testimage.jpg',
        'story' => 'test story'
      ],
      ['HTTP_Authorization' => 'Bearer ' . $this->token]
    );
    $response->seeStatusCode(201)
      ->seeJsonContains([
        'message' => 'Entry created successfully',
        'entry' => [
          'title' => 'test title',
          'category' => 'test category',
          'image' => 'testimage.jpg',
          'story' => 'test story',
          'user_id' => 1
        ]
      ]);
  }

  /**
   * test entry.
   *
   * @test
   */
  public function should_fail_to_create_new_entry_if_entry_exists()
  {
    User::create($this->user);
    Entry::create([
      'title' => 'test title',
      'category' => 'test category',
      'image' => 'testimage.jpg',
      'story' => 'test story',
      'user_id' => 1
    ]);

    $response = $this->post('/api/v1/entries',
      [
        'title' => 'test title',
        'category' => 'test category',
        'image' => 'testimage.jpg',
        'story' => 'test story'
      ],
      ['HTTP_Authorization' => 'Bearer ' . $this->token]
    );

    $response->seeStatusCode(409)
      ->seeJsonContains([
        'message' => 'Entry already exists',
      ]);
  }

  /**
   * test entry.
   *
   * @test
   */
  public function should_return_a_message_is_user_has_no_entry_yet()
  {
    User::create($this->user);
    $response = $this->get('/api/v1/entries',
      ['HTTP_Authorization' => 'Bearer ' . $this->token]
    );

    $response->seeStatusCode(200)
      ->seeJsonContains([
        'message' => 'No entries available'
      ]);
  }

  /**
   * test entry.
   *
   * @test
   */
  public function should_get_all_entries()
  {
    User::create($this->user);
    Entry::create([
      'title' => 'test title',
      'category' => 'test category',
      'image' => 'testimage.jpg',
      'story' => 'test story',
      'user_id' => 1
    ]);

    $response = $this->get('/api/v1/entries',
      ['HTTP_Authorization' => 'Bearer ' . $this->token]
    );
    $response->seeStatusCode(200)
      ->seeJsonContains([
        'message' => 'Entries retrieved'
      ]);
  }

  /**
   * test entry.
   *
   * @test
   */
  public function should_get_a_single_entry()
  {
    User::create($this->user);
    Entry::create([
      'title' => 'test title',
      'category' => 'test category',
      'image' => 'testimage.jpg',
      'story' => 'test story',
      'user_id' => 1
    ]);

    $response = $this->get('/api/v1/entries/1',
      ['HTTP_Authorization' => 'Bearer ' . $this->token]
    );

    $response->seeStatusCode(200)
      ->seeJsonContains([
        'id' => 1,
        'title' => 'test title',
        'category' => 'test category',
        'image' => 'testimage.jpg',
        'story' => 'test story',
        'user_id' => 1
      ]);
  }

  /**
   * test entry.
   *
   * @test
   */
  public function should_fail_to_get_a_single_entry_if_entry_id_is_not_integer()
  {
    $response = $this->get('/api/v1/entries/erwe',
      ['HTTP_Authorization' => 'Bearer ' . $this->token]
    );

    $response->seeStatusCode(400)
      ->seeJsonContains([
        'message' => 'Invalid product id'
      ]);
  }

  /**
   * test entry.
   *
   * @test
   */
  public function should_fail_to_get_a_single_entry_if_entry_does_not_exist()
  {
    User::create($this->user);

    $response = $this->get('/api/v1/entries/100',
      ['HTTP_Authorization' => 'Bearer ' . $this->token]
    );

    $response->seeStatusCode(404)
      ->seeJsonContains([
        'message' => 'Entry not found',
      ]);
  }

  /**
   * test delete entry.
   *
   * @test
   */
  public function should_fail_to_delete_an_entry_if_entry_id_is_not_integer()
  {
    $response = $this->delete('/api/v1/entries/we',
      ['token' => $this->token]);

    $response->seeStatusCode(400)
      ->seeJsonContains([
        'message' => 'Invalid product id'
      ]);
  }

  /**
   * test delete entry.
   *
   * @test
   */
  public function should_fail_to_delete_an_entry_if_entry_does_not_exist()
  {
    User::create($this->user);
    $response = $this->delete('/api/v1/entries/100',
      ['token' => $this->token]
    );

    $response->seeStatusCode(404)
      ->seeJsonContains([
        'message' => 'Entry not found',
      ]);
  }

  /**
   * test delete entry.
   *
   * @test
   */
  public function should_successfully_delete_an_entry_if_entry()
  {
    User::create($this->user);
    Entry::create([
      'title' => 'test title',
      'category' => 'test category',
      'image' => 'testimage.jpg',
      'story' => 'test story',
      'user_id' => 1
    ]);

    $response = $this->delete('/api/v1/entries/1',
      ['token' => $this->token]
    );

    $response->seeStatusCode(200)
      ->seeJsonContains([
        'message' => 'Entry deleted successfully',
      ]);
  }

  /**
   * test update entry.
   *
   * @test
   */
  public function should_fail_to_update_an_entry_if_entry_id_is_not_integer()
  {
    $response = $this->put('/api/v1/entries/we',
      ['token' => $this->token]);

    $response->seeStatusCode(400)
      ->seeJsonContains([
        'message' => 'Invalid product id'
      ]);
  }

  /**
   * test delete entry.
   *
   * @test
   */
  public function should_fail_to_update_an_entry_if_entry_does_not_exist()
  {
    User::create($this->user);
    $response = $this->put('/api/v1/entries/100',
      ['token' => $this->token]
    );

    $response->seeStatusCode(404)
      ->seeJsonContains([
        'message' => 'Entry not found',
      ]);
  }

  /**
   * test delete entry.
   *
   * @test
   */
  public function should_not_be_able_to_update_an_entry_if_entry_a_day_after_it_has_been_created()
  {
    User::create($this->user);
    $date = carbon::now()->subDays(1)->toDateTimeString();
    $newEntry = Entry::create([
      'title' => 'another title',
      'category' => 'test category',
      'image' => 'testimage.jpg',
      'story' => 'test story',
      'created_at' => $date,
      'user_id' => 1
    ]);

    $newEntry->created_at = carbon::now()->subDays(1)->toDateTimeString();
    $newEntry->save();
    $response = $this->put('/api/v1/entries/1',[
        'title' => 'update title',
        'category' => 'update category',
        'image' => 'updateimage.jpg',
        'story' => 'update story',
        'token' => $this->token
      ]
    );
    $response->seeStatusCode(403)
      ->seeJsonEquals([
        'message' => 'Entry can only be edited the day it is created',
      ]);
  }

  /**
   * test delete entry.
   *
   * @test
   */
  public function should_successfully_update_an_entry_if_entry()
  {
    User::create($this->user);
    Entry::create([
      'title' => 'test title',
      'category' => 'test category',
      'image' => 'testimage.jpg',
      'story' => 'test story',
      'user_id' => 1
    ]);

    $response = $this->put('/api/v1/entries/1',[
        'title' => 'update title',
        'category' => 'update category',
        'image' => 'updateimage.jpg',
        'story' => 'update story',
        'token' => $this->token
      ]
    );

    $response->seeStatusCode(200);
  }
}
