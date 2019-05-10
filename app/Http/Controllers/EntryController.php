<?php

namespace App\Http\Controllers;

use App\Entry;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon as carbon;

class EntryController extends Controller
{
  private $request;

  public function __construct(Request $request) {
    $this->request = $request;
  }

  public function addEntry()
  {
    $user_id = $this->request->auth->id;

    $this->validate($this->request, [
      'title' => 'required',
      'category' => 'required',
      'image' => 'required',
      'story' => 'required'
    ]);

    $foundEntry = Entry::where('title', $this->request->title)->first();
    if ($foundEntry) return response()->json(['message' => 'Entry already exists'], 409);

    $newEntry = Entry::create([
      'title' => $this->request->title,
      'category' => $this->request->category,
      'image' => $this->request->image,
      'story' => $this->request->story,
      'user_id' => $user_id
    ]);

    return response()->json([
      'message' => 'Entry created successfully',
      'entry' => [
        'title' => $newEntry->title,
        'category' => $newEntry->category,
        'image' => $newEntry->image,
        'story' => $newEntry->story,
        'user_id' => $newEntry->user_id
      ]
    ], 201);
  }

  public function getAllEntries()
  {
    $user_id = $this->request->auth->id;
    $entries = Entry::where('user_id', $user_id)->get();

    if (sizeof($entries) === 0) {
      return response()->json([
        'message' => 'No entries available'
      ]);
    }

    return response()->json([
      'message' => 'Entries retrieved',
      'entries' => $entries
    ]);
  }

  public function getEntry($id)
  {
    if((!is_numeric($id)) || is_double($id + 0) || is_bool($id)){
      return response()->json([
        'message' => 'Invalid product id'
      ], 400);
    }

    $user_id = $this->request->auth->id;
    $entry = Entry::where('id', $id)->where('user_id', $user_id)->get();

    if (sizeof($entry) === 0) {
      return response()->json([
        'message' => 'Entry not found',
      ], 404);
    }

    return response()->json([
      'message' => 'Entry found',
      'entry' => $entry
    ]);
  }

  public function deleteEntry($id)
  {
    if((!is_numeric($id)) || is_double($id + 0) || is_bool($id)){
      return response()->json([
        'message' => 'Invalid product id'
      ], 400);
    }

    $user_id = $this->request->auth->id;
    $entry = Entry::where('id', $id)->where('user_id', $user_id)->delete();

    if($entry) {
      return response()->json([
        'message' => 'Entry deleted successfully',
      ], 200);
    }

    return response()->json([
      'message' => 'Entry not found'
    ], 404);
  }

  public function updateEntry($id)
  {
    if((!is_numeric($id)) || is_double($id + 0) || is_bool($id)){
      return response()->json(['message' => 'Invalid product id'], 400);
    }

    $user_id = $this->request->auth->id;
    $entry = Entry::where('user_id', $user_id)->where('id', $id)->get();

    if (sizeof($entry) === 0) return response()->json(['message' => 'Entry not found',], 404);

    if ((carbon::now()->toDateString()) === ($entry[0]->created_at->toDateString())) {
      $entry[0]->title = $this->request->title;
      $entry[0]->category = $this->request->category;
      $entry[0]->image = $this->request->image;
      $entry[0]->story = $this->request->story;

      $entry[0]->save();
      return response()->json([
        'message' => 'entry updated successfully',
        'entry' => $entry[0]
      ]);
    }

    return response()->json(['message' => 'Entry can only be edited the day it is created'], 403);
  }
}
