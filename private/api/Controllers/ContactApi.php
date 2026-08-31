<?php

class ContactApi
{
    public static function store(): ApiResponse
    {
        global $email;

        $body = ApiRequest::json();
        $name = trim((string) ($body['name'] ?? ''));
        $from = trim((string) ($body['email'] ?? ''));
        $message = trim((string) ($body['message'] ?? ''));

        $errors = [];
        if ($name === '') {
            $errors['name'] = 'Please enter your name.';
        }
        if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }
        if (mb_strlen($message) < 10) {
            $errors['message'] = 'Please enter a message of at least 10 characters.';
        }
        if ($errors) {
            throw ApiException::validation($errors);
        }

        // Honeypot: bots fill hidden fields, accept silently so they don't retry.
        if (!empty($body['company'])) {
            return ApiResponse::created(['sent' => true]);
        }

        $configured = !empty($email['username']) && !empty($email['from']['email']);
        if (!$configured) {
            throw new ApiException(
                503,
                'The contact form isn\'t wired up yet. Please email me directly for now.',
                'contact_unavailable',
            );
        }

        $sent = Mailer::sendEmail(
            $email['to'],
            'Portfolio contact from ' . $name,
            sprintf(
                "<p><strong>From:</strong> %s &lt;%s&gt;</p><p>%s</p>",
                htmlspecialchars($name),
                htmlspecialchars($from),
                nl2br(htmlspecialchars($message)),
            ),
        );

        if ($sent !== true) {
            throw new ApiException(502, 'Your message could not be sent. Please try again later.', 'mail_failed');
        }

        return ApiResponse::created(['sent' => true]);
    }
}
