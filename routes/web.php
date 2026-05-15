<?php

use App\Http\Controllers\LabelController;
use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [NoteController::class, 'index'])->name('notes.index');

// Labels routes
Route::put('labels', [LabelController::class, 'update'])->name('labels.update');
Route::resource('labels', LabelController::class)->only(['show']);
Route::put('notes/{note}/add_label', [LabelController::class, 'addLabel'])->name('notes.add_label');

// Trash routes
Route::get('trash', [NoteController::class, 'trash'])->name('notes.trash');
Route::delete('send_trash/{note}', [NoteController::class, 'sendTrash'])->name('notes.sendTrash');
Route::put('restore/{note}', [NoteController::class, 'restore'])->name('notes.restore');
Route::delete('empty_trash/', [NoteController::class, 'emptyTrash'])->name('notes.empty_trash');
Route::get('notes/{note}/trash', [NoteController::class, 'showReadOnly'])->name('notes.read_only');

// Notes routes
Route::post('notes/search', [NoteController::class, 'search'])->name('notes.search');
Route::get('notes/search/query={search}', [NoteController::class, 'searchView'])->name('notes.searchView');
Route::get('notes/{note}/labels', [NoteController::class, 'showLabelsEdit'])->name('notes.show_labels');
Route::get('notes/{note}/make_copy', [NoteController::class, 'makeCopy'])->name('notes.make_copy');
Route::resource('notes', NoteController::class)->except(["index", "edit"]);
