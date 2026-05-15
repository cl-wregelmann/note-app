<div class="header-container">

     {{-- Header bar --}}
     <header class="header">
          <button class="sidebar-button" id="sidebar-button">
               <span class="material-icons-outlined">&#xe5d2;</span>
          </button>

          @isset($trash)
               <form action="{{ route('notes.empty_trash') }}" method="post" class="empty-trash">
               <form action="" method="post" class="empty-trash">
                    @csrf
                    @method('delete')
                    <button type="submit">Empty Trash Now</button>
               </form>
          @else
               <form action="{{ route('notes.search') }}" method="post" class="input-search">
                    @csrf
                    <input type="text" name="search" id="note-name" placeholder=" &#xf002 Search your notes" style="font-family:Arial, FontAwesome" value="@isset($search){{ $search }}@endisset" required>
                    <button type="submit">
                         <span class="material-icons-outlined">&#xf1df;</span>
                    </button>
               </form>
          @endisset
     </header>

</div>
