{{-- Shows an app file under its path, so an example can print the YAML or PHP that configures it --}}
@include('reference._code', [
    'path' => $path,
    'caption' => $caption ?? null,
    'code' => trim(file_get_contents(base_path($path))),
])
