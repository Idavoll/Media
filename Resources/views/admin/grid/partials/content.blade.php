<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ trans('media::media.file picker') }}</title>
    {!! Theme::style('vendor/bootstrap/dist/css/bootstrap.min.css') !!}
    {!! Theme::style('vendor/admin-lte/dist/css/AdminLTE.css') !!}
    {!! Theme::style('vendor/datatables.net-bs/css/dataTables.bootstrap.min.css') !!}
    {!! Theme::style('vendor/font-awesome/css/font-awesome.min.css') !!}
    <link href="{!! Module::asset('media:css/dropzone.css') !!}" rel="stylesheet" type="text/css"/>
    <style>
        body {
            background: #ecf0f5;
            margin-top: 20px;
        }

        .dropzone {
            border: 1px dashed #CCC;
            min-height: 227px;
            margin-bottom: 20px;
            display: none;
        }
    </style>
    <script>
        AuthorizationHeaderValue = 'Bearer {{ $currentUser->getFirstApiKey() }}';
    </script>
    @include('partials.asgard-globals')
</head>
<body>
<div class="container">
    <div class="row">
        <form method="POST" class="dropzone">
            {!! Form::token() !!}
        </form>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">{{ trans('media::media.choose file') }}</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool jsShowUploadForm" data-toggle="tooltip" title=""
                            data-original-title="Upload new">
                        <i class="fa fa-cloud-upload"></i>
                        Upload new
                    </button>
                </div>
            </div>
            <div class="box-body">
                <table class="data-table table table-bordered table-hover jsFileList data-table">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>{{ trans('core::core.table.thumbnail') }}</th>
                        <th>{{ trans('media::media.table.filename') }}</th>
                        <th data-sortable="false">{{ trans('core::core.table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{!! Theme::script('vendor/jquery/jquery.min.js') !!}
{!! Theme::script('vendor/bootstrap/dist/js/bootstrap.min.js') !!}
{!! Theme::script('vendor/datatables.net/js/jquery.dataTables.min.js') !!}
{!! Theme::script('vendor/datatables.net-bs/js/dataTables.bootstrap.min.js') !!}
<script src="{!! Module::asset('media:js/dropzone.js') !!}"></script>
<?php $config = config('asgard.media.config'); ?>
<script>
    var maxFilesize = '<?php echo $config['max-file-size'] ?>',
        acceptedFiles = '<?php echo $config['allowed-types'] ?>';
</script>
<script src="{!! Module::asset('media:js/init-dropzone.js') !!}"></script>
<script>
    $(document).ready(function () {
        $('.jsShowUploadForm').on('click', function (event) {
            event.preventDefault();
            $('.dropzone').fadeToggle();
        });
    });
</script>

<?php $locale = App::getLocale(); ?>
<script type="text/javascript">
    $(function () {
        function getThumbnailPath(thumbnails, name) {
            for (var i in thumbnails) {
                if (thumbnails.hasOwnProperty(i) && thumbnails[i].name === name) {
                    return thumbnails[i].path;
                }
            }
        }

        $('.data-table')
            .dataTable({
                'serverSide': true,
                'ajax': {
                    'url': "{{ route('api.media.all-vue') }}",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', AuthorizationHeaderValue);
                    }
                },
                columnDefs: [
                    {
                        'targets': 0,
                        'orderable': false,
                        'data': 'id'
                    }, {
                        'targets': 1,
                        'orderable': false,
                        'data': function (row, type, val, meta) {
                            if (row.is_image === true) {
                                return '<img src="' + row.medium_thumb + '"/>';
                            } else {
                                return '<i class="fa ' + row.fa_icon + '"></i>';
                            }
                        }
                    }, {
                        'targets': 2,
                        'orderable': false,
                        'data': 'filename'
                    },
                    {
                        targets: 3,
                        orderable: false,
                        data: function (row, type, val, meta) {
                            var buttons = '<div class="btn-group">\n';
                                buttons += '                                    <button type="button" class="btn btn-primary btn-flat dropdown-toggle" data-toggle="dropdown" aria-expanded="false">\n' +
                                '                                        {{ trans('media::media.insert') }} <span class="caret"></span>\n' +
                                '                                    </button>\n' +
                                '                                    <ul class="dropdown-menu" role="menu">\n';
                            <?php foreach ($thumbnails as $thumbnail): ?>
                                buttons += '<li>\n' +
                                '                                            <a data-file-path="'+ getThumbnailPath(row.thumbnails, "{{ $thumbnail->name() }}") + '"\n' +
                                '                                            data-id="' + row.id + '" data-media-type="' + row.media_type + '"\n' +
                                '                                            data-mimetype="' + row.mimetype + '"href="#" class="jsInsertImage">{{ $thumbnail->name() }} ({{ $thumbnail->size() }})</a>\n' +
                                '                                        </li>\n';
                            <?php endforeach; ?>
                                buttons += '                                        <li class="divider"></li>\n' +
                                '                                        <li>\n' +
                                '                                            <a data-file-path="' + row.path + '" data-id=' + row.id + '"\n' +
                                '                                            data-media-type="' + row.media_type + '" data-mimetype="' + row.mimetype + '" href="#" class="jsInsertImage">Original</a>\n' +
                                '                                        </li>\n' +
                                '                                    </ul>\n';
                                buttons += '                                </div>';
                            return buttons;
                        }
                    }
                ],
                'paginate': true,
                'lengthChange': true,
                'filter': true,
                'sort': true,
                'info': true,
                'autoWidth': true,
                'order': [[0, 'desc']],
                'language': {
                    'url': '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            })
    })
</script>
