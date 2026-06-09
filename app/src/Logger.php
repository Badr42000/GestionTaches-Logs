<?php

class Logger
{
    private string $host;
    private int $port;

    public function __construct()
    {
        $this->host = getenv('RSYSLOG_HOST') ?: 'rsyslog';
        $this->port = (int)(getenv('RSYSLOG_PORT') ?: 514);
    }

    public function send(string $severity, string $tag, string $message): void
    {
        $priorities = [
            'emerg' => 0, 'alert' => 1, 'crit' => 2, 'err' => 3,
            'warning' => 4, 'notice' => 5, 'info' => 6, 'debug' => 7,
        ];

        $priority = $priorities[$severity] ?? 6;
        $facility = 1;
        $code = $facility * 8 + $priority;

        $timestamp = date('M d H:i:s');
        $hostname = gethostname();
        $syslogMsg = "<{$code}>{$timestamp} {$hostname} {$tag}: {$message}\n";

        $socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if ($socket !== false) {
            @socket_sendto($socket, $syslogMsg, strlen($syslogMsg), 0, $this->host, $this->port);
            socket_close($socket);
        }
    }
}
