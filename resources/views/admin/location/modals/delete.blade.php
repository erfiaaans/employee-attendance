<div class="modal fade" id="deleteModal{{ $location->id }}" tabindex="-1"
    aria-labelledby="deleteModalLabel{{ $location->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('locations.destroy', $location->id) }}" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $location->id }}">Hapus Kantor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kantor <strong>{{ $location->office_name }}</strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger btn-sm">Ya, Hapus</button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>
