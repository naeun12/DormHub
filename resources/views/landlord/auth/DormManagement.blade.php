<div class="app container-fluid p-0">
    <div class="row g-0">
        @include('landlord.auth.partials.sidebar')
        <div class="col-md-10 main-content">
            @include('landlord.auth.partials.navigation')
            <div id="landlorddormManagement" data-landlord-id="{{ $landlord_id }}">
                <landlord-auth-dormManagement></landlord-auth-dormManagement>

            </div>
        </div>
        @include('landlord.auth.partials.footer')
