<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\App;

final class Mailer
{
    public function send(string $to, string $subject, string $textBody): void
    {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/plain; charset=UTF-8',
            'From: ' . config('mail.from_name') . ' <' . config('mail.from_address') . '>',
        ];

        $sent = @mail($to, $subject, $textBody, implode("\r\n", $headers));

        if ($sent || (bool) config('mail.debug_copy_to_storage', true)) {
            $directory = App::config('app.storage_path') . '/mail';
            if (!is_dir($directory)) {
                mkdir($directory, 0775, true);
            }

            $filename = $directory . '/' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.txt';
            file_put_contents($filename, "To: {$to}\nSubject: {$subject}\n\n{$textBody}");
        }
    }
}
