@extends('layout.admin.admin_layout')

@section('content')
  <form action="" method="POST">
    @csrf
    <div class="mb-3">
      <label>Email Body</label>
      <x-quill-editor name="email_body" :value="old('email_body','')" />
    </div>

  </form>
@endsection
