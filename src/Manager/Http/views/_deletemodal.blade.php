<div id="remove-line-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Remove this translation key?</h4>
            </div>
            <div class="modal-body text-center">
                <p>This will permanently remove translation key:
                    <em>{{ $line->key }}</em> and all of its translations.
                    <br><br>Are you sure?
                </p>
            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <form action="{{ route('squanto.lines.destroy',$line->id) }}" method="POST" class="admin-form">
                        {!! csrf_field() !!}
                        <input type="hidden" name="_method" value="DELETE">
                        <button class="btn btn-danger btn-lg" type="submit">Yes, delete the translation key</button>
                    </form>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
