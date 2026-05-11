@if (session('success'))
    <div class="modal fade" id="successPopup" tabindex="-1" aria-labelledby="successPopupLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title" id="successPopupLabel">Berjaya</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body py-4">
                    {{ session('success') }}
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const successPopup = document.getElementById('successPopup');

                if (successPopup) {
                    bootstrap.Modal.getOrCreateInstance(successPopup).show();
                }
            });
        </script>
    @endpush
@endif

@if ($errors->any())
    <div class="container pt-3">
        <div class="alert alert-danger alert-dismissible fade show">
            <div class="fw-semibold mb-1">Semakan diperlukan.</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif
