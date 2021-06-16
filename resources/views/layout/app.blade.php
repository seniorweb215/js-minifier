@extends('shopify-app::layouts.default')

@section('styles')
    <link href="{{ mix('css/app.css') }}" type="text/css" rel="stylesheet" />
@endsection

@section('content')
    <div id="app"></div>
@endsection

@section('scripts')
    @parent

    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        var AppBridge = window['app-bridge'];
        var actions = AppBridge.actions;
        var TitleBar = actions.TitleBar;
        var Button = actions.Button;
        var Redirect = actions.Redirect;
        var titleBarOptions = {
            title: '',
        };
        var myTitleBar = TitleBar.create(app, titleBarOptions);
    </script>
@endsection