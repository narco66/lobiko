<div class="table-responsive">
    <table {{ $attributes->merge(['class' => 'table table-striped align-middle']) }}>
        @isset($head)
            <thead>
                <tr>
                    {{ $head }}
                </tr>
            </thead>
        @endisset
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
