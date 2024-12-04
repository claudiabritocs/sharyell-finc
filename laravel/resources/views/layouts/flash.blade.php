@if(session('success'))
<div class="alert alert-block alert-success" id="flash" x-data="{flash: true}" x-show="flash" x-on:click.outside="flash = false">
    <i class="bi bi-check-circle-fill me-2"></i>
    {!! session('success') !!}
</div>
@endif

@if(session('problem'))
<div class="alert alert-block alert-danger" id="flash" x-data="{flash: true}" x-show="flash" x-on:click.outside="flash = false">
    <i class="bi bi-check-circle-fill me-2"></i>
    {!! session('problem') !!}
</div>
@endif

@if($errors->any())
<div class="alert alert-block alert-danger" id="flash" x-data="{flash: true}" x-show="flash" x-on:click.outside="flash = false">
    <i class="bi bi-exclamation-circle-fill me-2"></i>
    @foreach($errors->all() as $error)
    {!! $error !!}<br>
    @endforeach
</div>
@endif