<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabel;
use App\Models\Label;
use App\Models\Note;

class LabelController extends Controller
{
     public function show(Label $label)
     {
          $notes = $label->notes()
               ->where("delete", 0)
               ->orderByDesc('updated_at')
               ->get()->unique();

          $useMenu = true;
          $currentLabel = $label;
          $title = $label->name . ' - Note App';
          return view('notes.index', compact('notes', 'useMenu', 'currentLabel', 'title'));
     }

     public function addLabel(StoreLabel $request, Note $note)
     {
          if ($request->labels != null) {
               $note->labels()->sync($request->labels);
          }

          if ($request['new_label'] !== null) {
               $label = Label::create(['name' => $request['new_label']]);
               $note->labels()->attach($label);
          }

          return redirect()->route('notes.show', $note)->with('info', 'Labels updated successfully');
     }

     public function update(StoreLabel $request)
     {
          if ($request->labels != null) {
               if (count($request['labels']) !== count(array_unique($request['labels']))) {
                    return redirect()->route('notes.index')->withErrors("There are labels with the same name");
               }

               $requestLabels = array_combine($request["id-labels"], $request['labels']);

               foreach ($requestLabels as $label_id => $name) {
                    $label = Label::find($label_id);
                    if ($label && $label->name != $name) {
                         $label->name = $name;
                         $label->save();
                    }
               }
          }

          if ($request['new_label'] !== null) {
               Label::create(['name' => $request['new_label']]);
          }

          if ($request['delete-labels'] != null) {
               Label::whereIn('id', $request['delete-labels'])->delete();
          }

          return redirect()->route('notes.index')->with('info', "Complete!");
     }
}
