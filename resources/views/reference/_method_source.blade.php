{{-- Shows a method of the page's controller under the path of the file that defines it, so an example can print the PHP that builds its widgets --}}
@php
    $sourceMethod = new ReflectionMethod(request()->route()->getController(), $method);
    $sourceDoc = $sourceMethod->getDocComment();
    $sourceStart = $sourceMethod->getStartLine() - 1 - ($sourceDoc ? substr_count($sourceDoc, "\n") + 1 : 0);
    $sourceLines = array_slice(file($sourceMethod->getFileName()), $sourceStart, $sourceMethod->getEndLine() - $sourceStart);
    $sourceCode = preg_replace('/^    /m', '', rtrim(implode('', $sourceLines)));
    $sourcePath = str_replace(DIRECTORY_SEPARATOR, '/', substr($sourceMethod->getFileName(), strlen(base_path()) + 1));
@endphp
@include('reference._code', ['path' => $sourcePath, 'code' => $sourceCode, 'fragment' => true])
