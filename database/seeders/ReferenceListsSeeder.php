<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\Sample;
use Illuminate\Database\Seeder;

/**
 * ReferenceListsSeeder attaches gallery images, a brochure and documents to the samples after the reference sample, for the image and file list columns.
 */
class ReferenceListsSeeder extends Seeder
{
    /**
     * @var array<int, array<int, string>> documents attached to each sample, by extension.
     */
    protected array $documents = [
        ['csv', 'txt', 'json'],
        ['txt'],
        ['csv', 'json'],
    ];

    /**
     * Seed the files of the samples that have none yet, leaving the first sample to the form pages.
     */
    public function run(): void
    {
        $samples = Sample::query()->orderBy('id')->skip(1)->take(count($this->documents))->get();

        foreach ($samples->values() as $index => $sample) {
            if ($sample->attachments()->exists()) {
                continue;
            }

            foreach (range(1, $index + 1) as $number) {
                $this->attachImage($sample, $number);
            }

            $this->attachFile($sample, 'brochure', "{$sample->slug}-brochure.pdf", $this->makePdf($sample->name));

            foreach ($this->documents[$index] as $extension) {
                $this->attachFile($sample, 'attachments', "{$sample->slug}-details.{$extension}", $this->makeDocument($sample, $extension));
            }
        }
    }

    /**
     * attachImage generates a square in the sample's color, darker for each further number, when the GD extension is available.
     */
    protected function attachImage(Sample $sample, int $number): void
    {
        if (! function_exists('imagecreatetruecolor')) {
            return;
        }

        [$red, $green, $blue] = sscanf($sample->color ?: '#6a6cf7', '#%02x%02x%02x');
        $shade = 1 - ($number - 1) * 0.25;

        $image = imagecreatetruecolor(240, 240);
        imagefill($image, 0, 0, imagecolorallocate($image, (int) ($red * $shade), (int) ($green * $shade), (int) ($blue * $shade)));

        ob_start();
        imagepng($image);
        imagedestroy($image);

        $this->attachFile($sample, 'gallery', "{$sample->slug}-{$number}.png", ob_get_clean());
    }

    /**
     * attachFile stores the contents under the file name and attaches the file to the relation.
     */
    protected function attachFile(Sample $sample, string $relation, string $fileName, string $contents): void
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'amber');
        file_put_contents($tempPath, $contents);

        try {
            $sample->{$relation}()->add((new File)->fromFile($tempPath, $fileName));
        }
        finally {
            @unlink($tempPath);
        }
    }

    /**
     * makeDocument returns the sample's name and price in the given text format.
     */
    protected function makeDocument(Sample $sample, string $extension): string
    {
        return match ($extension) {
            'csv' => "name,price,quantity\n\"{$sample->name}\",{$sample->price},{$sample->quantity}\n",
            'json' => json_encode(['name' => $sample->name, 'price' => $sample->price], JSON_PRETTY_PRINT),
            default => "{$sample->name}\n\n{$sample->summary}\n",
        };
    }

    /**
     * makePdf returns a one page PDF showing the title.
     */
    protected function makePdf(string $title): string
    {
        $text = str_replace(['(', ')'], '', $title);
        $stream = "BT /F1 20 Tf 40 100 Td ({$text}) Tj ET";

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 420 200] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            '<< /Length '.strlen($stream)." >>\nstream\n{$stream}\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n{$object}\nendobj\n";
        }

        $xrefAt = strlen($pdf);
        $pdf .= 'xref'."\n".'0 '.(count($objects) + 1)."\n".'0000000000 65535 f '."\n";

        foreach ($offsets as $offset) {
            $pdf .= sprintf('%010d 00000 n ', $offset)."\n";
        }

        return $pdf.'trailer << /Size '.(count($objects) + 1).' /Root 1 0 R >>'."\n"."startxref\n{$xrefAt}\n%%EOF\n";
    }
}
