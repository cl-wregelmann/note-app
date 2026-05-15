<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabel;
use App\Models\Label;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;

class LabelController extends Controller
{
     public function show(Label $label)
     {
          $user = User::first();
          $notes = $label->notes()
               ->where("delete", 0)
               ->orderByDesc('updated_at')
               ->get()->unique();

          $useMenu = true;
          $currentLabel = $label;
          $title = $label->name . ' - Note App';
          return view('notes.index', compact('user', 'notes', 'useMenu', 'currentLabel', 'title'));
     }

     public function addLabel(StoreLabel $request, Note $note)
     {
          $user = User::first();

          if ($request->labels != null) {
               $note->labels()->sync($request->labels);
          }

          if ($request['new_label'] !== null) {
               $label = Label::create([
                    'name' => $request['new_label'],
                    'user_id' => $user->id
               ]);
               $note->labels()->attach($label);
          }
          return redirect()->route('notes.show', $note)->with('info', 'Labels updated successfully');
     }

     public function update(StoreLabel $request)
     {
          $user = User::first();

          if ($request->labels != null) {
               if (count($request['labels']) !== count(array_unique($request['labels']))) {
                    return redirect()->route('notes.index')->withErrors("There are labels with the same name");
               }

               $requestLabels = array_combine($request["id-labels"], $request['labels']);

               foreach ($requestLabels as $label_id => $name) {
                    $userLabel = $user->labels->find($label_id);

                    if ($userLabel->name != $name) {
                         $userLabel->name = $name;
                         $userLabel->save();
                    }
               }
          }

          if ($request['new_label'] !== null) {
               Label::create([
                    'name' => $request['new_label'],
                    'user_id' => $user->id
               ]);
          }

          if ($request['delete-labels'] != null) {
               foreach ($request['delete-labels'] as $labelToDelete)
                    $user->labels->find($labelToDelete)->delete();
          }

          return redirect()->route('notes.index')->with('info', "Complete!");
     }
}
