# Lumen PHP Framework

[![Build Status](https://travis-ci.org/slimsolz/myDiary-php.svg?branch=develop)](https://travis-ci.org/slimsolz/myDiary-php)
[![License](https://poser.pugx.org/laravel/lumen-framework/license.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Maintainability](https://api.codeclimate.com/v1/badges/5730add7516b6bd86a89/maintainability)](https://codeclimate.com/github/slimsolz/myDiary-php/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/5730add7516b6bd86a89/test_coverage)](https://codeclimate.com/github/slimsolz/myDiary-php/test_coverage)

## MyDiary

MyDiary is an online journal where users can pen down their thoughts and feelings.

## Features

- Sign up: `POST api/v1/auth/signup`
- Sign in: `POST api/v1/auth/signin`
- Update profile: `PUT api/v1/profile`
- View profile: `GET api/v1/profile`
- List all entries: `GET api/v1/entries`
- show a single entry: `GET api/v1/entries/{id}`
- Add new entry: `POST api/v1/entries`
- Update an entry: `PUT api/v1/entries/{id}`
- Delete an entry: `DELETE api/v1/entries/{id}`

### Dependencies

- Lumen: Web application framework for PHP.

### How To Contribute

- Fork the project & clone locally.
- Branch for each separate piece of work `$ git checkout -b <branch-name>`
- Do the work, write good commit messages.
- Push to your origin repository.
- Create a new PR in GitHub.
- Wait for approval.

### License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

#### Author

[Odumah Solomon](https://twitter.com/slimsolz)
