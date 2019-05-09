<?php

class JwtMiddlewareTest extends TestCase
{
  /**
   * Test token
   *
   * @test
   */
  public function should_fail_if_token_is_not_provided()
  {
    $response = $this->get('/api/v1/profile');
    $response->seeStatusCode(401)
      ->seeJsonEquals([
        'message' => 'Token not provided',
      ]);
  }

  /**
   * Test token
   *
   * @test
   */
  public function should_fail_if_token_provided_has_expired()
  {
    $response = $this->get('/api/v1/profile', ['HTTP_Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJqd3QiLCJzdWIiOjEsImlhdCI6MTU1NjczODI4OCwiZXhwIjoxNTU2ODI0Njg4fQ.Z_w51DeBc83pgw8FAZ5Nts6TWq-0M-KCZsmnHyoulcY']);
    $response->seeStatusCode(401)
      ->seeJsonEquals([
        'message' => 'Token expired, please login again',
      ]);
  }

  /**
   * Test token
   *
   * @test
   */
  public function should_fail_if_token_provided_has_bad_signature()
  {
    $response = $this->get('/api/v1/profile', ['HTTP_Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJqd3QiLCJzdWIiOjEsImlhdCI6MTU1NjcxOTYxNSwiZXhwIjoxNTU2ODA2MDE1fQ.7miK992AbTOT320jA0xre5CL8MVpyp3NMMABnJjLk']);
    $response->seeStatusCode(401)
      ->seeJsonEquals([
        'message' => 'Invalid token provided',
      ]);
  }

}
