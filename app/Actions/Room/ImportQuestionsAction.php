<?php

namespace App\Actions\Room;

use App\Enums\RoomStatus;
use App\Models\Room;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ImportQuestionsAction
{
    private const EXPECTED_HEADERS = [
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
    ];

    private const OPTION_LABELS = [
        'A',
        'B',
        'C',
        'D',
    ];

    public function execute(
        Room $room,
        UploadedFile $file,
    ): int {
        abort_unless(
            $room->status === RoomStatus::Locked,
            422,
            'Soal hanya dapat diimpor ketika room masih terkunci.',
        );

        $rows = $this->parseFile($file);

        if (count($rows) === 0) {
            throw ValidationException::withMessages([
                'file' => 'CSV tidak memiliki data soal.',
            ]);
        }

        return DB::transaction(
            function () use ($room, $rows) {
                $room = Room::query()
                    ->whereKey($room->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_unless(
                    $room->status === RoomStatus::Locked,
                    422,
                    'Room sudah tidak dapat diubah.',
                );

                $position = (int) $room
                    ->questions()
                    ->max('position');

                $imported = 0;

                foreach ($rows as $index => $row) {
                    /*
                     * +2 karena:
                     * row CSV pertama = header
                     */
                    $csvRowNumber = $index + 2;

                    $validated = $this->validateRow(
                        $row,
                        $csvRowNumber,
                    );

                    $position++;

                    $question = $room
                        ->questions()
                        ->create([
                            'text' =>
                                $validated['question'],

                            'position' =>
                                $position,

                            'game_order' =>
                                null,

                            'opened_at' =>
                                null,

                            'closed_at' =>
                                null,
                        ]);

                    foreach (
                        $validated['options']
                        as $option
                    ) {
                        $question
                            ->options()
                            ->create([
                                'label' =>
                                    $option['label'],

                                'text' =>
                                    $option['text'],

                                'is_correct' =>
                                    $option['is_correct'],
                            ]);
                    }

                    $imported++;
                }

                return $imported;
            },
        );
    }

    private function parseFile(
        UploadedFile $file,
    ): array {
        $handle = fopen(
            $file->getRealPath(),
            'r',
        );

        if ($handle === false) {
            throw ValidationException::withMessages([
                'file' => 'File CSV tidak dapat dibaca.',
            ]);
        }

        try {
            $delimiter =
                $this->detectDelimiter($handle);

            rewind($handle);

            $header = fgetcsv(
                $handle,
                0,
                $delimiter,
            );

            if (!$header) {
                throw ValidationException::withMessages([
                    'file' => 'Header CSV tidak ditemukan.',
                ]);
            }

            $header = array_map(
                fn ($value) =>
                    $this->normalizeHeader(
                        (string) $value,
                    ),
                $header,
            );

            $this->validateHeader($header);

            $rows = [];

            while (
                (
                    $values = fgetcsv(
                        $handle,
                        0,
                        $delimiter,
                    )
                ) !== false
            ) {
                /*
                 * Abaikan baris kosong.
                 */
                if (
                    count(
                        array_filter(
                            $values,
                            fn ($value) =>
                                trim((string) $value) !== '',
                        ),
                    ) === 0
                ) {
                    continue;
                }

                /*
                 * Samakan jumlah kolom dengan header.
                 */
                $values = array_pad(
                    $values,
                    count($header),
                    '',
                );

                $values = array_slice(
                    $values,
                    0,
                    count($header),
                );

                $rows[] = array_combine(
                    $header,
                    $values,
                );
            }

            return $rows;
        } finally {
            fclose($handle);
        }
    }

    private function detectDelimiter(
        $handle,
    ): string {
        $firstLine = fgets($handle);

        if ($firstLine === false) {
            return ',';
        }

        $delimiters = [
            ',' => substr_count(
                $firstLine,
                ',',
            ),

            ';' => substr_count(
                $firstLine,
                ';',
            ),

            "\t" => substr_count(
                $firstLine,
                "\t",
            ),
        ];

        arsort($delimiters);

        return (string) array_key_first(
            $delimiters,
        );
    }

    private function normalizeHeader(
        string $header,
    ): string {
        /*
         * Hapus UTF-8 BOM dari header pertama.
         */
        $header = preg_replace(
            '/^\xEF\xBB\xBF/',
            '',
            $header,
        );

        return strtolower(
            trim($header),
        );
    }

    private function validateHeader(
        array $header,
    ): void {
        foreach (
            self::EXPECTED_HEADERS
            as $requiredHeader
        ) {
            if (
                !in_array(
                    $requiredHeader,
                    $header,
                    true,
                )
            ) {
                throw ValidationException::withMessages([
                    'file' =>
                        "Kolom CSV \"{$requiredHeader}\" tidak ditemukan.",
                ]);
            }
        }
    }

    private function validateRow(
        array $row,
        int $rowNumber,
    ): array {
        $question = trim(
            (string) (
                $row['question'] ?? ''
            ),
        );

        if ($question === '') {
            $this->rowError(
                $rowNumber,
                'Pertanyaan wajib diisi.',
            );
        }

        if (mb_strlen($question) > 2000) {
            $this->rowError(
                $rowNumber,
                'Pertanyaan maksimal 2000 karakter.',
            );
        }

        $rawOptions = [
            'A' => trim(
                (string) (
                    $row['option_a'] ?? ''
                ),
            ),

            'B' => trim(
                (string) (
                    $row['option_b'] ?? ''
                ),
            ),

            'C' => trim(
                (string) (
                    $row['option_c'] ?? ''
                ),
            ),

            'D' => trim(
                (string) (
                    $row['option_d'] ?? ''
                ),
            ),
        ];

        if (
            $rawOptions['A'] === '' ||
            $rawOptions['B'] === ''
        ) {
            $this->rowError(
                $rowNumber,
                'Pilihan A dan B wajib diisi.',
            );
        }

        /*
         * Tidak boleh ada gap.
         *
         * Contoh invalid:
         * A ada
         * B ada
         * C kosong
         * D ada
         */
        if (
            $rawOptions['C'] === '' &&
            $rawOptions['D'] !== ''
        ) {
            $this->rowError(
                $rowNumber,
                'Pilihan D tidak boleh diisi jika pilihan C kosong.',
            );
        }

        foreach (
            $rawOptions
            as $label => $text
        ) {
            if (
                mb_strlen($text) > 1000
            ) {
                $this->rowError(
                    $rowNumber,
                    "Pilihan {$label} maksimal 1000 karakter.",
                );
            }
        }

        $correctAnswer = strtoupper(
            trim(
                (string) (
                    $row['correct_answer'] ?? ''
                ),
            ),
        );

        if (
            !in_array(
                $correctAnswer,
                self::OPTION_LABELS,
                true,
            )
        ) {
            $this->rowError(
                $rowNumber,
                'correct_answer harus A, B, C, atau D.',
            );
        }

        if (
            ($rawOptions[$correctAnswer] ?? '')
            === ''
        ) {
            $this->rowError(
                $rowNumber,
                "Jawaban benar {$correctAnswer} tidak memiliki teks pilihan.",
            );
        }

        $options = [];

        foreach (
            self::OPTION_LABELS
            as $label
        ) {
            $text =
                $rawOptions[$label];

            if ($text === '') {
                continue;
            }

            $options[] = [
                'label' =>
                    $label,

                'text' =>
                    $text,

                'is_correct' =>
                    $label ===
                    $correctAnswer,
            ];
        }

        return [
            'question' =>
                $question,

            'options' =>
                $options,
        ];
    }

    private function rowError(
        int $row,
        string $message,
    ): never {
        throw ValidationException::withMessages([
            'file' =>
                "Baris {$row}: {$message}",
        ]);
    }
}