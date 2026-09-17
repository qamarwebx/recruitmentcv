@extends('layout.admin.admin_layout')

@section('title', 'Chat')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="mb-3">Chat</h4>
        <div class="qchat qchat-page" id="qchatPageRoot" data-mode="page"></div>
    </div>
@endsection

@section('page-script')
    <script>
        $(function () {
            if (window.ChatEngine) {
                window.ChatEngine.mount('#qchatPageRoot', 'page');
            }
        });
    </script>
@endsection
