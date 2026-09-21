<<<<<<< HEAD
<x-setting::layouts.master>
    <h1>Hello World</h1>

    <p>Module: {!! config('setting.name') !!}</p>
</x-setting::layouts.master>
=======
nds('setting::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('setting.name') !!}
    </p>
@endsection
>>>>>>> laraxot/dev
