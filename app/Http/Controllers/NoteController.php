<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNote;
use App\Models\Background;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NoteController extends Controller
{
     public function index()
     {
          $user = User::first();
          $notes = $user->notes()->where("delete", 0)->orderByDesc('updated_at')->get();
          $useMenu = true;
          return view('notes.index', compact('user', 'notes', 'useMenu'));
     }

     public function create()
     {
          $colors = Background::all();
          return view('notes.create', compact('colors'));
     }

     public function store(StoreNote $request)
     {
          $user = User::first();
          $note = Note::create([
               'title' => $request['note-title'],
               'slug' => Str::slug($request['note-title']),
               'body' => $request->body,
               'abstract' => $this->closetags(Str::of($request->body)->limit(200)),
               'user_id' => $user->id
          ]);

          $note->background()->associate($request['background-color']);
          $note->save();

          return redirect()->route('notes.show', $note)->with('info', 'Note created successfully');
     }

     public function show(Note $note)
     {
          $user = User::first();
          $colors = Background::all();
          $edit = true;
          $title = $note->title . ' - Note App';
          return view('notes.show', compact('user', 'note', 'colors', 'edit', 'title'));
     }

     public function showLabelsEdit(Note $note)
     {
          return redirect()->route('notes.show', $note)->with('labels_active_event', true);
     }

     public function makeCopy(Note $note)
     {
          $clone = $note->replicate();
          $clone->push();
          $clone->title .= " - Copy";
          $clone->labels()->sync($note->labels);
          $clone->save();
          return redirect()->route('notes.index')->with('info', 'Note created successfully');
     }

     public function showReadOnly(Note $note)
     {
          $user = User::first();
          $colors = Background::all();
          $readOnly = true;
          $title = $note->title . ' - Note App';
          return view('notes.show', compact('user', 'note', 'colors', 'readOnly', 'title'));
     }

     public function update(StoreNote $request, Note $note)
     {
          $note->title = $request['note-title'];
          $note->body = $request->body;
          $note->abstract = $this->closetags(Str::of($request->body)->limit(200));
          $note->background()->associate($request['background-color']);
          $note->save();
          return redirect()->route('notes.show', $note)->with('info', 'Note updated successfully');
     }

     public function destroy(Note $note)
     {
          $note->delete();
          return redirect()->route('notes.trash')->with('info', 'Note deleted successfully');
     }

     public function sendTrash(Note $note)
     {
          $note->delete = 1;
          $note->save();
          return redirect()->route('notes.index')->with('info', 'Note moved to trash');
     }

     public function trash()
     {
          $user = User::first();
          $notes = $user->notes()->where("delete", 1)->orderByDesc('updated_at')->get();
          $useMenu = true;
          $trash = true;
          $title = 'Trash - Note App';

          return view('notes.index', compact('user', 'notes', 'useMenu', 'trash', 'title'));
     }

     public function emptyTrash(Request $request)
     {
          $notes = User::first()->notes->where("delete", 1);
          $nNotes = $notes->count();

          if ($nNotes > 0)
               foreach ($notes as $note)
                    $note->delete();

          if ($nNotes == 0) {
               $message = "There are no notes";
               return redirect()->route('notes.trash')->withErrors($message);
          }

          $message = $nNotes == 1 ? "Note deleted forever" : "Notes deleted forever";
          return redirect()->route('notes.trash')->with('info', $message);
     }

     public function restore(Note $note)
     {
          $note->delete = 0;
          $note->save();
          return redirect()->route('notes.trash')->with('info', 'Note restored successfully');
     }

     public function search(Request $request)
     {
          $search = $request->search;
          return redirect()->route("notes.searchView", $search);
     }

     public function searchView($search)
     {
          $user = User::first();
          $notes = Note::where([
               ["user_id", $user->id],
               ["delete", 0]
          ])->where(function ($query) use ($search) {
               $query
                    ->where("title", 'LIKE', "%{$search}%")
                    ->orWhere('body', 'LIKE', "%{$search}%");
          })->get();

          $useMenu = true;
          return view('notes.index', compact('user', 'notes', 'useMenu', 'search'));
     }

     public function closetags($html)
     {
          preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
          $openedtags = $result[1];
          preg_match_all('#</([a-z]+)>#iU', $html, $result);
          $closedtags = $result[1];
          $len_opened = count($openedtags);
          if (count($closedtags) == $len_opened) {
               return $html;
          }
          $openedtags = array_reverse($openedtags);
          for ($i = 0; $i < $len_opened; $i++) {
               if (!in_array($openedtags[$i], $closedtags)) {
                    $html .= '</' . $openedtags[$i] . '>';
               } else {
                    unset($closedtags[array_search($openedtags[$i], $closedtags)]);
               }
          }
          return $html;
     }
}
